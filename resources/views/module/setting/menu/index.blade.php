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
                <h3>Setting Menu <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('setting.menu')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Setting</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Menu</a></li>
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
                            @if($rolesA->ximport)
                                <!-- Tombol Import -->
                                <button type="button" class="btn btn-icon icon-left btn-outline-success">
                                    <i class="fas fa-file-excel"></i> Import Data
                                </button>
                            @endif

                            @if ($rolesA->xprint)
                                <!-- Tombol Print -->
                                <button id="printButton" type="button"
                                    class="btn btn-icon icon-left btn-outline-info rounded-pill"
                                    style="margin-right: 10px">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            @endif

                            @if($rolesA->xadd)
                                <!-- Tombol Tambah -->
                                <a href="javascript:void(0)" onclick="renderView(`{!!route('menu.add')!!}`)" class="spa_route btn btn-icon icon-left btn-outline-secondary rounded-pill">
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
                                {{-- <th></th> --}}
                                <th scope="col">#</th>
                                <th scope="col">Nama Menu</th>
                                <th scope="col">Url</th>
                                <th scope="col">Icon</th>
                                <th scope="col">Parent Menu</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section> 

    @include('module.setting.menu.modal')

    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/setting/menu/menu.js') }}"></script>
</div>
