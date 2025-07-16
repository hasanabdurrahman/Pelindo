<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Master Data Phase <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('master-project.phase')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Phase</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            {{-- <ul class="list-group sortable p-5" style="list-style: none">
                {!!$menus!!}
            </ul> --}}

            {{-- Action --}}
            <div class="card-header">
                <div class="row">
                    {{-- Left Nav --}}
                    <div class="col-12 col-md-6 order-md-1 order-last">
                    </div>

                    {{-- Right Nav --}}
                    <div class="col-12 col-md-6 order-md-2 order-first ">
                        <div class="float-start float-lg-end">
                            @if ($permission->xprint)
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
                                    </button>

                                    <!-- Tombol Import Pdf -->
                                    <button id="pdfButton" type="button"
                                        class="btn btn-icon icon-left btn-outline-success rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fa-regular fa-file-pdf"></i> Import Data
                                    </button> --}}
                            @endif

                            @if($permission->xadd)
                                <!-- Tombol Tambah -->
                                <a href="javascript:void(0)" onclick="renderView(`{!!route('phase.add')!!}`)" class="spa_route btn btn-icon icon-left btn-outline-secondary rounded-pill">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade active show" id="timeline" role="tabpanel" aria-labelledby="menu-tab">
                        <div class="table-responsive table-card px-3">
                            <table class="table table-centered align-middle table-nowrap mb-0 data-table" id="example" style="width: 100%">
                                <thead class="text-muted table-light">
                                    <tr>
                                        {{-- <th></th> --}}
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col" class="text-center">Nama Phase</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="tab-pane fade" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        Overview
                    </div> --}}
                </div>
            </div>
        </div>
    </section>

    @include('module.masterdata.phase.modal')

    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/masterdata/phase/phase.js') }}"></script>
</div>
