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
        $out_date = 0;
        $solved = 0;
        $progres = 0;
        $matchingTimelines = [];
        $endDateOneYearAgo = now()->subYear();
        $project_all = [];

        $query = DB::table('Project_Report')
            ->select('*')
            ->whereDate('End_Date', '>', $endDateOneYearAgo) // Menambahkan klausul WHERE untuk tanggal berakhir kurang dari satu tahun yang lalu
            ->get();

        $allowedRoles = ['kdv', 'kdp', 'sa', 'PM'];



        foreach ($query as $project) {
            $project_id = $project->Id_Project;
            $completePercentage = 0;


            // Menggunakan Eloquent untuk mengambil timeline yang sesuai dengan proyek
            $timelines = Timeline::select('t_timeline.*', 'm_project.id as project_id', 'm_project.xtype as type', 't_timelineA.fase', 't_timelineA.id', 't_timelineA.karyawan_id', 't_timelineA.closed', 't_timelineA.enddate', 't_timelineA.bobot')
                ->leftJoin('m_project', 't_timeline.project_id', '=', 'm_project.id')
                ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
                ->where('t_timeline.project_id', $project_id)
                ->get();


            $project_following = false;

            foreach ($timelines as $timeline) {
                $karyawan_id_array = explode(',', $timeline->karyawan_id);

                if (in_array(Auth::user()->roles->code, $allowedRoles)) {
                    $matchingTimelines[] = $timeline;
                } else {
                    // Periksa apakah user saat ini terkait dengan proyek yang sedang diproses
                    if (in_array(auth()->user()->id, $karyawan_id_array) || $project_following == true) { // Inisialisasi array $project_following) {
                        $project_following = true;
                        $matchingTimelines[] = $timeline;
                    }
                }
            }


            if ($project_following) {
                foreach ($timelines as $timeline) {
                    // Cek status "closed" dan tanggal "enddate" untuk setiap timeline
                    if ($timeline->closed == 1 && $timeline->project_id == $project_id) {
                        $completePercentage =  $timeline->bobot + $completePercentage;
                    }
                }
            } else {
                foreach ($matchingTimelines as $timeline) {
                    // Cek status "closed" dan tanggal "enddate" untuk setiap timeline
                    if ($timeline->closed == 1 && $timeline->project_id == $project_id) {
                        $completePercentage =  $timeline->bobot + $completePercentage;
                    }
                }
            }


            $project->progress = $completePercentage;
            $project->type = $timelines->isEmpty() ? null : $timelines[0]->type;

            foreach ($timelines as $timeline) {
                $karyawan_id_array = explode(',', $timeline->karyawan_id);

                if (in_array(Auth::user()->roles->code, $allowedRoles)) {
                    $project_all[] = $project;
                    break;
                } else {
                    if (in_array(auth()->user()->id, $karyawan_id_array)) {
                        $project_all[] = $project;
                        break;
                    }
                }
            }
        }

        foreach ($project_all as $key) {
            if ($key->Project_Status == "ON PROGRESS" || $key->Project_Status == "UP COMMING" || $key->Project_Status == "LATE") {
                $progres++;
            } else if ($key->Project_Status == "DONE") {
                $solved++;
            }

            if ($key->Project_Status == "LATE" || $key->progress == 100) {
                $out_date++;
            }
        }

        $data = [
            'project' => $project_all,
            'out_date' => $out_date,
            'solved' => $solved,
            'progres' => $progres,
            'all_project' => $progres + $solved,
        ];



        return $data;

        // // Mengambil semua proyek
        // $count = 0;
        // $out_date = 0;
        // $solved = 0;
        // $matchingTimelines = [];
        // $endDateOneYearAgo = now()->subYear();

        // $projects = Project::select('m_project.*')
        //     ->selectRaw('m_client.name AS client_name')
        //     ->selectRaw('pc_employee.name AS pc_name')
        //     ->selectRaw('sales_employee.name AS sales_name')
        //     ->join('m_client', 'm_client.id', '=', 'm_project.id_client')
        //     ->leftJoin('m_employee AS pc_employee', 'pc_employee.id', '=', 'm_project.pc_id')
        //     ->leftJoin('m_employee AS sales_employee', 'sales_employee.id', '=', 'm_project.sales_id')
        //     ->whereDate('m_project.enddate', '>', $endDateOneYearAgo) // Menambahkan klausul WHERE untuk tanggal berakhir kurang dari satu tahun yang lalu
        //     ->get();

        // $project_all = [];

        // foreach ($projects as $project) {
        //     $project_id = $project->id;

        //     // Menggunakan Eloquent untuk mengambil timeline yang sesuai dengan proyek
        //     $timelines = Timeline::select('t_timeline.*', 'm_project.id as project_id', 't_timelineA.fase', 't_timelineA.id', 't_timelineA.karyawan_id', 't_timelineA.closed', 't_timelineA.enddate', 't_timelineA.bobot')
        //         ->leftJoin('m_project', 't_timeline.project_id', '=', 'm_project.id')
        //         ->leftJoin('t_timelineA', 't_timeline.transactionnumber', '=', 't_timelineA.transactionnumber')
        //         ->where('t_timeline.project_id', $project_id)
        //         ->get();




        //     foreach ($timelines as $timeline) {
        //         $karyawan_id_array = explode(',', $timeline->karyawan_id);

        //         $allowedRoles = ['kdv', 'kdv', 'sa'];
        //         if (!in_array(Auth::user()->roles->code, $allowedRoles)) {
        //             $matchingTimelines[] = $timeline;
        //         } else {
        //             if (in_array(auth()->user()->id, $karyawan_id_array)) {
        //                 $matchingTimelines[] = $timeline;
        //             }
        //         }
        //     }

        //     $progres = 0;



        //     foreach ($timelines as $timeline) {
        //         $karyawan_id_array = explode(',', $timeline->karyawan_id);
        //         // Cek apakah pengguna saat ini cocok dengan karyawan dalam timeline

        //         $allowedRoles = ['kdv', 'kdv', 'sa'];
        //         if (!in_array(Auth::user()->roles->code, $allowedRoles)) {


        //             $completePercentage = 0;
        //             $count = 0;

        //             foreach ($matchingTimelines as $timeline) {
        //                 // Cek status "closed" dan tanggal "enddate" untuk setiap timeline

        //                 if ($timeline->closed == 1) {
        //                     $completePercentage =  $timeline->bobot + $completePercentage;
        //                 }

        //                 if (strtotime($timeline->enddate) < strtotime(now()) && $timeline->closed == 0) {
        //                     $count++;
        //                 }
        //             }


        //             // Menghitung proyek yang melewati tanggal akhir atau yang sudah selesai
        //             if ($count >= 1) {
        //                 $out_date++;
        //             }

        //             if ($completePercentage == 100) {
        //                 $solved++;
        //             }

        //             if ($completePercentage < 100) {
        //                 $progres++;
        //             }

        //             // Menyimpan hasil dalam array
        //             $project_all[] = [
        //                 'project' => $project,
        //                 'progress' => $completePercentage,

        //             ];

        //             break;
        //         } else {
        //             if (in_array(auth()->user()->id, $karyawan_id_array)) {
        //                 $completePercentage = 0;
        //                 $count = 0;

        //                 foreach ($matchingTimelines as $timeline) {
        //                     // Cek status "closed" dan tanggal "enddate" untuk setiap timeline

        //                     if ($timeline->closed == 1) {
        //                         $completePercentage =  $timeline->bobot + $completePercentage;
        //                     }

        //                     if (strtotime($timeline->enddate) < strtotime(now()) && $timeline->closed == 0) {
        //                         $count++;
        //                     }
        //                 }



        //                 // Menghitung proyek yang melewati tanggal akhir atau yang sudah selesai
        //                 if ($count >= 1) {
        //                     $out_date++;
        //                 }

        //                 if ($completePercentage == 100) {
        //                     $solved++;
        //                 }

        //                 if ($completePercentage < 100) {
        //                     $progres++;
        //                 }

        //                 // Menyimpan hasil dalam array
        //                 $project_all[] = [
        //                     'project' => $project,
        //                     'progress' => $completePercentage,
        //                 ];

        //                 break;
        //             }
        //         }
        //     }
        // }
        // $project = [
        //     'data' => $project_all,
        //     'solved' => $solved,
        //     'progres' => $progres,
        //     'out_date' => $out_date,
        // ];

        // return $project;
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

        $allowedRoles = ['kdv', 'kdp', 'sa', 'PM'];
        if (in_array(Auth::user()->roles->code, $allowedRoles)) {
            $result = DB::table('Timeline_Report')
                ->selectRaw('COUNT(DISTINCT Project_Name) as project_count, Karyawan, Jabatan_Code ,  Employe_Id')
                ->groupBy('Karyawan', 'Jabatan_Code', 'Employe_Id')
                ->get();
        } else {
            $query = DB::table('Timeline_Report')
                ->selectRaw('COUNT(DISTINCT Project_Name) as project_count, Karyawan, Jabatan_Code ,  Employe_Id')
                ->where('Employe_Id', Auth::user()->id)
                ->groupBy('Karyawan', 'Jabatan_Code', 'Employe_Id')
                ->first();

            $result = DB::table('Timeline_Report')
                ->selectRaw('COUNT(DISTINCT Project_Name) as project_count, Karyawan, Jabatan_Code ,  Employe_Id')
                ->where('PC', $query->Karyawan)
                ->groupBy('Karyawan', 'Jabatan_Code', 'Employe_Id')
                ->get();
        }


        return response()->json($result);
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
