<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Monitoring Project <i class="fas fa-refresh refresh-page" onclick="renderView(`{!! route('transaction.monitoring') !!}`)"></i>
                </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Monitoring</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-3" id="container-project-list">
                <div class="card" id="list-project">
                    @if(count($projects) > 0)
                        @include('module.transaction.monitoring.projectList')
                    @else
                        <div class="d-flex justify-content-center align-items-center m-5">
                            <center>
                                <h6 class="text-danger">Anda tidak memiliki project yang berjalan saat ini</h6>
                            </center>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-12 col-md-9" id="container-monitoring">
                <div class="card">
                    {{-- Action --}}
                    <div class="card-header">
                        <div class="row">
                            {{-- Left Nav --}}
                            <div class="col-12 col-md-3 order-md-1 order-last">
                                <button class="btn icon btn-sm rounded-pill btn-outline-secondary" id="action-button2" style="display: none" onclick="showLeftNav()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            </div>
        
                            {{-- Right Nav --}}
                            <div class="col-12 col-md-9 order-md-2 order-first ">
                                <div class="float-start float-lg-end" id="action-button" style="display: none">
                                    {{-- @if ($rolesA->xprint)
                                        <!-- Tombol Print -->
                                        <button id="printButton" type="button"
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
                                    {{-- @endif --}}
        
                                    {{-- @if ($rolesA->xadd)
                                        <!-- Tombol Tambah -->
                                        <a href="javascript:void(0)" id="btn-add"
                                            class="spa_route btn btn-icon icon-left btn-outline-primary rounded-pill"
                                            style="margin-right: 10px; display:none">
                                            <i class="fas fa-plus"></i> Tambah
                                        </a>
                                    @endif

                                    @if($rolesA->xupdate)
                                        <a href="javascript:void(0)" id="btn-edit"
                                            class="spa_route btn btn-icon icon-left btn-outline-primary rounded-pill"
                                            style="margin-right: 10px; display:none">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif --}}
        
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="card-body">
                        <div id="project-monitoring-container">
                            <div class="d-flex justify-content-center align-items-center m-5">
                                <h6>*Harap pilih project dari list project di kiri layar</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/monitoring/monitoring.js') }}"></script>
</div>


