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

        .dt-buttons {
            display: none;
        }
    </style>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Project <i class="fas fa-refresh refresh-page" onclick="renderView(`{!! route('master-project.project') !!}`)"></i>
                    </h3>
                    <p class="text-subtitle text-muted"></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Master</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Project</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section row">
            <div class="card">
                {{-- <ul class="list-group sortable p-5" style="list-style: none">
                {!!$project!!}
            </ul> --}}

                {{-- Action --}}
                <div class="card-header">
                    <div class="row">
                        {{-- Left Nav --}}
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">

                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="project-tab" data-bs-toggle="tab" href="#project"
                                        role="tab" aria-controls="project" aria-selected="false"
                                        tabindex="-1">Project</a>
                                </li>
                            </ul>
                        </div>

                        {{-- Right Nav --}}
                        <div class="col-12 col-md-6 order-md-2 order-first ">
                            <div class="float-start float-lg-end">

                                @if ($rolesA->xprint)
                                    <!-- Tombol Print -->
                                    {{-- <button id="printButton" type="button"
                                        class="btn btn-icon icon-left btn-outline-info rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fas fa-print"></i> Print
                                    </button> --}}

                                    <!-- Tombol Import Excel -->
                                    {{-- <button id="excelButton" type="button"
                                        class="btn btn-icon icon-left btn-outline-success rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fas fa-file-excel"></i> Import Data
                                    </button> --}}
                                @endif

                                @if ($rolesA->xadd)
                                    <!-- Tombol Tambah -->
                                    <a href="javascript:void(0)" onclick="renderView(`{!! route('project.add') !!}`)"
                                        class="spa_route btn btn-icon icon-left btn-outline-primary rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fas fa-plus"></i> Tambah
                                    </a>

                                    <!-- Import Project By Excel -->
                                    <a href="javascript:void(0)" onclick="$('#modal-import-data').modal('toggle')"
                                        class="spa_route btn btn-icon icon-left btn-outline-success rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fas fa-file-excel"></i> Import Data
                                    </a>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade active show" id="project" role="tabpanel" aria-labelledby="menu-tab">
                            <div class="table-responsive px-3">
                                <table class="table table-card table-centered align-middle table-nowrap mb-0 data-table" id="example">
                                    <thead class="text-muted table-light">
                                        <tr>
                                            <th>#</th>
                                            <th style="width: 400px">Project</th>
                                            <th style="width: 300px">Project Date</th>
                                            <th style="width: 300px">Project PIC</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            {{-- <th>Status</th> --}}
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade text-left" id="modal-detail-delete" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel19" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel19">Detail Deleted</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-x">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span>Deleted By : <span id="deleted_by"></span> </span>
                        <br>
                        <span>Deleted At : <span id="deleted_at"></span> </span>
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

        <!-- Modal Import Data -->
        <div class="modal fade text-left" id="modal-import-data" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel19" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel19">Import Data</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-x">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <form action="javascript:void(0)" id="import-project">
                        @csrf
                        <div class="modal-body">
                            <p>Import data project dengan file .xlsx, harap sesuaikan file excel dengan format yang sudah
                                ditentukan</p>
                            {{-- <br><br> --}}
                            <img src="{{ asset('assets/images/samples/template-import-project.png') }}" class="img-fluid"
                                alt="template import project image">
                            <b class="text-danger">*Contoh format excel untuk import project</b>

                            <br><br><br>
                            <input type="file" class="form-control form-control-file form-input required"
                                name="project_data" id="project_data" accept=".xlsx">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-sm-block d-none">Close</span>
                            </button>
                            <button type="submit" class="btn btn-light-success btn-sm">Import Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @prepend('after-script')
        <script type="text/javascript" src="{{ asset('js/module/masterdata/project/project.js') }}"></script>
