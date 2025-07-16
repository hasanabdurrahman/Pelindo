<?php

namespace App\Http\Controllers\Module\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\Menu;
use App\Models\Setting\Roles;
use App\Models\Setting\RolesA;
use Exception;
use \Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $permission = getPermission(Route::currentRouteName());
        $menus = Menu::where('deleted_status', 0)->get();
        return view('module.setting.roles.index', compact('permission', 'menus'))->render();
    }

    /**
     * Get data and show as datatable
     */
    public function datatable(Request $request)
    {
        // Permission
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        $query = Roles::orderBy('deleted_status', 'ASC')
                        ->orderBy('id', 'asc')
                        ->get();

        return DataTables::of($query)
            ->editColumn('action', function ($query) use($permission) {
                return self::renderAction($query->id, $query->deleted_status, $permission);
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function renderAction($id, $deleted_status, $permission)
    {
        $blade = '';

        if ($permission->xupdate && $deleted_status == 0) {
            $blade .= "
            <a href='javascript:void(0)' onclick='renderView(`" . route('roles.edit', $id) . "`)'  class='btn icon btn-sm btn-outline-primary rounded-pill'>
                <i class='fas fa-edit'></i>
            </a>";
        }

        if ($permission->xdelete) {
            $icon = $deleted_status == 1 ? 'fa-eye' : 'fa-trash' ;
            $blade .= "
            <a href='javascript:void(0)' onclick='deleteRoles(`$id`, `$deleted_status`)' class='btn icon btn-sm btn-outline-danger rounded-pill'>
                <i class='fa-regular $icon'></i>
            </a>";
        }

        return $blade;
    }

    public function create()
    {
        $menus = Menu::where('deleted_status', 0)->get();
        $accordionMenu = self::renderAccordionMenu($menus);
        return view('module.setting.roles.add', compact('menus', 'accordionMenu'));
    }

    public function edit(string $id)
    {
        $roles = Roles::findOrFail($id);
        $menus = Menu::where('deleted_status', 0)->get();
        $accordionMenu = self::renderAccordionMenu($menus, 'edit', $roles);
        return view('module.setting.roles.edit', compact('roles', 'accordionMenu'))->render();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:m_roles',
            'code' => 'required|unique:m_roles',
        ],[
            'name.unique' => 'Nama Role sudah digunakan',
            'code.unique' => 'Code Role sudah digunakan'
        ]);

        try {
            $roleData = $request->all();
            $roleData['created_by'] = auth()->user()->name;

            $role = Roles::create([
                'code' => $roleData['code'],
                'name' => $roleData['name'],
                'created_by' => $roleData['created_by']
            ]);

            self::setPermission($request, $roleData, $role);

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => $role,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:m_roles,name,' . $request->id,
            'code' => 'required|unique:m_roles,code,' . $request->id,
        ],[
            'name.unique' => 'Nama Role sudah digunakan',
            'code.unique' => 'Code Role sudah digunakan'
        ]);
        
        try {
            $role = Roles::findOrFail($request->id);

            $roleData = $request->all();
            $roleData['updated_by'] = auth()->user()->name;
            $roleData['updated_at'] = now();
            $roleData['prev_roleCode'] = $role->code;
            
            $role->update([
                'name' => $roleData['name'],
                'code' => $roleData['code'],
                'updated_by' => $roleData['updated_by'],
                'updated_at' => $roleData['updated_at']
            ]);

            self::setPermission($request, $roleData, $role, 'update');

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => $role,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function setPermission(Request $request, $roleData, $role, $type = 'store'){
        foreach ($request->rolesA as $key => $val) {
            if($val == 1){
                if($type == 'store'){
                    $roleA = RolesA::create([
                        'code' => $role->code,
                        'id_menu' => $key,
                        'xadd' => 0,
                        'xupdate' => 0,
                        'xdelete' => 0,
                        'xprint' => 0,
                        'xapprove' => 0,
                        'created_by' => Auth::user()->name,
                        'created_at' => now()
                    ]);
                } else {
                    $needEditRoleCode = $roleData['prev_roleCode'] == $roleData['code'] ? false : true;
                    $roleA = RolesA::where('id_menu', $key)->where('code', $roleData['prev_roleCode']);
                    if($roleA->first() != null){
                        if ($needEditRoleCode){
                            $roleA->update([
                                'code' => $role->code,
                                'updated_by' => Auth::user()->name,
                                'updated_at' => now(),
                                'deleted_status' => '0'
                            ]);
                        } else {
                            $roleA->update([
                                'updated_by' => Auth::user()->name,
                                'updated_at' => now(),
                                'deleted_status' => '0'
                            ]);
                        }
                    } else {
                        $roleA = RolesA::create([
                            'code' => $role->code,
                            'id_menu' => $key,
                            'xadd' => 0,
                            'xupdate' => 0,
                            'xdelete' => 0,
                            'xprint' => 0,
                            'xapprove' => 0,
                            'created_by' => Auth::user()->name,
                            'created_at' => now()
                        ]);
                    }
                }

                $roleA = RolesA::where('id_menu', $key)->where('code', $roleData['code'])->first();
                foreach ($request->xadd as $key => $val) {
                    if($key == $roleA->id_menu && $role->code == $roleData['code']){
                        if ($val == 1){
                            $roleA->xadd = 1;
                            $roleA->save();
                        } else {
                            $roleA->xadd = 0;
                            $roleA->save();
                        }
                    }
                }

                foreach ($request->xupdate as $key => $val) {
                    if($key == $roleA->id_menu && $role->code == $roleData['code']){
                        if ($val == 1){
                            $roleA->xupdate = 1;
                            $roleA->save();
                        } else {
                            $roleA->xupdate = 0;
                            $roleA->save();
                        }
                    }
                }

                foreach ($request->xdelete as $key => $val) {
                    if($key == $roleA->id_menu && $role->code == $roleData['code']){
                        if ($val == 1){
                            $roleA->xdelete = 1;
                            $roleA->save();
                        } else {
                            $roleA->xdelete = 0;
                            $roleA->save();
                        }
                    }
                }

                foreach ($request->xapprove as $key => $val) {
                    if($key == $roleA->id_menu && $role->code == $roleData['code']){
                        if ($val == 1){
                            $roleA->xapprove = 1;
                            $roleA->save();
                        } else {
                            $roleA->xapprove = 0;
                            $roleA->save();
                        }
                    }
                }

                foreach ($request->xprint as $key => $val) {
                    if($key == $roleA->id_menu && $role->code == $roleData['code']){
                        if ($val == 1){
                            $roleA->xprint = 1;
                            $roleA->save();
                        } else {
                            $roleA->xprint = 0;
                            $roleA->save();
                        }
                    }
                }
            } else {
                if ($type == 'store'){
                    $rolesA = RolesA::where('id_menu', $key)->where('code', $roleData['code']);
                } else {
                    $rolesA = RolesA::where('id_menu', $key)->where('code', $roleData['prev_roleCode']);
                }
                $rolesA->update([
                    'deleted_by' => Auth::user()->name,
                    'deleted_at' => now(),
                    'deleted_status' => '1'
                ]);
            }
        }
    }

    public function destroy(string $id)
    {
        try {
            $role = Roles::findOrFail($id);

            $roleData = [];
            $roleData['deleted_by'] = Auth::user()->name;
            $roleData['deleted_at'] = now();
            $roleData['deleted_status'] = 1;

            $role->update($roleData);

            $rolesA = RolesA::where('code', $role->code);
            $rolesA->update($roleData);

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => null,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function active(string $id)
    {
        try {
            $role = Roles::findOrFail($id);

            $roleData = [];
            $roleData['updated_by'] = auth()->user()->name;
            $roleData['updated_at'] = now();
            $roleData['deleted_status'] = 0;

            $role->update($roleData);

            $rolesA = RolesA::where('code', $role->code);
            $rolesA->update($roleData);

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => null,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Function to render accordion menu
     */
    public function renderAccordionMenu($menus, $type = 'create', $role = null){
        $blade = '';
        foreach ($menus as $val) {
            $parent_id = $val->parent_id;
            $checked  = '';
            $value = 0;

            if($type == 'edit'){
                $permission = RolesA::where('code', $role->code)->where('id_menu', $val->id)->where('deleted_status' , 0)->first();
                if ($permission != null){
                    $checked = 'checked';
                    $value = '1';
                } else {
                    $checked  = '';
                    $value = 0;
                }
            }

            if (!$parent_id) {
                $blade .= "
                <li class='list-group-item'>
                    <input type='checkbox' class='form-check-input form-input form-check-success' name='rolesA[$val->id]' id='rolesA-$val->id' $checked onchange='setPermissionCb(this)' value='$value'>
                    <label class='form-check-label' for='rolesA-$val->id'>
                        $val->name
                    </label>
                    <a href='javascript:void(0)' data-toggle='#ls-$val->id' class='list-down-btn'><span class='fas fa-chevron-down'></span></a>
                    <ul class='list-group' id='ls-$val->id' style='display: none;'>
                        ".self::getAccordionChild($menus, $val->id, $type, $role)."
                    </ul>
                </li>
                ";
            }
        }

        return $blade;
    }

    public function getAccordionChild($menus, $parent_id, $type = 'create', $role = null){
        $child = '';
        foreach ($menus as $val) {
            $mn = $val;

            $checked['menu'] = '';
            $checked['add'] = '';
            $checked['update'] = '';
            $checked['delete'] = '';
            $checked['approve'] = '';
            $checked['print'] = '';

            $value['menu'] = 0;
            $value['add'] = 0;
            $value['update'] = 0;
            $value['delete'] = 0;
            $value['approve'] = 0;
            $value['print'] = 0;

            if($type == 'edit'){
                $permission = RolesA::where('code', $role->code)->where('id_menu', $val->id)->where('deleted_status' , 0)->first();
                if($permission != null){
                    $checked['menu'] = 'checked';
                    $value['menu'] = '1';
    
                    $checked['add'] = $permission->xadd == 1 ? 'checked' : '';
                    $value['add'] = $permission->xadd;
                    
                    $checked['update'] = $permission->xupdate == 1 ? 'checked' : '';
                    $value['update'] = $permission->xupdate; 
    
                    $checked['delete'] = $permission->xdelete == 1 ? 'checked' : '';
                    $value['delete'] = $permission->xdelete;
    
                    $checked['approve'] = $permission->xapprove == 1 ? 'checked' : '';
                    $value['approve'] = $permission->xapprove;
                    
                    $checked['print'] = $permission->xprint == 1 ? 'checked' : '';
                    $value['print'] = $permission->xprint;
                }

            }

            if ($mn->parent_id != null) {
                if ($mn->parent_id == $parent_id && Route::has($mn->xurl)) {
                    $child .= "
                        <li class='list-group-item'>
                            <div class='d-flex justify-content-between align-items-center'>
                                <div class='col-2'>
                                    <input type='checkbox' disabled class='form-check-input form-input form-check-success pr-rolesA-$parent_id' name='rolesA[$mn->id]' id='rolesA-$mn->id' ".$checked['menu']." onchange='setPermissionCb(this)' value='".$value['menu']."'>
                                    <label class='form-check-label' for='rolesA-$mn->id'>
                                        $val->name
                                    </label>
                                </div>

                                <div class='col-9'>
                                    <table class='table table-centered align-middle table-nowrap mb-0' id='table-rolesA-$mn->id' style='width: 100%'>
                                        <thead class='text-muted'>
                                            <tr>
                                                <th class='text-center'>Create</th>
                                                <th class='text-center'>Update</th>
                                                <th class='text-center'>Delete</th>
                                                <th class='text-center'>Print</th>
                                                <th class='text-center'>Approve</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class='text-center'>
                                                    <input type='checkbox' disabled class='form-check-input form-input form-check-success' name='xadd[$mn->id]' id='xadd' onchange='setPermissionCb(this)' ".$checked['add']." value='".$value['add']."'>
                                                </td>
                                                <td class='text-center'>
                                                    <input type='checkbox' disabled class='form-check-input form-input form-check-success' name='xupdate[$mn->id]' id='xupdate' onchange='setPermissionCb(this)' ".$checked['update']." value='".$value['update']."'>
                                                </td>
                                                <td class='text-center'>
                                                    <input type='checkbox' disabled class='form-check-input form-input form-check-success' name='xdelete[$mn->id]' id='xdelete' onchange='setPermissionCb(this)' ".$checked['delete']." value='".$value['delete']."'>
                                                </td>
                                                <td class='text-center'>
                                                    <input type='checkbox' disabled class='form-check-input form-input form-check-success' name='xprint[$mn->id]' id='xprint' onchange='setPermissionCb(this)' ".$checked['print']." value='".$value['print']."'>
                                                </td>
                                                <td class='text-center'>
                                                    <input type='checkbox' disabled class='form-check-input form-input form-check-success' name='xapprove[$mn->id]' id='xapprove' onchange='setPermissionCb(this)' ".$checked['approve']." value='".$value['approve']."'>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class='col-1'>
                                    <a href='javascript:void(0)' class='list-down-btn' data-toggle='#ls-$val->id'><span class='fas fa-chevron-down'></a>
                                </div>
                            </div>
                            <ul class='list-group' id='ls-$val->id'style='display: none;'>
                                ".self::getAccordionChild($menus, $val->id)."
                            </ul>
                        </li>
                    ";
                }
            }
        }

        return $child;
    }
    /**
     * END of function to render accordion menu
     */
}
