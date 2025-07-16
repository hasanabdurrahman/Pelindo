$(function() {

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
        buttons: [
            'copy', 'excelFlash', 'excel', 'pdf', 'print', {
                text: 'Reload',
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                }
            }
        ],
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}setting/roles/datatable`,
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
                data: 'code',
                name: 'code',
                className: 'text-center'
            },
            {
                data: 'name',
                name: 'name',
                className: 'text-center'
            },
            {
                data: 'inactive_date',
                name: 'inactive_date',
                className: 'text-center'
            },
            {
                data: 'inactive_by',
                name: 'inactive_by',
                className: 'text-center'
            },
            {
                data: 'updated_by',
                name: 'updated_by',
                className: 'text-center'
            },
            {
                data: 'created_by',
                name: 'created_by',
                className: 'text-center'
            },
            {
                data: 'created_at',
                name: 'created_at',
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
        ]
    });

    ImportExport(table);
});

function deleteRoles(id, del_status) {
    let text = del_status == 0 ? 'Menhapus' : 'Mengaktifkan'
    let type = del_status == 0 ? 'delete' : 'active'
    let url = del_status == 0 ? `setting/roles/delete/${id}` : `setting/roles/active/${id}`

    prompt(type, 'Roles', (confirm) => {
        if(confirm){
            apiCall(url, 'DELETE', '', 
            {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
            null,
            (err) => {
                $('.loading').hide()
                Toastify({
                    text: `Gagal ${text} data Roles, ${err.responseJSON.err_detail}`,
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
                    text: `Berhasil ${text} data Roles`,
                    duration: 1000,
                    close:true,
                    gravity:"top",
                    callback: function() {
                        renderView(
                            `${$('meta[name="baseurl"]').attr('content')}setting/roles`
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

$('#edit-roles').find('input[type="checkbox"]').each((k, el) => {
    let id = $(el).attr('id')

    if($(el).is(":checked")){
        $(el).val('1')
        $(el).parent().find('input').removeAttr('disabled')
        $(`#table-${id}`).find('input').removeAttr('disabled')
    } else{
        $(el).val('0')
        $(el).parent().find(`input:not(#${id})`).attr('disabled', 'disabled').prop('checked', false).val('0')
        $(`#table-${id}`).find('input').attr('disabled', 'disabled').prop('checked', false).val('0')
    }
})

$('.list-down-btn').on('click', function(e){
    e.preventDefault()
    var target = $(this).attr('data-toggle');
    $(target).slideToggle();
    var clicked = e.target;
    $(clicked).toggleClass("fas fa-chevron-down  fas fa-chevron-up");
})

// Action to submit data
$('#btn-save').on('click', function() {
    const fAddComponent = $('#add-roles')
    var required = fAddComponent.find('.required')
    var canInput = true

    required.removeClass('is-invalid')

    // Form Validation
    for (var i = 0; i < required.length; i++) {
        if (required[i].value == '') {
            canInput = false
            fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
            fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
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

    // Proceed to input if all required form has filled
    if (canInput == true) {
        // Call API Call Fn
        /**
         * Param List : 
         * 1. URL
         * 2. Method
         * 3. Form Container ID
         * 4. header
         * 5. before ajax (leave it null to set default)
         * 6. on error (leave it null to set default)
         * 7. showError (boolean)
         * 8. callback response
         */
        prompt('submit', 'Role', (confirm) => {
            if(confirm){
                apiCall('setting/roles/store', 'POST', 'add-roles', {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    null,
                    null, 
                    true, 
                    (res) => {
                        console.log(res)
                        $('.loading').hide()
                        Toastify({
                            text: `Berhasil simpan data roles`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            callback: function() {
                                renderView(
                                    `${$('meta[name="baseurl"]').attr('content')}setting/roles`)
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

// Action to Update data
$('#btn-update').on('click', function() {
    const fAddComponent = $('#edit-roles')
    var required = fAddComponent.find('.required')
    var canInput = true

    required.removeClass('is-invalid')

    // Form Validation
    for (var i = 0; i < required.length; i++) {
        if (required[i].value == '') {
            canInput = false
            fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
            fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
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

    // Proceed to input if all required form has filled
    if (canInput == true) {
        prompt('update', 'Role', (confirm) => {
            if(confirm){
                apiCall(`setting/roles/update`, 'POST', 'edit-roles', {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    null,
                    null, 
                    true, (res) => {
                        console.log(res)
                        $('.loading').hide()
                        Toastify({
                            text: `Berhasil update data roles`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            callback: function() {
                                renderView(
                                    `${$('meta[name="baseurl"]').attr('content')}setting/roles`)
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

function setPermissionCb(el){
    let id = $(el).attr('id')
    if($(el).is(":checked")){
        $(el).val('1')
        $(el).parent().find('input').removeAttr('disabled')
        $(`#table-${id}`).find('input').removeAttr('disabled').prop('checked', true).val('1')
    } else{
        $(el).val('0')
        $(el).parent().find(`input:not(#${id})`).attr('disabled', 'disabled').prop('checked', false).val('0')
        $(`#table-${id}`).find('input').attr('disabled', 'disabled').prop('checked', false).val('0')
    }
}

function showDeletedDetail(el){
    $('#modal-detail-delete').modal('show')
    $('#deleted_by').html($(el).data('deleted_by') != '' ? $(el).data('deleted_by') : '-')
    $('#deleted_at').html($(el).data('deleted_at') != '' ? $(el).data('deleted_at') : '-')
}