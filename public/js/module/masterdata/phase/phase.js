$( function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.data-table').DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}master-project/phase/datatable`,
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
                data: 'name',
                name: 'name',
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

    $( "#sortable" ).sortable({
        handle: 'button',
        cancel: ''
    });
})

$('input[name="order"]').inputmask({"mask": 999, placeholder: ''})

// Action to submit data
$('#btn-save').on('click', function(){
    prompt('submit', 'Phase', (confirm) => {
        if (confirm){
            const fAddComponent = $('#add-phase')
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
                apiCall('master-project/phase/store', 'POST', 'add-phase',
                {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
                null,
                null,
                true,(res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil simpan data phase`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/phase`)
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

$('#btn-update').on('click', function(){
    prompt('update', 'Phase', (confirm) => {
        if(confirm){
            const fAddComponent = $('#edit-phase')
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
                apiCall(`master-project/phase/update`, 'POST', 'edit-phase',
                {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
                null,null,true,(res) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil update data phase`,
                        duration: 1000,
                        close:true,
                        gravity:"top",
                        callback: function() {
                            renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/phase`)
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

function deletePhase(id, del_status){
    let text = del_status == 0 ? 'Menonaktifkan' : 'Mengaktifkan'
    let type = del_status == 0 ? 'delete' : 'active'
    prompt(type, 'Phase', (confirm) => {
        if(confirm){
            apiCall(`master-project/phase/delete/${id}`, 'DELETE', '',
            {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
            null,null,true,(res) => {
                console.log(res)
                $('.loading').hide()
                Toastify({
                    text: `Berhasil ${text} data phase`,
                    duration: 1000,
                    close:true,
                    gravity:"top",
                    callback: function() {
                        renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/phase`)
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

function addPhase(el){
    let html = `
        <li>
            <div class="row align-items-center">
                <div class="col-md-1 col-12">
                    <button class="btn btn-sm btn-secondary">
                        <i class="bi bi-justify"></i>
                    </button>
                </div>
                <div class="col-md-5 col-12">
                    <div class="form-group">
                        <label for="type">Phase Name</label>
                        <input type="text" id="name" class="form-input form-control required" name="name[]" placeholder="Phase Name">
                    </div>
                </div>
                <div class="col-md-2 col-12">
                    <a href='javascript:void(0)' onclick="removePhase(this)" class='btn icon btn-sm btn-outline-danger rounded-pill btn-remove-work'>
                        <i class="bi bi-trash-fill"></i>
                    </a>
                </div>
            </div>
        </li>
    `

    $('.form-phase-container').append(html)
}

function removePhase(el){
    $(el).parent().parent().remove()
}
