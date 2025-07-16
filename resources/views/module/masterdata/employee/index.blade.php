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
                <h3>Employee <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('masterdata.employee')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Employee</a></li>
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
                                <a class="nav-link active" id="employees-tab" data-bs-toggle="tab" href="#employees" role="tab" aria-controls="employees" aria-selected="false" tabindex="-1">Employee</a>
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
                                    {{-- <button id="excelButton" type="button" class="btn btn-icon icon-left btn-outline-success rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fas fa-file-excel"></i> Import Data
                                    </button>

                                    <!-- Tombol Import Pdf -->
                                    <button id="pdfButton" type="button" class="btn btn-icon icon-left btn-outline-success rounded-pill"
                                        style="margin-right: 10px">
                                        <i class="fa-regular fa-file-pdf"></i> Import Data
                                    </button> --}}
                                @endif

                            @if($rolesA->xadd)
                                <!-- Tombol Tambah -->
                                <a href="javascript:void(0)" onclick="renderView(`{!!route('employee.add')!!}`)" class="spa_route btn btn-icon icon-left btn-outline-secondary rounded-pill">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>  
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade active show" id="employees" role="tabpanel" aria-labelledby="employee-tab">
                        <div class="table-responsive table-card px-3">
                            <table class="table table-centered align-middle table-nowrap mb-0 data-table" id="example" style="width: 100%">
                                <thead class="text-muted table-light">
                                    <tr>
                                        {{-- <th></th> --}}
                                        <th scope="col">No</th>
                                        <th scope="col">NIK</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Division</th>
                                        <th scope="col">Jabatan</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </div> 
    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/masterdata/employee/employee.js') }}"></script>
    