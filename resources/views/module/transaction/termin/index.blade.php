@prepend('after-styles')
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
</style>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Termin Project <i class="fas fa-refresh refresh-page" onclick="renderView(`{!! route('master-project.termin') !!}`)"></i>
                </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Termin</a></li>
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
                    <div class="col-12 col-md-6 order-md-1 order-last">
                    </div>
                    {{-- Right Nav --}}
                    <div class="col-12 col-md-6 order-md-2 order-first ">
                        <div class="float-start float-lg-end">
                            @if($rolesA->xadd)
                                <!-- Tombol Tambah -->
                                <a href="javascript:void(0)" onclick="renderView(`{!!route('termin.add')!!}`)" class="spa_route btn btn-icon icon-left btn-outline-secondary rounded-pill">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card px-3">
                    <table class="table table-centered align-middle table-nowrap mb-0 data-table" id="example" style="width: 100%">
                        <thead class="text-muted table-light">
                            <tr>
                                <th></th>
                                <th scope="col">#</th>
                                {{-- <th scope="col">Transaction Number</th> --}}
                                <th scope="col">Nama Project</th>
                                <th scope="col">Nama PC</th>
                                <th scope="col">Nama Sales</th>
                                <th scope="col">Value Project</th>
                                <th scope="col">Project Start Date</th>
                                <th scope="col">Project End Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>


    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/termin/termin.js') }}"></script>
</div>
