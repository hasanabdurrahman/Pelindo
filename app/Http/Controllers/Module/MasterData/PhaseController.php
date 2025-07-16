<?php

namespace App\Http\Controllers\Module\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Phase;
use App\Models\MasterData\PhaseA;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PhaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permission = getPermission(Route::currentRouteName());
        return view('module.masterdata.phase.index', compact('permission'))->render();
    }

    /** 
     * Display data as datatable
     */
    public function datatable(){
        // Permission
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        $query = Phase::where('deleted_status', 0)->get();

        return DataTables::of($query)
            ->editColumn('action', function ($query) use($permission) {
                return self::renderAction($query->id, $query->deleted_status, $permission);
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function renderAction($id, $deleted_status, $permission){
        $id = base64_encode($id);

        $blade = "
            <a href='javascript:void(0)' onclick='renderView(`".route('phase.show', $id)."`)'  class='btn icon btn-sm btn-outline-warning rounded-pill'>
                <i class='fas fa-eye'></i>
            </a>
        ";
        if ($permission->xupdate && $deleted_status == 0){
            $blade .= "
                <a href='javascript:void(0)' onclick='renderView(`".route('phase.edit', $id)."`)'  class='btn icon btn-sm btn-outline-primary rounded-pill'>
                    <i class='fas fa-edit'></i>
                </a>
            ";
        } 
        
        if ($permission->xdelete){
            $icon = 'fa-trash' ;
            $blade .= "
                <a href='javascript:void(0)' onclick='deletePhase(`$id`, `$deleted_status`)' class='btn icon btn-sm btn-outline-danger rounded-pill'>
                    <i class='fas $icon'></i>
                </a>
            ";
        }

        return $blade;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('module.masterdata.phase.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required', Rule::unique('m_phase')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)->where('deleted_status', 0);
            }),
            'name' => 'required',
            // 'order' => 'required'
        ],[
            'type.unique' => 'Tipe Timeline sudah ada',
        ]);

        DB::beginTransaction();
        try {
            $timeline_type = Phase::create([
                'name' => $request->type,
                'created_at' => now(),
                'created_by' => Auth::user()->name,
                'deleted_status' => 0
            ]);

            for ($i=0; $i < count($request->name); $i++) { 
                // $phase = PhaseA::where('phase_id', $timeline_type->id)->where('order', $request->order[$i]);
    
                // Change phase order
                // if($phase->first() != null){
                //     $currPhase = $phase->get();
                //     foreach ($currPhase as $val) {
                //         $val->order = (int)$val->order + 1;
                //         $val->updated_by = Auth::user()->name;
                //         $val->updated_at = now();
                //         $val->save();
                //     }
                // } else {
                    $phase = PhaseA::create([
                        'phase_id' => $timeline_type->id,
                        'name' => $request->name[$i],
                        'order' => (int)$i+1,
                        'created_at' => now(),
                        'created_by' => Auth::user()->name
                    ]);
                // }
            }

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => '$phase',
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = base64_decode($id);
        $phase = Phase::where('id',$id)->with('detail')->first();
        return view('module.masterdata.phase.show', compact('phase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = base64_decode($id);
        $phase = Phase::where('id',$id)->with('detail')->first();
        return view('module.masterdata.phase.edit', compact('phase'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|unique:m_phase,name,' . $request->type .',name',
            'name' => 'required',
            // 'order' => 'required'
        ],[
            'type.unique' => 'Tipe Timeline sudah ada',
        ]);

        DB::beginTransaction();
        try {
            $timeline = Phase::find($request->id);
            $timeline->update([
                'name' => $request->type,
                'updated_by' => Auth::user()->name,
                'updated_at' => now(),
            ]);

            for ($i=0; $i < count($request->name); $i++) { 
                if(isset($request->phaseA_id[$i])){
                    /** SET DELETED STATUS 1 FIRST */
                    $phaseNotIN = PhaseA::where('phase_id', $request->id)->whereNot('id', $request->phaseA_id[$i]);
                    $phaseNotIN->update([
                        'deleted_by' => Auth::user()->name,
                        'deleted_at' => now(),
                        'deleted_status' => 1,
                        'order' => 0
                    ]);

                    $phase = PhaseA::where('phase_id', $request->id)->where('id', $request->phaseA_id[$i]);
                    
                    $phase->update([
                        'name' => $request->name[$i],
                        'order' => (int)$i+1,
                        'updated_at' => now(),
                        'updated_by' => Auth::user()->name,
                        'deleted_by' => NULL,
                        'deleted_at' => NULL,
                        'deleted_status' => 0,
                    ]);
                } else {
                    PhaseA::create([
                        'phase_id' => $timeline->id,
                        'name' => $request->name[$i],
                        'order' => (int)$i+1,
                        'created_at' => now(),
                        'created_by' => Auth::user()->name
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => $phase,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = base64_decode($id);

        DB::beginTransaction();
        try {
            $timeline_type = Phase::findOrFail($id);
            
            if($timeline_type->deleted_status == 0){
                $timeline_type->deleted_status = 1;
                $timeline_type->deleted_at = now();
                $timeline_type->deleted_by = Auth::user()->name;
            } else {
                $timeline_type->deleted_status = 0;
            }
            
            $timeline_type->save();

            $phase = PhaseA::where('phase_id', $id);
            $phase->update([
                'deleted_status' => 1,
                'deleted_at' => now(),
                'deleted_by' => Auth::user()->name
            ]);

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 'data' => null
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
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
