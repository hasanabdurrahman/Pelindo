$(document).ready(function(){
    $('select').select2({
        theme: 'bootstrap-5',
        placeholder: "Pilih Parent Menu",
        allowClear: true
    });
    
    // Render Main Table
    table = $('.data-table').DataTable({
        responsive: true,
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
            url: `${$('meta[name="baseurl"]').attr('content')}setting/menu/data`,
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
                data: 'xurl',
                name: 'xurl'
            },
            {
                data: 'xicon',
                name: 'xicon'
            },
            {
                data: 'parent',
                name: 'parent'
            },
            {
                data: 'action',
                name: 'action',
                className: 'text-center'
            }
        ],
    });

    ImportExport(table);
})

// Action to submit data
$('#btn-save').on('click', function(){
    prompt('submit', 'Menu', (confirm) => {
        if (confirm){
            const fAddComponent = $('#add-menu')
            var required = fAddComponent.find('.required')
            var canInput = true
        
            required.removeClass('is-invalid')
        
            // Form Validation
            for(var i = 0; i < required.length; i++){
                if (required[i].value == ''){
                    canInput = false
                    fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
                    fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
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
                 * 8. callback respons
                 */
                apiCall('setting/menu/store', 'POST', 'add-menu', 
                {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
                null, 
                null,
                true,
                (res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil simpan data menu`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}setting/menu`)
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

function showFormParentMenu(){
    if($('#defaultParent').css('display') != 'none'){
        $('#defaultParent').fadeOut()
        $('#formSelect').fadeIn()
    } else {
        $('#formSelect').fadeOut()
        $('#defaultParent').fadeIn()
    }
}

$('#btn-update').on('click', function(){
    prompt('update', 'Menu', (confirm) => {
        if(confirm){
            const fAddComponent = $('#edit-menu')
            var required = fAddComponent.find('.required')
            var canInput = true
        
            required.removeClass('is-invalid')
        
            // Form Validation
            for(var i = 0; i < required.length; i++){
                if (required[i].value == ''){
                    canInput = false
                    fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
                    fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
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
                apiCall(`setting/menu/update`, 'POST', 'edit-menu', 
                {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
                null, 
                null,
                true,
                (res) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil update data menu`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}setting/menu`)
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

function deleteMenu(id, del_status){
    let text = del_status == 0 ? 'Menghapus' : 'Mengaktifkan'
    let type = del_status == 0 ? 'delete' : 'active'
    prompt(type, 'Menu', (confirm) => {
        if(confirm){
            apiCall(`setting/menu/delete/${id}`, 'DELETE', '', 
            {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
            null,
            null,
            true,
            (res) => {
                console.log(res)
                $('.loading').hide()
                Toastify({
                    text: `Berhasil ${text} data menu`,
                    duration: 1000,
                    close:true,
                    gravity:"top",
                    callback: function() {
                        renderView(`${$('meta[name="baseurl"]').attr('content')}setting/menu`)
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

function showDeletedDetail(el){
    $('#modal-detail-delete').modal('show')
    $('#deleted_by').html($(el).data('deleted_by') != '' ? $(el).data('deleted_by') : '-')
    $('#deleted_at').html($(el).data('deleted_at') != '' ? $(el).data('deleted_at') : '-')
}