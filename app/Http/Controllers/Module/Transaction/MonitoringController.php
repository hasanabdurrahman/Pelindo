<?php

namespace App\Http\Controllers\Module\Transaction;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Employee;
use App\Models\MasterData\Project;
use App\Models\Setting\Roles;
use App\Models\Transaction\Timeline;
use App\Models\Transaction\TimelineA;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;
use Psy\Command\WhereamiCommand;
use Yajra\DataTables\DataTables;

class MonitoringController extends Controller
{
    public function index(Request $request)
{
    $rolesA = getPermission(Route::currentRouteName());

    // Dapatkan ID pengguna yang login
    $loggedInUserId = auth()->user()->id;
    $matchingTimelines = [];

    // Saring data timelines berdasarkan ID pengguna yang login dan karyawan_id yang sesuai
    $timelines = Timeline::whereNotNull('approved_by')
        ->where('deletestatus', 0)
        ->with(['timeline_detail'])
        ->get();
        // ->filter(function ($timelines) use ($loggedInUserId) {
        //     $karyawanIds = explode(',', $timelines->timeline_detail->karyawan_id);
        //     return in_array($loggedInUserId, $karyawanIds);
        // });
       

    // Dapatkan ID proyek yang sesuai dengan timelines
    $projectIds = $timelines->pluck('project_id')->toArray();

    // Dapatkan proyek-proyek yang sesuai dengan ID proyek di atas
    $projects = Project::whereIn('id', $projectIds)
        ->oldest()
        ->where('deleted_status', 0)
        ->whereDate('enddate', '>=', Carbon::now())
        ->paginate(10);

    if (count($request->query()) > 0) {
        return view('module.transaction.monitoring.projectList', compact('projects'))->render();
    }

    // dd($timelines);
    return view('module.transaction.monitoring.index', compact('rolesA', 'projects'));
}



    public function renderMonitoring($id)
{
    try {
        $id = base64_decode($id);
        $data['timeline'] = Timeline::where('project_id', $id)->first();
        $project = Project::find($id);
        if ($data['timeline'] != null) {
            $data['timeline']->with('timeline_detail');
        }

        if ($data['timeline'] != null) {
            $data['phase_parent'] = DB::table('t_timelineA')
                ->select('fase')
                ->where('transactionnumber', $data['timeline']->transactionnumber)
                ->where('closed', 0)
                ->groupBy('fase')
                ->get();

            $data['phase'] = [];
            $fillColor = ['#008FFB', '#00E396', '#775DD0', '#FEB019', '#FF4560'];
            foreach ($data['phase_parent'] as $val) {
                $rawPhase = DB::table('t_timelineA')
                    ->where('transactionnumber', $data['timeline']->transactionnumber)
                    ->where('fase', $val->fase)
                    ->get();

                $data['phase'][$val->fase] = [];
                foreach ($rawPhase as $phase) {
                    $arrEmp = explode(',', $phase->karyawan_id);
                    $emp = '';
                    for ($i = 0; $i < count($arrEmp); $i++) {
                        $employee = DB::table('m_employee')->select('name')->where('id', $arrEmp[$i])->first();
                        $emp .= $employee->name . ', ';
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
        $blade = view('module.transaction.monitoring.projectMonitoring', compact('data'))->render();

        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => JsonResponse::HTTP_OK,
            ],
            'blade' => $blade,
            'project' => $project,
            'data' => $data,
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


public function datatable(Request $request)
{
    $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
    $permission = getPermission($routePrevious);
    $loggedInUserName = Auth::user()->name;

    // Ambil data karyawan yang terkait dengan proyek pada TimelineA
    $query = DB::table('t_timelineA')
        ->where('transactionnumber', $request->tn_number)
        ->join('m_employee', function ($join) use ($loggedInUserName) {
            $join->on('t_timelineA.karyawan_id', 'LIKE', DB::raw("CONCAT('%', m_employee.id, '%')"))
            ->where('m_employee.name', $loggedInUserName);
        })
        ->join('m_roles', 'm_employee.roles_id', '=', 'm_roles.id') 
        ->select('m_employee.*', 't_timelineA.detail as employee_detail','t_timelineA.fase as employee_fase', 'm_roles.name as role_name')
        ->orderBy('m_employee.id', 'asc')
        ->distinct()
        ->get();
        if ($query->isEmpty()) {
            return response()->json([
                'message' => 'Anda tidak tergabung dalam tim.'
            ], 200);
        }

    return DataTables::of($query)
        ->editColumn('karyawan', function ($query) {
            return "<span class='badge bg-light-info'>$query->name</span>";
        })
        ->editColumn('nik', function ($query) {
            return "<span class='badge bg-light-info'>$query->code</span>";
        })
        ->editColumn('role', function ($query) {
            return "<span class='badge bg-light-info'>$query->role_name</span>";
        })
        ->editColumn('start', function ($query) {
            $formattedDate = date('Y-F-d', strtotime($query->created_at));
            return "<span class='badge bg-light-info'>$formattedDate</span>";
        })
        ->editColumn('contract', function ($query) {
            return "<span class='badge bg-light-info'>$query->active</span>";
        })
        ->editColumn('fase', function ($query) {
            return "<span class='badge bg-light-info'>$query->employee_fase</span>";
        })
        ->editColumn('detail', function ($query) {
            return "<span class='badge bg-light-info'>$query->employee_detail</span>";
        })
        ->addIndexColumn()
        ->rawColumns(['karyawan', 'nik', 'role', 'start', 'contract','fase', 'detail'])
        ->make(true);
}
public function datatableall(Request $request)
{
    $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
    $permission = getPermission($routePrevious);

    // Ambil data karyawan yang terkait dengan proyek pada TimelineA
    $query = DB::table('t_timelineA')
    ->where('transactionnumber', $request->tn_number)
    ->join('m_employee', function ($join) {
        $join->on('t_timelineA.karyawan_id', 'LIKE', DB::raw("CONCAT('%', m_employee.id, '%')"));
    })
    ->join('m_roles', 'm_employee.roles_id', '=', 'm_roles.id') 
    ->select('m_employee.*', 't_timelineA.detail as employee_detail', 't_timelineA.fase as employee_fase', 'm_roles.name as role_name', 't_timelineA.closed as status')
    ->orderBy('m_employee.id', 'asc')
    ->distinct()
    ->get();


    return DataTables::of($query)
        ->editColumn('karyawan', function ($query) {
            return "<span class='badge bg-light-info'>$query->name</span>";
        })
        ->editColumn('nik', function ($query) {
            return "<span class='badge bg-light-info'>$query->code</span>";
        })
        ->editColumn('role', function ($query) {
            return "<span class='badge bg-light-info'>$query->role_name</span>";
        })
        ->editColumn('start', function ($query) {
            $formattedDate = date('Y-F-d', strtotime($query->created_at));
            return "<span class='badge bg-light-info'>$formattedDate</span>";
        })
        ->editColumn('contract', function ($query) {
            return "<span class='badge bg-light-info'>$query->active</span>";
        })
        ->editColumn('fase', function ($query) {
            return "<span class='badge bg-light-info'>$query->employee_fase</span>";
        })
        ->editColumn('detail', function ($query) {
            return "<span class='badge bg-light-info'>$query->employee_detail</span>";
        })
        ->editColumn('status', function ($query) {
            if ($query->status == '1') {
                return "<span class='badge bg-light-success'>Approved</span>";
            } else if($query->status == '0'){
                return "<span class='badge bg-light-danger'>Pending</span>";
            }
        })
        ->addIndexColumn()
        ->rawColumns(['karyawan', 'nik', 'role', 'start', 'contract','fase', 'detail','status'])
        ->make(true);
}

public function calculateProgress($id)
 {
    try {
        $id = base64_decode($id);
        $data['timeline'] = Timeline::where('project_id', $id)->first();
        $project = Project::find($id);
        
        if ($data['timeline'] != null) {
            $bobotValues = DB::table('t_timelineA')
                ->select('bobot')
                ->where('transactionnumber', $data['timeline']->transactionnumber)
                ->where('closed', 1)
                ->get();

            $totalBobot = 0;
            foreach ($bobotValues as $bobot) {
                $totalBobot += $bobot->bobot;
            }
            
            // Mengembalikan data total bobot dalam format JSON
            return response()->json([
                'totalBobot' => $totalBobot,
            ]);
        } else {
            return response()->json([
                'totalBobot' => 0,
            ]);
        }
    } catch (\Throwable $th) {
        return response()->json([
            'totalBobot' => 0,
            'error' => $th->getMessage(),
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
// monitoringcontroller.php
public function chart($id)
{
    try {
        $id = base64_decode($id);
        $data['timeline'] = Timeline::where('project_id', $id)->first();
        $project = Project::find($id);

        // Ambil tanggal startdate dan enddate dari tabel Project
        $startDate = Carbon::parse($project->startdate);
        $endDate = Carbon::parse($project->enddate);
        if ($data['timeline'] != null) {

            // Query untuk mengambil data bobot dan approvedat
            $bobotValues = DB::table('t_timelineA')
                ->join('t_tasklist', 't_timelineA.id', '=', 't_tasklist.timelineA_id')
                ->select('t_timelineA.bobot', 't_tasklist.approved_at', 't_tasklist.approve', 't_timelineA.detail')
                ->where('t_timelineA.closed', 1)
                ->where('t_timelineA.transactionnumber', $data['timeline']->transactionnumber)
                ->whereBetween('t_tasklist.approved_at', [$startDate, $endDate])
                ->where('t_tasklist.approve', 1)
                ->orderBy('t_tasklist.approved_at')
                ->get();

            // Inisialisasi array data progress dan date range dengan nilai 0
            $progressData = [];
            $dateRange = [];
            $totalProgress = 0;

            // Buat rentang tanggal dari startDate hingga endDate
            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                $dateRange[] = $currentDate->format('Y-m-d');
                $progressData[] = 0;
                $currentDate->addDay();
            }

            // Isi progressData dengan bobot yang sesuai
            $previousApprovedDate = null; // Menyimpan tanggal persetujuan sebelumnya
            $currentProgress = 0; // Menyimpan akumulasi bobot per tanggal

            foreach ($bobotValues as $bobot) {
                $approvedAt = Carbon::parse($bobot->approved_at)->format('Y-m-d');
                $index = array_search($approvedAt, $dateRange);

                if ($index !== false) {
                    if ($previousApprovedDate === $approvedAt) {
                        // Jika tanggal sama, tambahkan bobot ke akumulasi saat ini
                        $currentProgress += $bobot->bobot;
                    } else {
                        // Jika tanggal berbeda, inisialisasi akumulasi baru
                        $previousApprovedDate = $approvedAt;
                        $currentProgress += $bobot->bobot;
                    }

                    // Update progressData dengan akumulasi saat ini
                    $progressData[$index] = $currentProgress;
                }
            }
            // dd($currentProgress,$previousApprovedDate,$progressData);
            // Mengembalikan data grafik dalam format JSON
            return response()->json([
                'progressData' => $progressData,
                'dateRange' => $dateRange,
            ]);
        } else {
            return response()->json([
                'progressData' => [],
                'dateRange' => [],
                'detail' => [],
            ]);
        }
    } catch (\Throwable $th) {
        return response()->json([
            'progressData' => [],
            'dateRange' => [],
            'detail' => [],
            'error' => $th->getMessage(),
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}









}