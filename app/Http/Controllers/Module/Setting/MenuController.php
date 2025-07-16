<?php

namespace App\Http\Controllers\Module\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\Menu;
use App\Models\Setting\Permission;
use App\Models\Setting\PermissionRoles;
use App\Models\Setting\RolesA;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use \Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class MenuController extends Controller
{
    /**
     * Index page of Menu list
     */
    public function index(Request $request){
        $rolesA = getPermission(Route::currentRouteName());
        return view('module.setting.menu.index', compact('rolesA'))->render();
    }

    /**
     * Get data and show as datatable
     */
    public function data(Request $request){
        // Permission
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        $query = Menu::where('deleted_status', 0)
                    ->orderBy('id', 'asc')
                    ->get();

        return DataTables::of($query)
            ->editColumn('action', function($query) use ($permission) {
                return self::renderAction($query->id, $permission, $query->deleted_status);
            })
            ->editColumn('parent', function($query) {
                $parentMenu = $query->where('id', $query->parent_id)->get();
                $parentMenu = count($parentMenu) > 0 ? $parentMenu[0]->name : '';
                return "
                    <span>$parentMenu</span>
                ";
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'parent', 'status'])
            ->make(true);
    }

    public function renderAction($id, $permission, $deleted_status){
        $blade = '';
        if ($permission->xupdate && $deleted_status == 0){
            $blade .= "
                <a href='javascript:void(0)' onclick='renderView(`".route('menu.edit', $id)."`)'  class='btn icon btn-sm btn-outline-primary rounded-pill'>
                    <i class='fas fa-edit'></i>
                </a>
            ";
        } 
        
        if ($permission->xdelete){
            $icon = $deleted_status == 1 ? 'fa-eye' : 'fa-trash' ;
            $blade .= "
                <a href='javascript:void(0)' onclick='deleteMenu(`$id`, `$deleted_status`)' class='btn icon btn-sm btn-outline-danger rounded-pill'>
                    <i class='fas $icon'></i>
                </a>
            ";
        }

        return $blade;
    }

    /**
     * Show form to add menu
     */
    public function create(){
        $parentMenu = Menu::where('deleted_status', 0)->get();
        return view('module.setting.menu.add', compact('parentMenu'))->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $this->validate($request, [
            'xurl' => 'nullable|sometimes|unique:m_menu',
            'name' => 'required',
            'xlevel' => 'required',
        ], [
            'xurl.unique' => 'Url sudah digunakan',
        ]);

        try {
            $rawData = $request->all();
            $arrData = [];
            
            foreach ($rawData as $key => $val) {
                $arrData[$key] = $val;
            }
            
            $arrData['created_by'] = Auth::user()->name;

            $post = Menu::create($arrData);
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ], 
                'data' => $post
            ], JsonResponse::HTTP_OK);
        } catch (Exception $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ], 
                'data' => null,
                'err_detail' => $th->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
        $menu = Menu::find($id);
        $parentMenu = Menu::where('id', $menu->parent_id)->first();
        $parentMenuList = Menu::where('deleted_status', 0)->get();

        return view('module.setting.menu.edit', compact('menu', 'parentMenu', 'parentMenuList'))->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'xurl' => 'nullable|sometimes|unique:m_menu,xurl,' . $request->id,
            'name' => 'required',
            'xlevel' => 'required',
        ], [
            'xurl.unique' => 'Url sudah digunakan',
        ]);

        try {
            $menu = Menu::find($request->id);
            $rawData = $request->all();
            $arrData = [];
            
            foreach ($rawData as $key => $val) {
                if($key != 'id' && $key != 'default-parent_id'){
                    $arrData[$key] = $val;
                }
    
                if($key == 'parent_id'){
                    if($val == null && isset($rawData['default-parent_id'])){
                        $arrData[$key] = $rawData['default-parent_id'];
                    }
                }
            }
            
            $arrData['updated_by'] = Auth::user()->name;
            $arrData['updated_at'] = Carbon::now()->toDateTimeString();

            $menu->update($arrData);

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 'data' => $menu
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => 500,
                ], 
                'data' => null,
                'err_detail' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $menu = Menu::find($id);
            $rolesA = RolesA::where('id_menu', $id)->first();

            if($rolesA != null){
                if($rolesA->deleted_status == 0){
                    $rolesA->deleted_status = 1;
                    $rolesA->deleted_at = Carbon::now()->toDateTimeString();
                    $rolesA->deleted_by = Auth::user()->name;
                } else {
                    $rolesA->deleted_status = 0;
                }
                $rolesA->save();
            }
            
            if($menu->deleted_status == 0){
                $menu->deleted_status = 1;
                $menu->deleted_at = Carbon::now()->toDateTimeString();
                $menu->deleted_by = Auth::user()->name;
            } else {
                $menu->deleted_status = 0;
            }
            
            $menu->save();

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

    public function getMenu($menus){
        $blade = '';
        foreach ($menus as $val) {
            $parent_id = $val->parent_id;
            if ($parent_id == null){
                $blade .= "
                    <li class='list-group-item list-group-item-action' style='list-style: none'>
                        <a href='javascript:void(0)' style='color:black'> 
                            <i class='bi bi-justify fs-3'></i>
                            $val->name 
                        </a>
                        <ul class='submenu-list' style='list-style: none'>
                            ".self::getMenuChild($menus, $val->id)."
                        </ul>
                    </li>
                ";
            }
        }
        return $blade;  
    }

    public function getMenuChild($menu, $parent_id){
        $child = '';
        foreach($menu as $val){
            $mn = $val;
            if($mn->parent_id != null){
                if ($mn->parent_id == $parent_id){
                    $child .= "
                    <li class='list-group-item list-group-item-action' style='list-style: none'>
                        <a href='javascript:void(0)' style='color:black'>
                            <i class='bi bi-justify fs-3'></i>
                            $val->name 
                        </a>
                        <ul class='submenu-list' style='list-style: none'>
                            ".self::getMenuChild($menu, $val->id)."
                        </ul>
                    </li>
                    ";
                }
            }
        }

        return $child;
    }
}
