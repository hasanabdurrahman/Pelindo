<?php

namespace App\Http\Controllers\Module\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Division;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Yajra\DataTables\DataTables;

class DivisionController extends Controller
{
    public function index(Request $request){
        $rolesA = getPermission(Route::currentRouteName());
        return view('module.masterdata.division.index',compact('rolesA'))->render();
    }

    public function datatable(Request $request){
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        $query = Division::where('deleted_status',0)->latest()->get();
        return DataTables::of($query)
        // ->editColumn('status', function ($query) {
        //     if ($query->deleted_status) {
        //         return '<span class="badge bg-light-danger" style="cursor:pointer" onclick="showDeletedDetail(this)" data-deleted_by="' . $query->deleted_by . '" data-deleted_at="' . $query->deleted_at . '">Inactive</span>';
        //     } else {
        //         return '<span class="badge bg-light-success">Active</span>';
        //     }
        // })
            ->editColumn('action', function($query) use ($permission) {
                
                return self::renderAction($query->id, $permission,$query->deleted_status);
            })

            ->editColumn('parent', function($query) {
                $parentDivision = $query->where('id', $query->parent_id)->get();
                $parentDivision = count($parentDivision) > 0 ? $parentDivision[0]->name : '';
                return "
                    <span>$parentDivision</span>
                ";
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'parent'])
            ->make(true);
    }
    public function renderAction($id, $permission, $deleted_status)
    {
        $blade = '';
        if ($permission->xupdate){
            $blade .= "
                <a href='javascript:void(0)' onclick='renderView(`".route('division.edit', $id)."`)'  class='btn icon btn-sm btn-outline-primary rounded-pill'>
                    <i class='fas fa-edit'></i>
                </a>
            ";
        } 
        
        if ($permission->xdelete) {

            if ($deleted_status == 0) {
                $blade .= "
                <a href='javascript:void(0)' onclick='deleteDivision(`$id`)' class='btn icon btn-sm btn-outline-danger rounded-pill'>
                <i class='bi-regular bi bi-trash'></i>
                </a>";
            } 
        }

        return $blade;
    }
    /**
     * Show form to add division
     */
    public function create(){
        return view('module.masterdata.division.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $this->validate($request,[
            'name' => 'required', 'string', Rule::unique('m_division')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)->where('deleted_status', 0);
            }),
            'code' => 'required', 'string', Rule::unique('m_division')->where(function ($query) use ($request) {
                return $query->where('code', $request->code)->where('deleted_status', 0);
            })
        ]);
        
        try {
            $division=$request->all();
            $division['created_by'] = auth()->user()->name;
            $division['created_at'] = now();
            $division['deleted_status'] = 0;
            $division = Division::create($division);
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 'data' => $division
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $division = Division::findOrFail($id);

        return view('module.masterdata.division.edit', compact('division'))->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'code' => 'required|unique:m_division,code,' . $request->code . ',code',
            'name' => 'required|unique:m_division,name,' . $request->name . ',name',
        ]);
        try {
            $division = Division::findOrFail($request->id);
            $division['updated_by'] = auth()->user()->name;
            $division['updated_at'] = now();
            $division->update($request->all());

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 'data' => $division
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $division = Division::findOrFail($id);
            $data = [];

            $division['deleted_by'] = auth()->user()->name;
            $division['deleted_at'] = now();
            $division['inactive_date'] = now();
            $division['inactive_by'] = auth()->user()->name;
            $division['deleted_status'] = 1;
            $division->update($data);


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

    public function active(string $id)
    {
        try {
            $division = Division::findOrFail($id);

            $data = [];

            $division['updated_by'] = auth()->user()->name;
            $division['updated_at'] = now();
            $division['deleted_status'] = 0;
            $division->update($data);

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
}
