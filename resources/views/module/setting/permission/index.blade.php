<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Permission Setting <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('setting.permission')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Setting</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Permission</a></li>
                    </ol>
                </nav>
            </div>
        </div>
        <hr>
    </div>

    <div class="section row">
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
                                <a class="nav-link active" id="permissions-tab" data-bs-toggle="tab" href="#permissions" role="tab" aria-controls="permissions" aria-selected="false" tabindex="-1">Permission</a>
                            </li>
                        </ul>
                    </div>

                    {{-- Right Nav --}}
                    <div class="col-12 col-md-6 order-md-2 order-first ">
                        <div class="float-start float-lg-end">
                                @if($permission->ximport)
                                    <!-- Tombol Import -->
                                    <button type="button" class="btn btn-icon icon-left btn-outline-success">
                                        <i class="fas fa-file-excel"></i> Import Data
                                    </button>
                                @endif

                                @if($permission->xprint)
                                    <!-- Tombol Print -->
                                    <button type="button" class="btn btn-icon icon-left btn-outline-info">
                                        <i class="fas fa-print"></i> Print
                                    </button> 
                                @endif

                                @if ($permission->xadd)
                                    <!-- Tombol Tambah -->
                                    <a href="javascript:void(0)" onclick="renderView(`{!!route('permission.create')!!}`)" class="spa_route btn btn-icon icon-left btn-outline-secondary rounded-pill">
                                        <i class="fas fa-plus"></i> Tambah
                                    </a>  
                                @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade active show" id="permissions" role="tabpanel" aria-labelledby="menu-tab">
                        <div class="table-responsive" id="users-table-wrapper">
                            <form action="javascript:void(0)" class="form" id="form-permission">
                                @csrf
                                @foreach ($menus as $menu)
                                    @if ($menu->parent_id == null)
                                        <div class="card bg-light-primary p-3" style="cursor: pointer">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="col-12 col-md-1 align-self-center">
                                                    <button type="button" id="btn-grant-access" class="btn icon btn-outline-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="Grant Access To Roles"  onclick="grantAccess('{{$menu->id}}')">
                                                        <i class="fas fa-user-lock"></i>
                                                    </button>
                                                </div>
                                                <div class="col-12 col-md-10 text-left">
                                                    <span>
                                                        {{$menu->name}}
                                                    </span>
                                                </div>
                                                <div class="col-12 col-md-1 text-center">
                                                    <i class="fas fa-chevron-down" style="cursor: pointer" onclick="grantAccess('{{$menu->id}}')"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                <div id="table-action">

                                </div>
                                {{-- <table class="table table-striped table-borderless">
                                    <thead>
                                        <tr>
                                            <th class="min-width-200">@lang('Name')</th>
                                            @foreach ($roles as $role)
                                                <th class="text-center">{{ $role->name }}</th>
                                            @endforeach
                                            <th class="text-center min-width-100">@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($permissions))
                                        @foreach ($permissions as $permission)
                                            <tr>
                                                <td>{{ $permission->display_name ?: $permission->name }}</td>
                    
                                                @foreach ($roles as $role)
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input 
                                                                type="checkbox" 
                                                                class="form-input form-check-input form-check-success" 
                                                                @if (count(hasPermission($permission->id, $role->id)) > 0)
                                                                    @checked(true)
                                                                @endif
                                                                value="@if (count(hasPermission($permission->id, $role->id)) > 0) 1 @else 0 @endif"
                                                                name="roles[{{$role->id}}][{{$permission->id}}]" 
                                                                id="cb-{{$role->id}}-{{$permission->id}}"
                                                                onchange="setPermissionCb(this)"
                                                            >
                                                        </div>
                                                    </td>
                                                @endforeach
                    
                                                <td class="text-center">
                                                    <a href='javascript:void(0)' onclick='renderView(`permission.edit`,{{$permission->id}})'  class='btn icon btn-sm btn-outline-primary rounded-pill'>
                                                        <i class='fas fa-edit'></i>
                                                    </a>
                    
                                                    <a href='javascript:void(0)' onclick='deletePermission(`{{$permission->id}}`)' class='btn icon btn-sm btn-outline-danger rounded-pill'>
                                                        <i class='fas fa-trash'></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4"><em>@lang('Tidak ada data.')</em></td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" id="btn-save" class="btn btn-primary me-1 mb-1">Save Permission</button>
                                </div> --}}
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        Overview
                    </div>
                </div>
            </div>
        </div>

        @include('module.setting.permission.modal')
    </div>
    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/setting/permission/permission.js') }}"></script>
</div>