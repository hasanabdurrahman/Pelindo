<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Notifikasi;
use App\Models\MasterData\Project;
use App\Models\MasterData\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction\Timeline;
use App\Models\Transaction\TimelineA;
use App\Models\MasterData\Employee;
use App\Models\Setting\Roles;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    private function getAllTimelinActive()
    {

        $timelines = DB::table('t_timeline')
            ->select(
                't_timeline.*',
                'm_project.id as project_id',
                't_timelineA.fase',
                't_timelineA.id',
                't_timelineA.enddate',
                't_timelineA.karyawan_id',
                'm_project.name as project_name',
                't_timelineA.detail',
                'm_client.name as client_name'
            )
            ->leftJoin('m_project', 't_timeline.project_id', '=', 'm_project.id')
            ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
            ->join('m_client', 'm_client.id', '=', 'm_project.id_client')
            ->where('t_timelineA.closed', 0)
            ->get();



        $matchingTimelines = []; // Inisialisasi array untuk menyimpan hasil pencocokan

        foreach ($timelines as $timeline) {
            $karyawan_id_array = explode(',', $timeline->karyawan_id);

            // Cek apakah auth()->user()->id cocok dengan setiap $karyawan_id dalam array
            $allowedRoles = ['kdv', 'kdp', 'sa', 'PM'];

            if (in_array(Auth::user()->roles->code, $allowedRoles)) {
                $matchingTimelines[] = $timeline;
            } else {
                if (in_array(auth()->user()->id, $karyawan_id_array)) {
                    $matchingTimelines[] = $timeline; // Tambahkan timeline yang cocok ke dalam array
                }
            }
        }

        return $matchingTimelines;
    }

    private function getAllTimelinOutSchedule()
    {
        $timelines = DB::table('t_timeline')
            ->select(
                't_timeline.*',
                'm_project.id as project_id',
                't_timelineA.fase',
                't_timelineA.id',
                't_timelineA.karyawan_id',
                'm_project.name as project_name',
                't_timelineA.detail',
                't_timelineA.startdate',
                't_timelineA.enddate',
                'm_client.name as client_name'
            )
            ->leftJoin('m_project', 't_timeline.project_id', '=', 'm_project.id')
            ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
            ->join('m_client', 'm_client.id', '=', 'm_project.id_client')
            ->where('t_timelineA.closed', 0)
            ->whereDate('t_timelineA.enddate', '<', now()->toDateString()) // Filter by startdate greater than today
            ->get();



        $matchingTimelines = []; // Inisialisasi array untuk menyimpan hasil pencocokan

        foreach ($timelines as $timeline) {
            $karyawan_id_array = explode(',', $timeline->karyawan_id);

            // Cek apakah auth()->user()->id cocok dengan setiap $karyawan_id dalam array

            // Cek apakah auth()->user()->id cocok dengan setiap $karyawan_id dalam array
            $allowedRoles = ['kdv', 'kdp', 'sa', 'PM'];

            if (in_array(Auth::user()->roles->code, $allowedRoles)) {
                $matchingTimelines[] = $timeline;
            } else {
                if (in_array(auth()->user()->id, $karyawan_id_array)) {
                    $matchingTimelines[] = $timeline; // Tambahkan timeline yang cocok ke dalam array
                }
            }
        }

        return $matchingTimelines;
    }


    /**
     * Mengambil daftar proyek beserta statistik terkait untuk pengguna terautentikasi.
     *
     * @return array
     */
    private function projectlist()
    {
        $out_date         = $solved = $progres = $closed = $all_project = 0;
        $solvedList       = $inProgressList = $out_date_list = $closedList = [];
        $allowedRoles     = ['kdv','kdp','sa','PM'];    // <-- tambah ini
        $total_tim_IT     = 0;
        $project_all      = [];

        // Hitung total tim IT
        $roles = [
            "Project Manager","Project Coordinator","Business Analyst",
            "Software Analysis","Sistem Analyst","Technical Writer",
            "Programmer","Super Admin",
        ];
        $total_tim_IT = DB::table('m_employee')
            ->join('m_roles','m_employee.roles_id','=','m_roles.id')
            ->whereIn('m_roles.name',$roles)
            ->count();

        $reports = DB::table('Project_Report')->get();

        foreach ($reports as $project) {
            // reset untuk tiap proyek
            $matchingTimelines = [];
            $completePercentage = 0;
            $project_id = $project->Id_Project;

            $timelines = Timeline::select('t_timeline.*', 'm_project.id as project_id', 'm_project.xtype as type', 't_timelineA.*')
                ->leftJoin('m_project','t_timeline.project_id','=','m_project.id')
                ->leftJoin('t_timelineA','t_timeline.transactionnumber','=','t_timelineA.transactionnumber')
                ->where('t_timeline.project_id',$project_id)
                ->get();

            // kumpulkan timelines sesuai role/user
            foreach ($timelines as $t) {
                $ids = explode(',',$t->karyawan_id);
                if (
                    in_array(Auth::user()->roles->code, $allowedRoles)
                    || in_array(Auth::user()->id, $ids)
                ) {
                    $matchingTimelines[] = $t;
                }
            }

            // hitung progress
            foreach ($matchingTimelines as $t) {
                if ($t->closed == 1 && $t->project_id == $project_id) {
                    $completePercentage += $t->bobot;
                }
            }

            $project->progress = $completePercentage;
            $project->type     = $timelines->isEmpty() ? null : $timelines[0]->type;
            $project_all[]     = $project; // simpan untuk UI

            // switch status → count + detail list
            switch ($project->Project_Status) {
                // semua DONE
                case "DONE":
                    $solved++;
                    $solvedList[] = $project;
                    break;

                
                case "UP COMMING":
                case "ON PROGRESS":
                case "LATE":
                    $progres++;
                    $inProgressList[] = $project;
                    break;

                // semua CLOSED 
                case "CLOSED":
                    $closed++;
                    $closedList[] = $project;
                    break;
            }

            // semua LATE dan (atau progress=100)
            if ($project->Project_Status == "LATE" || $project->progress == 100) {
                $out_date++;
                $out_date_list[] = $project;
            
            }
            // semua selain DONE dan (atau progress=100)
            if ($project->Project_Status != "DONE" || $project->progress == 100) {
                $all_project++;
            }
        }

        return [
            'project'                => $project_all,
            'solved'                 => $solved,
            'solved_projects'        => $solvedList,
            'inProgress'             => $progres,
            'inProgress_projects'    => $inProgressList,
            'out_date'               => $out_date,
            'out_date_list'          => $out_date_list,
            'closed'                 => $closed,
            'closed_projects'        => $closedList,
            'total_tim_IT'           => $total_tim_IT,
            'all_project'            => $all_project,
        ];
    }



    private function getPercen()
    {
        $timelines = DB::table('t_timeline')
            ->select(
                't_timeline.*',
                'm_project.id as project_id',
                't_timelineA.fase',
                't_timelineA.id',
                't_timelineA.karyawan_id',
                'm_project.name as project_name',
                't_timelineA.detail',
                'm_client.name as client_name',
                't_timelineA.closed',
                't_timelineA.enddate'
            )
            ->leftJoin('m_project', 't_timeline.project_id', '=', 'm_project.id')
            ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
            ->join('m_client', 'm_client.id', '=', 'm_project.id_client')
            ->get();



        $matchingTimelines = []; // Inisialisasi array untuk menyimpan hasil pencocokan
        $completeCount = 0;
        $inProgressCount = 0;
        $outScheduleCount = 0;

        $allowedRoles = ['kdv', 'kdp', 'sa', 'PM'];

        foreach ($timelines as $timeline) {
            $karyawan_id_array = explode(',', $timeline->karyawan_id);
            // Cek apakah auth()->user()->id cocok dengan setiap $karyawan_id dalam array
            if (in_array(Auth::user()->roles->code, $allowedRoles)) {
                $matchingTimelines[] = $timeline;
            } else {
                if (in_array(auth()->user()->id, $karyawan_id_array)) {
                    $matchingTimelines[] = $timeline; // Tambahkan timeline yang cocok ke dalam array
                }
            }
        }

        $completeCount = 0;
        $inProgressCount = 0;
        $outScheduleCount = 0;

        $currentDate = date('Y-m-d');

        foreach ($matchingTimelines as $timeline) {
            // Cek status "closed" dan tanggal "enddate" untuk setiap timeline
            if ($timeline->closed == 1) {
                $completeCount++;
            } elseif ($timeline->closed == 0 && strtotime($timeline->enddate) <= strtotime($currentDate)) {
                $outScheduleCount++;
            } elseif ($timeline->closed == 0) {
                $inProgressCount++;
            }
        }



        $totalCount = count($matchingTimelines);

        if ($totalCount != 0) {
            $completePercentage = ($completeCount / $totalCount) * 100;
            $inProgressPercentage = ($inProgressCount / $totalCount) * 100;
            $outSchedulePercentage = ($outScheduleCount / $totalCount) * 100;
        } else {
            $completePercentage = 0;
            $inProgressPercentage = 0;
            $outSchedulePercentage = 0;
        }

        $percentages = [
            'complete' => floor($completePercentage),
            'inProgress' => floor($inProgressPercentage),
            'outSchedule' => floor($outSchedulePercentage),
        ];

        return $percentages;
    }



    public function redirect(Request $request)
    {
        $redirectData = $request->session()->get('Redirect');
        if (isset($redirectData)) {
            return view('includes.redirect');
        } else {
            abort('404');
        }
    }

    // public function projectStakeholder()
    // {

    //     $query = DB::table('m_employee')
    //         ->select('m_employee.name', 'm_employee.created_at', 'm_employee.id',  'm_division.name AS division_name', 'm_roles.code AS code', 'm_roles.id AS roles_id')
    //         ->join('m_division', 'm_employee.divisi_id', '=', 'm_division.id')
    //         ->join('m_roles', 'm_employee.roles_id', '=', 'm_roles.id');
    //     $query->latest();

    //     $employees = $query->get();

    //     // Menggunakan Eloquent untuk mengambil timeline yang sesuai dengan proyek
    //     $timelines = Timeline::select('m_project.id as project_id', 't_timelineA.id', 't_timelineA.karyawan_id')
    //         ->leftJoin('m_project', 't_timeline.project_id', '=', 'm_project.id')
    //         ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
    //         ->distinct()
    //         ->get();



    //     $matchingTimelines = [];
    //     $allowedRoles = ['kdv', 'kdp', 'sa'];
    //     foreach ($timelines as $timeline) {
    //         $karyawan_id_array = explode(',', $timeline->karyawan_id);
    //         // Cek apakah pengguna saat ini cocok dengan karyawan dalam timeline
    //         if (in_array(Auth::user()->roles->code, $allowedRoles)) {
    //             $matchingTimelines[] = $timeline;
    //         } else {
    //             if (in_array(auth()->user()->id, $karyawan_id_array)) {
    //                 $matchingTimelines[] = $timeline;
    //             }
    //         }
    //     }

    //     $filteredEmployees = [];


    //     // Mendapatkan jumlah proyek yang dikerjakan oleh setiap karyawan
    //     foreach ($employees as $employee) {
    //         $employee->project_count = 0; // Inisialisasi jumlah proyek

    //         foreach ($matchingTimelines as $timeline) {
    //             $karyawan_id_array = explode(',', $timeline->karyawan_id);

    //             // Cek apakah karyawan saat ini cocok dengan karyawan dalam timeline
    //             if (in_array($employee->id, $karyawan_id_array)) {
    //                 $employee->project_count++;
    //                 break;
    //             }
    //         }

    //         if ($employee->project_count > 0) {
    //             $filteredEmployees[] = $employee;
    //         }
    //     }

    //     $data = [];
    //     foreach ($employees as $item) {
    //         if ($item->project_count != 0) {
    //             $data[] = $item;
    //         }
    //     }

    //     return response()->json($data);
    // }

        public function projectStakeholder()
    {
        // 1) Roles master
        $roles = [
            "Project Manager","Project Coordinator","Business Analyst",
            "Software Analysis","Sistem Analyst","Technical Writer",
            "Programmer","Super Admin", "UI UX", "User Project", "Engineer On site",
        ];

        // 2) Ambil semua karyawan dengan salah satu roles di atas
        //    + hitung COUNT DISTINCT project per karyawan (boleh 0)
        $result = DB::table('m_employee AS e')
            ->join('m_roles AS r', 'e.roles_id', '=', 'r.id')
            ->leftJoin('Timeline_Report AS tr', 'tr.Employe_Id', '=', 'e.id')
            ->whereIn('r.name', $roles)
            ->selectRaw("
                r.code AS Jabatan_Code,
                e.name AS Karyawan,
                e.id   AS Employe_Id,
                COUNT(DISTINCT tr.Project_Name) AS project_count
            ")
            ->groupBy('e.id','r.code','e.name')
            ->get();

        // 3) Tambah tombol Resource
        foreach ($result as $row) {
            $row->action = '<button
                class="btn btn-sm btn-primary btn-resource"
                data-id="'. $row->Employe_Id .'">
                Resource
            </button>';
        }

        return response()->json($result);
    }

    /**
     * Kembalikan detail semua projek yang sedang dikerjakan karyawan tertentu.
     */
    public function projectStakeholderDetail(Request $request)
    {
        $empId = $request->input('employe_id');

        // Kita join ke view Project_Report untuk dapat kolom Project_Status
        $list = DB::table('m_project AS p')
            ->join('Project_Report AS pr', 'pr.Id_Project', '=', 'p.id')
            ->join('m_client AS c', 'c.id', '=', 'p.id_client')
            ->leftJoin('m_employee AS pc', 'pc.id', '=', 'p.pc_id')
            ->leftJoin('m_employee AS s',  's.id',  '=', 'p.sales_id')
            ->select([
                'p.code as Code_Project',
                'p.name as Project_Name',
                'p.contract_number as Contract_Numer',
                'c.name as Client',
                'p.value as Value',
                'p.startdate as Start_Date',
                'p.enddate as End_Date',
                'pc.name as PC',
                's.name  as Sales',
                'pr.Project_Status',
                // subquery progress khusus untuk karyawan ini
                DB::raw("(
                    SELECT SUM(a.bobot)
                    FROM t_timelineA a
                    JOIN t_timeline  t ON a.transactionnumber = t.transactionnumber
                    WHERE t.project_id = p.id
                    AND FIND_IN_SET($empId, a.karyawan_id)
                    AND a.closed = 1
                ) as progress")
            ])
            // hanya projek yang melibatkan karyawan ini
            ->whereExists(function($q) use ($empId) {
                $q->select(DB::raw('1'))
                ->from('t_timelineA AS a')
                ->join('t_timeline AS t', 'a.transactionnumber', '=', 't.transactionnumber')
                ->whereRaw("t.project_id = p.id AND FIND_IN_SET($empId, a.karyawan_id)");
            })
            ->get();

        return response()->json($list);
    }


    public function getTimelineWithProject(Request $request)
    {
        $id = $request->input('id');
        $timelines = DB::table('t_timeline')
            ->select(
                't_timeline.*',
                'm_project.id as project_id',
                't_timelineA.fase',
                't_timelineA.id',
                't_timelineA.bobot',
                't_timelineA.karyawan_id',
                'm_project.name as project_name',
                't_timelineA.detail',
                'm_client.name as client_name',
                DB::raw("CASE WHEN t_timelineA.closed = 0 THEN 'On Progress' ELSE 'Done' END as status"),
                't_timelineA.enddate',
                't_timelineA.startdate',
                't_terminA.name as termin_name'
            )
            ->leftJoin('m_project', 't_timeline.project_id', '=', 'm_project.id')
            ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
            ->leftJoin('t_terminA', 't_timelineA.id', '=', 't_terminA.timelineA_id')
            ->join('m_client', 'm_client.id', '=', 'm_project.id_client')
            ->where('m_project.id', $id)
            ->orderBy('t_timelineA.order')
            ->get();

        return response()->json($timelines);
    }

    public function searchEmployeeWithProject(Request $request)
    {
        $query = DB::table('m_employee')
            ->select('m_employee.name', 'm_employee.created_at', 'm_employee.id',  'm_division.name AS division_name', 'm_roles.name AS roles_name', 'm_roles.id AS roles_id')
            ->join('m_division', 'm_employee.divisi_id', '=', 'm_division.id')
            ->join('m_roles', 'm_employee.roles_id', '=', 'm_roles.id')
            ->where('m_employee.deleted_status', 0);

        // Filter berdasarkan roles (m_roles.id)
        $roles = $request->input('roles');
        if (!empty($roles)) {
            $query->where('m_roles.id', $roles);
        }

        // Pencarian berdasarkan nama karyawan ('m_employee.name')
        $searchValue = $request->input('search');
        if (!empty($searchValue)) {
            $query->where('m_employee.name', 'LIKE', '%' . $searchValue . '%');
        }

        $query->latest()->limit(10);

        $employees = $query->get();

        $timelines = DB::table('t_timelineA')
            ->select('karyawan_id')
            ->distinct()
            ->get();

        $filteredEmployees = [];

        // Mendapatkan jumlah proyek yang dikerjakan oleh setiap karyawan
        foreach ($employees as $employee) {
            $employee->project_count = 0; // Inisialisasi jumlah proyek

            foreach ($timelines as $timeline) {
                $karyawan_id_array = explode(',', $timeline->karyawan_id);

                // Cek apakah karyawan saat ini cocok dengan karyawan dalam timeline
                if (in_array($employee->id, $karyawan_id_array)) {
                    $employee->project_count++;
                    break;
                }
            }

            if ($employee->project_count > 0) {
                $filteredEmployees[] = $employee;
            }
        }

        return response()->json($employees);
    }


    public function index()
    {

        return redirect('/home');
    }

    public function dashboard()
    {
        $project_all = $this->projectlist();
        $outSchedule = $this->getAllTimelinOutSchedule();
        $timeline_active = $this->getAllTimelinActive();

        $percentages = $this->getPercen();

        $userCounts = DB::table('m_employee')
            ->selectRaw('SUM(CASE WHEN active = "Active" AND deleted_status = 0 THEN 1 ELSE 0 END) AS users_active,
                    SUM(CASE WHEN active = "Inactive" AND deleted_status = 1 THEN 1 ELSE 0 END) AS users_non_active')
            ->first();


        $roles = Roles::orderBy('deleted_status', 'ASC')
            ->orderBy('id', 'asc')
            ->get();

        // $notifikasi = Notifikasi::select('*')->get();

        return view('dashboard', compact('project_all', 'outSchedule', 'timeline_active', 'percentages', 'userCounts', 'roles'));
    }

    private function totalProject()
    {
        $data = [];
        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->subYear()->format('Y');

        for ($m = 1; $m <= 12; ++$m) {
            $month = Carbon::parse(date('Y-F-d', mktime(0, 0, 0, $m, 1, $currentYear)))->translatedFormat('Y-m-d');

            $projectList = Project::where(function ($query) use ($m) {
                $query->whereMonth('created_at', $m)
                    ->orWhereMonth('updated_at', $m);
            })->where(function ($query) use ($currentYear) {
                $query->whereYear('created_at', $currentYear)
                    ->orWhereYear('updated_at', $currentYear);
            });

            if (!in_array(Auth::user()->roles->code, ['kdp', 'kdp', 'PM'])) {
                if (in_array(Auth::user()->roles->code, ['pc'])) {
                    $projectList = $projectList->where('pc_id', Auth::user()->id);
                } else if (in_array(Auth::user()->roles->code, ['sls'])) {
                    $projectList = $projectList->where('sales_id', Auth::user()->id);
                }
            }

            $projectList = $projectList->count();

            $tempData = [
                'date' => $month,
                'value' => $projectList
            ];
            array_push($data, $tempData);
        }

        $data = json_encode($data);
        return $data;
    }

    private function projectByJenis()
    {
        $data = [];
        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->subYear()->format('Y');

        for ($m = 1; $m <= 12; ++$m) {
            $month = Carbon::parse(date('F', mktime(0, 0, 0, $m, 1, $currentYear)))->translatedFormat('F');

            $projectList = Project::where(function ($query) use ($m) {
                $query->whereMonth('created_at', $m)
                    ->orWhereMonth('updated_at', $m);
            })->where(function ($query) use ($currentYear) {
                $query->whereYear('created_at', $currentYear)
                    ->orWhereYear('updated_at', $currentYear);
            })->where('xtype', 'Project');

            $ManageService = Project::where(function ($query) use ($m) {
                $query->whereMonth('created_at', $m)
                    ->orWhereMonth('updated_at', $m);
            })->where(function ($query) use ($currentYear) {
                $query->whereYear('created_at', $currentYear)
                    ->orWhereYear('updated_at', $currentYear);
            })->where('xtype', 'Manage Service');

            if (!in_array(Auth::user()->roles->code, ['kdp', 'kdp', 'PM'])) {
                if (in_array(Auth::user()->roles->code, ['pc'])) {
                    $projectList = $projectList->where('pc_id', Auth::user()->id);
                    $ManageService = $ManageService->where('pc_id', Auth::user()->id);
                } else if (in_array(Auth::user()->roles->code, ['sls'])) {
                    $projectList = $projectList->where('sales_id', Auth::user()->id);
                    $ManageService = $ManageService->where('pc_id', Auth::user()->id);
                }
            }

            $projectList = $projectList->count();
            $ManageService = $ManageService->count();

            $tempData = [
                'category' => $month,
                'project' => $projectList,
                'manage_service' => $ManageService
            ];
            array_push($data, $tempData);
        }

        $data = json_encode($data);
        return $data;
    }

    private function projectByClient()
    {
        $data = [];
        $currentYear = Carbon::now()->subYear()->format('Y');
        $data = DB::table('m_project')->select([DB::raw("COUNT(*) as total"), "m_client.name as client_name"])
            ->leftJoin('m_client', 'm_project.id_client', '=', 'm_client.id')
            ->groupBy(['m_project.id_client'])
            ->where(function ($query) use ($currentYear) {
                $query->whereYear('m_project.created_at', $currentYear)
                    ->orWhereYear('m_project.updated_at', $currentYear);
            });

        if (!in_array(Auth::user()->roles->code, ['kdp', 'kdp', 'PM'])) {
            if (in_array(Auth::user()->roles->code, ['pc'])) {
                $data = $data->where('pc_id', Auth::user()->id);
            } else if (in_array(Auth::user()->roles->code, ['sls'])) {
                $data = $data->where('sales_id', Auth::user()->id);
            }
        }

        $data = json_encode($data->get()->toArray());

        return $data;
    }

    public function dashboardProject(Request $request)
    {
        $data['totalProject'] = self::totalProject();
        $data['projectByJenis'] = self::projectByJenis();
        $data['projectByClient'] = self::projectByClient();

        $currentYear = Carbon::now()->subYear()->format('Y');
        $data['allProject'] = Project::with('client')->with('pc')->with('sales')->where(function ($query) use ($currentYear) {
            $query->whereYear('created_at', $currentYear)
                ->orWhereYear('updated_at', $currentYear);
        });

        if (!in_array(Auth::user()->roles->code, ['kdp', 'kdp', 'PM'])) {
            if (in_array(Auth::user()->roles->code, ['pc'])) {
                $data['allProject'] = $data['allProject']->where('pc_id', Auth::user()->id);
            } else if (in_array(Auth::user()->roles->code, ['sls'])) {
                $data['allProject'] = $data['allProject']->where('sales_id', Auth::user()->id);
            }
        }

        $data['allProject'] = $data['allProject']->paginate(5);
        $data['progressProject'] = [];
        foreach ($data['allProject'] as $val) {
            $timeline = Timeline::where('project_id', $val->id)->first();

            if ($timeline != null) {
                $bobotValues = DB::table('t_timelineA')
                    ->select('bobot')
                    ->where('transactionnumber', $timeline->transactionnumber)
                    ->where('closed', 1)
                    ->get();

                $totalBobot = 0;
                foreach ($bobotValues as $bobot) {
                    $totalBobot += $bobot->bobot;
                }

                // Mengembalikan data total bobot dalam format JSON
                array_push($data['progressProject'], [
                    'id_project' => $val->id,
                    'val' => $totalBobot
                ]);
            } else {
                array_push($data['progressProject'], [
                    'id_project' => $val->id,
                    'val' => 0
                ]);
            }
        }

        if (count($request->query()) > 0) {
            return view('includes.card-project-dashboard', compact('data'))->render();
        }

        return view('dashboard-project', compact('data'));
    }
}