@prepend('after-style')
<style>
    td.details-control {
        background: url(https://www.datatables.net/examples/resources/details_open.png) no-repeat center center;
        cursor: pointer;
        width: 30px;
        transition: .5s;
    }

    tr.shown td.details-control {
        background: url(https://www.datatables.net/examples/resources/details_close.png) no-repeat center center;
        width: 30px;
        transition: .5s;
    }
    td.details-control1 {
        background: url(https://www.datatables.net/examples/resources/details_open.png) no-repeat center center;
        cursor: pointer;
        width: 30px;
        transition: .5s;
    }

    tr.shown td.details-control1 {
        background: url(https://www.datatables.net/examples/resources/details_close.png) no-repeat center center;
        width: 30px;
        transition: .5s;
    }
</style>
<meta name="loggedInUserName" content="{{ Auth::user()->name }}">
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tasklist <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('transaction.tasklist')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tasklist</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">

            {{-- Action --}}
            <div class="card-header">
                <div class="row">
                    {{-- Left Nav --}}
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @if($roles->code == 'pc')
                            {{-- if nya belum selesai pc tidak bisa liat overview --}}
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true" onclick="reloadTable('overview')">Team Tasklist</a>
                            </li>
                            @endif


                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tasklists-tab" data-bs-toggle="tab" href="#tasklists" role="tab" aria-controls="tasklists" aria-selected="false" tabindex="-1" onclick="reloadTable('my_tasklist')">My Tasklist</a>
                            </li>

                            @if ($roles->code != 'pc')
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="rejected-tab" data-bs-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false" tabindex="-1" onclick="reloadTable('rejected')">Rejected Tasklist</a>
                            </li>
                            @endif

                        </ul>
                    </div>

                    {{-- Right Nav --}}
                    <div class="col-12 col-md-6 order-md-2 order-first ">
                        <div class="float-start float-lg-end">
                            {{-- @if ($rolesA->xprint)
                                    <!-- Tombol Print -->
                                    <button id="printButton" type="button" class="btn btn-icon icon-left btn-outline-info rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fas fa-print"></i> Print
                                    </button> --}}

                                    <!-- Tombol Import Excel -->
                                    {{-- <button id="excelButton" type="button" class="btn btn-icon icon-left btn-outline-success rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fas fa-file-excel"></i> Import Data
                                    </button>

                                    <!-- Tombol Import Pdf -->
                                    <button id="pdfButton" type="button" class="btn btn-icon icon-left btn-outline-success rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fa-regular fa-file-pdf"></i> Import Data
                                    </button> --}}
                                {{-- @endif --}}

                            @if($rolesA->xadd)
                                <!-- Tombol Tambah -->
                                <a href="javascript:void(0)" onclick="renderView(`{!!route('tasklist.add')!!}`)" class="spa_route btn btn-icon icon-left btn-outline-secondary rounded-pill">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">

                {{-- start date dan end date filter--}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" onchange="updateEndDateMin()" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" name="end_date" onchange="checkEndDate()" class="form-control">
                        </div>
                    </div>

                    @if($roles->code == 'pc')
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="1">Approve</option>
                                    <option value="0" selected>Unapprove</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="project">Project:</label>
                            <select id="project" name="project" class="form-control project_id">
                                <option value="" selected>All</option>
                                @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="timelineA">Timeline / Pekerjaan</label>
                            <select id="timelineA" name="timelineA"
                                class="form-input form-control required" style="height:2rem">
                            </select>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade active show" id="tasklists" role="tabpanel" aria-labelledby="tasklist-tab">
                        <div class="table-responsive table-card px-3">
                            <table class="table table-centered align-middle table-nowrap mb-0 data-table" id="table" style="width: 100%">
                                <thead class="text-muted table-light">
                                    <tr>
                                        {{-- <th></th> --}}
                                        <th>No</th>
                                        <th>Employee</th>
                                        <th>Periode Pekerjaan</th>
                                        <th>Tgl. Input</th>
                                        <th>Transaction Number</th>
                                        <th>Project</th>
                                        <th>Phase</th>
                                        <th>Progress %</th>
                                        <th>Action</th>
                                    </tr>

                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total:</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <table class="table table-centered align-middle table-nowrap mb-0 data-table-all" id="table_all"
                            style="width: 100%">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Employee</th>
                                    <th>Periode Pekerjaan</th>
                                    <th>Tgl. Input</th>
                                    <th>Status Approval</th>
                                    <th>Transaction Number</th>
                                    <th>Project</th>
                                    <th>Phase</th>
                                    <th>Progress %</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                        <table class="table table-centered align-middle table-nowrap mb-0 data-table-rejected" id="table_rejected"
                            style="width: 100%">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Employee</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Approve</th>
                                    <th>Transaction Number</th>
                                    <th>Project</th>
                                    <th>Time Line</th>
                                    <th>Progress %</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade text-left" id="modal-detail-approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel19" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel19">Detail</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span>Approved By : <span id="approved_by"></span> </span>
                        <br>
                        <span>Approved At : <span id="approved_at"></span> </span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-sm-block d-none">Close</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-reject" tabindex="-1" aria-labelledby="modal-reject" aria-hidden="true">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5">Reject Tasklist</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0)" id="reason-reject">
                      @csrf
                        <div class="form-group">
                            <label for="notes">Alasan Reject</label>
                            <input type="text" name="reason" id="reason" class="form-control form-input required">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary btn-confirm">Confirm</button>
                </div>
              </div>
            </div>
        </div>

        <div class="modal fade" id="modal-history" tabindex="-1" aria-labelledby="modal-reject" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5">History Approval Tasklist</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
        </div>

        <div class="modal fade" id="modal-requestApprove" tabindex="-1" aria-labelledby="modal-reject" aria-hidden="true">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5">Request Approval Ulang</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0)" id="reason-approve">
                      @csrf
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <input type="text" name="notes" id="notes" class="form-control form-input required">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary btn-confirm">Confirm</button>
                </div>
              </div>
            </div>
        </div>
    </div>
    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/tasklist/tasklist.js') }}"></script>
