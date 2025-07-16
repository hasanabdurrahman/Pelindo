<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Timeline Project <i class="fas fa-refresh refresh-page"
                        onclick="renderView(`{!! route('master-project.timeline') !!}`)"></i>
                </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master Project</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Timeline</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            {{-- Left Nav --}}
            <div class="col-12 col-md-6 order-md-1 order-last">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation" onclick="changeTab(false)">
                        <a class="nav-link active" id="timeline-null-tab" data-bs-toggle="tab" href="#timeline-null"
                            role="tab" aria-controls="timeline-null" aria-selected="true">Belum Ada Timeline</a>
                    </li>
                    <li class="nav-item" role="presentation" onclick="changeTab(true)">
                        <a class="nav-link" id="timeline-exists-tab" data-bs-toggle="tab" href="#timeline-exists"
                            role="tab" aria-controls="timeline-exists" aria-selected="false" tabindex="-1">Ada
                            Timeline</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active show" id="timeline-null" role="tabpanel" aria-labelledby="timeline-null-tab">
            <section class="section">
                <div class="row">
                    <div class="col-12 col-md-3" id="container-project-list">
                        <div class="card" id="list-project">
                            @if (count($projects) > 0)
                                @include('module.transaction.timeline.projectList')
                            @else
                                <div class="d-flex justify-content-center align-items-center m-5">
                                    <center>
                                        <h6 class="text-danger">Anda tidak memiliki project yang berjalan saat ini</h6>
                                    </center>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-9" id="container-timeline">
                        <div class="card">
                            {{-- Action --}}
                            <div class="card-header">
                                <div class="row">
                                    {{-- Left Nav --}}
                                    <div class="col-12 col-md-3 order-md-1 order-last">
                                        <button class="btn icon btn-sm rounded-pill btn-outline-secondary"
                                            id="action-button2" style="display: none" onclick="showLeftNav(false)">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                    </div>

                                    {{-- Right Nav --}}
                                    <div class="col-12 col-md-9 order-md-2 order-first ">
                                        <div class="float-start float-lg-end" id="action-button" style="display: none">
                                            @if ($rolesA->xprint)
                                                <!-- Tombol Print -->
                                                <a type="button"
                                                    class="btn btn-icon icon-left btn-outline-danger rounded-pill btn-print"
                                                    style="margin-right: 10px; display:none" id="export-pdf">
                                                    <i class="fa-regular fa-file-pdf"></i> Export PDF
                                                </a>
                                                <a class="btn btn-icon icon-left btn-outline-success rounded-pill btn-print"
                                                    style="margin-right: 10px; display:none" id="export-excel">
                                                    <i class="fa-regular fa-file-excel"></i> Export Excel
                                                </a>
                                            @endif

                                            @if ($rolesA->xadd)
                                                <!-- Tombol Tambah -->
                                                <div class="btn-group mb-1" id="container-btn-add">
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn btn-icon icon-left btn-outline-primary rounded-pill"
                                                            type="button" id="dropdownMenuButton"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="fas fa-plus"></i>
                                                            Tambah
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                                            data-popper-placement="bottom-start">
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                id="btn-add">Tambah Manual </a>
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                id="btn-template" data-project-id="">Tambah Dengan
                                                                Template</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <a href="javascript:void(0)" id="btn-add"
                                                class="spa_route btn btn-icon icon-left btn-outline-primary rounded-pill"
                                                style="margin-right: 10px; display:none">
                                                <i class="fas fa-plus"></i> Tambah
                                            </a> --}}
                                            @endif

                                            @if ($rolesA->xupdate)
                                                <a href="javascript:void(0)" id="btn-edit"
                                                    class="spa_route btn btn-icon icon-left btn-outline-primary rounded-pill"
                                                    style="margin-right: 10px; display:none">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            @endif

                                            @if ($rolesA->xapprove)
                                                <a href="javascript:void(0)" id="btn-approve"
                                                    class="spa_route btn btn-icon icon-left btn-outline-success rounded-pill"
                                                    style="margin-right: 10px; display:none">
                                                    <i class="fas fa-calendar-check"></i> Approve
                                                </a>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="project-timeline-container">
                                    <div class="d-flex justify-content-center align-items-center m-5">
                                        <h6>*Harap pilih project dari list project di kiri layar</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="tab-pane fade" id="timeline-exists" role="tabpanel" aria-labelledby="timeline-exists-tab">
            <section class="section">
                <div class="row">
                    <div class="col-12 col-md-3" id="container-project-list-2">
                        <div class="card" id="list-project-2">
                            @if (count($projects) > 0)
                                @include('module.transaction.timeline.projectListHasTimeline')
                            @else
                                <div class="d-flex justify-content-center align-items-center m-5">
                                    <center>
                                        <h6 class="text-danger">Anda tidak memiliki project yang berjalan saat ini</h6>
                                    </center>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-9" id="container-timeline-2">
                        <div class="card">
                            {{-- Action --}}
                            <div class="card-header">
                                <div class="row">
                                    {{-- Left Nav --}}
                                    <div class="col-12 col-md-3 order-md-1 order-last">
                                        <button class="btn icon btn-sm rounded-pill btn-outline-secondary"
                                            id="action-button2-2" style="display: none" onclick="showLeftNav(true)">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                    </div>

                                    {{-- Right Nav --}}
                                    <div class="col-12 col-md-9 order-md-2 order-first ">
                                        <div class="float-start float-lg-end" id="action-button-2"
                                            style="display: none">
                                            @if ($rolesA->xprint)
                                                <!-- Tombol Print -->
                                                <a type="button"
                                                    class="btn btn-icon icon-left btn-outline-danger rounded-pill btn-print"
                                                    style="margin-right: 10px; display:none" id="export-pdf">
                                                    <i class="fa-regular fa-file-pdf"></i> Export PDF
                                                </a>
                                                <a class="btn btn-icon icon-left btn-outline-success rounded-pill btn-print"
                                                    style="margin-right: 10px; display:none" id="export-excel">
                                                    <i class="fa-regular fa-file-excel"></i> Export Excel
                                                </a>
                                            @endif

                                            @if ($rolesA->xadd)
                                                <!-- Tombol Tambah -->
                                                <div class="btn-group mb-1" id="container-btn-add-2">
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn btn-icon icon-left btn-outline-primary rounded-pill"
                                                            type="button" id="dropdownMenuButton"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="fas fa-plus"></i>
                                                            Tambah
                                                        </button>
                                                        <div class="dropdown-menu"
                                                            aria-labelledby="dropdownMenuButton"
                                                            data-popper-placement="bottom-start">
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                id="btn-add">Tambah Manual </a>
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                id="btn-template" data-project-id="">Tambah Dengan
                                                                Template</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <a href="javascript:void(0)" id="btn-add"
                                                class="spa_route btn btn-icon icon-left btn-outline-primary rounded-pill"
                                                style="margin-right: 10px; display:none">
                                                <i class="fas fa-plus"></i> Tambah
                                            </a> --}}
                                            @endif

                                            @if ($rolesA->xupdate)
                                                <a href="javascript:void(0)" id="btn-edit-2"
                                                    class="spa_route btn btn-icon icon-left btn-outline-primary rounded-pill"
                                                    style="margin-right: 10px; display:none">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            @endif

                                            @if ($rolesA->xapprove)
                                                <a href="javascript:void(0)" id="btn-approve-2"
                                                    class="spa_route btn btn-icon icon-left btn-outline-success rounded-pill"
                                                    style="margin-right: 10px; display:none">
                                                    <i class="fas fa-calendar-check"></i> Approve
                                                </a>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="project-timeline-container-2">
                                    <div class="d-flex justify-content-center align-items-center m-5">
                                        <h6>*Harap pilih project dari list project di kiri layar</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @include('module.transaction.timeline.modal')

    @prepend('after-script')
        <script type="text/javascript" src="{{ asset('js/module/transaction/timeline/timeline.js') }}"></script>
    </div>
