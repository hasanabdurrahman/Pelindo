var table_all, table_my_tasklist, table_rejected
var startMy, endMy, projectMy, statusPr, phase

$(function () {
    loadTableAll()
    loadMyTasklist()
    loadRejectedTable()

    $('#project_id').select2({
        theme: 'bootstrap-5',
        placeholder: "Pilih Project",
        allowClear: true
    });

    $('.project_id').select2({
        theme: 'bootstrap-5',
        placeholder: "Pilih Project",
        allowClear: true
    });

    $('#timelineA_id').select2({
        theme: 'bootstrap-5',
        placeholder: "Pilih Timeline Detail",
        allowClear: true
    });

    $('#timelineA').select2({
        theme: 'bootstrap-5',
        placeholder: "Pilih Timeline Detail",
        allowClear: true
    });
});

function loadTableAll(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });

    // Inisialisasi start dan end
    startMy = $('#start_date').val();
    endMy = $('#end_date').val();
    projectMy = $('#project').val();
    statusPr = $('#status').val();
    phase = $('#timelineA_id').val();

    table_all = $('.data-table-all').DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        buttons: [
            'copy', 'excelFlash', 'excel', 'pdf', 'print', {
                text: 'Reload',
                action: function(e, dt, node, config) {
                    dt.ajax.reload();
                }
            }
        ],
        pageLength: 10, // Menampilkan 10 data per halaman awal
        lengthMenu: [10, 25, 50, 75, 100], // Opsi untuk panjang tampilan halaman
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist/datatableall`,
            method: "POST",
            data: function(data) {
                data.start_date = startMy;
                data.end_date = endMy;
                data.project = projectMy
                data.status = statusPr;
                data.phase = phase
                data._token = `${$('meta[name="csrf-token"]').attr('content')}`
            },
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                className: 'text-center',
                width: '20px'
            },
            {
                data: 'karyawan_name',
                name: 'karyawan_name',
                className: 'text-center'
            },
            {
                data: 'periode_pekerjaan',
                name: 'periode_pekerjaan',
                className: 'text-center'
            },
            {
                data: 'input_date',
                name: 'input_date',
                className: 'text-center'
            },
            {
                data: 'approve',
                name: 'approve',
                className: 'text-center'
            },
            {
                data: 'transactionnumber',
                name: 'transactionnumber',
                className: 'text-center'
            },
            {
                data: 'project_name',
                name: 'project_name',
                className: 'text-center'
            },
            {
                data: 'timelineA',
                name: 'timelineA',
                className: 'text-center'
            },
            {
                data: 'progress',
                name: 'progress',
                className: 'text-center'
            },
            {
                data: 'description',
                name: 'description',
                className: 'text-center'
            },
            // {
            //     data: 'status',
            //     name: 'status',
            //     className: 'text-center'
            // },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center',
                width: '200px'
            },
        ],
        fnDrawCallback: () => {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        },
        search: {
            "regex": true
          }
    });

    ImportExport(table_all);
}

function loadMyTasklist(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });
    startMy = $('#start_date').val();
    endMy = $('#end_date').val();
    projectMy = $('#project').val();

    table_my_tasklist = $('.data-table').DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        buttons: [
            'copy', 'excelFlash', 'excel', 'pdf', 'print', {
                text: 'Reload',
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                }
            }
        ],
        pageLength: 10, // Menampilkan 10 data per halaman awal
        lengthMenu: [10, 25, 50, 75, 100], // Opsi untuk panjang tampilan halaman

        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist/datatable`,
            method: "POST",
            data: function (data) {
                data.start_date = startMy;
                data.end_date = endMy;
                data.project = projectMy;
                data.karyawan_name = '{{ Auth::user()->name }}';
                data.phase = phase
                data._token = `${$('meta[name="csrf-token"]').attr('content')}`
            },
        },
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Calculate the sum of values in the "progress" column
            var totalProgress = api
                .column('progress:name', { search: 'applied' })
                .data()
                .reduce(function (acc, val) {
                    return acc + parseFloat(val);
                }, 0);

            // Display the sum in the footer row
            $(api.column('progress:name').footer()).html('Total: ' + totalProgress + '%');
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            className: 'text-center',
            width: '20px'
        },
       {
                data: 'karyawan_name',
                name: 'karyawan_name',
                className: 'text-center',
            },
            {
                data: 'periode_pekerjaan',
                name: 'periode_pekerjaan',
                className: 'text-center'
            },
            {
                data: 'input_date',
                name: 'input_date',
                className: 'text-center'
            },
            {
                data: 'transactionnumber',
                name: 'transactionnumber',
                className: 'text-center'
            },
            {
                data: 'project_name',
                name: 'project_name',
                className: 'text-center',
            },
            {
                data: 'timelineA',
                name: 'timelineA',
                className: 'text-center'
            },
            {
                data: 'progress',
                name: 'progress',
                className: 'text-center'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center',
                width: '200px'

            }
        ],
        fnDrawCallback: () => {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        },
        search: {
            "regex": true
          }
    });

    ImportExport(table_my_tasklist);
}

$('#project').change(function() {
    projectMy = $('#project').val();
    $('#timelineA').select2('val', '')
    phase = ''

    if (projectMy) {
        $.ajax({
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist/get-all-timelinea/`+projectMy,
            type: 'GET',
            data: {},
            dataType: 'json',
            success: function(data) {

                // console.log(data);
                if (data.length > 0) {
                    $('#timelineA').empty();
                    $('#timelineA').append('<option value="" hidden>-Pilih-</option>');
                    $.each(data, function (key, timelineA) {
                        $('#timelineA').append(
                            '<option data-isDocument="'+timelineA.is_document+'" value="' + timelineA.id + '">' +
                            timelineA.detail + ' (' + timelineA.startdate + ' - ' + timelineA.enddate + ')</option>'
                        );
                    });
                    $('#timelineA').val('{{ $tasklist->timelineA_id }}');
                } else{
                    $('#timelineA').empty();
                }
                $('#timelineA').val('{{ $tasklist->timelineA_id }}');
            }
        });
    } else {
        $('#timelineA').empty();
    }

    table_my_tasklist.ajax.reload();
    table_all.ajax.reload();
});

$('#timelineA').on('change', function(){
    phase = $('#timelineA').val()
    table_my_tasklist.ajax.reload();
    table_all.ajax.reload();
})

$('#status').change(function() {
    statusPr = $('#status').val();
    table_all.ajax.reload();
});

$('#start_date, #end_date').change(function() {
    startMy = $('#start_date').val();
    endMy = $('#end_date').val();
    table_my_tasklist.ajax.reload();
    table_all.ajax.reload();
});

function loadRejectedTable(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });

    let start_date = $('#start_date').val();
    let end_date = $('#end_date').val();

    table_rejected = $('.data-table-rejected').DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        buttons: [
            'copy', 'excelFlash', 'excel', 'pdf', 'print', {
                text: 'Reload',
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                }
            }
        ],
        pageLength: 10, // Menampilkan 10 data per halaman awal
        lengthMenu: [10, 25, 50, 75, 100], // Opsi untuk panjang tampilan halaman

        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist/datatable-rejected`,
            method: "POST",
            data: function (data) {
                data.start_date = start_date;
                data.end_date = end_date;
                data.karyawan_name = '{{ Auth::user()->name }}';
                data._token = `${$('meta[name="csrf-token"]').attr('content')}`
            },
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            className: 'text-center',
            width: '20px'
        },
       {
                data: 'karyawan_name',
                name: 'karyawan_name',
                className: 'text-center',
            },
            {
                data: 'startdate',
                name: 'startdate',
                className: 'text-center'
            },
            {
                data: 'enddate',
                name: 'enddate',
                className: 'text-center'
            },
            {
                data: 'approve',
                name: 'approve',
                className: 'text-center'
            },
            {
                data: 'transactionnumber',
                name: 'transactionnumber',
                className: 'text-center'
            },
            {
                data: 'project_name',
                name: 'project_name',
                className: 'text-center'
            },
            {
                data: 'timelineA',
                name: 'timelineA',
                className: 'text-center'
            },
            {
                data: 'progress',
                name: 'progress',
                className: 'text-center'
            },
            {
                data: 'description',
                name: 'description',
                className: 'text-center'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center',
                width: '200px'

            },
        ],
        fnDrawCallback: () => {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        },
        search: {
            "regex": true
        }
    });

    $('#start_date, #end_date').change(function() {
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        table_rejected.ajax.reload();
    });

    ImportExport(table_rejected);
}

function reloadTable(type){
    if (type == 'rejected'){
        table_rejected.ajax.reload();
    } else if(type == 'overview'){
        table_all.ajax.reload();
    } else if(type == 'my_tasklist') {
        table_my_tasklist.ajax.reload();
    }
}

function updateEndDateMin() {
    var startDateInput = document.getElementById('start_date');
    var endDateInput = document.getElementById('end_date');

    // Set atribut 'min' pada 'End Date' menjadi nilai 'Start Date'
    endDateInput.min = startDateInput.value;
    // endDateInput.value = startDateInput.value; // Juga atur nilai 'End Date'
}

function checkEndDate() {
    var startDateInput = document.getElementById('start_date');
    var endDateInput = document.getElementById('end_date');

    if (startDateInput.value && endDateInput.value && startDateInput.value > endDateInput.value) {
        alert("End Date harus lebih besar dari atau sama dengan Start Date");
        endDateInput.value = startDateInput.value;
    }
}

function deleteTasklist(id) {
    Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus tasklist?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus tasklist'
    }).then((result) => {
        if (result.isConfirmed) {
            apiCall(`transaction/tasklist/delete/${id}`, 'DELETE', '', {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
                null,
                (err) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal hapus data tasklist, harap coba lagi`,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                        }

                    }).showToast();
                }, true, (res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil hapus data tasklist`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist`
                            )
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

function showDeletedDetail(el) {
    $('#modal-detail-delete').modal('show')
    $('#deleted_by').html($(el).data('deleted_by') != '' ? $(el).data('deleted_by') : '-')
    $('#deleted_at').html($(el).data('deleted_at') != '' ? $(el).data('deleted_at') : '-')
}

function showApprovedDetail(el) {
    $('#modal-detail-approve').modal('show')
    $('#approved_by').html($(el).data('approved_by') != '' ? $(el).data('approved_by') : '-')
    $('#approved_at').html($(el).data('approved_at') != '' ? $(el).data('approved_at') : '-')
}

function historyReject(id){
    apiCall(`transaction/tasklist/history-approval/${id}`, 'GET', '', null, null, null, true,
    (res) => {
        $('.loading').hide()
        $('#modal-history').find('.modal-body').html(res.blade)
        $('#modal-history').modal('toggle')
        console.log(res)
    })
}

function ActiveTasklist(id) {
    Swal.fire({
        title: 'Apakah Anda yakin ingin mengaktifkan tasklist?', // Mengganti teks konfirmasi
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, aktifkan tasklist'
    }).then((result) => {
        if (result.isConfirmed) {
            apiCall(`transaction/tasklist/active/${id}`, 'POST', '', {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
                null,
                (err) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal mengaktifkan tasklist, harap coba lagi`,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                        }

                    }).showToast();
                }, true, (res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil mengaktifkan tasklist`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist`
                            )
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

function ApproveTasklist(id) {
    Swal.fire({
        title: 'Apakah Anda yakin ingin approve tasklist?', // Mengganti teks konfirmasi
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Approve tasklist'
    }).then((result) => {
        if (result.isConfirmed) {
            apiCall(`transaction/tasklist/approve/${id}`, 'POST', '', {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
                null,
                (err) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal approve tasklist, harap coba lagi`,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                        }

                    }).showToast();
                }, true, (res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil approve tasklist`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist`
                            )
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

function modalRequestApprove(id){
    $('#modal-requestApprove').modal('toggle')
    $('#modal-requestApprove').find('.btn-confirm').attr('onclick', `requestApprove('${id}')`)
}

function requestApprove(id){
    const fComponent = $('#reason-approve')
    var required = fComponent.find('.required')
    var canInput = true

    required.removeClass('is-invalid')

    for(var i = 0; i < required.length; i++){
        if (required[i].value == ''){
            canInput = false
            fComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
            var form_name = required[i].id.replace('_', ' ')

            Toastify({
                text: `Form ${form_name} is Required`,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                }
            }).showToast();
        }
    }

    if(canInput){
        Swal.fire({
            title: 'Apakah Anda yakin ingin approve tasklist?', // Mengganti teks konfirmasi
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Approve tasklist'
        }).then((result) => {
            if(result.isConfirmed){
                apiCall(
                    `transaction/tasklist/request-approval/${id}`,
                    'POST',
                    'reason-approve',
                    {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    null,
                    null,
                    true,
                    (res) => {
                        $('.loading').hide()
                        $('#modal-requestApprove').modal('hide')
                        Toastify({
                            text: `Berhasil request approval ulang tasklist`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            callback: function() {
                                renderView(
                                    `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist`)
                            },
                            position: "right",
                            style: {
                                background: "linear-gradient(to right, #00b09b, #96c93d)",
                            }

                        }).showToast();
                    }
                )
            }
        })
    }
}

function modalReject(id){
    $('#modal-reject').modal('toggle')
    $('#modal-reject').find('.btn-confirm').attr('onclick', `reject('${id}')`)
}

function reject(id){
    const fComponent = $('#reason-reject')
    var required = fComponent.find('.required')
    var canInput = true

    required.removeClass('is-invalid')

    for(var i = 0; i < required.length; i++){
        if (required[i].value == ''){
            canInput = false
            fComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
            var form_name = required[i].id.replace('_', ' ')

            Toastify({
                text: `Form ${form_name} is Required`,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                }
            }).showToast();
        }
    }

    if(canInput){
        prompt('reject', 'Approval Tasklist',
            (confirm) => {
                if(confirm){
                    apiCall(
                        `transaction/tasklist/reject/${id}`,
                        'POST',
                        'reason-reject',
                        {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        null,
                        null,
                        true,
                        (res) => {
                            $('.loading').hide()
                            $('#modal-reject').modal('hide')
                            Toastify({
                                text: `Berhasil reject approval tasklist`,
                                duration: 1000,
                                close: true,
                                gravity: "top",
                                callback: function() {
                                    renderView(
                                        `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist`)
                                },
                                position: "right",
                                style: {
                                    background: "linear-gradient(to right, #00b09b, #96c93d)",
                                }

                            }).showToast();
                        }
                    )
                }
            }
        )
    }
}

$('#btn-save').on('click', function () {
    const fAddComponent = $('#add-tasklist')
    var formData = new FormData(fAddComponent[0]);
    var required = fAddComponent.find('.required')
    var progress = fAddComponent.find('#progress')
    var project_id = fAddComponent.find('#project_id').val();
    var karyawanId = fAddComponent.find('#karyawan_id');
    var canInput = true

    required.removeClass('is-invalid')
    var progressValue = parseInt(progress.val());

    // Form Validation
    for (var i = 0; i < required.length; i++) {
        if (required[i].value == '') {
            canInput = false
            fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
            fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
            fAddComponent.find(`textarea[name="${required[i].name}"]`).addClass('is-invalid')
            var form_name = required[i].id.replace('_', ' ').toUpperCase()
            Toastify({
                text: `Form ${form_name} is Required`,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                }
            }).showToast();
        }
    }

    if (canInput == true) {
        prompt('submit', 'Tasklist', (confirm) => {
            if(confirm){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });
                $.ajax({
                    url: `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist/store`,
                    method: "POST",
                    data: formData, // Kirim FormData sebagai data
                    processData: false, // Tidak memproses data
                    contentType: false, // Tidak mengatur jenis konten

                    error: function (err) {
                        onAjaxError(err)
                    }, success: function (res) {
                        //start socket.io send to server
                        if (progressValue === 100) {
                                socket.emit('tasklist', {
                                    transactionnumber: res.transactionnumber,
                                    karyawan_id: karyawanId.val(),
                                    project_id: project_id,
                                    value: 'progress',
                                });
                        }
                        //end socket.io send to server
                        console.log(res);
                        $('.loading').hide()
                        Toastify({
                            text: `Berhasil simpan data tasklist`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            callback: function () {
                                renderView(
                                    `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist`
                                )
                            },
                            position: "right",
                            style: {
                                background: "linear-gradient(to right, #00b09b, #96c93d)",
                            }

                        }).showToast();
                    }
                })
            }
        })
    }
})

$('#btn-update').on('click', function () {
    const fEditComponent = $('#edit-tasklist');
    var required = fEditComponent.find('.required');
    var progress = fEditComponent.find('#progress');
    var project_id = fEditComponent.find('#project_id').val();
    var karyawanId = fEditComponent.find('#karyawan_id').val();
    var canInput = true

    required.removeClass('is-invalid')
    var progressValue = parseInt(progress.val());
    var transactionnumber = fEditComponent.find('#transactionnumber').val();
    // console.log('Transaction Number:', transactionnumber); // Log the value to the console
    // console.log('karyawan id:', karyawanId); // Log the value to the console

    // Form Validation
    for (var i = 0; i < required.length; i++) {
        if (required[i].value == '') {
            canInput = false;
            fEditComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid');
            fEditComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid');
            var form_name = required[i].id.replace('_', ' ').toUpperCase();
            Toastify({
                text: `Form ${form_name} is Required`,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                }
            }).showToast();
        }
    }
    if (canInput == true) {
        // Call API Call Fn
        /**
         * Param List :
         * 1. UR L
         * 2. Method
         * 3. Form Container ID
         * 4. header
         * 5. before ajax (leave it null to set default)
         * 6. on error (leave it null to set default)
         * 7. showError (boolean)
         * 8. callback response
         */
        prompt('update', 'Tasklist', (confirm) => {
            if(confirm){
        // Menggunakan FormData untuk mengirim data
        var formData = new FormData(fEditComponent[0]);
        formData.append('transactionnumber', transactionnumber);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });

    // Kirim data melalui Ajax
    $.ajax({
        url: `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist/update`,
        method: "post",
        data: formData, // Menggunakan FormData
        processData: false,
        contentType: false,
        project_id: project_id,
        error: function (err) {
            console.log(err);
            error = err.responseJSON;
            $('.loading').hide();
            Toastify({
                text: error.message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                }
            }).showToast();
        },
        success: function (res) {
            //start socket.io send to server
            if (progressValue === 100) {
                socket.emit('tasklist', {
                    transactionnumber: transactionnumber,
                    karyawan_id: karyawanId,
                    project_id: project_id,
                    value: 'progress',
                });
            }
            //end socket.io send to server
            console.log(res);
            $('.loading').hide()
            Toastify({
                text: `Berhasil update data tasklist`,
                duration: 1000,
                close: true,
                gravity: "top",
                callback: function () {
                    renderView(
                        `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist`
                    )
                },
                position: "right",
                style: {
                    background: "linear-gradient(to right, #007BFF, #00BFFF)",
                }
            }).showToast();
        }
    })
}
})
}
})

$('#project_id').on('change', function () {
    var Projectid = $(this).val();
    console.log(Projectid);
    // Ambil tahapan berdasarkan proyek
    if (Projectid) {
        $.ajax({
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/tasklist/get-timelinea/`+Projectid,
            type: 'GET',
            data: {},
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                if (data.length > 0) {
                    $('#timelineA_id').empty();
                    $('#timelineA_id').append('<option value="" hidden>-Pilih-</option>');
                    $.each(data, function (key, timelineA) {
                        console.log(timelineA)
                        $('#timelineA_id').append(
                            '<option data-startperiod="' + timelineA.startdate + '" data-endperiod="' + timelineA.enddate + '" data-isDocument="'+timelineA.is_document+'" value="' + timelineA.id + '">' +
                            timelineA.detail + ' (' + timelineA.startdate + ' - ' + timelineA.enddate + ')</option>'
                        );
                    });
                    $('#timelineA_id').val('{{ $tasklist->timelineA_id }}');
                } else{
                    $('#timelineA_id').empty();
                }
                $('#timelineA_id').val('{{ $tasklist->timelineA_id }}');
            }
        });
    } else {
        $('#timelineA_id').empty();
    }
});

// Check if this timelineA need document to upload
$('#timelineA_id').on('change', function(){
    const isDocument = $(this).find(':selected').data('isdocument')
    const progress = $('input[name="progress"]').val()
    if(isDocument == 1 && progress == 100){
        $('#upload_document').fadeIn()
        $('#document').addClass('required')
    } else {
        $('#upload_document').fadeOut()
        $('#document').removeClass('required')
    }

    // const startPeriod = $(this).find(':selected').data('startperiod').split('/').reverse().join('-')
    // const endPeriod = $(this).find(':selected').data('endperiod').split('/').reverse().join('-')

    // $('input[name="tx_date"]').attr('min', startPeriod)
    // $('input[name="tx_date"]').attr('max', endPeriod)
})

$('input[name="progress"]').on('keyup', function(){
    const isDocument = $('#timelineA_id').find(':selected').data('isdocument')
    const progress = $('input[name="progress"]').val()
    if(isDocument == 1 && progress == 100){
        $('#upload_document').fadeIn()
        $('#document').addClass('required')
    } else {
        $('#upload_document').fadeOut()
        $('#document').removeClass('required')
    }
})

$('#toggle-work').on('change', function(){
    if($(this).is(':checked') == true){
        $('select[name="timelineA_id"]').select2("destroy");
        $('select[name="timelineA_id"]').removeClass('required').prop('readonly', true)
        // $('select[name="timelineA_id"]').select2("readonly", true);
    } else {
        $('select[name="timelineA_id"]').addClass('required')
        $('select[name="timelineA_id"]').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Timeline Detail",
            allowClear: true
        });
}
})
