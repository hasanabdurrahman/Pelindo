// Action to submit data
$('#btn-save').on('click', function () {
    const fAddComponent = $('#add-client')
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
        prompt('submit', 'Client', (confirm) => {
            if(confirm){
                apiCall('master-project/client/store', 'POST', 'add-client',
                {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                null,
                null,
                true,
                (res) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil simpan data client`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}master-project/client`
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
})

$('#btn-update').on('click', function () {
    const fAddComponent = $('#edit-client')
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
        prompt('update', 'Client', (confirm) => {
            if(confirm){
                apiCall(`master-project/client/update`, 'POST', 'edit-client', {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                null,
                null,
                true,
                (res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil update data client`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}master-project/client`
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
})

$(function () {
    $('input[name="company_phone"]').inputmask({ mask: '9999999999999', placeholder: '' })

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
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
            url: `${$('meta[name="baseurl"]').attr('content')}master-project/client/datatable`,
            method: "POST",
            data: function (data) {
                data._token = `${$('meta[name="csrf-token"]').attr('content')}`
            },
        },
        columns: [
            {
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
                data: 'contact_person',
                name: 'contact_person',
                className: 'text-center'
            },
            {
                data: 'company_phone',
                name: 'company_phone',
                className: 'text-center'
            },
            {
                data: 'email',
                name: 'email',
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

    $("#clients-tab").click(function () {
        table.ajax.reload(null, false);
    });

    ImportExport(table);
});

function deleteClient(id) {
    Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus client?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus client'
    }).then((result) => {
        if (result.isConfirmed) {
            apiCall(`master-project/client/delete/${id}`, 'DELETE', '', {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
                null,
                (err) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal hapus data client, harap coba lagi`,
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
                        text: `Berhasil hapus data client`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}master-project/client`
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

function ActiveClient(id) {
    Swal.fire({
        title: 'Apakah Anda yakin ingin mengaktifkan client?', // Mengganti teks konfirmasi
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, aktifkan client'
    }).then((result) => {
        if (result.isConfirmed) {
            apiCall(`master-project/client/active/${id}`, 'POST', '', {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
                null,
                (err) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal mengaktifkan client, harap coba lagi`,
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
                        text: `Berhasil mengaktifkan client`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}master-project/client`
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
