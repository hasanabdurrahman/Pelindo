$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });
    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    var table = $('.data-table').DataTable({
        searching:true,
        iDisplayLength: 50,
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
            url: `${$('meta[name="baseurl"]').attr('content')}master-project/project/datatable`,
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
                data: 'project',
                name: 'project'
            },
            {
                data: 'project_date',
                name: 'project_date',
            },
            {
                data: 'project_pic',
                name: 'project_pic',
            },
            {
                data: 'xtype',
                name: 'xtype',
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
            ]

        });

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    $("#overview-tab").click(function() {
        table_all.ajax.reload(null, false);
    });

    $("#project-tab").click(function() {
        table.ajax.reload(null, false);
    });


    ImportExport(table);
});


function deleteProject(id) {
    Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus project?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus project'
    }).then((result) => {
        if (result.isConfirmed) {
            apiCall(`master-project/project/delete/${id}`, 'DELETE', '', {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
                null,null, true, (res) => {
                    console.log(res)
                    $('.loading').hide()
                    Toastify({
                        text: `Berhasil hapus data project`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}master-project/project`
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

function ActiveProject(id) {
    Swal.fire({
        title: 'Apakah Anda yakin ingin mengaktifkan project?', // Mengganti teks konfirmasi
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, aktifkan project'
    }).then((result) => {
        if (result.isConfirmed) {
            apiCall(`master-project/project/active/${id}`, 'POST', '', {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
                null,
                (err) => {
                    $('.loading').hide()
                    Toastify({
                        text: `Gagal mengaktifkan project, harap coba lagi`,
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
                        text: `Berhasil mengaktifkan project`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}master-project/project`
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

$('#btn-update').on('click', function () {
    const fAddComponent = $('#edit-project')
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
        prompt('update', 'Project', (confirm) => {
            if(confirm){
        apiCall(`master-project/project/update`, 'POST', 'edit-project', {
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
                    text: `Berhasil update data project`,
                    duration: 1000,
                    close: true,
                    gravity: "top",
                    callback: function () {
                        renderView(
                            `${$('meta[name="baseurl"]').attr('content')}master-project/project`
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


$(document).ready(function () {
    // Initialize Select2 for each select element
    $('#sales_id').select2({
        theme: 'bootstrap-5',
        placeholder: "Pilih Sales",
        allowClear: true
    });

    $('#id_client').select2({
        theme: 'bootstrap-5',
        placeholder: "Pilih Client",
        allowClear: true
    });

    $('#pc_id').select2({
        theme: 'bootstrap-5',
        placeholder: "Pilih PC",
        allowClear: true
    });
    $('#xtype').select2({
        theme: 'bootstrap-5',
        placeholder: "Pilih Type",
        allowClear: true
    });
});

$('#btn-save').on('click', function () {
    const fAddComponent = $('#add-project')
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
         * 1. UR L
         * 2. Method
         * 3. Form Container ID
         * 4. header
         * 5. before ajax (leave it null to set default)
         * 6. on error (leave it null to set default)
         * 7. showError (boolean)
         * 8. callback response
         */
        prompt('submit', 'Project', (confirm) => {
            if(confirm){
                apiCall('master-project/project/store', 'POST', 'add-project', {
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
                        text: `Berhasil simpan data project`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr('content')}master-project/project`
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

$('#import-project').on('submit', function(){
    const fAddComponent = $('#import-project')
    var formData = new FormData(fAddComponent[0]);
    var required = fAddComponent.find('.required')
    var canInput = true

    required.removeClass('is-invalid')

    // Form Validation
    for (var i = 0; i < required.length; i++) {
        if (required[i].value == '') {
            canInput = false
            fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
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
        prompt('submit', 'Project', (confirm) => {
            if(confirm){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });
                $.ajax({
                    url: `${$('meta[name="baseurl"]').attr('content')}master-project/project/import`,
                    method: "POST",
                    data: formData, // Kirim FormData sebagai data
                    processData: false, // Tidak memproses data
                    contentType: false, // Tidak mengatur jenis konten
                    beforeSend: function(){
                        $('.loading').show()
                    },
                    error: function (err) {
                        $('.loading').hide()
                        $('#modal-import-data').modal('toggle')
                        onAjaxError(err)
                    },
                    success: function (res) {
                        $('#modal-import-data').modal('toggle')
                        $('.loading').hide()

                        if (res.status?.code == 200) {
                            Toastify({
                                text: `Berhasil import data project`,
                                duration: 1000,
                                close: true,
                                gravity: "top",
                                callback: function () {
                                    renderView(
                                        `${$('meta[name="baseurl"]').attr('content')}master-project/project`
                                    )
                                },
                                position: "right",
                                style: {
                                    background: "linear-gradient(to right, #00b09b, #96c93d)",
                                }

                            }).showToast();
                        } else {
                            onAjaxError(res);
                        }
                    }
                })
            }
        })
    }
})

$(document).ready(function () {
    // Memastikan bahwa input "value" hanya berisi angka
    $('#value').on('input', function() {
        // Menghapus karakter selain angka dan koma
        let sanitizedValue = $(this).val().replace(/[^0-9,]/g, '');

        // Menghapus separator ribuan lama
        sanitizedValue = sanitizedValue.replace(/,/g, '');

        // Menambahkan separator ribuan baru
        let formattedValue = formatNumberWithCommas(sanitizedValue);

        // Memperbarui nilai input dengan format ribuan
        $(this).val(formattedValue);
    });

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
});

$(document).ready(function () {
    // Mendapatkan elemen input Start Date
    const startDateInput = document.getElementById("startdate");

    // Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, "0");
    const day = String(today.getDate()).padStart(2, "0");
    const formattedDate = `${year}-${month}-${day}`;

    // Setel nilai awal input Start Date menjadi tanggal hari ini
    startDateInput.value = formattedDate;
});




