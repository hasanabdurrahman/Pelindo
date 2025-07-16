 @prepend('after-style')
     <style>
         .widget {
             padding-top: 20px;
             padding-right: 10px;
             padding-left: 20px
         }

         .scroll {
             margin: 4px, 4px;
             padding: 4px;
             height: 280px;
             overflow-x: hidden;
             overflow-y: auto;
             text-align: justify;
         }

         .scroll-project {
             margin: 4px, 4px;
             padding: 4px;
             height: 370px;
             overflow-x: hidden;
             overflow-y: auto;
             text-align: justify;
         }

         .wrapper {
             height: 70px;
             overflow: hidden;
             padding-right: 20px;
             padding-bottom: 10px
                 /* Untuk memotong konten yang terlalu panjang */
         }
     </style>
     <div id="render">
         <div class="page-heading">
             <div class="page-title">
                 <div class="row">
                     <div class="col-12 col-md-6 order-md-1 order-last">
                         <h3>Dashboard</h3>
                         <p class="text-subtitle text-muted"></p>
                     </div>

                 </div>
             </div>
             <section class="section row">
                 <div class="col">
                     <div class="row">
                         <div class="col-6 col-lg-3 col-md-6">
                             <div class="card">
                                 <div class="card-body px-4 py-4-5">
                                     <div class="row">
                                         <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                             <div class="stats-icon blue mb-2">
                                                 <i class="iconly-boldActivity"></i>
                                             </div>
                                         </div>
                                         <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                             <h6 class="text-muted font-semibold">Total Project</h6>
                                             <h6 class="font-extrabold mb-0">{{ count($project_all) }}</h6>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-6 col-lg-3 col-md-6">
                             <div class="card">
                                 <div class="card-body px-4 py-4-5">
                                     <div class="row">
                                         <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                             <div class="stats-icon green mb-2">
                                                 <i class="iconly-boldTick-Square"></i>
                                             </div>
                                         </div>
                                         <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                             <h6 class="text-muted font-semibold">Solved Project</h6>
                                             <h6 class="font-extrabold mb-0">
                                                 {{ count($project_all) > 0 ? $project_all[0]['solved'] : 0 }}</h6>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-6 col-lg-3 col-md-6">
                             <div class="card">
                                 <div class="card-body px-4 py-4-5">
                                     <div class="row">
                                         <div class="col-md-2 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                             <div class="stats-icon purple mb-2">
                                                 <i class="iconly-boldChart"></i>
                                             </div>
                                         </div>
                                         <div class="col-md-10 col-lg-12 col-xl-12 col-xxl-7">
                                             <h6 class="text-muted font-semibold">Project On Progress</h6>
                                             <h6 class="font-extrabold mb-0">
                                                 {{ count($project_all) > 0 ? $project_all[0]['progres'] : 0 }}</h6>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-6 col-lg-3 col-md-6">
                             <div class="card">
                                 <div class="card-body px-4 py-4-5">
                                     <div class="row">
                                         <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                             <div class="stats-icon red mb-2">
                                                 <i class="iconly-boldDanger"></i>
                                             </div>
                                         </div>
                                         <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                             <h6 class="text-muted font-semibold">Project Out of Schedule</h6>
                                             <h6 class="font-extrabold mb-0">
                                                 {{ count($project_all) > 0 ? $project_all[0]['out_date'] : 0 }}</h6>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-md-3">
                             <div class="card">
                                 <div class="card-body">
                                     <img src="{{ asset('assets/images/rocket.png') }}" height="150px"
                                         class="rounded mx-auto d-block">
                                     <br>
                                     <div class="row">
                                         <div class="col">
                                             <div>
                                                 <h7>Complete</h7>
                                                 <div class="progress">
                                                     <div class="progress-bar" role="progressbar"
                                                         style="width: {{ $percentages['complete'] }}%;"
                                                         aria-valuenow="{{ $percentages['complete'] }}" aria-valuemin="0"
                                                         aria-valuemax="100">{{ $percentages['complete'] }}%
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                     <div class="row mt-4">
                                         <div class="col">
                                             <div>
                                                 <h7>In Progress</h7>
                                                 <div class="progress">
                                                     <div class="progress-bar" role="progressbar"
                                                         style="width: {{ $percentages['inProgress'] }}%;"
                                                         aria-valuenow="{{ $percentages['inProgress'] }}" aria-valuemin="0"
                                                         aria-valuemax="100">{{ $percentages['inProgress'] }}%
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                     <div class="row mt-4">
                                         <div class="col">
                                             <div>
                                                 <h7>Out of Schedule</h7>
                                                 <div class="progress">
                                                     <div class="progress-bar" role="progressbar"
                                                         style="width: {{ $percentages['outSchedule'] }}%;"
                                                         aria-valuenow="{{ $percentages['outSchedule'] }}"
                                                         aria-valuemin="0" aria-valuemax="100">
                                                         {{ $percentages['outSchedule'] }}%
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col">
                             <div class="card">
                                 <div class="widget">
                                     <div class="row">
                                         <div class="col">
                                             <button type="button" class="btn btn-primary btn-sm">
                                                 To Do
                                                 <span class="badge bg-transparent">{{ count($timeline_active) }}</span>
                                             </button>
                                         </div>
                                         <div class="col">
                                             <div class="float-start float-lg-end">
                                                 <div class="col" style="float: right;padding-right:10px">
                                                     <button type="button" class="btn btn-light">
                                                         <i class="fas fa-plus"></i>
                                                     </button>
                                                 </div>
                                                 <div class="col" style="float: right;padding-right:10px">
                                                     <button type="button" class="btn btn-light">
                                                         <i class="fas fa-ellipsis-h"></i>
                                                     </button>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>

                                 <div class="card-body ">
                                     <div class="scroll">
                                         @foreach ($timeline_active as $item)
                                             <div class="bg-body" style="padding:20px;border-radius:20px;padding">
                                                 <div class="badges">
                                                     @if (strtotime($item->enddate) > strtotime(now()))
                                                         <span class="badge bg-info">End Time :
                                                             {{ $item->enddate }}</span>
                                                     @else
                                                         <span class="badge bg-info">End Time :
                                                             {{ $item->enddate }}</span>
                                                         <span class="badge bg-danger">Out of Schedule</span>
                                                     @endif




                                                 </div>
                                                 <br>
                                                 <h6 class="card-title">{{ $item->project_name }}</h6>
                                                 <div class="wrapper">
                                                     <p class="card-text">{{ $item->detail }}</p>
                                                 </div>
                                                 <br>
                                                 <div class="d-flex justify-content-between align-items-center">
                                                     <div class="d-inline">
                                                         <img src="{{ asset('assets/images/rocket.png') }}"
                                                             alt="Profile Image" class="rounded-circle"
                                                             style="width: 50px;">
                                                         <p class="mt-2 d-inline">{{ $item->client_name }}</p>
                                                     </div>
                                                     <a href="#" class="btn btn-info">Progress</a>
                                                 </div>
                                             </div>
                                             <br>
                                         @endforeach
                                     </div>



                                 </div>
                             </div>
                         </div>
                         <div class="col">
                             <div class="card">
                                 <div class="widget">
                                     <div class="row">
                                         <div class="col">
                                             <button type="button" class="btn btn-primary btn-sm">
                                                 Out of schedule
                                                 <span class="badge bg-transparent">{{ count($outSchedule) }}</span>
                                             </button>
                                         </div>
                                         <div class="col">
                                             <div class="float-start float-lg-end">
                                                 <div class="col" style="float: right;padding-right:10px">
                                                     <button type="button" class="btn btn-light">
                                                         <i class="fas fa-plus"></i>
                                                     </button>
                                                 </div>
                                                 <div class="col" style="float: right;padding-right:10px">
                                                     <button type="button" class="btn btn-light">
                                                         <i class="fas fa-ellipsis-h"></i>
                                                     </button>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>

                                 <div class="card-body ">
                                     <div class="scroll">
                                         @foreach ($outSchedule as $item)
                                             <div class="bg-body" style="padding:20px;border-radius:20px;padding">
                                                 <div class="badges">
                                                     @if (strtotime($item->enddate) > strtotime(now()))
                                                         <span class="badge bg-info">End Time :
                                                             {{ $item->enddate }}</span>
                                                     @else
                                                         <span class="badge bg-info">End Time :
                                                             {{ $item->enddate }}</span>
                                                         <span class="badge bg-danger">Out of Schedule</span>
                                                     @endif

                                                 </div>
                                                 <br>
                                                 <h6 class="card-title">{{ $item->project_name }}</h6>
                                                 <div class="wrapper">
                                                     <p class="card-text">{{ $item->detail }}</p>
                                                 </div>
                                                 <br>
                                                 <div class="d-flex justify-content-between align-items-center">
                                                     <div class="d-inline">
                                                         <img src="{{ asset('assets/images/rocket.png') }}"
                                                             alt="Profile Image" class="rounded-circle"
                                                             style="width: 50px;">
                                                         <p class="mt-2 d-inline">{{ $item->client_name }}</p>
                                                     </div>
                                                     <a href="#" class="btn btn-warning">Progress</a>
                                                 </div>
                                             </div>
                                             <br>
                                         @endforeach
                                     </div>



                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="row">

                         <div class="col">
                             <div class="card">
                                 <div class="widget">
                                     @php
                                         $data = [
                                             [
                                                 'code' => 'prg',
                                                 'nama' => 'Programmer',
                                             ],
                                             [
                                                 'code' => 'pc',
                                                 'nama' => 'Project Controller',
                                             ],
                                             [
                                                 'code' => 'tw',
                                                 'nama' => 'Technical Writer',
                                             ],
                                             [
                                                 'code' => 'soa',
                                                 'nama' => 'Software Analyst',
                                             ],
                                         ];

                                     @endphp
                                     <div class="row">
                                         <div class="col">
                                             <h5>Project Stakeholder</h5>
                                         </div>

                                     </div>

                                     <div class="card-body ">
                                         <div class="row">
                                             @foreach ($data as $key)
                                                 <div class="col">
                                                     <div class="row text-center">
                                                         <div class="col">
                                                             <div class="mb-3">
                                                                 <h3 class="text-primary"
                                                                     id="count-table-{{ $key['code'] }}">
                                                                     ?
                                                                 </h3>
                                                             </div>
                                                             <div class="mb-3 text-primary">
                                                                 {{ $key['nama'] }}
                                                             </div>
                                                             <table class="table table-lg"
                                                                 id="data-table-{{ $key['code'] }}">
                                                                 <thead>
                                                                     <tr>
                                                                         <th>NAME</th>
                                                                         <th>Divisi</th>
                                                                     </tr>
                                                                 </thead>
                                                                 <tbody>

                                                                 </tbody>
                                                             </table>
                                                         </div>

                                                     </div>
                                                 </div>
                                             @endforeach

                                         </div>

                                     </div>
                                 </div>
                             </div>
                         </div>

                         <div class="row">
                             @php
                                 $allowedRoles = ['kdp', 'kdv', 'sa', 'PM'];
                             @endphp
                             @if (in_array(Auth::user()->roles->code, $allowedRoles))
                                 <div class="col-md-5">
                                     <div class="card">
                                         <div class="card-header">
                                             <h4>Employee On Project Overiview</h4>
                                             <hr>
                                         </div>

                                         <div class="card-body">
                                             <div class="row text-center">
                                                 <div class="col">
                                                     <div class="mb-3">
                                                         <h1 class="text-primary">{{ $userCounts->users_active }}</h1>
                                                     </div>
                                                     <div class="mb-3 text-primary">
                                                         Team Member
                                                     </div>
                                                 </div>
                                                 <div class="col">
                                                     <div class="mb-3">
                                                         <h1 class="text-danger">{{ $userCounts->users_non_active }}</h1>
                                                     </div>
                                                     <div class="mb-3 text-danger">
                                                         Team Member Leave
                                                     </div>
                                                 </div>

                                                 <div style="padding: 20px">
                                                     <input type="text" id="search-input" class="form-control"
                                                         placeholder="Search">
                                                     <br>
                                                     <select class="form-control filter" id="project-filter"
                                                         name="project_filter">
                                                         <option value="" hidden>Pilih Roles</option>
                                                         @foreach ($roles as $item)
                                                             <option value="{{ $item->id }}">{{ $item->name }}
                                                             </option>
                                                         @endforeach
                                                         <!-- Tambahkan opsi lain jika diperlukan -->
                                                     </select>
                                                 </div>

                                             </div>
                                             <div class="table-responsive">
                                                 <table class="table table-lg" id="data-table">
                                                     <thead>
                                                         <tr>
                                                             <th>NAME</th>
                                                             <th>RATE</th>
                                                             <th>SKILL</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody>

                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                     </div>

                                 </div>
                             @endif
                             <div class="col-md-7">
                                 <div class="card">
                                     <div class="card-header">
                                         <h4>My Project</h4>
                                         <hr>
                                     </div>
                                     <div class="card-body ">
                                         <div class="scroll-project">
                                             @foreach ($project_all as $project)
                                                 <div class="bg-body" style="padding:20px;border-radius:20px">
                                                     <h6 class="card-title">{{ $project['project']->name }}</h6>
                                                     <p class="text-truncate">{{ $project['project']->client_name }}</p>

                                                     <div class="d-flex justify-content-between align-items-center">
                                                         <div style="width: 80%;padding-right: 5%">
                                                             <div class="progress" role="progressbar"
                                                                 aria-label="Example with label"
                                                                 aria-valuenow="{{ $project['progress'] }}"
                                                                 aria-valuemin="0" aria-valuemax="100">
                                                                 <div class="progress-bar"
                                                                     style="width: {{ $project['progress'] }}%">
                                                                     {{ $project['progress'] }}%</div>
                                                             </div>
                                                         </div>
                                                         <a href="#" class="btn btn-primary" type="button"
                                                             data-bs-toggle="modal" data-bs-target="#default"
                                                             data-id="{{ $project['project']->id }}"
                                                             id="projectDetail">More
                                                             Detail</a>
                                                     </div>
                                                 </div>
                                                 <br>
                                             @endforeach
                                         </div>



                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>



             </section>
         </div>

         <!--Basic Modal -->
         <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
             aria-hidden="true">
             <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title" id="myModalLabelproject">Project - </h5>
                         <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                             <i data-feather="x"></i>
                         </button>
                     </div>
                     <div class="modal-body">
                         <div class="table-responsive">
                             <table class="table table-lg" id="data-table-timeline">
                                 <thead>
                                     <tr>
                                         <th>Fase</th>
                                         <th>Detail</th>
                                         <th>Start Date</th>
                                         <th>End Date</th>
                                         <th>Status</th>
                                     </tr>
                                 </thead>
                                 <tbody>

                                 </tbody>
                             </table>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn" data-bs-dismiss="modal">
                             <i class="bx bx-x d-block d-sm-none"></i>
                             <span class="d-none d-sm-block">Close</span>
                         </button>
                     </div>
                 </div>
             </div>
         </div>

     </div>


     @prepend('after-script')
         <script>
             $(document).ready(function() {
                 $('#projectDetail').click(function() {
                     // Mendapatkan nilai 'id' dari tombol
                     var id = $(this).data('id');

                     $.ajax({
                         headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                         url: '{{ route('dashboard.getTimelineWithProject') }}', // Gantilah ini dengan URL server Anda
                         method: 'POST', // Atur metode HTTP yang sesuai
                         dataType: 'json', // Atur tipe data yang diharapkan
                         data: {
                             id: id
                         }, // Mengirim 'id' sebagai parameter
                         success: function(data) {
                             // Bersihkan tbody
                             $('#data-table-timeline tbody').empty();

                             // Loop melalui data dan tambahkan ke dalam tabel
                             $.each(data, function(index, item) {
                                 $('#data-table-timeline tbody').append(
                                     '<tr>' +
                                     '<td class="text-bold-500">' + item.fase + '</td>' +
                                     '<td>' + item.detail + '</td>' +
                                     '<td class="text-bold-500">' + item.startdate +
                                     '<td class="text-bold-500">' + item.enddate +
                                     '<td class="text-bold-500">' + item.status +
                                     '</td>' +
                                     '</tr>'
                                 );
                                 $('#myModalLabelproject').text('Project - ' + item
                                     .project_name);
                             });


                         },
                         error: function(xhr, status, error) {
                             console.error(error);
                         }
                     });
                 });
             });
             $(document).ready(function() {
                 // Fungsi untuk memuat data menggunakan Ajax
                 function loadData() {
                     var searchValue = $('#search-input').val();
                     var projectFilter = $('#project-filter').val();
                     $.ajax({
                         headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                         url: '{{ route('dashboard.searchEmployeeWithProject') }}', // Gantilah ini dengan URL server Anda
                         method: 'POST', // Atur metode HTTP yang sesuai
                         dataType: 'json', // Atur tipe data yang diharapkan
                         data: {
                             search: searchValue,
                             roles: projectFilter
                         },
                         success: function(data) {
                             // Bersihkan tbody
                             $('#data-table tbody').empty();

                             // Loop melalui data dan tambahkan ke dalam tabel
                             $.each(data, function(index, item) {
                                 $('#data-table tbody').append(
                                     '<tr>' +
                                     '<td class="text-bold-500">' + item.name + '</td>' +
                                     '<td>' + item.division_name + '</td>' +
                                     '<td class="text-bold-500">' + item.project_count +
                                     '</td>' +
                                     '</tr>'
                                 );
                             });
                         },
                         error: function(xhr, status, error) {
                             console.error(error);
                         }
                     });
                 }

                 // Panggil fungsi loadData saat halaman dimuat
                 loadData();
                 // Tambahkan event listener untuk input pencarian
                 $('#search-input').on('input', function() {
                     loadData();
                 });

                 // Tambahkan event listener untuk select filter
                 $('#project-filter').change(function() {
                     loadData();
                 });
             });

             $(document).ready(function() {
                 // Fungsi untuk memuat data menggunakan Ajax
                 function loadDataprojectStakeholder() {
                     var searchValue = $('#search-input').val();
                     var projectFilter = $('#project-filter').val();
                     $.ajax({
                         headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                         url: '{{ route('dashboard.projectStakeholder') }}', // Gantilah ini dengan URL server Anda
                         method: 'POST', // Atur metode HTTP yang sesuai
                         dataType: 'json', // Atur tipe data yang diharapkan
                         data: {
                             search: searchValue,
                             roles: projectFilter
                         },
                         success: function(data) {
                             var prg = 0;
                             var pc = 0;
                             var tw = 0;
                             var soa = 0;
                             $.each(data, function(index, item) {
                                 if (item.code == 'prg') {
                                     $('#data-table-prg tbody').append(
                                         '<tr>' +
                                         '<td class="text-bold-500">' + item.name + '</td>' +
                                         '<td>' + item.division_name + '</td>' +
                                         '</td>' +
                                         '</tr>'
                                     );
                                     prg++;
                                 } else if (item.code == 'pc') {
                                     $('#data-table-pc tbody').append(
                                         '<tr>' +
                                         '<td class="text-bold-500">' + item.name + '</td>' +
                                         '<td>' + item.division_name + '</td>' +
                                         '</td>' +
                                         '</tr>'
                                     );
                                     pc++;
                                 } else if (item.code == 'tw') {
                                     $('#data-table-tw tbody').append(
                                         '<tr>' +
                                         '<td class="text-bold-500">' + item.name + '</td>' +
                                         '<td>' + item.division_name + '</td>' +
                                         '</td>' +
                                         '</tr>'
                                     );
                                     tw++;
                                 } else if (item.code == 'soa') {
                                     $('#data-table-soa tbody').append(
                                         '<tr>' +
                                         '<td class="text-bold-500">' + item.name + '</td>' +
                                         '<td>' + item.division_name + '</td>' +
                                         '</td>' +
                                         '</tr>'
                                     );
                                     soa++;
                                 }
                             });

                             $("#count-table-prg").text(prg);
                             $("#count-table-pc").text(pc);
                             $("#count-table-tw").text(tw);
                             $("#count-table-soa").text(soa);
                         },
                         error: function(xhr, status, error) {
                             console.error(error);
                         }
                     });
                 }

                 // Panggil fungsi loadDataprojectStakeholder saat halaman dimuat
                 loadDataprojectStakeholder();
                 // Tambahkan event listener untuk input pencarian
                 $('#search-input').on('input', function() {
                     loadDataprojectStakeholder();
                 });

                 // Tambahkan event listener untuk select filter
                 $('#project-filter').change(function() {
                     loadDataprojectStakeholder();
                 });
             });
         </script>
