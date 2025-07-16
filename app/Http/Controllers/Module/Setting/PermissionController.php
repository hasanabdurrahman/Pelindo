<?php

namespace App\Http\Controllers\Module\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\Menu;
use App\Models\Setting\Permission;
use App\Models\Setting\PermissionRoles;
use App\Models\Setting\Roles;
use App\Models\Setting\RolesA;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permission = getPermission(Route::currentRouteName());
        $menus = Menu::where('deleted_status', 0)->get();
        return view('module.setting.permission.index', compact('permission', 'menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('module.setting.permission.add')->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rawData = $request->all();
            $arrData = [];
            
            foreach ($rawData as $key => $val) {
                $arrData[$key] = $val;
            }
            
            $arrData['created_by'] = Auth::user()->id;

            $post = Permission::create($arrData);
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 'data' => $post
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => 500,
                ], 
                'data' => null,
                'err_detail' => $th
            ], 500);
        }
    }

    /**
     * Set Permission for Role
     */
    public function setPermission(Request $request){
        try {
            $permissions = Permission::all();
            foreach ($request->roles as $key => $value) {
                foreach ($permissions as $permVal) {
                    $permissionRole = PermissionRoles::where('role_id', $key)->where('permission_id', $permVal->id)->first();
                    if($permissionRole != null){
                        // Update Current Permission Data
                        if($key == $permVal->role_id){
                            if(isset($value[$permVal->id]) && $value[$permVal->id] == 1){
                                $permissionRole->inactive_date = null;
                                $permissionRole->inactive_by = null;
                                $permissionRole->updated_by = Auth::user()->id;
                                $permissionRole->updated_at = Carbon::now()->toDateTimeString();
                                $permissionRole->save();
                            } else if(isset($value[$permVal->id]) && $value[$permVal->id] == 0) {
                                $permissionRole->inactive_date = Carbon::now()->toDateTimeString();
                                $permissionRole->inactive_by = Auth::user()->id;
                                $permissionRole->updated_by = Auth::user()->id;
                                $permissionRole->updated_at = Carbon::now()->toDateTimeString();
                                $permissionRole->save();
                            }                    
                        }
                    } else {
                        if(isset($value[$permVal->id]) && $value[$permVal->id] == 1){
                            PermissionRoles::create([
                                'role_id' => $key,
                                'permission_id' => $permVal->id,
                                'created_at' => Carbon::now()->toDateTimeString(),
                                'created_by' => Auth::user()->id,
                            ]);
                        }
                    }
                }
            }

            Cache::forget('key');

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 'data' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => 500,
                ], 
                'data' => null,
                'err_detail' => $th
            ], 500);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getAllRoles($id_menu){
        try {
            $data['roles'] = Roles::where('deleted_status', 0)->get();
            $data['rolesA'] = RolesA::where('deleted_status', 0)->where('id_menu', $id_menu)->get();

            $blade = view('module.setting.permission.access-form', compact('data'))->render();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 
                'data' => $data,
                'blade' => $blade
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => 500,
                ], 
                'data' => null,
                'err_detail' => $th
            ], 500);
        }
    }
}
