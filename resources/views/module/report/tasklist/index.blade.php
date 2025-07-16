<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tasklist Report <i class="fas fa-refresh refresh-page"
                        onclick="renderView(`{!! route('report.tasklist-report') !!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Report</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Project</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <section class="section row">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-12">
                        <h5>Periode Report</h5>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">Start Date</label>
                            <input type="date" name="startdate" id="startdate"
                                class="form-control form-input required" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">End Date</label>
                            <input type="date" name="enddate" id="enddate" class="form-control form-input required"
                                value="{{ date('Y-m-d', strtotime(date('Y-m-d') . '+1 days')) }}">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center justify-content-between mt-2">
                    <div class="col-12">
                        <h5>Status & Filter</h5>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="">Status</label>
                            <select name="status" id="status" class="form-control form-select form-input">
                                <option value="All">All</option>
                                <option value="Done">Done</option>
                                <option value="On Progress">On Progress</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6" id="filterByContainer">
                        <div class="form-group">
                            <label for="">Filter By</label>
                            <select name="filter" id="filter" class="form-control form-select form-input">
                                <option value="none">None</option>
                                <option value="employee">Employee</option>
                                <option value="position">Position</option>
                                <option value="project">Project</option>
                                <option value="task">Task</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-12" id="search" style="display: none;">
                        <div class="form-group search">
                            <label for="search">Filter Search</label>
                            <input type="text" name="search" class="form-control form-input">
                        </div>
                    </div>
                </div>

                <div class="mt-3 col-12 d-flex justify-content-end">
                    <button type="submit" id="btn-preview" class="btn btn-primary me-1 mb-1">Show Preview</button>
                    <div class="btn-group dropup me-1 mb-1">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            Print Report
                        </button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="#" id="import-pdf">PDF</a>
                            <a class="dropdown-item" href="#" id="import-excel">Excel</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Preview --}}
    <section class="section row" style="display: none">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    {{-- Left Nav --}}
                    <div class="col-12 col-md-6 order-md-1 order-last">
                    </div>

                    {{-- Right Nav --}}
                    <div class="col-12 col-md-6 order-md-2 order-first ">
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card px-3">
                    <table class="table table-centered align-middle table-nowrap mb-0 data-table" id="data-table"
                        style="width: 100%">
                        <thead class="text-muted table-light">
                            <tr>
                                {{-- <th></th> --}}
                                <th scope="col">#</th>
                                <th scope="col">Transaction Number</th>
                                <th scope="col">Employee</th>
                                <th scope="col">Progress</th>
                                <th scope="col">Description</th>
                                <th scope="col">Project Name</th>
                                <th scope="col">Task Name</th>
                                <th scope="col">Tgl. Input</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- @include('module.setting.menu.modal') --}}
    @prepend('after-script')
        <script>
            $(document).ready(function() {
                // Fungsi yang akan dijalankan saat tombol "Show Preview" diklik
                $("#btn-preview").click(function() {
                    // Ambil nilai-nilai dari input form
                    var startDate = $("#startdate").val();
                    var endDate = $("#enddate").val();
                    var status = $("#status").val();
                    var filter = $("#filter").val();
                    var search = $("input[name='search']").val();

                    // Lakukan permintaan Ajax ke server
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST", // Ubah ke POST jika diperlukan
                        url: "{{ route('report.tasklist-preview') }}", // Ganti dengan URL endpoint server Anda
                        data: {
                            startDate: startDate,
                            endDate: endDate,
                            status: status,
                            filter: filter,
                            search: search
                        },
                        beforeSend: () => {
                            $('.loading').show()
                        },
                        success: function(data) {
                            $('.loading').hide()
                            $(".section[style='display: none']").show();

                            // Bersihkan tbody
                            $('#data-table tbody').empty();

                            // Loop melalui data dan tambahkan ke dalam tabel
                            $.each(data, function(index, item) {
                                var rowNumber = index + 1;
                                var txDate = item.tx_date != null ? item.tx_date : ' - '

                                $('#data-table tbody').append(
                                    '<tr>' +
                                    '<td class="text-bold-500">' + rowNumber + '</td>' +
                                    '<td class="text-bold-500">' + item
                                    .transactionnumber + '</td>' +
                                    '<td class="text-bold-500">' + item.karyawan_name +
                                    '</td>' +
                                    '<td>' + item.progress + '</td>' +
                                    '<td class="text-bold-500">' + item.description +
                                    '</td>' +
                                    '<td class="text-bold-500">' + item.project_name +
                                    '</td>' +
                                    '<td class="text-bold-500">' + item.timelineA +
                                    '</td>' +
                                    '<td class="text-bold-500">' + txDate +
                                    '</td>' +
                                    '<td class="text-bold-500">' + item.startdate +
                                    '</td>' +
                                    '<td class="text-bold-500">' + item.enddate +
                                    '</td>' +
                                    '<td class="text-bold-500">' + item.status +
                                    '</td>' +
                                    '</tr>'
                                );
                            });

                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $('.loading').hide()
                            onAjaxError(jqXHR)
                            // if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                            //     Swal.fire({
                            //         icon: 'error',
                            //         title: 'Error',
                            //         text: jqXHR.responseJSON.error
                            //     });

                            // } else {
                            //     Swal.fire({
                            //         icon: 'error',
                            //         title: 'Error',
                            //         text: errorThrown
                            //     });

                            // }
                        }
                    });
                });

                $("#import-excel").click(function() {
                    // Create a form element
                    var form = document.createElement('form');
                    form.method = 'GET';
                    form.action = "{{ route('report.export-pdf') }}"; // Replace with your server endpoint

                    // Add hidden input fields for your data
                    var startDateInput = document.createElement('input');
                    startDateInput.type = 'hidden';
                    startDateInput.name = 'startDate';
                    startDateInput.value = $("#startdate").val();
                    form.appendChild(startDateInput);

                    var endDateInput = document.createElement('input');
                    endDateInput.type = 'hidden';
                    endDateInput.name = 'endDate';
                    endDateInput.value = $("#enddate").val();
                    form.appendChild(endDateInput);

                    var statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = $("#status").val();
                    form.appendChild(statusInput);

                    var filterInput = document.createElement('input');
                    filterInput.type = 'hidden';
                    filterInput.name = 'filter';
                    filterInput.value = $("#filter").val();
                    form.appendChild(filterInput);

                    var searchInput = document.createElement('input');
                    searchInput.type = 'hidden';
                    searchInput.name = 'search';
                    searchInput.value = $("#search").val();
                    form.appendChild(searchInput);

                    var modeInput = document.createElement('input');
                    modeInput.type = 'hidden';
                    modeInput.name = 'mode';
                    modeInput.value = 'excel';
                    form.appendChild(modeInput);

                    // Add the form to the document body and submit it to open a new tab
                    document.body.appendChild(form);
                    form.submit();

                    // Remove the form from the document body
                    document.body.removeChild(form);

                    setTimeout(() => {
                        Toastify({
                            text: `Berhasil Mencetak Tasklist`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "linear-gradient(to right, #00b09b, #96c93d)",
                            }

                        }).showToast();
                    }, 1500);
                });

                $("#import-pdf").click(function() {
                    // Create a form element
                    var form = document.createElement('form');
                    form.method = 'GET';
                    form.action = "{{ route('report.export-pdf') }}"; // Replace with your server endpoint

                    // Add hidden input fields for your data
                    var startDateInput = document.createElement('input');
                    startDateInput.type = 'hidden';
                    startDateInput.name = 'startDate';
                    startDateInput.value = $("#startdate").val();
                    form.appendChild(startDateInput);

                    var endDateInput = document.createElement('input');
                    endDateInput.type = 'hidden';
                    endDateInput.name = 'endDate';
                    endDateInput.value = $("#enddate").val();
                    form.appendChild(endDateInput);

                    var statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = $("#status").val();
                    form.appendChild(statusInput);

                    var filterInput = document.createElement('input');
                    filterInput.type = 'hidden';
                    filterInput.name = 'filter';
                    filterInput.value = $("#filter").val();
                    form.appendChild(filterInput);

                    var searchInput = document.createElement('input');
                    searchInput.type = 'hidden';
                    searchInput.name = 'search';
                    searchInput.value = $("#search").val();
                    form.appendChild(searchInput);

                    var modeInput = document.createElement('input');
                    modeInput.type = 'hidden';
                    modeInput.name = 'mode';
                    modeInput.value = 'pdf';
                    form.appendChild(modeInput);

                    // Add the form to the document body and submit it to open a new tab
                    document.body.appendChild(form);
                    form.submit();

                    // Remove the form from the document body
                    document.body.removeChild(form);
                    
                    setTimeout(() => {
                        Toastify({
                            text: `Berhasil Mencetak Tasklist`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "linear-gradient(to right, #00b09b, #96c93d)",
                            }

                        }).showToast();
                    }, 1500);
                });


            });

            $(document).ready(function() {
                // Tangkap elemen input "Filter Search" menggunakan jQuery
                var searchInput = $("#search");

                // Tangkap elemen "Filter By" menggunakan jQuery
                var filterByContainer = $("#filterByContainer");

                // Tambahkan event listener ke input "Filter Search"
                $(filterByContainer).on("change", function() {
                    console.log($("#filterByContainer option").filter(':selected').val())
                    if($("#filterByContainer option").filter(':selected').val() != 'none'){
                        $(searchInput).show();
                    } else {
                        $(searchInput).hide();
                    }
                });

                $('#startdate').on('change', function(){
                    $('#enddate').attr('min', $(this).val())
                })
            });
        </script>



    </div>
