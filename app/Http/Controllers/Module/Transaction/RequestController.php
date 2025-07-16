<?php

namespace App\Http\Controllers\Module\Transaction;

use App\Helpers\DataAccessHelpers;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Employee;
use App\Models\MasterData\Project;
use App\Models\Setting\Roles;
use App\Models\Transaction\RequestTeam;
use App\Models\Transaction\RequestTeamViewModel;
use App\Models\Transaction\TaskList;
use App\Models\Transaction\TimelineA;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

use function Psy\debug;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rolesA = getPermission(Route::currentRouteName());
        return view('module.transaction.request-team.index', compact('rolesA'))->render();
    }

    /** Display data as datatable */
    public function datatable(){
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        $role = Roles::where('id', Auth::user()->roles_id)->first();
                    
        if($role->code != 'kdp'){
            $query = RequestTeamViewModel::select(['id', 'transactionnumber', 'transactiondate', 'project_id', 'karyawan_id', 'startdate', 'enddate', 'description', 'approval1', 'approval2', 'reason1', 'reason2', 'requester'])
                    ->with('karyawan')->has('karyawan')->with('project')
                    ->where('requester', Auth::user()->id)
                    ->orWhere('PC_ID', Auth::user()->id)
                    ->groupBy(['id', 'transactionnumber', 'transactiondate', 'project_id', 'karyawan_id', 'startdate', 'enddate', 'description', 'approval1', 'approval2', 'reason1', 'reason2', 'requester'])
                    ->get();
            
            foreach ($query as $val) {
                $pc = RequestTeamViewModel::select(['id', 'karyawan_id', 'PC_ID'])
                    ->where('id', $val->id)
                    ->get();

                $PC_ID = '';
                foreach ($pc as $valPC) {
                    $PC_ID .= $valPC->PC_ID.',';
                }

                $PC_ID = rtrim($PC_ID, ',');
                $val->PC_ID = $PC_ID;
            }
        } else {
            $query = RequestTeam::where('deleted_status', 0)
                ->with('karyawan')->has('karyawan')
                ->with('project')
                ->get();
        }

        return DataTables::of($query)
            ->editColumn('approval_pc', function($query){
                if($query->approval1 === 1){
                    return '<span class="badge bg-light-success">Approved</span>';
                } else if ($query->approval1 === 0) {
                    return '<span class="badge bg-light-danger" onclick="rejected_reason(`'.$query->reason1.'`, this)" data-project="'.$query->project->name.'" data-start_date="'.Carbon::parse($query->startdate)->translatedFormat('d-m-Y').'" data-end_date="'.Carbon::parse($query->enddate)->translatedFormat('d-m-Y').'" style="cursor:pointer">Rejected</span>';
                } else {
                    return '<span class="badge bg-light-warning">Need Approval</span>';
                }
            })
            ->editColumn('approval_kadep', function($query){
                if($query->approval2 === 1){
                    return '<span class="badge bg-light-success">Approved</span>';
                } else if ($query->approval2 === 0) {
                    return '<span class="badge bg-light-danger" onclick="rejected_reason(`'.$query->reason2.'`)" style="cursor:pointer">Rejected</span>';
                } else if ($query->approval1 === 0) {
                    return '<span class="badge bg-light-warning">Need Review</span>';
                } else {
                    return '<span class="badge bg-light-warning">Need Approval</span>';
                }
            })
            ->editColumn('action', function($query) use ($permission, $role){
                return self::renderAction($query, $permission, $role);
            })
            ->editColumn('startdate', function($query){
                return Carbon::parse($query->startdate)->translatedFormat('d-m-Y');
            })
            ->editColumn('enddate', function($query){
                return Carbon::parse($query->enddate)->translatedFormat('d-m-Y');
            })
            ->editColumn('transactiondate', function($query){
                return Carbon::parse($query->transactiondate)->translatedFormat('d-m-Y');
            })
            ->addIndexColumn()
            ->rawColumns(['approval_pc', 'approval_kadep', 'action'])
            ->make(true);
    }

    /** Render Action data table */
    public function renderAction($query, $permission, $role){
        $blade = '';

        $id = base64_encode($query->id);

        if ($permission->xapprove) {
            if($role->code != 'kdp'){
                $PC = explode(',',$query->PC_ID);
                if($query->approval1 === null && in_array(Auth::user()->id, $PC)){
                    $blade .= "
                        <a href='javascript:void(0)' onclick='approval(`$id`, `approve`)' class='btn icon btn-sm btn-outline-success rounded-pill me-2 mb-2' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Approve Request'>
                            <i class='fas fa-user-check'></i>
                        </a>
                        <a href='javascript:void(0)' onclick='approval(`$id`, `reject`)' class='btn icon btn-sm btn-outline-danger rounded-pill me-2 mb-2' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Reject Request'>
                            <i class='fas fa-user-xmark'></i>
                        </a>
                    ";
                }
            } else {
                if($query->approval2 === null && $query->approval1 === 0){
                    $blade .= "
                        <a href='javascript:void(0)' onclick='approval(`$id`, `review`, this)' class='btn icon btn-sm btn-outline-warning rounded-pill me-2 mb-2' data-karyawan_id='".base64_encode($query->karyawan_id)."' data-project_id='".base64_encode($query->project_id)."' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Review Karyawan'>
                            <i class='fas fa-clipboard-question'></i>
                        </a>
                    ";
                }
            }
            
        }
        if($query->approval1 === null && $query->approval2 === null && $query->requester === Auth::user()->id){
            if($permission->xdelete){
                if($role->code != 'kdp'){
                    if($query->requester == Auth::user()->id){
                        $blade .= "
                                <a href='javascript:void(0)' onclick='cancelRequest(`$id`)' class='btn icon btn-sm btn-outline-danger rounded-pill me-2 mb-2' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Cancel Request'>
                                    <i class='fas fa-cancel'></i>
                                </a>
                        ";
                    }
                }
            }
    
            if($permission->xupdate){
                if($role->code != 'kdp'){
                    if($query->requester == Auth::user()->id){
                        $blade .= "
                                <a href='javascript:void(0)' onclick='renderView(`".route('request-team.edit', $id)."`)' class='btn icon btn-sm btn-outline-warning rounded-pill me-2 mb-2' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Edit Request'>
                                    <i class='fas fa-edit'></i>
                                </a>
                        ";
                    }
                }
            }
        }

        return $blade;
    }

    public function getWorkDetail($karyawan_id){
        $karyawan_id = base64_decode($karyawan_id);
        
        // GET TOTAL PROJECT 
        $project =  DB::table('m_project as mp')
                        ->select('mp.id', 'mp.code', 'mp.name', 'tt.transactionnumber', 'tta.karyawan_id', 'tta.closed')
                        ->join('t_timeline as tt', 'tt.project_id', '=', 'mp.id')
                        ->join('t_timelineA as tta', 'tta.transactionnumber', '=', 'tt.transactionnumber')
                        ->get();

        $data['project'] = [];

        /** LIST PROJECT */
        foreach ($project as $val) {
            $storedKaryawanId = explode(',', $val->karyawan_id);

            if(in_array($karyawan_id, $storedKaryawanId)){
                unset($val->karyawan_id);
                unset($val->closed);
                unset($val->id);
                if(!in_array($val, $data['project'])){
                    array_push($data['project'], $val);
                }
            }
        }

        /** 
         * PROJECT DONE, ON PROGRESS & OVERDUE BY THIS USER 
         * List based on timeline assigned to user
         */
        $data['project_done'] = [];
        $data['project_on_progress'] = [];
        $data['project_overdue'] = [];
        foreach ($data['project'] as $val) {
            $timelineA = DB::table('t_timelineA as tta')
                            ->select('tta.transactionnumber', 'tta.closed', 'tta.karyawan_id', 'tt.project_id', 'mp.name', 'mp.code')
                            ->join('t_timeline as tt', 'tt.transactionnumber', 'tta.transactionnumber')
                            ->join('m_project as mp', 'mp.id', 'tt.project_id')
                            ->where('tta.transactionnumber', $val->transactionnumber);

            $countClosed = 0;
            $all_timelineA = [];
            $done_timelineA = [];
            foreach ($timelineA->get() as $dt) {
                $storedKaryawanId = explode(',', $dt->karyawan_id);

                if(in_array($karyawan_id, $storedKaryawanId)){
                    array_push($all_timelineA, $dt);
                    if($dt->closed === 1){
                        array_push($done_timelineA, $dt);
                    }
                }
            }

            if(count($all_timelineA) === count($done_timelineA)){
                foreach ($done_timelineA as $dtA) {
                    unset($dtA->karyawan_id);
                    unset($dtA->closed);
                    unset($dtA->id);
                    if(!in_array($dtA, $data['project_done'])){
                        array_push($data['project_done'], $dtA);
                    }
                }
            } else {
                foreach ($all_timelineA as $dtA) {
                    unset($dtA->karyawan_id);
                    unset($dtA->closed);
                    unset($dtA->id);
                    if(!in_array($dtA, $data['project_on_progress'])){
                        array_push($data['project_on_progress'], $dtA);
                    }
                }
            }

            /** Project Overdue */
            $overdue_timeline = [];
            $timelineA = $timelineA->whereDate('tta.enddate', '<=', Carbon::now())->get();
            foreach ($timelineA as $t_overdue) {
                $storedKaryawanId = explode(',', $t_overdue->karyawan_id);

                if(in_array($karyawan_id, $storedKaryawanId)){
                    if($t_overdue->closed === 0){
                        array_push($overdue_timeline, $t_overdue);
                    }
                }
            }

            foreach ($overdue_timeline as $dt_overdue) {
                unset($dt_overdue->karyawan_id);
                unset($dt_overdue->closed);
                unset($dt_overdue->id);
                if(!in_array($dt_overdue, $data['project_overdue'])){
                    array_push($data['project_overdue'], $dt_overdue);
                }
            }
        }

        /**
         * List tasklist
         */
        $tasklist['all'] = TaskList::where('karyawan_id', $karyawan_id)
                            ->with('project')
                            ->with('timelineA')
                            ->whereHas('timelineA', function($q){
                                $q->where('closed', 0);
                            })
                            ->where('deleted_status', 0)
                            ->orderBy('progress', 'DESC')
                            ->get();

        $projects = DB::table('t_tasklist as tsk')
                    ->select('tsk.project_id', 'mp.name')
                    ->join('m_project as mp', 'tsk.project_id', 'mp.id')
                    ->groupBy('tsk.project_id', 'mp.name')
                    ->get();

        $tasklist['groupBy'] = [];
        foreach ($projects as $project) {
            $tasklist['groupBy'][str_replace(' ', '_', $project->name)] = [];

            $tasklists = TaskList::where('karyawan_id', $karyawan_id)
                        ->with('project')
                        ->with('timelineA')
                        ->where('deleted_status', 0)
                        ->where('project_id', $project->project_id)
                        ->orderBy('progress', 'DESC')
                        ->get();

            foreach ($tasklists as $tsk_value) {
                array_push($tasklist['groupBy'][str_replace(' ', '_', $project->name)], $tsk_value);
            }
        }

        $blade = view('module.transaction.request-team.bodySummaryPekerjaan', compact('data'))->render();
        $bladeDetail = view('module.transaction.request-team.bodyListTasklist', compact('tasklist'))->render();

        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => JsonResponse::HTTP_OK,
            ],
            'data' => $data,
            'blade' => $blade,
            'bladeDetail' => $bladeDetail
        ], JsonResponse::HTTP_OK);
        // dd($data);
        // GET PRESENTASI PEKERJAAN PER PROJECT
    }

    /** 
     * Reject Request Team
     */
    public function reject(Request $request, $id){
        $id = base64_decode($id);
        $role = Roles::select('code')->where('id', Auth::user()->roles_id)->first();

        DB::beginTransaction();
        try {
            if($role->code != 'kdp'){
                $data = [
                    'approval1' => 0,
                    'reason1' => $request->reason,
                    'updated_by' => Auth::user()->name,
                    'updated_at' => now(),
                ];
            } else {
                $data = [
                    'approval2' => 0,
                    'reason2' => $request->reason,
                    'updated_by' => Auth::user()->name,
                    'updated_at' => now(),
                ];
            }

            $requestTeam = RequestTeam::find($id);
            $requestTeam->update($data);

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Gagal melakukan proses reject request',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Approve Request Team
     */
    public function approve($id){
        $id = base64_decode($id);
        $role = Roles::select('code')->where('id', Auth::user()->roles_id)->first();

        DB::beginTransaction();
        try {
            if($role->code != 'kdp'){
                $data = [
                    'approval1' => 1,
                    'reason1' => '',
                    'approval2' => 1,
                    'reason2' => '',
                    'updated_by' => Auth::user()->name,
                    'updated_at' => now(),
                ];
            } else {
                $data = [
                    'approval2' => 1,
                    'reason2' => '',
                    'updated_by' => Auth::user()->name,
                    'updated_at' => now(),
                ];
            }

            $requestTeam = RequestTeam::find($id);
            $requestTeam->update($data);

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Gagal melakukan proses reject request',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** 
     * Check Team Availabillity 
     */
    public function checkAvail(Request $request){
        $employee = Employee::find($request->karyawan_id);
        $timelineA = DB::table('t_timelineA as tta')
                    ->select('tta.transactionnumber', 'tta.closed', 'tta.karyawan_id','tta.startdate','tta.enddate', 'tt.project_id', 'mp.name', 'mp.code')
                    ->join('t_timeline as tt', 'tt.transactionnumber', 'tta.transactionnumber')
                    ->join('m_project as mp', 'mp.id', 'tt.project_id')
                    ->whereDate('tta.enddate', '<=', $request->enddate)
                    ->whereDate('tta.startdate', '>=', $request->startdate)
                    ->get();

        $arrAvail = [];
        foreach ($timelineA as $tlA) {
            $storedKaryawanId = explode(',', $tlA->karyawan_id);

            if(in_array($employee->id, $storedKaryawanId)){
                if($tlA->closed === 0){
                    $text = "Karyawan $employee->name masih memiliki pekerjaan di periode waktu yang anda pilih!";
                    array_push($arrAvail, $text);
                    break;
                }
            }
        }

        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => JsonResponse::HTTP_OK,
            ],
            'availibility' => $arrAvail,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $project = Project::where('deleted_status', 0)
                    ->whereHas('timelines')
                    ->where('pc_id', Auth::user()->id)
                    ->oldest()
                    ->get();

        $employee = Employee::where('deleted_status', 0)->orderBy('name')->get();
        // $timelineA = TimelineA::where('closed', 0)->get();

        // $employee = [];
        // foreach ($timelineA as $tA_val) {
        //     $arrEmp = explode(',', $tA_val->karyawan_id);
            
        //     foreach ($allEmployee as $allEmp_val) {
        //         if (!in_array($allEmp_val->id, $arrEmp)) {
        //             $employee[] = $allEmp_val;
        //         }
        //     }
        // }   

        $data = [
            'project' => $project,
            'employee' => $employee
        ];

        return view('module.transaction.request-team.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $transNumber = DataAccessHelpers::generateRequestTeamTransNumber();
        $this->validate($request, [
            'project_id' => 'required',
            'karyawan_id' => 'required',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after:startdate',
            'description' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['requester'] = Auth::user()->id;
            $data['created_by'] =  Auth::user()->name;
            $data['created_at'] =  now();
            $data['transactiondate'] =  now();
            $data['transactionnumber'] = $transNumber;

            $request = RequestTeam::create($data);

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => $request,
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = base64_decode($id);
        $project = Project::where('deleted_status', 0)
                    ->whereHas('timelines')
                    ->oldest()
                    ->get();

        $employee = Employee::where('deleted_status', 0)->orderBy('name')->get();
        // $timelineA = TimelineA::where('closed', 0)->get();

        // $employee = [];
        // foreach ($timelineA as $tA_val) {
        //     $arrEmp = explode(',', $tA_val->karyawan_id);
            
        //     foreach ($allEmployee as $allEmp_val) {
        //         if (!in_array($allEmp_val->id, $arrEmp)) {
        //             $employee[] = $allEmp_val;
        //         }
        //     }
        // }  
        $requestTeam = RequestTeam::find($id);

        $data = [
            'project' => $project,
            'employee' => $employee,
            'storedData' => $requestTeam
        ];

        return view('module.transaction.request-team.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'project_id' => 'required',
            'karyawan_id' => 'required',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after:startdate',
            'description' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $requestTeam = RequestTeam::find($request->id);
            $requestTeam['updated_by'] = Auth::user()->name;
            $requestTeam['updated_at'] = now();
            $requestTeam->update($request->all());

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => $request,
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
            $requestTeam = RequestTeam::findOrFail($id);

            $data['deleted_by'] = auth()->user()->name;
            $data['deleted_at'] = now();
            $data['deleted_status'] = 1;

            $requestTeam->update($data);

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => null,
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
}
