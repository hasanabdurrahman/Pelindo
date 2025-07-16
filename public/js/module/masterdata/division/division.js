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
            pageLength: 10, // Menampilkan 10 data per halaman awal
            lengthMenu: [10, 25, 50, 75, 100], // Opsi untuk panjang tampilan halaman
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}masterdata/division/datatable`,
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
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center',
                width: '200px'
            },
        ]
    });

    $("#project-tab").click(function() {
    table.ajax.reload(null, false);
    });
    ImportExport(table);
});


function showDeletedDetail(el) {
    $('#modal-detail-delete').modal('show')
    $('#deleted_by').html($(el).data('deleted_by') != '' ? $(el).data('deleted_by') : '-')
    $('#deleted_at').html($(el).data('deleted_at') != '' ? $(el).data('deleted_at') : '-')
}

function deleteDivision(id) {
Swal.fire({
title: 'Apakah Anda yakin ingin menghapus Division?',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#3085d6',
cancelButtonColor: '#d33',
confirmButtonText: 'Ya, hapus Division'
}).then((result) => {
if (result.isConfirmed) {
    apiCall(`masterdata/division/delete/${id}`, 'DELETE', '', {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        null,
        (err) => {
            $('.loading').hide()
            Toastify({
                text: `Gagal hapus data Division, harap coba lagi`,
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
                text: `Berhasil hapus data roles`,
                duration: 1000,
                close: true,
                gravity: "top",
                callback: function() {
                    renderView(
                        `${$('meta[name="baseurl"]').attr('content')}masterdata/division`
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

function ActiveDivision(id) {
    Swal.fire({
        title: 'Apakah Anda yakin ingin mengaktifkan division?', // Mengganti teks konfirmasi
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, aktifkan division'
    }).then((result) => {
        if (result.isConfirmed) {
            apiCall(`masterdata/division/active/${id}`, 'POST', '', {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                null,
                (err) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal mengaktifkan division, harap coba lagi`,
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
                        text: `Berhasil mengaktifkan division`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function() {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}masterdata/division`
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

$('#btn-update').on('click', function() {
    const fAddComponent = $('#edit-division')
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
        prompt('update', 'Division', (confirm) => {
            if(confirm){
        apiCall(`masterdata/division/update`, 'POST', 'edit-division', {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            null,
            (err) => {
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
            }, true, (res) => {
                console.log(res)
                $('.loading').hide()
                Toastify({
                    text: `Berhasil update data division`,
                    duration: 1000,
                    close: true,
                    gravity: "top",
                    callback: function() {
                        renderView(
                            `${$('meta[name="baseurl"]').attr('content')}masterdata/division`)
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

$('#btn-save').on('click', function(){
        const fAddComponent = $('#add-division')
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
             * 8. callback response
             */
            prompt('submit', 'Division', (confirm) => {
                if(confirm){
            apiCall('masterdata/division/store', 'POST', 'add-division', 
            {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
            null, 
            (err) => {
                error = err.responseJSON;
                $('.loading').hide()
                Toastify({
                    text: error.message,
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
                    text: `Berhasil simpan data division`,
                    duration: 1000,
                    close:true,
                    gravity:"top",
                    callback: function() {
                        renderView(`${$('meta[name="baseurl"]').attr('content')}masterdata/division`)
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
