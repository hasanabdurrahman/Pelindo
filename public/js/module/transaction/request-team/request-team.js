$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });
    var  table = $('.data-table').DataTable({
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
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/request-team/datatable`,
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
                data: 'description',
                name: 'description',
                className: 'text-center'
            },
            {
                data: 'approval_pc',
                name: 'approval_pc',
                className: 'text-center'
            },
            {
                data: 'approval_kadep',
                name: 'approval_kadep',
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

    $("#request-tab").click(function() {
        table.ajax.reload(null, false);
    });
    ImportExport(table);

    $('.form-select').select2({
        theme: 'bootstrap-5'
    });

    $('.form-select-multiple').select2();
});

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
        
        checkAvail($(this))
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
        checkAvail($(this))
    }
})

function checkAvail(el){
    const formId = $(el).parent().parent().parent().parent().parent().attr('id')

    apiCall('transaction/request-team/check-avail', 'POST', formId, 
        {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
    null, 
    null,
    true,
    (res) => {
        const notif = res.availibility
        let html = ''

        for (let i = 0; i < notif.length; i++) {
            html += `<small class='text-danger'>*${notif[i]}</small><br>`
        }

        $('#notif-availibility').html(html)
        $('.loading').hide()
    })
}

function approval(id, type, el){
    switch (type) {
        case 'approve':
            approve(id) 
            break;

        case 'reject':
            $('#modal-reject').modal('toggle')
            $('#modal-reject').find('.btn-confirm').attr('onclick', `reject('${id}')`)
            break;
    
        case 'review':
            review(id, el) 
            break;

        default:
            break;
    }
}

function modalReject(id){
    $('#modal-reject').modal('toggle')
    $('#modal-reject').find('.btn-confirm').attr('onclick', `reject('${id}')`)
}

function approve(id){
    prompt('approve', 'Request Team', 
        (confirm) => {
            if(confirm){
                apiCall(
                    `transaction/request-team/approve/${id}`, 
                    'GET', 
                    '',
                    null,
                    null,
                    null,
                    true,
                    (res) => {
                        $('.loading').hide()
                        $('#modal-review').modal('hide')
                        Toastify({
                            text: `Berhasil approve request team`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            callback: function() {
                                renderView(
                                    `${$('meta[name="baseurl"]').attr('content')}transaction/request-team`)
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
        prompt('reject', 'Request Team', 
            (confirm) => {
                if(confirm){
                    apiCall(
                        `transaction/request-team/reject/${id}`, 
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
                            $('#modal-review').modal('hide')
                            $('#modal-reject').modal('hide')
                            Toastify({
                                text: `Berhasil reject request team`,
                                duration: 1000,
                                close: true,
                                gravity: "top",
                                callback: function() {
                                    renderView(
                                        `${$('meta[name="baseurl"]').attr('content')}transaction/request-team`)
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

function review(id, el) {
    let karyawan_id = $(el).data('karyawan_id')
    apiCall(`transaction/request-team/detail-pekerjaan/${karyawan_id}`, 
        'GET', 
        '', 
        null, 
        null, 
        null, 
        true, 
        (res) => {
            $('.loading').hide()
            $('#summary-container').html(res.blade)
            $('#list-tasklist').html(res.bladeDetail)
            $('#modal-review').modal('toggle')

            $('#modal-review').find('#btn-reject').attr('onclick', `modalReject('${id}')`)
            $('#modal-review').find('#btn-approve').attr('onclick', `approve('${id}')`)
        }
    )

}

function rejected_reason(text, el){
    let project = $(el).data('project')
    let startDate = $(el).data('start_date')
    let endDate = $(el).data('end_date')

    $('#modal-rejected').find('#project').html(project)
    $('#modal-rejected').find('#startDate').html(startDate)
    $('#modal-rejected').find('#endDate').html(endDate)
    $('#modal-rejected').find('#reason').html(text)

    $('#modal-rejected').modal('toggle')
}

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
        prompt('submit', 'Request Team', (confirm) => {
            if (confirm){
                apiCall('transaction/request-team/store', 'POST', 'add-request', 
                    {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
                null, 
                null,
                true,
                (res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil membuat request team baru`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}transaction/request-team`)
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
        prompt('update', 'Request Team', (confirm) => {
            if (confirm){
                apiCall('transaction/request-team/update', 'POST', 'edit-request', 
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
                            renderView(`${$('meta[name="baseurl"]').attr('content')}transaction/request-team`)
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
    prompt('cancel', 'Request Team', (confirm) => {
        if (confirm){
            apiCall(`transaction/request-team/cancel/${id}`, 'GET', '', 
            null,
            null, 
            null,
            true,
            (res) => {
                console.log(res)
                $('.loading').hide()
                Toastify({
                    text: `Berhasil cancel request team`,
                    duration: 1000,
                    close:true,
                    gravity:"top",
                    callback: function() {
                        renderView(`${$('meta[name="baseurl"]').attr('content')}transaction/request-team`)
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