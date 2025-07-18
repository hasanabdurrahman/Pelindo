<script>
    window.projectAll = @json($project_all);
</script>

@php
$stats = [
['title'=>'Total Project', 'key'=>'project', 'value'=>$project_all['all_project'], 'icon'=>'blue'],
['title'=>'Project Completed', 'key'=>'solved_projects', 'value'=>$project_all['solved'], 'icon'=>'green'],
['title'=>'Project On Progress', 'key'=>'inProgress_projects', 'value'=>$project_all['inProgress'], 'icon'=>'purple'],
['title'=>'Project Out Date', 'key'=>'out_date_list', 'value'=>$project_all['out_date'], 'icon'=>'red'],
['title'=>'Project Closed', 'key'=>'closed_projects', 'value'=>$project_all['closed'], 'icon'=>'orange'],
['title'=>'Total Tim IT DEV', 'key'=>'total_tim_IT', 'value'=>$project_all['total_tim_IT'], 'icon'=>'pink'],
];
@endphp


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
                    @foreach($stats as $s)
                    <div class="col-12 col-lg-6 col-md-4">
                        <div class="card stats-card"
                            data-key="{{ $s['key'] }}"
                            style="cursor:pointer">
                            <div class="card-body px-4 py-4-5">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="stats-icon {{ $s['icon'] }} mb-2">
                                            <i class="iconly-boldActivity"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h6 class="text-muted font-semibold">{{ $s['title'] }}</h6>
                                        <h6 class="font-extrabold mb-0 stat-value">{{ $s['value'] }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                

                <!-- Modal statistik -->
                <div class="modal fade" id="statsModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="statsModalLabel">Daftar Project</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped table-hover w-100" id="statsModalTable">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Project Name</th>
                                            <th>Contract Number</th>
                                            <th>Client</th>
                                            <th>Value</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>PC</th>
                                            <th>Sales</th>
                                            <th>Project Status</th>
                                            <th>Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                $allowedRoles = ['kdv', 'kdp', 'sa', 'PM'];
                @endphp

                @if (!in_array(Auth::user()->roles->code, $allowedRoles))
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
                                                    aria-valuenow="{{ $percentages['complete'] }}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    {{ $percentages['complete'] }}%
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
                                                    aria-valuenow="{{ $percentages['inProgress'] }}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    {{ $percentages['inProgress'] }}%
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
                    <div class="col-md-5">
                        <div class="card">
                            <div class="widget">
                                <div class="row">
                                    <div class="col">
                                        <button type="button" class="btn btn-primary btn-sm">
                                            To Do
                                            <span
                                                class="badge bg-transparent">{{ count($timeline_active) }}</span>
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
                                            @if (strtotime($item->enddate) >= strtotime(now()->format('Y-m-d')))
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
                    <div class="col-md-4">
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
                @endif

                <div class="row">

                    <div class="row">
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
                        'code' => 'PM',
                        'nama' => 'Project Manager',
                        ],
                        [
                        'code' => 'tw',
                        'nama' => 'Technical Writer',
                        ],
                        [
                        'code' => 'soa',
                        'nama' => 'Software Analyst',
                        ],
                        [
                        'code' => 'ba',
                        'nama' => 'Business Analyst',
                        ],
                        [
                        'code' => 'eos',
                        'nama' => 'Engineer On site',
                        ],
                        [
                        'code' => 'up',
                        'nama' => 'User Project',
                        ],
                        [
                        'code' => 'uiux',
                        'nama' => 'UI UX',
                        ],
                        ];

                        @endphp
                        @foreach ($data as $key)
                        <div class="col-md-4">
                            <div class="card">
                                <div class="widget">


                                </div>

                                <div class="card-body ">
                                    <div class="scroll">
                                        <div class="row text-center">
                                            <div class="mb-3">
                                                <h2 class="text-primary" id="count-table-{{ $key['code'] }}">
                                                    ?
                                                </h2>
                                            </div>
                                            <div class="mb-3">
                                                <h4 class="text-primary">{{ $key['nama'] }}</h4>
                                            </div>
                                            <table class="table table-lg" id="data-table-{{ $key['code'] }}">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Project</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if (in_array(Auth::user()->roles->code, $allowedRoles))
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="m-0">Overdue Task</h4>
                                    <p class="m-0">Today : {{ date('d F Y') }}</p>
                                </div>
                                <div class="card-body ">
                                    <div class="scroll-project">
                                        <table class="table table-lg">
                                            <thead>
                                                <tr>
                                                    <th>User Project</th>
                                                    <th>Project</th>
                                                    <th>Assignee</th>
                                                    <th>Progress</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $lateFound = false; @endphp
                                                @foreach ($project_all['project'] as $project)
                                                @if ($project->Project_Status == 'LATE')
                                                <tr>
                                                    <td>{{ $project->Client }}</td>
                                                    <td>{{ $project->Project_Name }}</td>
                                                    <td>{{ $project->PC }}</td>
                                                    <td>{{ $project->progress }}</td>
                                                </tr>
                                                @php $lateFound = true; @endphp
                                                @endif
                                                @endforeach
                                                @if (!$lateFound)
                                                <tr>
                                                    <td colspan="4" class="text-center">No late projects
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>

                                    </div>



                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col">
                            <div class="card" id="list-project">
                                <div class="card-header">

                                    @if (in_array(Auth::user()->roles->code, $allowedRoles))
                                    <h4>List Project</h4>
                                    @else
                                    <h4>My Project</h4>
                                    @endif

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="type">Type:</label>
                                            <select id="type" name="type" class="form-control">
                                                <option value="Project">Project</option>
                                                <option value="Manage Service">Manage Service</option>
                                            </select>
                                        </div>
                                    </div>

                                    <hr>

                                </div>

                                <div class="card-body ">
                                    <div class="scroll-project">
                                        @foreach ($project_all['project'] as $project)
                                        <div class="bg-body" style="padding:20px;border-radius:20px">

                                            <h6 class="card-title">{{ $project->Project_Name }}</h6>
                                            <p class="text-truncate">{{ $project->Client }}</p>

                                            <p style="color :red">{{ $project->type }}</p>


                                            <div class="d-flex justify-content-between align-items-center">
                                                <div style="width: 80%;padding-right: 5%">
                                                    <div class="progress" role="progressbar"
                                                        aria-label="Example with label"
                                                        aria-valuenow="{{ $project->progress }}"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar"
                                                            style="width: {{ $project->progress }}%">
                                                            {{ $project->progress }}%
                                                        </div>
                                                    </div>
                                                </div>

                                                <a href="#" class="btn btn-primary projectDetail"
                                                    type="button" data-bs-toggle="modal"
                                                    data-bs-target="#default"
                                                    data-id="{{ $project->Id_Project }}"
                                                    id="projectDetail_{{ $project->Id_Project }}"
                                                    onclick="showProjectDetail(`{{$project->Id_Project}}`)">More Detail</a>
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
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
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
                                    <th>Bobot</th>
                                    <th>Status</th>
                                    <th>Termin</th>
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
    $(function(){
        // ketika salah satu stats-card diklik
        $('.stats-card').on('click', function(){
        const key    = $(this).data('key'),
                title  = $(this).find('h6.text-muted').text(),
                list   = window.projectAll[key] || [];

        // set judul modal
        $('#statsModalLabel').text(title);

        // kosongkan dulu
        const $tb = $('#statsModalTable tbody').empty();

        if (list.length === 0) {
            $tb.append('<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>');
        } else {
            list.forEach(p => {
            $tb.append(`
                <tr>
                <td>${p.Code_Project}</td>
                <td>${p.Project_Name}</td>
                <td>${p.Contract_Numer}</td>
                <td>${p.Client}</td>
                <td>${p.Value}</td>
                <td>${p.Start_Date}</td>
                <td>${p.End_Date}</td>
                <td>${p.PC}</td>
                <td>${p.Sales}</td>
                <td>${p.Project_Status}</td>
                <td>${p.progress}%</td>
                </tr>
            `);
            });
        }

        // show modal (Bootstrap 5)
        var statsModal = new bootstrap.Modal(document.getElementById('statsModal'));
        statsModal.show();
        });
    });

    var projectTypes = []; // Langkah 1

    @foreach($project_all['project'] as $project)
    // Langkah 2
    if (!projectTypes.includes('{{ $project->type }}')) {
        projectTypes.push('{{ $project->type }}');
    }
    @endforeach

    // Langkah 3
    var typeDropdown = $('#type');
    typeDropdown.empty();
    typeDropdown.append('<option value="All">All</option>'); // Tambahkan opsi "All"
    projectTypes.forEach(function(type) {
        typeDropdown.append('<option value="' + type + '">' + type + '</option>');
    });

    // Langkah 4
    typeDropdown.change(function() {
        var selectedType = $(this).val();

        // Filter data proyek sesuai dengan jenis yang dipilih untuk card "list-project"
        $('#list-project .scroll-project').empty();
        @foreach($project_all['project'] as $project)
        if (selectedType === 'All' || '{{ $project->type }}' === selectedType) {
            // Tampilkan proyek yang sesuai dengan filter
            $('#list-project .scroll-project').append('<div class="bg-body" style="padding:20px;border-radius:20px">' +
                '<h6 class="card-title">{{ $project->Project_Name }}</h6>' +
                '<p class="text-truncate">{{ $project->Client }}</p>' +
                '<p style="color: red">{{ $project->type }}</p>' +
                '<div class="d-flex justify-content-between align-items-center">' +
                '<div style="width: 80%;padding-right: 5%">' +
                '<div class="progress" role="progressbar" aria-label="Example with label" ' +
                'aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">' +
                '<div class="progress-bar" style="width: {{ $project->progress }}%">' +
                '{{ $project->progress }}%</div></div></div>' +
                '<a href="#" class="btn btn-primary projectDetail" type="button" ' +
                'data-bs-toggle="modal" data-bs-target="#default" ' +
                'data-id="{{ $project->Id_Project }}"' +
                'id="projectDetail_{{ $project->Id_Project }}" onclick="showProjectDetail(`{{$project->Id_Project}}`)">More Detail</a></div></div><br>');
        }
        @endforeach
    });



    function showProjectDetail(id) {
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
                    let termin_name = item.termin_name != null ? item.termin_name : ''
                    $('#data-table-timeline tbody').append(
                        '<tr>' +
                        '<td class="text-bold-500">' + item.fase + '</td>' +
                        '<td>' + item.detail + '</td>' +
                        '<td class="text-bold-500">' + item.startdate +
                        '<td class="text-bold-500">' + item.enddate +
                        '<td class="text-bold-500">' + item.bobot +
                        '<td class="text-bold-500">' + item.status +
                        '<td class="text-bold-500">' + termin_name +
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
    }

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
                var ba = 0;
                var eos = 0;
                var up = 0;
                var uiux = 0;
                $.each(data, function(index, item) {
                    if (item.Jabatan_Code == 'prg') {
                        $('#data-table-prg tbody').append(
                            '<tr>' +
                            '<td class="text-bold-500">' + item.Karyawan + '</td>' +
                            '<td>' + item.project_count + '</td>' +
                            '</td>' +
                            '</tr>'
                        );
                        prg++;
                    } else if (item.Jabatan_Code == 'pc') {
                        $('#data-table-pc tbody').append(
                            '<tr>' +
                            '<td class="text-bold-500">' + item.Karyawan + '</td>' +
                            '<td>' + item.project_count + '</td>' +
                            '</td>' +
                            '</tr>'
                        );
                        pc++;
                    } else if (item.Jabatan_Code == 'tw') {
                        $('#data-table-tw tbody').append(
                            '<tr>' +
                            '<td class="text-bold-500">' + item.Karyawan + '</td>' +
                            '<td>' + item.project_count + '</td>' +
                            '</td>' +
                            '</tr>'
                        );
                        tw++;
                    } else if (item.Jabatan_Code == 'soa') {
                        $('#data-table-soa tbody').append(
                            '<tr>' +
                            '<td class="text-bold-500">' + item.Karyawan + '</td>' +
                            '<td>' + item.project_count + '</td>' +
                            '</td>' +
                            '</tr>'
                        );
                        soa++;
                    } else if (item.Jabatan_Code == 'ba') {
                        $('#data-table-ba tbody').append(
                            '<tr>' +
                            '<td class="text-bold-500">' + item.Karyawan + '</td>' +
                            '<td>' + item.project_count + '</td>' +
                            '</td>' +
                            '</tr>'
                        );
                        ba++;
                    } else if (item.Jabatan_Code == 'eos') {
                        $('#data-table-eos tbody').append(
                            '<tr>' +
                            '<td class="text-bold-500">' + item.Karyawan + '</td>' +
                            '<td>' + item.project_count + '</td>' +
                            '</td>' +
                            '</tr>'
                        );
                        eos++;
                    } else if (item.Jabatan_Code == 'up') {
                        $('#data-table-up tbody').append(
                            '<tr>' +
                            '<td class="text-bold-500">' + item.Karyawan + '</td>' +
                            '<td>' + item.project_count + '</td>' +
                            '</td>' +
                            '</tr>'
                        );
                        up++;
                    } else if (item.Jabatan_Code == 'uiux') {
                        $('#data-table-uiux tbody').append(
                            '<tr>' +
                            '<td class="text-bold-500">' + item.Karyawan + '</td>' +
                            '<td>' + item.project_count + '</td>' +
                            '</td>' +
                            '</tr>'
                        );
                        uiux++;
                    }
                });

                $("#count-table-prg").text(prg);
                $("#count-table-pc").text(pc);
                $("#count-table-tw").text(tw);
                $("#count-table-soa").text(soa);
                $("#count-table-ba").text(ba);
                $("#count-table-eos").text(eos);
                $("#count-table-up").text(up);
                $("#count-table-uiux").text(uiux);
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
</script>