<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Add Menu <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('menu.add')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Setting</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('setting.menu')!!}`)">Menu</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            {{-- Empty div container to store hidden form input --}}
            <div id="menu_res_data">

            </div>

            <div class="card-body">
                <form class="form form-vertical" id="add-menu" action="javascript:void(0)">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="nama">Nama Menu</label>
                                    <input type="text" id="nama" class="form-input form-control required" name="name" placeholder="Nama Menu">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="parent_menu">
                                        Pilih Parent Menu
                                    </label>
                                    <select class="form-input form-select select-parent-menu" id="parent_menu" name="parent_id" style="height:2rem">
                                        <option></option>
                                        @foreach ($parentMenu as $index => $value)
                                            <option class="{{ $index }}" value="{{ $value->id }}">
                                                {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="menu_order">Menu Order</label>
                                    <input type="number" id="menu_order" class="form-input form-control required" name="xlevel" placeholder="Menu Order">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="url">Url</label>
                                    <input type="text" id="url" class="form-input form-control" name="xurl" placeholder="ex: dashboard.index">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="menu_icon">Menu Icon</label>
                                    <input type="text" id="menu_icon" class="form-input form-control" name="xicon" placeholder="Menu Icon (bootstrap icon / fontawesome)">
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" id="btn-save" class="btn btn-primary me-1 mb-1">Submit</button>
                                <button type="button" class="btn btn-warning me-1 mb-1" onclick="renderView(`{!!route('setting.menu')!!}`)" >Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </section> 

    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/setting/menu/menu.js') }}"></script>
</div>