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

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Monitoring <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('transaction.monitoring')!!}`)"></i> </h3>
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
    <section class="section row">
        <div class="card">

            {{-- Action --}}
            <div class="card-header">
                <div class="row">
                    {{-- Left Nav --}}
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="monitorings-tab" data-bs-toggle="tab" href="#monitorings" role="tab" aria-controls="monitorings" aria-selected="false" tabindex="-1">Monitoring</a>
                            </li>
                        </ul>
                    </div>

                    {{-- Right Nav --}}
                    <div class="col-12 col-md-6 order-md-2 order-first ">
                        <div class="float-start float-lg-end">
                            @if ($rolesA->xprint)
                                    <!-- Tombol Print -->
                                    <button id="printButton" type="button" class="btn btn-icon icon-left btn-outline-info rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fas fa-print"></i> Print
                                    </button>

                                    <!-- Tombol Import Excel -->
                                    <button id="excelButton" type="button" class="btn btn-icon icon-left btn-outline-success rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fas fa-file-excel"></i> Import Data
                                    </button>

                                    <!-- Tombol Import Pdf -->
                                    <button id="pdfButton" type="button" class="btn btn-icon icon-left btn-outline-success rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fa-regular fa-file-pdf"></i> Import Data
                                    </button>
                                @endif

                            @if($rolesA->xadd)
                                <!-- Tombol Tambah -->
                                <a href="javascript:void(0)" onclick="renderView(`{!!route('monitoring.add')!!}`)" class="spa_route btn btn-icon icon-left btn-outline-secondary rounded-pill">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>  
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade active show" id="monitorings" role="tabpanel" aria-labelledby="monitoring-tab">
                        <div class="table-responsive table-card px-3">
                            <table class="table table-centered align-middle table-nowrap mb-0 data-table" id="example" style="width: 100%">
                                <thead class="text-muted table-light">
                                    <tr>
                                        {{-- <th></th>
                                        <th scope="col">No</th>
                                        <th scope="col">Employee</th>
                                        <th scope="col">Approve</th>
                                        <th scope="col">Transaction Number</th>
                                        <th scope="col">Project</th>
                                        <th scope="col">Time Line</th>
                                        <th scope="col">Progress %</th>
                                        <th scope="col">Description</th> --}}
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <table class="table table-centered align-middle table-nowrap mb-0 data-table-all" id="example"
                            style="width: 100%">
                            <thead class="text-muted table-light">
                               Overview
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>    
        </div>
        <div class="modal fade text-left" id="modal-detail-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel19" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel19">Detail</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
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
        
        
    </div> 
    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/monitoring/monitoring.js') }}"></script>
    