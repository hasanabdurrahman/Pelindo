<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Request Team<i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('transaction.request-team')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Request Team</a></li>
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
                                <a class="nav-link active" id="requests-tab" data-bs-toggle="tab" href="#requests" role="tab" aria-controls="requests" aria-selected="false" tabindex="-1">Request Team</a>
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
                                <a href="javascript:void(0)" onclick="renderView(`{!!route('request-team.add')!!}`)" class="spa_route btn btn-icon icon-left btn-outline-secondary rounded-pill">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>  
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
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
                                <th>Description</th>
                                <th>Approve PC</th>
                                <th>Approve KADEP</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>    
        </div>
    </section>
</div>
@include('module.transaction.request-team.modal')

@prepend('after-script')
<script type="text/javascript" src="{{ asset('js/module/transaction/request-team/request-team.js') }}"></script>