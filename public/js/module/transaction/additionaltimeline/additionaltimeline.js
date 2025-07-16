$( function(){
    $('.select-employee').select2();
    $('input[name="bobot[]"]').inputmask({ regex: "^[0-9][0-9]?$|^100$", placeholder: '' })
})

function renderTimeline(id){
    apiCall(`master-project/additional-timeline/getTimeline/${id}`, 'GET', '', null, null,
    null,
    true,
    (res) => {
        $('.loading').hide()

        const mainTimeline = res.data.mainTimeline
        if(mainTimeline != null){
            if(mainTimeline.approved_by != null){
                $('#action-button').fadeIn()
                $('#action-button2').fadeIn()
                $('#project-timeline-container').html(res.blade)
                $('#btn-add').attr('onclick', `renderView('${$('meta[name="baseurl"]').attr('content')}master-project/additional-timeline/add/${btoa(mainTimeline.project_id)}')`)
                $('#currentTimeline-tab').attr('onclick', `getCurrentTimeline('${btoa(mainTimeline.transactionnumber)}')`)

                timelineDataTable(mainTimeline.transactionnumber)
            } else {
                $('#action-button').fadeOut()
                $('#action-button2').fadeOut()
                $('#project-timeline-container').html(`
                    <center class="mb-5 mt-3">
                        <h6 class="text-danger">Tidak ada data timeline yang sudah di approve, tidak dapat menambahkan additional timeline</h6>
                    </center>
                `)
            }
        } else {
            $('#action-button').fadeOut()
            $('#action-button2').fadeOut()
            $('#project-timeline-container').html(res.blade)
        }
    })
}

function timelineDataTable(tn_number){
    tn_number = btoa(tn_number)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var table = $('.data-table').DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}master-project/additional-timeline/datatable`,
            method: "POST",
            data: function(data) {
                data._token = `${$('meta[name="csrf-token"]').attr('content')}`,
                data.tn_number = tn_number
            },
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                className: 'text-center',
                width: '20px'
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center',
            },
            {
                data: 'ad_number',
                name: 'ad_number',
                className: 'text-center'
            },
            {
                data: 'transactionnumber',
                name: 'transactionnumber',
                className: 'text-center'
            },
            {
                data: 'action',
                name: 'action',
                className: 'text-center'
            },
        ]
    });
}

function setPhase(){
    $('select[name="timeline-type"]').removeClass('is-invalid')
    let timelineType = $('select[name="timeline-type"]').val()
    let project_id = $('input[name="project_id"]').val()
    project_id = btoa(project_id)

    if(timelineType != null){
        apiCall(`master-project/additional-timeline/set-phase/${timelineType}/${project_id}`, 'GET', '', null, null,
        null,
        true,
        (res) => {
            $('.loading').hide()
            $('#choose-timeline-type').fadeOut()
            $('#btn-save').fadeIn()
            $('#bobotContainer').fadeIn()

            let html = ''
            res.data.map((v, k) => {
                html += res.blade
                $('#phase-container').html(html)
            })

            const elForm = $('#phase-container').find('input[name="show_phase[]"]')
            const phaseForm = $('#phase-container').find('input[name="phase[]"]')
            for (let i = 0; i < elForm.length; i++) {
                $(elForm[i]).val(res.data[i].name)//.prop('readonly', true)
                $(phaseForm[i]).val(res.data[i].name)
            }

            // $('.deletePhase').fadeOut()
            $('.select-employee').select2();
            $('#phase-container').fadeIn(750)
        })
    } else {
        $('select[name="timeline-type"]').addClass('is-invalid')
        Toastify({
            text: `Harap pilih tipe timeline`,
            duration: 3000,
            close:true,
            gravity:"top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #ff5f6d, #ffc371)",
            }
        }).showToast();
    }
}

function addPhase(project_id){
    apiCall(`master-project/additional-timeline/render-form/${btoa(project_id)}`, 'GET', '', null, null,
    null,
    true,
    (res) => {
        $('.loading').hide()
        $('#phase-container').append(res.blade)
        $('.select-employee').select2();
    })
}

function addWork(el){
    let parentEl = $(el).parent().parent().parent()
    $('.select-employee').select2('destroy');
    let duplicateForm = $(el).parent().parent().clone().appendTo(parentEl).find('.timelineA_id').val('')
    $(duplicateForm).find('.btn-remove-work').fadeIn()
    $('.select-employee').select2();
}

function removeWork(el){
    Swal.fire({
        title: 'Apakah anda yakin ingin menghapus baris ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText:'Batal',
        confirmButtonText: 'Ya, Hapus baris'
    }).then((result) => {
        if (result.isConfirmed)
            $(el).parent().parent().remove()
    })
}

function removePhase(el){
    Swal.fire({
        title: 'Apakah anda yakin ingin menghapus phase ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText:'Batal',
        confirmButtonText: 'Ya, Hapus phase'
    }).then((result) => {
        if (result.isConfirmed){
            $(el).parent().parent().parent().next().remove()
            $(el).parent().parent().parent().remove()
        }
    })
}


function cancel(){
    Swal.fire({
        title: 'Apakah anda ingin kembali atau mengganti jenis timeline?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText:'Kembali ke Halaman Awal',
        confirmButtonText: 'Ganti Timeline'
    }).then((result) => {
        if (result.isConfirmed){
            $('#phase-container').html('')
            $('#phase-container').fadeOut()
            $('#btn-save').fadeOut(function() {
                $(this).attr("style", "display: none !important");
            });
            $('#choose-timeline-type').fadeIn(750)
            $('#bobotTotal').html('0')
            $('#bobotContainer').fadeOut()
        } else if(result.isDismissed) {
            renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/additional-timeline`)
        }
    })
}

function changeVal(el){
    if($(el).hasClass('is-invalid') && $(el).val() != ''){
        $(el).removeClass('is-invalid')
    } else if ($(el).val() == ''){
        $(el).addClass('is-invalid')
    }

    // Set Min Date on end_date
    if($(el).attr('type') == 'date' && $(el).attr('name') == 'start_date[]') {
        const minEndDate = $(el).val()
        $(el).parent().parent().find('input[name="end_date[]"]').attr('min', minEndDate).prop('disabled', false)
        if(minEndDate == ''){
            $(el).parent().parent().find('input[name="end_date[]"]').prop('disabled', true).val('')
        }
    }

    // Set Phase hidden value
    if($(el).attr('name') == 'show_phase[]'){
        $(el).parent().parent().parent().find('input[name="phase[]"]').val($(el).val())
    }
}

function countBobot(el){
    // Show Total Bobot
    let totalBobot = 0
    $('input[name="bobot[]"]').each(function(k,v){
        if($(v).val() != ''){
            totalBobot += parseInt($(v).val())
        }
    })

    $('#bobotTotal').html(`${totalBobot}`)
    if(totalBobot > 100){
        $('#bobotTotal').removeAttr('class').addClass('text-danger')
    } else if(totalBobot == 100){
        $('#bobotTotal').removeAttr('class').addClass('text-success')
    } else {
        $('#bobotTotal').removeAttr('class').addClass('text-black')
    }
}

$('#btn-save').on('click', function(){
    prompt('submit', 'Additional Timeline', (confirm) => {
        if (confirm){
            const fAddComponent = $('#add-timeline')
            var required = fAddComponent.find('.required')
            var canInput = true
            let totalBobot = 0

            required.removeClass('is-invalid')

            // Form Validation
            for(var i = 0; i < required.length; i++){
                if (required[i].value == ''){
                    console.log(required[i])
                    canInput = false
                    fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
                    fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
                    fAddComponent.find(`textarea[name="${required[i].name}"]`).addClass('is-invalid')
                }

                if(required[i].name == 'bobot[]'){
                    totalBobot += parseInt(required[i].value)
                }
            }

            if(totalBobot != 100){
                let text = totalBobot > 100 ? 'lebih' : 'kurang'
                fAddComponent.find(`input[name="bobot[]"]`).addClass('is-invalid')
                Toastify({
                    text: `Total bobot keseluruhan harus 100`,
                    duration: 3000,
                    close:true,
                    gravity:"top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    }
                }).showToast();

                return false
            }


            if (canInput == false){
                Toastify({
                    text: `Masih ada data yang kosong, harap lengkapi semua data terlebih dahulu`,
                    duration: 3000,
                    close:true,
                    gravity:"top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    }
                }).showToast();
            } else if (canInput == true){
                apiCall('master-project/additional-timeline/store', 'POST', 'add-timeline',
                {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
                null,
                (err) => {
                    console.log(err)
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal simpan data Additional Timeline, ${err.statusText}`,
                        duration: 3000,
                        close:true,
                        gravity:"top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                        }

                    }).showToast();
                },true,(res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil simpan data Additional Timeline`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/additional-timeline`)
                            $(`v-pills-${$('input[name="project_id"]').val()}-tab`).trigger('click')
                        },
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #00b09b, #96c93d)",
                        }

                    }).showToast();
                })
            }
        }
    })
})

function showLeftNav(){
    $('#container-project-list').animate({width:'toggle'},350)
    setTimeout(() => {
        if($('#container-project-list').css('display') == 'none'){
            $('#container-timeline').removeClass('col-md-9').addClass('col-md-12')
            $('#action-button2').children().removeClass('fa-chevron-left').addClass('fa-chevron-right')
        }
    }, 400);

    if($('#container-project-list').css('display') != 'none'){
        $('#container-timeline').removeClass('col-md-12').addClass('col-md-9')
        $('#action-button2').children().removeClass('fa-chevron-right').addClass('fa-chevron-left')
    }
}

function changeProject(){
    if($('#currentProject').css('display') == 'none'){
        $('#changeProject').fadeOut()
        $('#currentProject').fadeIn()
    } else {
        $('select[name="new_project_id"]').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Project",
            allowClear: true
        });
        $('#changeProject').fadeIn()
        $('#currentProject').fadeOut()
    }
}

function changePerson(el){
    if($(el).parent().parent().parent().parent().find('#default-emp').css('display') == 'none'){
        $(el).parent().parent().parent().parent().find('#form-emp').fadeOut()
        $(el).parent().parent().parent().parent().find('#default-emp').fadeIn()
        $(el).parent().parent().parent().parent().find('.select-employee').removeClass('required')
    } else {
        console.log($(el).parent().parent().parent().parent().find('.select-employee'))

        $(el).parent().parent().parent().parent().find('#default-emp').fadeOut()
        $(el).parent().parent().parent().parent().find('#form-emp').fadeIn()
        $(el).parent().parent().parent().parent().find('.select-employee').select2();
        $(el).parent().parent().parent().parent().find('.select-employee').addClass('required')
    }
}

$('#btn-update').on('click', function(){
    prompt('update', 'Additional Timeline', (confirm) => {
        if (confirm){
            const fAddComponent = $('#edit-timeline')
            var required = fAddComponent.find('.required')
            var canInput = true
            let totalBobot = 0

            required.removeClass('is-invalid')

            // Form Validation
            for(var i = 0; i < required.length; i++){
                if (required[i].value == ''){
                    canInput = false
                    fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
                    fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
                    fAddComponent.find(`textarea[name="${required[i].name}"]`).addClass('is-invalid')
                    console.log(required[i].name)
                }

                if(required[i].name == 'bobot[]'){
                    totalBobot += parseInt(required[i].value)
                }
            }

            if(totalBobot != 100){
                let text = totalBobot > 100 ? 'lebih' : 'kurang'
                fAddComponent.find(`input[name="bobot[]"]`).addClass('is-invalid')
                Toastify({
                    text: `Total bobot keseluruhan tidak boleh ${text} dari 100`,
                    duration: 3000,
                    close:true,
                    gravity:"top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    }
                }).showToast();

                return false
            }

            if (canInput == false){
                Toastify({
                    text: `Masih ada data yang kosong, harap lengkapi semua data terlebih dahulu`,
                    duration: 3000,
                    close:true,
                    gravity:"top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    }
                }).showToast();
            } else if (canInput == true){
                apiCall('master-project/additional-timeline/update', 'POST', 'edit-timeline',
                {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
                null,
                (err) => {
                    console.log(err)
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal simpan data additional timeline, ${err.statusText}`,
                        duration: 3000,
                        close:true,
                        gravity:"top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                        }

                    }).showToast();
                },true,(res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil simpan data additional timeline`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/additional-timeline`)
                            $(`v-pills-${$('input[name="project_id"]').val()}-tab`).trigger('click')
                        },
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #00b09b, #96c93d)",
                        }

                    }).showToast();
                })
            }
        }
    })
})

function expandChart(cardBody){
    if($(cardBody).css('display') == 'none'){
        $(cardBody).slideDown()
    } else {
        $(cardBody).slideUp()
    }
}

function approve(id){
    id = btoa(id)

    prompt('approve', 'Additional Timeline', (confirm) => {
        if(confirm){
            apiCall(`master-project/additional-timeline/approve/${id}`, 'GET', '',null, null,
            (err) => {
                console.log(err)
                $('.loading').hide()
                Toastify({
                    text: `Gagal approve data additional timeline, ${err.statusText}`,
                    duration: 3000,
                    close:true,
                    gravity:"top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    }

                }).showToast();
            }, true, (res) => {
                $('.loading').hide()
                Toastify({
                    text: `Berhasil approve data additional timeline`,
                    duration: 1000,
                    close:true,
                    gravity:"top",
                    callback: function() {
                        renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/additional-timeline`)
                    },
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)",
                    }

                }).showToast();
            })
        }
    })
}

function setDefault(id){
    id = btoa(id)

    prompt('set-default', 'Additional Timeline', (confirm) => {
        if(confirm){
            apiCall(`master-project/additional-timeline/set-default/${id}`, 'GET', '',null, null,
            (err) => {
                console.log(err)
                $('.loading').hide()
                Toastify({
                    text: `Gagal set default data additional timeline, ${err.statusText}`,
                    duration: 3000,
                    close:true,
                    gravity:"top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    }

                }).showToast();
            }, true, (res) => {
                $('.loading').hide()
                Toastify({
                    text: `Berhasil set default data additional timeline`,
                    duration: 1000,
                    close:true,
                    gravity:"top",
                    callback: function() {
                        renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/additional-timeline`)
                    },
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)",
                    }

                }).showToast();
            })
        }
    })
}

function getCurrentTimeline(tn_number){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.data-table-current').DataTable().destroy()
    var table = $('.data-table-current').DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}master-project/additional-timeline/show-current-datatable`,
            method: "POST",
            data: function(data) {
                data._token = `${$('meta[name="csrf-token"]').attr('content')}`,
                data.tn_number = tn_number
            },
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                className: 'text-center',
                width: '20px'
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center'
            },
            {
                data: 'transactionnumber',
                name: 'transactionnumber',
                className: 'text-center'
            },
            {
                data: 'detail',
                name: 'detail',
                className: 'text-center'
            },
            {
                data: 'startdate',
                name: 'startdete',
                className: 'text-center'
            },
            {
                data: 'enddate',
                name: 'enddate',
                className: 'text-center'
            },
            {
                data: 'bobot',
                name: 'bobot',
                className: 'text-center'
            },
            {
                data: 'karyawan',
                name: 'karyawan',
                className: 'text-center'
            },
        ]
    });
}
