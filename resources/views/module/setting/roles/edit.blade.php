@prepend('after-style')
<style>
    .list-down-btn {
        float: right;
        color: black;
        margin-top: 2px;
    }

    .list-group-item {
        border: 0px !important
    }

    .list-group-collapse {
        overflow: hidden;
    }

    .list-group-collapse li  ul {
        margin-left: -15px;
        margin-right: -15px;
        margin-bottom: -11px;
        border-radius: 0px;
    }

    .list-group-collapse li ul {
        border-radius: 0px !important;
        margin-top: 8px;
    }

    .list-group-collapse li  ul li {
        border-radius: 0px !important;
        border-left: none;
        border-right: none;
        padding-left: 32px;
    }

    #filterList {
        display: none;
    }

    #subgroup {
        display: none;
    }
</style>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Roles <i class="fas fa-refresh refresh-page" onclick="renderView(`{!! route('roles.edit', $roles->id) !!}`)"></i>
                </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Setting</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)"
                                onclick="renderView(`{!! route('roles.add') !!}`)">Roles</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            <div class="card-header">
                {{-- Left Nav --}}
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="roles-tab" data-bs-toggle="tab" href="#roles"
                                role="tab" aria-controls="roles" aria-selected="true">Roles</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="permission-tab" data-bs-toggle="tab" href="#permission"
                                role="tab" aria-controls="permission" aria-selected="false" tabindex="-1">Permission</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <form class="form form-vertical" id="edit-roles" action="javascript:void(0)">
                    @csrf
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade active show" id="roles" role="tabpanel" aria-labelledby="roles-tab">
                            <div class="form-body">
                                <input type="hidden" name="id" value="{{ $roles->id }}" class="form-input">
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="roles_order">Nama Roles</label>
                                            <input type="text" id="rolesName" value="{{ $roles->name }}"
                                                class="form-input form-control required" name="name"
                                                placeholder="roles Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="nama">Code Roles</label>
                                            <input type="text" id="roleCode" value="{{ $roles->code }}"
                                                class="form-input form-control required" name="code"
                                                placeholder="roles Code">
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="button" class="btn btn-warning me-1 mb-1"
                                            onclick="renderView(`{!! route('setting.roles') !!}`)">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="permission" role="tabpanel" aria-labelledby="permission-tab">
                            <ul class="list-group list-group-collapse">
                                 {!!$accordionMenu!!}
                            </ul>


                            <div class="col-12 d-flex justify-content-end mt-4">
                                <button type="submit" id="btn-update" class="btn btn-primary me-1 mb-1">Save</button>
                                <button type="button" class="btn btn-warning me-1 mb-1"
                                    onclick="renderView(`{!! route('setting.roles') !!}`)">Cancel</button>
                            </div>
                        </div>

                        <small>*Harap setting permission setelah input roles</small>
                    </div>
                </form>
            </div>

        </div>
    </section>

    @prepend('after-script')
    <script src="{{asset('js/module/setting/roles/roles.js')}}"></script>
</div>
