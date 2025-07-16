<?php

namespace App\Http\Controllers\Module\Transaction;

use App\Helpers\DataAccessHelpers;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Employee;
use App\Models\MasterData\Phase;
use App\Models\MasterData\PhaseA;
use App\Models\MasterData\Project;
use App\Models\Setting\Roles;
use App\Models\Transaction\AdditionalTimeline;
use App\Models\Transaction\AdditionalTimelineA;
use App\Models\Transaction\Timeline;
use App\Models\Transaction\TimelineA;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class AdditionalTimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rolesA = getPermission(Route::currentRouteName());
        $projects = Project::oldest()
                    ->where('deleted_status', 0)
                    ->whereDate('enddate', '>=', Carbon::now());

        $role = Roles::where('id', Auth::user()->roles_id)->first();
        if($role->code == 'kdp'){
            $projects = $projects->paginate(5);
        } else {
            $projects = $projects->where('pc_id', Auth::user()->id)->paginate(5);
        }
                    
        if(count($request->query()) > 0){
            return view('module.transaction.additional_timeline.projectList', compact('projects'))->render();
        }

        return view('module.transaction.additional_timeline.index', compact('rolesA', 'projects'));
    }

    /** Render Timeline View */
    public function renderTimeline($id){
        try {
            $id = base64_decode($id);
            $data['timeline'] = AdditionalTimeline::where('project_id', $id);
            $data['mainTimeline'] = Timeline::where('project_id', $id)->first();

            if($data['timeline'] != null){
                $data['timeline'] = $data['timeline']->with('timeline_detail')->latest()->get();
            } else {
                $data['timeline'] = $data['timeline']->latest()->get();
            }

            if($data['timeline'] != null && $data['mainTimeline']){
                $data['phase_parent'] = DB::table('t_timelineA')
                                        ->select('fase', 'order')
                                        ->where('transactionnumber', $data['mainTimeline']->transactionnumber)
                                        ->orderBy('order')
                                        ->groupBy('fase', 'order')
                                        ->get();

                $data['phase_parent'] = $data['phase_parent']->unique('fase');
                $data['phase'] = [];
                $fillColor = ['#008FFB', '#00E396', '#775DD0', '#FEB019', '#FF4560'];
                foreach ($data['phase_parent'] as $val) {
                    $rawPhase = DB::table('t_timelineA')
                        ->where('transactionnumber', $data['mainTimeline']->transactionnumber)
                        ->where('fase', $val->fase)
                        ->orderBy('order')
                        ->get();
                    
                    $data['phase'][$val->fase] = [];
                    foreach ($rawPhase as $phase) {
                        $arrEmp = explode(',',$phase->karyawan_id);
                        $emp = '';
                        for ($i=0; $i < count($arrEmp); $i++) { 
                            $employee = DB::table('m_employee')->select('name')->where('id', $arrEmp[$i])->first();
                            $emp .= $employee->name.', ';
                        }

                        array_push($data['phase'][$val->fase], [
                            'x' => $phase->detail,
                            'y' => [
                                Carbon::parse($phase->startdate)->getTimestampMs(),
                                Carbon::parse($phase->enddate)->getTimestampMs(),
                            ],
                            'fillColor' => $fillColor[array_rand($fillColor)]
                        ]);
                    }
                }
                $data['phase'] = json_encode($data['phase']);
            }

            $blade = view('module.transaction.additional_timeline.projectTimeline', compact('data'))->render();

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'blade' => $blade,
                'data' => $data
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

    public function datatable(Request $request){
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        $tn_number = base64_decode($request->tn_number);
        $query = AdditionalTimeline::where('transactionnumber', $tn_number)
                            ->latest()
                            ->get();

        return DataTables::of($query)
            ->editColumn('status', function($query) {
                if($query->used){
                    return '<span class="badge bg-light-success">Used</span>';
                } else {
                    return '<span class="badge bg-light-warning">Not-Used</span>';
                }
            })
            ->editColumn('action', function($query) use ($permission){
                $blade = "
                    <a href='javascript:void(0)' onclick=renderView(`".route('additional-timeline.show', base64_encode($query->id))."`) class='btn icon btn-sm btn-outline-info rounded-pill me-2'>
                        <i class='fas fa-eye'></i>
                    </a>";

                if ($permission->xupdate && $query->approved_at == null) {
                    $blade .= "
                    <a href='javascript:void(0)' onclick='renderView(`" . route('additional-timeline.edit', base64_encode($query->id)) . "`)' class='btn icon btn-sm btn-outline-warning rounded-pill me-2'>
                        <i class='fas fa-edit'></i>
                    </a>";
                }
                
                if ($permission->xapprove && $query->approved_at == null) {
                    $blade .= "
                    <a href='javascript:void(0)' onclick=approve(`$query->id`) class='btn icon btn-sm btn-outline-success rounded-pill me-2'>
                        <i class='fas fa-circle-check'></i>
                    </a>";
                }

                if ($query->used == 0 && $query->approved_at != null) {
                    $blade .= "
                    <a href='javascript:void(0)' onclick=setDefault(`$query->id`) class='btn icon btn-sm btn-outline-primary rounded-pill'>
                        <i class='fas fa-screwdriver-wrench'></i>
                    </a>";
                }

                return $blade;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Approve Additional Timeline
     */
    public function approve($id){
        $id = base64_decode($id);

        DB::beginTransaction();
        try {
            $timeline = AdditionalTimeline::find($id);
            $timeline->update([
                'approved_at' => now(),
                'approved_by' => Auth::user()->name,
            ]);

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => [
                    'msg' => 'Gagal Approve additional timeline, harap coba lagi',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Set Timeline as default timeline
     */
    public function setDefault($id){
        $id = base64_decode($id);

        DB::beginTransaction();
        try {
            /** HEADER ADDITIONAL */
            $ad_timelineH = AdditionalTimeline::where('id', $id);

            /** Duplicate data additional header and update main timeline data based on this data */
            $ad_timelineH->each(function($old_header) {
                $newTimelineH = $old_header->replicate();
                $newTimelineH = $newTimelineH->toArray();

                if(isset($newTimelineH['ad_number'])) unset($newTimelineH['ad_number']);
                if(isset($newTimelineH['used'])) unset($newTimelineH['used']);

                $timelineH = Timeline::where('transactionnumber', $newTimelineH['transactionnumber']);
                $timelineH->update($newTimelineH);
            });

            /** DETAIL ADDITIONAL */
            $ad_timelineA = AdditionalTimelineA::where('ad_number', $ad_timelineH->first()->ad_number);

            // Delete Timeline Detail first
            TimelineA::where('transactionnumber', $ad_timelineA->first()->transactionnumber)->forceDelete();

            /** Duplicate data additional header and update main timeline data based on this data */
            $ad_timelineA->each(function($old_detail) {
                $newTimelineA = $old_detail->replicate();
                $newTimelineA = $newTimelineA->toArray();
                $newTimelineA['id'] = $newTimelineA['timelineA_id'] != null ? $newTimelineA['timelineA_id'] : null;
                
                unset($newTimelineA['timelineA_id']);
                if(isset($newTimelineA['ad_number'])) unset($newTimelineA['ad_number']);
                if(isset($newTimelineA['used'])) unset($newTimelineA['used']);
                
                // Insert new one
                $timelineA = TimelineA::create($newTimelineA);
                $old_detail->update([
                    'timelineA_id' => $timelineA->id,
                ]);
                $old_detail->save();
            });

            /** UPDATE USED FIELD TO 1 AND ANOTHER ADDITIONAL TO 0 */
            $ad_timelineH->update(['used' => 1,]);
            $ad_timelineA->update(['used' => 1]);

            $notInDataHeader = AdditionalTimeline::where('id', '<>', $id);
            $notInDataHeader->update(['used' => 0]);
            
            $notInDataDetail = AdditionalTimelineA::where('ad_number', '<>', $ad_timelineH->first()->ad_number);
            $notInDataDetail->update(['used' => 0]);

            DB::commit();
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => [
                    'msg' => 'Gagal Approve additional timeline, harap coba lagi',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $project_id = base64_decode($id);
        $data['project'] = Project::where('id', $project_id)->first();
        $data['timeline'] = Timeline::where('project_id', $project_id)->with('timeline_detail')->first();
        $data['all_projects'] = Project::oldest()
                                ->where('deleted_status', 0)
                                ->whereDate('enddate', '>=', Carbon::now())
                                ->get();

        $data['phase_parent'] = DB::table('t_timelineA')
                                ->select('fase', 'order')
                                ->where('transactionnumber', $data['timeline']->transactionnumber)
                                ->orderBy('order')
                                ->groupBy('fase', 'order')
                                ->get();
        $data['phase_parent'] = $data['phase_parent']->unique('fase');

        $data['employees'] = Employee::with('roles')->whereDoesntHave('roles', function($q){
                    $q->where('code', 'sls')->where('deleted_status', 0);
                })->where('deleted_status', 0)->get();
        
        $data['phase'] = [];
        $data['totalBobot'] = 0;
        foreach ($data['phase_parent'] as $val) {
            $rawPhase = DB::table('t_timelineA')
                ->where('transactionnumber', $data['timeline']->transactionnumber)
                ->where('fase', $val->fase)
                ->orderBy('order')
                ->get();
            
            $data['phase'][$val->fase] = [];
            foreach ($rawPhase as $phase) {
                $arrEmp = explode(',',$phase->karyawan_id);
                $emp = '';
                for ($i=0; $i < count($arrEmp); $i++) { 
                    $employee = DB::table('m_employee')->select('name')->where('id', $arrEmp[$i])->first();
                    $emp .= "<span class='badge bg-light-info'>$employee->name</span>";
                }

                array_push($data['phase'][$val->fase], [
                    'task' => $phase->detail,
                    'startdate' => $phase->startdate,
                    'enddate' => $phase->enddate,
                    'employe_html' => $emp,
                    'employe' => $phase->karyawan_id,
                    'closed' => $phase->closed,
                    'bobot' => $phase->bobot,
                    'timelineA_id' => $phase->id
                ]);

                $data['totalBobot'] = $data['totalBobot'] + $phase->bobot;
            }
        }

        $data['timeline_type'] = Phase::where('deleted_status', 0)->with('detail')->get();

        return view('module.transaction.additional_timeline.add', compact('data'));
    }

    /**
     * Set phase form automatic from form input
     */
    public function setPhase($type, $project_id){
        try {
            $data['renderFromController'] = true;
            $data['employees'] = Employee::with('roles')->whereDoesntHave('roles', function($q){
                $q->where('code', 'sls')->where('deleted_status', 0);
            })->where('deleted_status', 0)->get();
            $data['project'] = Project::find(base64_decode($project_id));

            $phase = PhaseA::where('phase_id', $type)->orderBy('order', 'asc')->get();
            $blade = view('module.transaction.additional_timeline.formAdd', compact('data'))->render();

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'blade' => $blade,
                'data' => $phase
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
     * Render new blank phase
     */
    public function renderForm($project_id)
    {
        $project_id = base64_decode($project_id);
        $data['renderFromController'] = true;
        $data['employees'] = Employee::with('roles')->whereDoesntHave('roles', function($q){
            $q->where('code', 'sls')->where('deleted_status', 0);
        })->where('deleted_status', 0)->get();

        $data['project'] = Project::find($project_id);

        $blade = view('module.transaction.additional_timeline.formAdd', compact('data'))->render();
        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => JsonResponse::HTTP_OK,
            ],
            'blade' => $blade
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $adTransNumber = DataAccessHelpers::generateAdTransNumber($request->project_id, $request->transactionnumber);   
        /** 
         * Insert data to Header
         */
        DB::beginTransaction();
        try {
            $header['transactionnumber'] = $request->transactionnumber;
            $header['ad_number'] = $adTransNumber;
            $header['project_id'] = $request->project_id;
            $header['created_by'] = Auth::user()->name;
            $header['created_at'] = now();
            $header['deletestatus'] = 0;
    
            $t_header = AdditionalTimeline::create($header);

            /**
             * Start Insert Detail
             */
            try {
                for ($i=0; $i < count($request->phase) ; $i++) { 
                    AdditionalTimelineA::create([
                        'transactionnumber' => $request->transactionnumber,
                        'timelineA_id' => $request->timelineA_id[$i],
                        'ad_number' => $adTransNumber,
                        'fase' => $request->phase[$i],
                        'detail' => $request->work[$i],
                        'startdate' => $request->start_date[$i],
                        'enddate' => $request->end_date[$i],
                        'bobot' => $request->bobot[$i],
                        'karyawan_id' => $request->employee[$i],
                        'order' => (int)$i+1,
                        'closed' => 0
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status' => [
                        'msg' => 'OK',
                        'code' => JsonResponse::HTTP_OK,
                    ],
                ], JsonResponse::HTTP_OK);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => [
                        'msg' => 'Gagal Insert data Additional Timeline (detail), harap coba lagi',
                        'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    ],
                    'data' => null,
                    'err_detail' => $th,
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => [
                    'msg' => 'Gagal Insert data Additional Timeline (header), harap coba lagi',
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
        $data['timeline'] = AdditionalTimeline::where('id', $id)->with('timeline_detail')->first();

        $data['project'] = Project::where('id', $data['timeline']->project_id)->first();
        $data['all_projects'] = Project::oldest()
                                ->where('deleted_status', 0)
                                ->whereDate('enddate', '>=', Carbon::now())
                                ->get();

        $data['phase_parent'] = DB::table('t_additionalA')
                                ->select('fase', 'order')
                                ->where('ad_number', $data['timeline']->ad_number)
                                ->orderBy('order')
                                ->groupBy('fase', 'order')
                                ->get();
        $data['phase_parent'] = $data['phase_parent']->unique('fase');

        $data['employees'] = Employee::with('roles')->whereDoesntHave('roles', function($q){
                    $q->where('code', 'sls')->where('deleted_status', 0);
                })->where('deleted_status', 0)->get();
        
        $data['phase'] = [];
        foreach ($data['phase_parent'] as $val) {
            $rawPhase = DB::table('t_additionalA')
                ->where('ad_number', $data['timeline']->ad_number)
                ->where('fase', $val->fase)
                ->orderBy('order')
                ->get();
            
            $data['phase'][$val->fase] = [];
            foreach ($rawPhase as $phase) {
                $arrEmp = explode(',',$phase->karyawan_id);
                $emp = '';
                for ($i=0; $i < count($arrEmp); $i++) { 
                    $employee = DB::table('m_employee')->select('name')->where('id', $arrEmp[$i])->first();
                    $emp .= "<span class='badge bg-light-info'>$employee->name</span>";
                }

                array_push($data['phase'][$val->fase], [
                    'task' => $phase->detail,
                    'startdate' => $phase->startdate,
                    'enddate' => $phase->enddate,
                    'employe_html' => $emp,
                    'employe' => $phase->karyawan_id,
                    'closed' => $phase->closed,
                    'bobot' => $phase->bobot
                ]);
            }
        }

        return view('module.transaction.additional_timeline.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = base64_decode($id);
        $data['timeline'] = AdditionalTimeline::where('id', $id)->with('timeline_detail')->first();

        $data['project'] = Project::where('id', $data['timeline']->project_id)->first();
        $data['all_projects'] = Project::oldest()
                                ->where('deleted_status', 0)
                                ->whereDate('enddate', '>=', Carbon::now())
                                ->get();

        $data['phase_parent'] = DB::table('t_additionalA')
                                ->select('fase', 'order')
                                ->where('ad_number', $data['timeline']->ad_number)
                                ->orderBy('order')
                                ->groupBy('fase', 'order')
                                ->get();

        $data['phase_parent'] = $data['phase_parent']->unique('fase');

        $data['employees'] = Employee::with('roles')->whereDoesntHave('roles', function($q){
                    $q->where('code', 'sls')->where('deleted_status', 0);
                })->where('deleted_status', 0)->get();
        
        $data['phase'] = [];
        $data['totalBobot'] = 0;
        foreach ($data['phase_parent'] as $val) {
            $rawPhase = DB::table('t_additionalA')
                ->where('ad_number', $data['timeline']->ad_number)
                ->where('fase', $val->fase)
                ->orderBy('order')
                ->get();
            
            $data['phase'][$val->fase] = [];
            foreach ($rawPhase as $phase) {
                $arrEmp = explode(',',$phase->karyawan_id);
                $emp = '';
                for ($i=0; $i < count($arrEmp); $i++) { 
                    $employee = DB::table('m_employee')->select('name')->where('id', $arrEmp[$i])->first();
                    $emp .= "<span class='badge bg-light-info'>$employee->name</span>";
                }

                array_push($data['phase'][$val->fase], [
                    'task' => $phase->detail,
                    'startdate' => $phase->startdate,
                    'enddate' => $phase->enddate,
                    'employe_html' => $emp,
                    'employe' => $phase->karyawan_id,
                    'closed' => $phase->closed,
                    'bobot' => $phase->bobot
                ]);

                $data['totalBobot'] = $data['totalBobot'] + $phase->bobot;
            }
        }

        return view('module.transaction.additional_timeline.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        /** 
         * Insert data to Header
         */
        DB::beginTransaction();
        try {
            // Delete Timeline First
            AdditionalTimeline::where('ad_number', $request->default_adnumber)->where('id', $request->timeline_id)->forceDelete();

            $header['transactionnumber'] = $request->default_transactionnumber;
            $header['ad_number'] = $request->default_adnumber;
            $header['project_id'] = $request->new_project_id == null ? $request->project_id : $request->new_project_id;
            $header['created_by'] = Auth::user()->name;
            $header['created_at'] = now();
            $header['deletestatus'] = 0;
            $header['used'] = 0;

            $t_header = AdditionalTimeline::create($header);
            /**
             * Start Insert Detail
             */
            try {
                // Delete Timeline_A first
                AdditionalTimelineA::where('ad_number', $request->default_adnumber)->forceDelete();

                for ($i=0; $i < count($request->phase) ; $i++) { 
                    AdditionalTimelineA::create([
                        'transactionnumber' => $request->default_transactionnumber,
                        'ad_number' => $request->default_adnumber,
                        'fase' => $request->phase[$i],
                        'detail' => $request->work[$i],
                        'startdate' => $request->start_date[$i],
                        'enddate' => $request->end_date[$i],
                        'bobot' => $request->bobot[$i],
                        'karyawan_id' => $request->employee[$i] == null ? $request->default_karyawan_id[$i] : $request->employee[$i],
                        'closed' => 0,
                        'order' => (int)$i+1,
                        'used' => 0
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status' => [
                        'msg' => 'OK',
                        'code' => JsonResponse::HTTP_OK,
                    ],
                ], JsonResponse::HTTP_OK);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => [
                        'msg' => 'Gagal Insert data timeline (detail), harap coba lagi',
                        'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    ],
                    'data' => null,
                    'err_detail' => $th,
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => [
                    'msg' => 'Gagal Insert data timeline (header), harap coba lagi',
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
        //
    }

    /**
     * Show Current active timeline datatable
     */
    public function showCurrentDatatable(Request $request)
    {
        $tn_number = base64_decode($request->tn_number);
        $query = TimelineA::where('transactionnumber', $tn_number)
                            ->orderBy('order', 'asc')
                            ->get();

        return DataTables::of($query)
            ->editColumn('status', function($query) {
                if($query->closed){
                    return '<span class="badge bg-light-success">Closed</span>';
                } else {
                    return '<span class="badge bg-light-warning">Open</span>';
                }
            })
            ->editColumn('karyawan', function($query) {
                $arrEmp = explode(',',$query->karyawan_id);
                $emp = '';
                for ($i=0; $i < count($arrEmp); $i++) { 
                    $employee = DB::table('m_employee')->select('name')->where('id', $arrEmp[$i])->first();
                    $emp .= "<span class='badge bg-light-info'>$employee->name</span>";
                }

                return $emp;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'karyawan'])
            ->make(true);
    }
}
