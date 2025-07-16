<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Request Ticket<i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('transaction.request-ticket')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Request Ticket</a></li>
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
                                <a class="nav-link active" id="requests-tab" data-bs-toggle="tab" href="#requests" role="tab" aria-controls="requests" aria-selected="false" tabindex="-1">Request Ticket</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="rejected-tab" data-bs-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false" tabindex="-1">Rejected Ticket</a>
                            </li>
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
                            @endif --}}

                            @if($rolesA->xadd)
                                <!-- Tombol Tambah -->
                                @if($roles->code === 'pc')
                                <a href="javascript:void(0)" onclick="renderView(`{!!route('request-ticket.add')!!}`)" class="spa_route btn btn-icon icon-left btn-outline-secondary rounded-pill">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="requests" role="tabpanel" aria-labelledby="requests-tab">
                        <div class="table-responsive table-card px-3">
                            <table class="table table-centered align-middle table-nowrap mb-0 data-table" id="table" style="width: 100%">
                                <thead class="text-muted table-light">
                                    <tr>
                                        {{-- <th></th> --}}
                                        <th>No</th>
                                        <th style="width: 150px">Project</th>
                                        <th>Karyawan</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Issue</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                        <table class="table table-centered align-middle table-nowrap mb-0 data-table-rejected" id="table_rejected"
                            style="width: 100%">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th>No</th>
                                    <th style="width: 150px">Project</th>
                                    <th>Karyawan</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Issue</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@include('module.transaction.request-ticket.modal')

@prepend('after-script')
<script type="text/javascript" src="{{ asset('js/module/transaction/request-ticket/request-ticket.js') }}"></script>
