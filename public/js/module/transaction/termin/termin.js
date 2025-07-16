var table
$( function(){
    function format(rowData) {
        var childTable = `<table id="cl-${rowData.transactionnumber}" class="display compact nowrap w-100" width="100%">` +
            `<thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nama Termin</th>
                    <th scope="col">Persentase (%)</th>
                    <th scope="col">Value (Rp)</th>
                    <th scope="col">Pekerjaan</th>
                    <th scope="col">Due Date</th>
                </tr>
            </thead >` +
            '</table>';
        return $(childTable).toArray();
    }

    table = $('.data-table').DataTable({
        dom: 't',
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}master-project/termin/data`,
            method: "POST",
            data: function(data) {
                data._token = `${$('meta[name="csrf-token"]').attr('content')}`
            },
        },
        columns: [
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: ''
            },
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'project_detail.name',
                name: 'project_detail.name'
            },
            {
                data: 'project_detail.pc.name',
                name: 'project_detail.pc.name'
            },
            {
                data: 'project_detail.sales.name',
                name: 'project_detail.sales.name'
            },
            {
                data: 'project_detail.value',
                name: 'project_detail.value',
                render: function (data, type, row) {
                    // Memastikan bahwa format hanya diterapkan saat menampilkan data, bukan pada data yang dikirimkan ke server
                    if (type === 'display') {
                        return formatNumberWithCommas(data);
                    }
                    return data;
                }
            },
            {
                data: 'project_detail.startdate',
                name: 'project_detail.startdate'
            },
            {
                data: 'project_detail.enddate',
                name: 'project_detail.enddate',
                className: 'text-center'
            }
        ],
    });

    $('.data-table tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var rowData = row.data();

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');

            // Destroy the Child Datatable
            $(`#cl-${rowData.transactionnumber}`).DataTable().destroy();
        }
        else {
            // Open this row
            row.child(format(rowData)).show();
            var id = rowData.transactionnumber;

            childTable = $(`#cl-${id}`).DataTable({
                dom: "t",
                ajax: {
                    url: `${$('meta[name="baseurl"]').attr('content')}master-project/termin/detail/${rowData.transactionnumber}`,
                    method: "POST",
                    data: function(data) {
                        data._token = `${$('meta[name="csrf-token"]').attr('content')}`
                    },
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'percentage',
                        name: 'percentage'
                    },
                    {
                        data: 'value',
                        name: 'value',
                        render: function (data, type, row) {
                            // Memastikan bahwa format hanya diterapkan saat menampilkan data, bukan pada data yang dikirimkan ke server
                            if (type === 'display') {
                                return formatNumberWithCommas(data);
                            }
                            return data;
                        }
                    },
                    {
                        data: 'pekerjaan',
                        name: 'pekerjaan'
                    },
                    {
                        data: 'duedate',
                        name: 'duedate',
                    }
                ],
                select: false,
                fnDrawCallback: () => {
                    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
                },
            });

            tr.addClass('shown');
        }
    });

    $('.select-project').select2({
        theme: 'bootstrap-5'
    });
})

function formatNumberWithCommas(number) {
    return 'Rp. ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function changeVal(el){
    if($(el).attr('id') == 'project_id'){
        let project_id = btoa($(el).val())
        $('#form-inner').html('')
        $('#form-add-container').slideUp()
        $('#percentageTotal').html('0')
        $('#percentageContainer').fadeOut()
        getProjectDetail(project_id)
    }

    if($(el).attr('id') == 'percentage'){
        var value_project = $('span#value').html()
        value_project = Number(value_project.replace(/[^0-9.-]+/g,""));
        const value_termin = formatNumberWithCommas(value_project * Number($(el).val()) / 100)
        $(el).parent().next().children().val(value_termin)
    }

    if($(el).attr('name') == 'timelineA_id[]'){
        let dueDate = $(el).find(':selected').data('duedate')
        console.log($(el).parent().next().children().val(dueDate))
    }
}

function getProjectDetail(project_id){
    apiCall(`master-project/termin/project-detail/${project_id}`, 'GET', '', null, null,
    null,
    true,
    (res) => {
        $('.loading').hide()

        var pcName, salesName, value, project_time
        pcName = res.data.pc.name
        salesName = res.data.sales.name
        value = formatNumberWithCommas(res.data.value)
        project_time = res.data.startdate + ' - ' + res.data.enddate

        $('#project-detail').find('#nama_pc').html(pcName)
        $('#project-detail').find('#nama_sales').html(salesName)
        $('#project-detail').find('#value').html(value)
        $('#project-detail').find('#project_time').html(project_time)

        $('#project-detail').slideDown()
    })
}

$('.btn-add').on('click', function(){
    if($('#form-add-container').css('display') == 'none'){
        var project_id = $('select[name="project_id"] option:selected').val()
        project_id = btoa(project_id)

        apiCall(`master-project/termin/render-form-add/${project_id}`, 'GET', '', null, null,
        null,
        true,
        (res) => {
            $('.loading').hide()
            $('#form-inner').html(res.blade)
            $('.select-timelineA').select2()
            $('#form-add-container').slideDown()
            $('#percentageContainer').fadeIn()
        })
    }
})

function addTermin(el){
    let parentEl = $(el).parent().parent().parent()
    $('.select-timelineA').select2('destroy');
    let duplicateForm = $(el).parent().parent().clone(true).appendTo(parentEl)
    $(duplicateForm).find('.btn-remove-termin').fadeIn()

    $('.select-timelineA').select2();
    countValue()
}

function removeTermin(el){
    Swal.fire({
        title: 'Apakah anda yakin ingin menghapus termin ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText:'Batal',
        confirmButtonText: 'Ya, Hapus termin'
    }).then((result) => {
        if (result.isConfirmed == true){
            $(el).parent().parent().remove()
        }
    })
}

function countValue(el){
    // Show Total percentage
    let totalPercentage = 0
    $('input[name="percentage[]"]').each(function(k,v){
        if($(v).val() != ''){
            totalPercentage += parseInt($(v).val())
        }
    })

    $('#percentageTotal').html(`${totalPercentage}`)
    if(totalPercentage > 100){
        $('#percentageTotal').removeAttr('class').addClass('text-danger')
    } else if(totalPercentage == 100){
        $('#percentageTotal').removeAttr('class').addClass('text-success')
    } else {
        $('#percentageTotal').removeAttr('class').addClass('text-black')
    }
}

function formatNumberWithCommas(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$('#btn-save').on('click', function(){
    prompt('submit', 'Termin', (confirm) => {
        if (confirm){
            const fAddComponent = $('#add-termin')
            var required = fAddComponent.find('.required')
            var canInput = true
            let totalTermin = 0

            required.removeClass('is-invalid')

            // Form Validation
            for(var i = 0; i < required.length; i++){
                if (required[i].value == ''){
                    canInput = false
                    fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
                    fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
                    fAddComponent.find(`textarea[name="${required[i].name}"]`).addClass('is-invalid')
                }

                if(required[i].name == 'percentage[]'){
                    totalTermin += parseInt(required[i].value)
                }
            }

            if(totalTermin != 100){
                let text = totalTermin > 100 ? 'lebih' : 'kurang'
                fAddComponent.find(`input[name="percentage[]"]`).addClass('is-invalid')
                Toastify({
                    text: `Total persentase termin keseluruhan harus 100`,
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
                apiCall('master-project/termin/store', 'POST', 'add-termin',
                {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
                null,
                (err) => {
                    console.log(err)
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal simpan data termin, ${err.statusText}`,
                        duration: 3000,
                        close:true,
                        gravity:"top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                        }

                    }).showToast();
                },true,(res) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil simpan data termin`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/termin`)
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
