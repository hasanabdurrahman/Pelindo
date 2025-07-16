<?php

namespace App\Http\Controllers\Module\Transaction;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Client;
use App\Models\MasterData\Employee;
use App\Models\MasterData\Project;
use App\Models\Setting\Roles;
use App\Models\Transaction\HistoryApprovalTasklist;
use App\Models\Transaction\RequestTeam;
use App\Models\Transaction\TaskList;
use App\Models\Transaction\Timeline;
use App\Models\Transaction\TimelineA;
use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Psy\Command\WhereamiCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class TasklistController extends Controller
{
    public function index(Request $request)
    {
        $rolesA = getPermission(Route::currentRouteName());
        $roles = Roles::where('id', Auth::user()->roles_id)->first();
        $tasklist = TaskList::where('deleted_status', 0)->where('karyawan_id', auth()->user()->id)->get();
        $loggedInUserName = Auth::user()->name;

        $projectIds = $tasklist->pluck('project_id')->unique();

        // Retrieve projects associated with the tasks
        $projects = Project::whereIn('id', $projectIds)
            ->where('deleted_status', 0)
            ->get();

        return view('module.transaction.tasklist.index', compact('rolesA', 'tasklist', 'roles','projects'))->render();
    }

    public function datatable(Request $request)
    {
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);
        $loggedInUserName = Auth::user()->name;

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $project = $request->input('project');
        $phase = $request->input('phase');

        $query = TaskList::select('t_tasklist.*', 'm_project.name AS project_name', 'm_employee.name AS karyawan_name', 't_timelineA.detail AS timelineA', 't_timelineA.startdate AS startdate', 't_timelineA.enddate AS enddate')
            ->join('m_project', 't_tasklist.project_id', '=', 'm_project.id')
            ->join('m_employee', 't_tasklist.karyawan_id', '=', 'm_employee.id')
            ->join('t_timelineA', 't_tasklist.timelineA_id', '=', 't_timelineA.id')
            ->where('t_tasklist.deleted_status', 0)
            ->whereNot('t_tasklist.approve', 2)
            ->where('m_employee.name', $loggedInUserName);

            if ($project !== null) {
                $query->where('t_tasklist.project_id', $project);
            }
        // Tambahkan kondisi untuk filter berdasarkan tanggal
        if ($start_date && $end_date) {
            $query->where(function ($q) use ($start_date, $end_date) {
                $q->where('t_timelineA.startdate', '>=', $start_date)
                    ->where('t_timelineA.enddate', '<=', $end_date);
            });
        }

        // Tambahkan filter untuk mengecualikan data dengan End Date yang sama dengan Start Date
        // $query->whereRaw('t_timelineA.startdate <> t_timelineA.enddate');
           // Tambahkan kondisi untuk filter berdasarkan tanggal
        if ($start_date && $end_date) {
            $query->where(function($q) use ($start_date, $end_date) {
                $q->where('t_timelineA.startdate', '>=', $start_date)
                ->where('t_timelineA.enddate', '<=', $end_date);
            });
        }

        if($phase != null) {
            $query->where('t_timelineA.id', $phase);
        }

        $query->orderBy('t_tasklist.created_at', 'desc');
        // Tambahkan filter untuk mengecualikan data dengan End Date yang sama dengan Start Date
        // $query->whereRaw('t_timelineA.startdate <> t_timelineA.enddate');

        return DataTables::of($query->get())
            ->editColumn('action', function ($query) use ($permission) {

                return self::renderAction($query->id, $permission, $query->deleted_status, $query->approve, $query->created_at, $query->show);
            })
            ->editColumn('periode_pekerjaan', function($query){
                return $query->startdate .' s/d '. $query->enddate;
            })
            ->editColumn('input_date', function($query){
                return $query->tx_date == null ? Carbon::parse($query->created_at)->translatedFormat('d F Y') : Carbon::parse($query->tx_date)->translatedFormat('d F Y');
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'periode_pekerjaan', 'input_date'])
            ->make(true);
    }

    public function datatableall(Request $request)
    {
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $status = $request->input('status');
        $project = $request->input('project');
        $phase = $request->input('phase');

        $query = TaskList::select(
            't_tasklist.*',
            'm_project.name AS project_name',
            'm_employee.name AS karyawan_name',
            't_timelineA.detail AS timelineA',
            't_timelineA.startdate AS startdate',
            't_timelineA.enddate AS enddate',
            'm_project.xtype as type',
            'm_roles.code as code'
        )
            ->join('m_project', 't_tasklist.project_id', '=', 'm_project.id')
            ->join('m_employee', 't_tasklist.karyawan_id', '=', 'm_employee.id')
            ->join('t_timelineA', 't_tasklist.timelineA_id', '=', 't_timelineA.id')
            ->join('m_roles', 'm_employee.roles_id', '=', 'm_roles.id')
            ->where('t_tasklist.deleted_status', 0)
            ->whereNot('t_tasklist.approve', 2)
            ->where('t_tasklist.progress', 100)
            ->where('m_project.pc_id', Auth::user()->id);

            if ($project !== null) {
                $query->where('t_tasklist.project_id', $project);
            }

            if ($status !== null) {
                $query->where('t_tasklist.approve', $status);
            }
            if ($start_date && $end_date) {
                $query->where(function($q) use ($start_date, $end_date) {
                    $q->where('t_timelineA.startdate', '>=', $start_date)
                      ->where('t_timelineA.enddate', '<=', $end_date);
                });
            }
            if($phase != null) {
                $query->where('t_timelineA.id', $phase);
            }

            // Tambahkan filter untuk mengecualikan data dengan End Date yang sama dengan Start Date
            // $query->whereRaw('t_timelineA.startdate <> t_timelineA.enddate');

        $query->orderBy('t_tasklist.created_at', 'desc');
        return DataTables::of($query->get())
            ->editColumn('approve', function ($query) {
                if ($query->approve == 1) {
                    return '<span class="badge bg-light-success" style="cursor:pointer" onclick="showApprovedDetail(this)" data-approved_by="' . $query->approved_by . '" data-approved_at="' . $query->approved_at . '">Approved</span>';
                } else if ($query->approve == 0) {
                    return '<span class="badge bg-light-danger">Pending</span>';
                } else if ($query->approve == 2) {
                    return '<span class="badge bg-light-warning">Rejected</span>';
                }
            })
            ->editColumn('action', function ($query) use ($permission) {
                $blade = '';
                if ($query->type == 'Project') {
                    if ($permission->xapprove && $query->approve == 0 && $query->deleted_status == 0) {
                        $blade .= "
                        <a href='javascript:void(0)' onclick='ApproveTasklist(`$query->id`)'  class='btn icon btn-sm btn-outline-primary rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Approve Tasklist'>
                            <i class='bi bi-check-lg'></i>
                        </a>

                        <a href='javascript:void(0)' onclick='modalReject(`$query->id`)'  class='btn icon btn-sm btn-outline-warning rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Reject Tasklist'>
                            <i class='bi bi-x-lg'></i>
                        </a>
                        ";
                    }
                } elseif ($query->type == 'Manage Service' && $query->code == 'pc') {
                    if ($permission->xapprove && $query->approve == 0 && $query->deleted_status == 0) {
                        $blade .= "
                    <a href='javascript:void(0)' onclick='ApproveTasklist(`$query->id`)'  class='btn icon btn-sm btn-outline-primary rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Approve Tasklist'>
                    <i class='bi bi-check-lg'></i>
                    </a>
                    ";
                    }
                }
                $blade .= " <a href='javascript:void(0)' onclick=renderView(`" . route('tasklist.show', $query->id) . "`) class='btn icon btn-sm btn-outline-info rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Preview Tasklist'>
                    <i class='fas fa-eye'></i>
                    </a>";
                return $blade;
            })
            ->editColumn('periode_pekerjaan', function($query){
                return $query->startdate .' s/d '. $query->enddate;
            })
            ->editColumn('input_date', function($query){
                return $query->tx_date == null ? Carbon::parse($query->created_at)->translatedFormat('d F Y') : Carbon::parse($query->tx_date)->translatedFormat('d F Y');
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'approve', 'periode_pekerjaan', 'input_date'])
            ->make(true);
    }

    public function datatableRejected(Request $request)
    {
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);
        $loggedInUserName = Auth::user()->name;

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $query = TaskList::select('t_tasklist.*', 'm_project.name AS project_name', 'm_employee.name AS karyawan_name', 't_timelineA.detail AS timelineA', 't_timelineA.startdate AS startdate', 't_timelineA.enddate AS enddate')
            ->join('m_project', 't_tasklist.project_id', '=', 'm_project.id')
            ->join('m_employee', 't_tasklist.karyawan_id', '=', 'm_employee.id')
            ->join('t_timelineA', 't_tasklist.timelineA_id', '=', 't_timelineA.id')
            ->where('t_tasklist.deleted_status', 0)
            ->where('t_tasklist.approve', 2)
            ->where('m_employee.name', $loggedInUserName);

        // Tambahkan kondisi untuk filter berdasarkan tanggal
        if ($start_date && $end_date) {
            $query->where(function ($q) use ($start_date, $end_date) {
                $q->where('t_timelineA.startdate', '>=', $start_date)
                    ->where('t_timelineA.enddate', '<=', $end_date);
            });
        }

        // Tambahkan filter untuk mengecualikan data dengan End Date yang sama dengan Start Date
        // $query->whereRaw('t_timelineA.startdate <> t_timelineA.enddate');

        return DataTables::of($query->get())
            ->editColumn('action', function ($query) use ($permission) {

                return self::renderAction($query->id, $permission, $query->deleted_status, $query->approve, $query->created_at, $query->show);
            })
            ->editColumn('approve', function ($query) {
                if ($query->approve == 1) {
                    return '<span class="badge bg-light-success" style="cursor:pointer" onclick="showApprovedDetail(this)" data-approved_by="' . $query->approved_by . '" data-approved_at="' . $query->approved_at . '">Approved</span>';
                } else if ($query->approve == 0) {
                    return '<span class="badge bg-light-danger">Pending</span>';
                } else if ($query->approve == 2) {
                    return '<span class="badge bg-light-warning">Rejected</span>';
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'approve', 'data',])
            ->make(true);
    }

    public function renderAction($id, $permission, $deleted_status, $approve, $created_at, $show)
    {
        $blade = '';
        $currentDate = Carbon::now();

        $show =  $blade .= " <a href='javascript:void(0)' onclick=renderView(`" . route('tasklist.show', $id) . "`) class='btn icon btn-sm btn-outline-info rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Preview Tasklist'>
        <i class='fas fa-eye'></i>
        </a>";;

        if ($permission->xupdate) {
            $createdAtDate = Carbon::parse($created_at);

            if ($approve == 0 && $createdAtDate->isSameDay($currentDate)) {
                $blade .= "
                <a href='javascript:void(0)' onclick='renderView(`" . route('tasklist.edit', $id) . "`)'  class='btn icon btn-sm btn-outline-primary rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Edit Tasklist'>
                    <i class='fas fa-edit'></i>
                </a>
            ";
            }

            if ($approve == 2) {
                $blade .= "
                    <a href='javascript:void(0)' onclick='historyReject(`$id`)'  class='btn icon btn-sm btn-outline-warning rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='History Approval'>
                        <i class='fas fa-clock'></i>
                    </a>
                    <a href='javascript:void(0)' onclick='modalRequestApprove(`$id`)'  class='btn icon btn-sm btn-outline-success rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Request Approval Ulang'>
                        <i class='fas fa-check'></i>
                    </a>
                ";
            }
        }

        if ($permission->xdelete) {
            $createdAtDate = Carbon::parse($created_at);
            if ($approve == 0 && $deleted_status == 0 && $createdAtDate->isSameDay($currentDate)) {
                $blade .= "
                <a href='javascript:void(0)' onclick='deleteTasklist(`$id`)' class='btn icon btn-sm btn-outline-danger rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Hapus Tasklist'>
                <i class='fa-regular fa-eye-slash'></i>
                </a>";
            }
        }
        return $blade;
    }

    public function create()
    {
        /** LAMA */
        // Ambil semua timeline yang approve_at-nya tidak null
        // $timelines = Timeline::whereNotNull('approved_by')->where('deletestatus', 0)->get();

        // Ambil daftar project yang sesuai dengan timeline di atas
        // $projects = Project::whereIn('id', $timelines->pluck('project_id'))->where('deleted_status', 0)->get();

        /** BARU */
        $karyawan_id = Auth::user()->id;
        $project =  DB::table('m_project as mp')
            ->select('mp.id as project_id', 'mp.name', 'tta.karyawan_id')
            ->join('t_timeline as tt', 'tt.project_id', '=', 'mp.id')
            ->join('t_timelineA as tta', 'tta.transactionnumber', '=', 'tt.transactionnumber')
            ->whereNotNull('tt.approved_by')
            ->where('tta.closed', 0)
            ->orWhere('pc_id', $karyawan_id);
        // ->get();

        $requestTeam = DB::table('trx_requestteam as tr')
            ->select('tr.project_id', 'mp.name', 'tr.karyawan_id')
            ->join('m_project as mp', 'tr.project_id', 'mp.id')
            ->join('t_timeline as tt', 'mp.id', 'tt.project_id')
            ->where('tr.approval1', '1')
            ->orWhere('tr.approval2', '1')
            ->where('tr.karyawan_id', $karyawan_id)
            ->union($project)
            ->get();

        $projects = [];

        /** LIST PROJECT BASED ON TIMELINE A ASSIGNED & REQUEST TEAM */
        foreach ($requestTeam as $val) {
            $storedKaryawanId = explode(',', $val->karyawan_id);
            if (in_array($karyawan_id, $storedKaryawanId)) {
                unset($val->karyawan_id);
                // dump(in_array($val, $projects));
                if (!in_array($val, $projects)) {
                    array_push($projects, $val);
                }
            }
        }

        $employee = Employee::where('id', Auth::user()->id)->first();

        return view('module.transaction.tasklist.add', compact('employee', 'projects'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'project_id' => 'required|integer',
            'timelineA_id' => 'integer',
            'progress' => 'required|integer|between:1,100',
            'description' => 'required',
            'image.*' => 'image|mimes:jpeg,jpg,png,svg|max:500000',
            'document.*' => 'mimes:docx,doc,pdf|max:500000',
        ]);
        $projectYear = date('Y');
        $projectCount = TaskList::whereYear('created_at', '=', $projectYear)->count();
        $projectCount++;
        $formattedCounter = str_pad($projectCount, 5, '0', STR_PAD_LEFT);
        $projectCode = Project::find($request->input('project_id'))->code;
        $clientCode = substr($projectCode, 0, 3);
        $transactionnumber = "{$clientCode}/{$projectYear}/{$formattedCounter}";

        try {
            $images = [];
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $imageFile) {
                    $filename = date('y-m-d') . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalName();
                    $path = $filename;
                    $imageFile->move(public_path('tasklist'), $path);
                    $images[] = $path;
                }
                // if (!file_exists(public_path().'tasklist')) {
                //     if(FacadesFile::makeDirectory(public_path().'tasklist',0777,true)){
                //       }
                //     }
            }

            $documents = [];
            if ($request->hasFile('document')) {
                foreach ($request->file('document') as $documentFile) {
                    $filename = $documentFile->getClientOriginalName();
                    $path = $filename;
                    $documentFile->move(public_path('tasklist/document'), $path);
                    $documents[] = $path;
                }
                // if (!file_exists(public_path().'tasklist')) {
                //     if(FacadesFile::makeDirectory(public_path().'tasklist',0777,true)){
                //       }
                //     }
            }

            $tasklist = $request->all();
            if (!empty($images)) {
                $tasklist['image'] = implode(',', $images);
            } else {
                $tasklist['image'] = null;
            }

            if (!empty($documents)) {
                $tasklist['document'] = implode(',', $documents);
            } else {
                $tasklist['document'] = null;
            }
            $tasklist['transactionnumber'] = $transactionnumber;
            $tasklist['created_by'] = auth()->user()->name;
            $tasklist['created_at'] = now();
            $tasklist['deleted_status'] = 0;
            $tasklist['approve'] = 0;
            $tasklist['karyawan_id'] = auth()->user()->id;
            $tasklist['timelineA_id'] = $request->timelineA_id == null ? 0 : $request->timelineA_id;
            $tasklist = TaskList::create($tasklist);

        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => 200,
            ], 'data' => $tasklist,
            'transactionnumber' => $transactionnumber,
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => [
                'msg' => 'Err',
                'code' => 500,
            ],
            'data' => null,
            'err_detail' => $th->getMessage(),
        ], 500);
    }
}

    public function edit(string $id)
    {
        $tasklist = TaskList::findOrFail($id);
        $loggedInUserId = auth()->user()->id;
        $timelineA = Timeline::select('t_timeline.*', 't_timelineA.detail', 't_timelineA.id', 't_timelineA.karyawan_id', 't_timelineA.closed')
            ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
            ->where('t_timelineA.closed', 0)
            ->where('t_timeline.project_id', $tasklist->project_id)
            ->get();

        // Filter timelineA yang sesuai dengan karyawan_id yang mengandung ID pengguna yang masuk
        $filteredTimelineA = $timelineA->filter(function ($timeline) use ($loggedInUserId) {
            $karyawanIds = explode(',', $timeline->karyawan_id);
            return in_array($loggedInUserId, $karyawanIds) || empty($timeline->karyawan_id);
        });
        // if (!file_exists(public_path().'tasklist')) {
        // if(FacadesFile::makeDirectory(public_path().'tasklist',0777,true)){
        //   }
        // }

        $timelines = Timeline::whereNotNull('approved_by')->where('deletestatus', 0)->get();
        $project = Project::whereIn('id', $timelines->pluck('project_id'))->where('deleted_status', 0)->get();
        return view('module.transaction.tasklist.edit', compact('tasklist', 'project', 'filteredTimelineA'))->render();
    }

    public function show(string $id)
    {
        $tasklist = TaskList::findOrFail($id);
        $loggedInUserId = auth()->user()->id;
        $timelineA = Timeline::select('t_timeline.*', 't_timelineA.detail', 't_timelineA.id', 't_timelineA.karyawan_id', 't_timelineA.closed')
            ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
            ->where('t_timelineA.closed', 0)
            ->where('t_timeline.project_id', $tasklist->project_id)
            ->get();

        // Filter timelineA yang sesuai dengan karyawan_id yang mengandung ID pengguna yang masuk
        $filteredTimelineA = $timelineA->filter(function ($timeline) use ($loggedInUserId) {
            $karyawanIds = explode(',', $timeline->karyawan_id);
            return in_array($loggedInUserId, $karyawanIds) || empty($timeline->karyawan_id);
        });

        // if (!file_exists(public_path().'tasklist')) {
        // if(FacadesFile::makeDirectory(public_path().'tasklist',0777,true)){
        //   }
        // }
        $timelines = Timeline::whereNotNull('approved_by')->where('deletestatus', 0)->get();
        $project = Project::whereIn('id', $timelines->pluck('project_id'))->where('deleted_status', 0)->get();
        return view('module.transaction.tasklist.show', compact('tasklist', 'project', 'filteredTimelineA'))->render();
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'project_id' => 'required|integer',
            'timelineA_id' => 'integer',
            'progress' => 'required|integer|between:1,100',
            'description' => 'required',
            'image.*' => 'image|mimes:jpeg,jpg,png,svg|max:5012',
        ]);

        try {
            $tasklist = TaskList::findOrFail($request->id);
            $tasklist['updated_by'] = auth()->user()->name;
            $tasklist['updated_at'] = now();
            $tasklist['timelineA_id'] = $request->timelineA_id == null ? 0 : $request->timelineA_id;
            $tasklist->update($request->except('image'));

            // Tambahkan gambar tambahan jika ada yang diunggah
            if ($request->hasFile('image')) {
                $imagePaths = !empty($tasklist->image) ? explode(',', $tasklist->image) : [];

                foreach ($request->file('image') as $imageFile) {
                    $filename = date('y-m-d') . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalName();
                    $path = $filename;
                    $imageFile->move(public_path('tasklist'), $path);
                    $imagePaths[] = $path;
                }
                // if (!file_exists(public_path().'tasklist')) {
                // if(FacadesFile::makeDirectory(public_path().'tasklist',0777,true)){
                //   }
                // }

                // Gabungkan gambar yang sudah ada dengan yang baru diunggah
                $tasklist->image = implode(',', $imagePaths);
                $tasklist->save();
            }

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ],
                'data' => $tasklist,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Err',
                    'code' => 500,
                ],
                'data' => null,
                'err_detail' => $th,
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $tasklist = TaskList::findOrFail($id);
            $data = [];

            $tasklist['deleted_by'] = auth()->user()->name;
            $tasklist['deleted_at'] = now();
            $tasklist['deleted_status'] = 1;
            $tasklist->update($data);

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
            $tasklist = TaskList::findOrFail($id);

            $data = [];

            $tasklist['updated_by'] = auth()->user()->name;
            $tasklist['updated_at'] = now();
            $tasklist['deleted_status'] = 0;
            $tasklist->update($data);

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

    public function approve(string $id)
    {
        try {
            $tasklist = TaskList::findOrFail($id);

            $timelineA = TimelineA::select('t_timelineA.*')
                ->where('t_timelineA.id', $tasklist->timelineA_id)
                ->where('t_timelineA.closed', 0)
                ->get();

            $approved = true; //asumsi semua karyawan telah diapprove

            foreach ($timelineA as $timeline) {
                $karyawanIds = explode(',', $timeline->karyawan_id);

                // Periksa setiap karyawan_id apakah sudah diapprove di t_tasklist
                foreach ($karyawanIds as $karyawanId) {
                    $matchingTasklist = TaskList::where('id', $karyawanId)
                        ->where('approve', 0)
                        ->first();

                    // if ($matchingTasklist) {
                    //     $approved = false;
                    //     break;
                    // }
                }
            }

            if ($approved) {
                TimelineA::where('id', $tasklist->timelineA_id)
                    ->where('closed', 0)
                    ->update(['closed' => 1]);

                $data = [];

                $tasklist['approved_by'] = auth()->user()->name;
                $tasklist['approved_at'] = now();
                $tasklist['deleted_status'] = 0;
                $tasklist['approve'] = 1;
                $tasklist->update($data);

                return response()->json([
                    'status' => [
                        'msg' => 'OK',
                        'code' => JsonResponse::HTTP_OK,
                    ],
                    'data' => null,
                ], JsonResponse::HTTP_OK);
            } else {
                return response()->json([
                    'status' => [
                        'msg' => 'Gagal approve tasklist. Ada karyawan yang belum diapprove dalam t_tasklist.',
                        'code' => JsonResponse::HTTP_BAD_REQUEST,
                    ],
                    'data' => null,
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
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

    public function reject(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Update Tasklist status
            $tasklist = TaskList::where('id', $id);
            $data['approve'] = 2;
            $tasklist->update($data);

            // Insert into history tasklist approval
            $historyApprovalTasklist = HistoryApprovalTasklist::create([
                'tasklist_id' => $id,
                'status' => 0,
                'notes' => $request->reason,
                'created_by' => Auth::user()->name,
            ]);

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

    public function requestApproval(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Update Tasklist status
            $tasklist = TaskList::where('id', $id);
            $data['approve'] = 0;
            $tasklist->update($data);

            // Insert into history tasklist approval
            $historyApprovalTasklist = HistoryApprovalTasklist::create([
                'tasklist_id' => $id,
                'status' => 0,
                'notes' => $request->notes,
                'created_by' => Auth::user()->name,
            ]);

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

    public function showHistoryApproval($id){
        $history = HistoryApprovalTasklist::where('tasklist_id', $id)->with('employeeCreated')->get();

        $blade = view('module.transaction.tasklist.historyApproval', compact('history'))->render();

        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => JsonResponse::HTTP_OK,
            ],
            'data' => $history,
            'blade' => $blade
        ], JsonResponse::HTTP_OK);
    }

    public function getTimelineAByProject($Projectid)
    {
        // Ambil proyek berdasarkan ID
        $project = Project::find($Projectid);

        // Jika proyek ditemukan, ambil timeline yang sesuai
        if ($project) {
            $timelines = Timeline::select('t_timeline.*', 'm_project.id as project_id', 't_timelineA.detail', 't_timelineA.id', 't_timelineA.karyawan_id', 't_timelineA.startdate', 't_timelineA.enddate', 't_timelineA.is_document')
                ->leftJoin('m_project', 't_timeline.project_id', '=', 'm_project.id')
                ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
                ->where('t_timeline.project_id', $Projectid)
                ->where('t_timelineA.closed', 0)
                ->get(); // Ambil semua data sekaligus

            $matchingTimelines = []; // Inisialisasi array untuk menyimpan hasil pencocokan

            foreach ($timelines as $timeline) {
                $karyawan_id_array = explode(',', $timeline->karyawan_id);

                // Cek apakah auth()->user()->id cocok dengan setiap $karyawan_id dalam array
                if (in_array(auth()->user()->id, $karyawan_id_array)) {
                    $timeline->enddate = Carbon::parse($timeline->enddate)->format('d/m/Y');
                    $timeline->startdate = Carbon::parse($timeline->startdate)->format('d/m/Y');
                    $matchingTimelines[] = $timeline; // Tambahkan timeline yang cocok ke dalam array
                }
            }

            // Jika ada timeline yang sesuai, kembalikan respons JSON
            if (!empty($matchingTimelines)) {
                return response()->json($matchingTimelines);
            }
        }
        // Jika tidak ada proyek atau timeline yang sesuai, kembalikan respons kosong atau pesan kesalahan sesuai kebutuhan Anda.
        return response()->json([]);
    }

    public function getAllTimelineAByProject($Projectid)
    {
         // Ambil proyek berdasarkan ID
         $project = Project::find($Projectid);

         // Jika proyek ditemukan, ambil timeline yang sesuai
         if ($project) {
             $timelines = Timeline::select('t_timeline.*', 'm_project.id as project_id', 't_timelineA.detail', 't_timelineA.id', 't_timelineA.karyawan_id', 't_timelineA.startdate', 't_timelineA.enddate', 't_timelineA.is_document')
                 ->leftJoin('m_project', 't_timeline.project_id', '=', 'm_project.id')
                 ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
                 ->where('t_timeline.project_id', $Projectid)
                 ->get(); // Ambil semua data sekaligus

             $matchingTimelines = []; // Inisialisasi array untuk menyimpan hasil pencocokan

             foreach ($timelines as $timeline) {
                 $karyawan_id_array = explode(',', $timeline->karyawan_id);

                 // Cek apakah auth()->user()->id cocok dengan setiap $karyawan_id dalam array
                 if (in_array(auth()->user()->id, $karyawan_id_array)) {
                     $timeline->enddate = Carbon::parse($timeline->enddate)->format('d/m/Y');
                     $timeline->startdate = Carbon::parse($timeline->startdate)->format('d/m/Y');
                     $matchingTimelines[] = $timeline; // Tambahkan timeline yang cocok ke dalam array
                 }
             }

             // Jika ada timeline yang sesuai, kembalikan respons JSON
             if (!empty($matchingTimelines)) {
                 return response()->json($matchingTimelines);
             }
         }
         // Jika tidak ada proyek atau timeline yang sesuai, kembalikan respons kosong atau pesan kesalahan sesuai kebutuhan Anda.
         return response()->json([]);
    }
}
