var table, tableReject
$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });

    $("#requests-tab").click(function() {
        table.ajax.reload(null, false);
    });

    $("#rejected-tab").click(function() {
        tableReject.ajax.reload(null, false);
    });

    $('.form-select').select2({
        theme: 'bootstrap-5'
    });

    $('.form-select-multiple').select2();

    getRequestTicket()
    getRejectedTicket()
});

function getRequestTicket(){
    table = $('.data-table').DataTable({
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
        pageLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/request-ticket/datatable`,
            method: "POST",
            data: function(data) {
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
                data: 'project.name',
                name: 'project.name',
                className: 'text-center'
            },
            {
                data: 'karyawan.name',
                name: 'karyawan.name',
                className: 'text-center'
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
                data: 'issue',
                name: 'issue',
                className: 'text-center'
            },
            {
                data: 'status',
                name: 'status',
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
    });

}

function getRejectedTicket(){
    tableReject = $('.data-table-rejected').DataTable({
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
        pageLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/request-ticket/datatable-reject`,
            method: "POST",
            data: function(data) {
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
                data: 'project.name',
                name: 'project.name',
                className: 'text-center'
            },
            {
                data: 'karyawan.name',
                name: 'karyawan.name',
                className: 'text-center'
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
                data: 'issue',
                name: 'issue',
                className: 'text-center'
            },
            {
                data: 'status',
                name: 'status',
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
    });
}

$('input[name="startdate"]').on('change', function(e){
    if($('select[name="karyawan_id"]').val().length == 0){
        Toastify({
            text: `Harap pilih team terlebih dahulu`,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #ff5f6d, #ffc371)",
            }
        }).showToast();
        let defaultDate = new Date()
        defaultDate =  defaultDate.getFullYear() + '-' + ('0' + (defaultDate.getMonth()+1)).slice(-2) + '-' + ('0' + defaultDate.getDate()).slice(-2)
        $(this).val(defaultDate).attr('min', defaultDate)
        return false
    } else {
        let minEnd = new Date($(this).val())
        minEnd.setDate(minEnd.getDate() + 1)
        minEnd =  minEnd.getFullYear() + '-' + ('0' + (minEnd.getMonth()+1)).slice(-2) + '-' + ('0' + minEnd.getDate()).slice(-2)

        $('input[name="enddate"]').attr('min', minEnd).val(minEnd)

        // checkAvail($(this))
    }

})

$('input[name="enddate"]').on('change', function(e){
    if($('select[name="karyawan_id"]').val().length == 0){
        Toastify({
            text: `Harap pilih team terlebih dahulu`,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #ff5f6d, #ffc371)",
            }
        }).showToast();
        let defaultDate = new Date($('input[name="startdate"]').val())
        defaultDate.setDate(defaultDate.getDate()+1)
        defaultDate =  defaultDate.getFullYear() + '-' + ('0' + (defaultDate.getMonth()+1)).slice(-2) + '-' + ('0' + defaultDate.getDate()).slice(-2)
        $(this).val(defaultDate).attr('min', defaultDate)
        return false
    }  else {
        // checkAvail($(this))
    }
})

/**
 * NEW REQUEST
 */
$('#btn-save').on('click', function(){
    const fAddComponent = $('#add-request')
    var required = fAddComponent.find('.required')
    var canInput = true

    required.removeClass('is-invalid')

    // Form Validation
    for(var i = 0; i < required.length; i++){
        if (required[i].value == ''){
            canInput = false
            fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
            fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
            fAddComponent.find(`textarea[name="${required[i].name}"]`).addClass('is-invalid')
            var form_name = required[i].id.replace('_', ' ').toUpperCase()
            Toastify({
                text: `Form ${form_name} is Required`,
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

    // Proceed to input if all required form has filled
    if (canInput == true){
        prompt('submit', 'Request Ticket', (confirm) => {
            if (confirm){
                apiCall('transaction/request-ticket/store', 'POST', 'add-request',
                    {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                null,
                null,
                true,
                (res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil membuat request ticket baru`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}transaction/request-ticket`)
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
})

/** UPDATE REQUEST */
$('#btn-update').on('click', function(){
    const fAddComponent = $('#edit-request')
    var required = fAddComponent.find('.required')
    var canInput = true

    required.removeClass('is-invalid')

    // Form Validation
    for(var i = 0; i < required.length; i++){
        if (required[i].value == ''){
            canInput = false
            fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
            fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
            fAddComponent.find(`textarea[name="${required[i].name}"]`).addClass('is-invalid')
            var form_name = required[i].id.replace('_', ' ').toUpperCase()
            Toastify({
                text: `Form ${form_name} is Required`,
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

    // Proceed to input if all required form has filled
    if (canInput == true){
        prompt('update', 'Request Ticket', (confirm) => {
            if (confirm){
                apiCall('transaction/request-ticket/update', 'POST', 'edit-request',
                    {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
                null,
                null,
                true,
                (res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil edit request team`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}transaction/request-ticket`)
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
})

/** CANCEL REQUEST */
function cancelRequest(id){
    prompt('cancel', 'Request Ticket', (confirm) => {
        if (confirm){
            apiCall(`transaction/request-ticket/cancel/${id}`, 'GET', '',
            null,
            null,
            null,
            true,
            (res) => {
                console.log(res)
                $('.loading').hide()
                Toastify({
                    text: `Berhasil cancel request ticket`,
                    duration: 1000,
                    close:true,
                    gravity:"top",
                    callback: function() {
                        renderView(`${$('meta[name="baseurl"]').attr('content')}transaction/request-ticket`)
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

/** SOLVED REQUEST */
function solved(id){
    prompt('approve', 'Request Ticket',
        (confirm) => {
            if(confirm){
                apiCall(
                    `transaction/request-ticket/solved/${id}`,
                    'GET',
                    '',
                    null,
                    null,
                    null,
                    true,
                    (res) => {
                        $('.loading').hide()
                        Toastify({
                            text: `Berhasil solved request request`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            callback: function() {
                                renderView(
                                    `${$('meta[name="baseurl"]').attr('content')}transaction/request-ticket`)
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

/** REJECT SOLVED */
function rejectSolvedTicket(id){
    $('#modal-reject').modal('toggle')
    $('input[name="ticket_id"]').val(id)
}

function reject(){
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
        prompt('reject', 'Approval Ticket',
            (confirm) => {
                if(confirm){
                    apiCall(
                        `transaction/request-ticket/reject`,
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
                                text: `Berhasil reject approval ticket`,
                                duration: 1000,
                                close: true,
                                gravity: "top",
                                callback: function() {
                                    renderView(
                                        `${$('meta[name="baseurl"]').attr('content')}transaction/request-ticket`)
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

function historyReject(id){
    apiCall(`transaction/request-ticket/history-approval/${id}`, 'GET', '', null, null, null, true,
    (res) => {
        $('.loading').hide()
        $('#modal-history').find('.modal-body').html(res.blade)
        $('#modal-history').modal('toggle')
        console.log(res)
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
            title: 'Apakah Anda yakin ingin request approval ulang ticket?', // Mengganti teks konfirmasi
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Approve tasklist'
        }).then((result) => {
            if(result.isConfirmed){
                apiCall(
                    `transaction/request-ticket/request-approval/${id}`,
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
                            text: `Berhasil request approval ulang ticket`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            callback: function() {
                                renderView(
                                    `${$('meta[name="baseurl"]').attr('content')}transaction/request-ticket`)
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
