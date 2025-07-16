<?php

namespace App\Http\Controllers\Module\Transaction;

use App\Helpers\ExampleExport;
use App\Helpers\DataAccessHelpers;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Phase;
use App\Models\MasterData\Project;
use App\Models\MasterData\Employee;
use App\Models\MasterData\PhaseA;
use App\Models\Setting\Roles;
use App\Models\Transaction\AdditionalTimeline;
use App\Models\Transaction\AdditionalTimelineA;
use App\Models\Transaction\Timeline;
use App\Models\Transaction\TimelineA;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class TimelineController extends Controller
{
    public function index(Request $request)
    {
        $rolesA = getPermission(Route::currentRouteName());

        // $projects = Project::oldest()
        //             ->where('deleted_status', 0)
        //             ->whereHas('timelines');
                    // ->whereDate('enddate', '>=', Carbon::now());

       /** BARU */
       $pc_id = Auth::user()->id;
       $projects =  DB::table('Project_List')
                        ->select(['*']);

        $role = Roles::where('id', Auth::user()->roles_id)->first();

        if($role->code == 'kdp'){
            $projects = $projects->get();
        } else {
            $projects = $projects->where('pc_id', Auth::user()->id)->where('Project_Status', '<>', 'DONE')->get();
        }


        if(count($request->query()) > 0){
            return view('module.transaction.timeline.projectList', compact('projects'))->render();
        }

        $timeline_type = Phase::where('deleted_status', 0)->get();
        return view('module.transaction.timeline.index', compact('rolesA', 'projects', 'timeline_type'));
    }

    public function renderTimeline($id){
        try {
            $id = base64_decode($id);
            $project = Project::findOrFail($id);
            $data['timeline'] = Timeline::where('project_id', $id)->first();
            $project = Project::find($id);
            if($data['timeline'] != null){
                $data['timeline']->with('timeline_detail');
            }

            if($data['timeline'] != null){
                $data['phase_parent'] = DB::table('t_timelineA')
                                        ->select('fase', 'order')
                                        ->where('transactionnumber', $data['timeline']->transactionnumber)
                                        ->orderBy('order')
                                        ->groupBy('fase', 'order')
                                        ->get();

                $data['phase'] = [];
                $fillColor = ['#008FFB', '#00E396', '#775DD0', '#FEB019', '#FF4560'];
                // unset()
                $data['phase_parent'] = $data['phase_parent']->unique('fase');
                foreach ($data['phase_parent'] as $val) {
                    $rawPhase = DB::table('t_timelineA')
                        ->where('transactionnumber', $data['timeline']->transactionnumber)
                        ->where('fase', $val->fase)
                        ->orderBy('order')
                        ->get();

                    // array_push($data['phase'], [
                    //     'x' => str_replace(' ', '_', $val->fase),
                    //     'y' => [
                    //         '',
                    //         '',
                    //     ],
                    //     'fillColor' => '#D3D3D3'
                    // ]);

                    // $data['phase'][$val->fase] = [];
                    foreach ($rawPhase as $phase) {
                        // $arrEmp = explode(',',$phase->karyawan_id);
                        // $emp = '';
                        // for ($i=0; $i < count($arrEmp); $i++) {
                        //     $employee = DB::table('m_employee')->select('name')->where('id', $arrEmp[$i])->first();
                        //     $emp .= $employee->name.', ';
                        // }

                        array_push($data['phase'], [
                            'x' => $phase->detail,
                            'y' => [
                                Carbon::parse($phase->startdate)->subHours(9)->getTimestampMs(),
                                Carbon::parse($phase->enddate)->addHours(6)->getTimestampMs(),
                            ],
                            'fillColor' => $fillColor[array_rand($fillColor)]
                        ]);
                    }
                }
                $data['phase'] = json_encode($data['phase']);
            }

            $blade = view('module.transaction.timeline.projectTimeline', compact('data'))->render();

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'blade' => $blade,
                'project' => $project,
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

        $query = TimelineA::where('transactionnumber', $request->tn_number)
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
            ->editColumn('close_action', function($query) {
                if($query->closed != 1){
                    return '<button data-id="'.$query->id.'" class="close_action badge bg-light-success" title="Close Timeline"><span class="fa fa-check"></span></button>';
                    // return '<span class="badge bg-light-success">Closed</span>';
                } else {
                    return '';
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
            ->rawColumns(['status', 'karyawan','close_action'])
            ->make(true);
    }

    public function create($project_id){
        $project_id = base64_decode($project_id);
        $data['timeline_type'] = Phase::where('deleted_status', 0)->get();
        $data['project'] = Project::find($project_id);
        return view('module.transaction.timeline.add', compact('data'));
    }

    public function renderForm($project_id)
    {
        $project_id = base64_decode($project_id);
        $data['renderFromController'] = true;
        $data['employees'] = Employee::with('roles')->whereDoesntHave('roles', function($q){
            $q->where('code', 'sls')->where('deleted_status', 0);
        })->where('deleted_status', 0)->get();

        $data['project'] = Project::find($project_id);

        $blade = view('module.transaction.timeline.formAdd', compact('data'))->render();
        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => JsonResponse::HTTP_OK,
            ],
            'blade' => $blade
        ], JsonResponse::HTTP_OK);
    }

    public function setPhase($type, $project_id){
        try {
            $data['renderFromController'] = true;
            $data['employees'] = Employee::with('roles')->whereDoesntHave('roles', function($q){
                $q->where('code', 'sls')->where('deleted_status', 0);
            })->where('deleted_status', 0)->get();
            $data['project'] = Project::find(base64_decode($project_id));

            $phase = PhaseA::where('phase_id', $type)->orderBy('order', 'asc')->get();
            $blade = '';
            foreach ($phase as $phaseVal) {
                $blade .= view('module.transaction.timeline.formAdd', compact('data'))->render();
            }
            // $blade = view('module.transaction.timeline.formAdd', compact('data'))->render();

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

    public function store(Request $request){
        $trans_number = DataAccessHelpers::generateTransactionNumber($request->project_id);
        /**
         * Insert data to Header
         */
        DB::beginTransaction();
        try {
            $header['transactionnumber'] = $trans_number;
            $header['project_id'] = $request->project_id;
            $header['created_by'] = Auth::user()->name;
            $header['created_at'] = now();
            // $header['bobot'] = $request->bobot;
            $header['deletestatus'] = 0;

            $t_header = Timeline::create($header);

            /**
             * Start Insert Detail
             */
            try {
                for ($i=0; $i < count($request->phase) ; $i++) {
                    TimelineA::create([
                        'transactionnumber' => $trans_number,
                        'fase' => $request->phase[$i],
                        'detail' => $request->work[$i],
                        'startdate' => $request->start_date[$i],
                        'enddate' => $request->end_date[$i],
                        'bobot' => $request->bobot[$i],
                        'karyawan_id' => $request->employee[$i],
                        'order' => (int)$i+1,
                        'closed' => 0,
                        'is_document' => $request->is_document[$i]
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

    public function edit($trans_number){
        $trans_number = base64_decode($trans_number);
        $data['timeline'] = Timeline::where('transactionnumber', $trans_number)->with('timeline_detail')->first();
        $data['project'] = Project::where('id', $data['timeline']->project_id)->first();
        $data['all_projects'] = Project::oldest()
                                ->where('deleted_status', 0)
                                ->whereDate('enddate', '>=', Carbon::now())
                                ->get();

        $data['phase_parent'] = DB::table('t_timelineA')
                                ->select('fase', 'order')
                                ->where('transactionnumber', $trans_number)
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
                ->where('transactionnumber', $trans_number)
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
                    'is_document' => $phase->is_document
                ]);

                $data['totalBobot'] = $data['totalBobot'] + $phase->bobot;
            }
        }

        return view('module.transaction.timeline.edit', compact('data'));
    }

    public function update(Request $request){
        // GENERATE NEW TRANSACTION NUMBER IF PROJECT CHANGED
        if($request->new_project_id != null){
            $trans_number = DataAccessHelpers::generateTransactionNumber($request->new_project_id);
        } else {
            $trans_number = $request->default_transactionnumber;
        }

        /**
         * Insert data to Header
         */
        DB::beginTransaction();
        try {
            // Delete Timeline First
            Timeline::where('transactionnumber', $request->default_transactionnumber)->where('id', $request->timeline_id)->forceDelete();

            $header['transactionnumber'] = $trans_number;
            $header['project_id'] = $request->new_project_id == null ? $request->project_id : $request->new_project_id;
            $header['created_by'] = Auth::user()->name;
            $header['created_at'] = now();
            $header['deletestatus'] = 0;

            $t_header = Timeline::create($header);
            /**
             * Start Insert Detail
             */
            try {
                // Delete Timeline_A first
                TimelineA::where('transactionnumber', $request->default_transactionnumber)->forceDelete();

                for ($i=0; $i < count($request->phase) ; $i++) {
                    TimelineA::create([
                        'transactionnumber' => $trans_number,
                        'fase' => $request->phase[$i],
                        'detail' => $request->work[$i],
                        'startdate' => $request->start_date[$i],
                        'enddate' => $request->end_date[$i],
                        'bobot' => $request->bobot[$i],
                        'karyawan_id' => $request->employee[$i] == null ? $request->default_karyawan_id[$i] : $request->employee[$i],
                        'order' => (int)$i+1,
                        'closed' => 0,
                        'is_document' => $request->is_document[$i]
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

    public function close_action($id){
        DB::beginTransaction();
        try {
            /**  update close timeline **/ 
            $timeline_d = TimelineA::find($id); 
            if (!$timeline_d) {
                throw new \Exception('Timeline not found');
            }
            $timeline_d->update([
                'closed' => 1,
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
                    'msg' => 'Gagal close timeline, harap coba lagi',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function approve($id){
        $id = base64_decode($id);
        /**
         * Copy Data to Additional Timeline
         */
        DB::beginTransaction();
        try {
            /** Header */
            $timeline_h = Timeline::where('id', $id);
            $adTransNumber = DataAccessHelpers::generateAdTransNumber($timeline_h->first()->project_id, $timeline_h->first()->transactionnumber);

            $timeline_h->each(function($old_header) use ($adTransNumber) {
                $newHeader = $old_header->replicate();
                $newHeader = $newHeader->toArray();
                $newHeader['created_at'] = now();
                $newHeader['created_by'] = Auth::user()->name;
                $newHeader['ad_number'] = $adTransNumber;
                $newHeader['used'] = 1;

                $additionalTimeline = new AdditionalTimeline();
                $additionalTimeline->create($newHeader);
            });

            /** Detail */
            $timeline_d = TimelineA::where('transactionnumber', $timeline_h->first()->transactionnumber);
            $timeline_d->each(function($old_detail) use ($adTransNumber){
                $newDetail = $old_detail->replicate();
                $newDetail = $newDetail->toArray();
                $newDetail['timelineA_id'] = $old_detail->id;
                $newDetail['ad_number'] = $adTransNumber;

                $additionalTimeline_d = new AdditionalTimelineA();
                $additionalTimeline_d->create($newDetail);
            });

            // update approved_at & approved_by main timeline
            $timeline_h->update([
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
                    'msg' => 'Gagal Approve timeline, harap coba lagi',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function exportFile($type, $id)
    {
        // INITIATE SPREADSHEET
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        $id = base64_decode($id);
        $timeline = Timeline::where('transactionnumber', $id)->with('project')->first();

        $start    = (new DateTime($timeline->project->startdate))->modify('first day of this month');
        $end      = (new DateTime($timeline->project->enddate))->modify('first day of next month');
        $diff = $end->diff($start);
        $numberOfWeeks = (int)ceil($diff->days / 7);

        /** SET HEADER  */
        $weekColumn = 'C';
        for ($i=1; $i <= $numberOfWeeks; $i++) {
            // set month value
            $activeWorksheet->setCellValue($weekColumn.'5', "W$i");
            $cellsRange = $weekColumn.'5:'.$weekColumn.'6';
            $activeWorksheet->mergeCells($cellsRange);

            /** SET STYLING HEADER (WEEKS) */
            //Background Color
            $activeWorksheet
            ->getStyle($weekColumn.'5')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('e4d00a');

            // Allignment
            $activeWorksheet
            ->getStyle($weekColumn.'5')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Font Size
            $activeWorksheet
            ->getStyle($weekColumn.'5')
            ->getFont()
            ->setSize('14');

            // Row Height
            $activeWorksheet->getRowDimension('5')->setRowHeight(30);

            $weekColumn++;
        }

        //Generate Array Phase & Timeline
        $data['phase_parent'] =
            DB::table('t_timelineA')
                ->select('fase')
                ->where('transactionnumber', $id)
                ->orderBy('order')
                ->groupBy('fase','order')
                ->get();

        $data['phase_parent'] = $data['phase_parent']->unique('fase');
        $data['phase'] = [];
        foreach ($data['phase_parent'] as $val_phase) {
            $rawPhase = DB::table('t_timelineA')
                ->where('transactionnumber', $id)
                ->where('fase', $val_phase->fase)
                ->orderBy('order')
                ->get();

            $data['phase'][$val_phase->fase] = [];
            foreach ($rawPhase as $phase) {
                array_push($data['phase'][$val_phase->fase], $phase->detail);
            }
        }

        /** SET PHASE & TIMELINE */
        $rowStart = 7;
        $activeWorksheet->getColumnDimension('A')->setWidth(20);
        foreach ($data['phase'] as $key => $value) {
            $phaseColumn = 'C';
            // SET TIMELINE PHASE
            $activeWorksheet->setCellValue('A'.$rowStart, strtoupper($key));

            // Merge Cells
            $cellsRange = 'A'.$rowStart.':B'.$rowStart;
            $activeWorksheet->mergeCells($cellsRange);

            /** SET STYLING PHASE */
            //Background Color
            $activeWorksheet
            ->getStyle($cellsRange)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('eeeeee');

            // Allignment
            $activeWorksheet
            ->getStyle($cellsRange)
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

            // Font Size
            $activeWorksheet
            ->getStyle($cellsRange)
            ->getFont()
            ->setSize('14');

            $activeWorksheet->getRowDimension($rowStart)->setRowHeight(30);

            for ($y=1; $y <= $numberOfWeeks; $y++) {
                $activeWorksheet
                ->getStyle($phaseColumn.$rowStart)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('eeeeee');

                $phaseColumn++;
            }

            /** SET TIMELINE TASK */
            foreach ($value as $task) {
                $rowStart++;
                $activeWorksheet->setCellValue('A'.$rowStart, strtoupper($task));

                // Merge Cells
                $cellsRange = 'A'.$rowStart.':B'.$rowStart;
                $activeWorksheet->mergeCells($cellsRange);

                /** SET STYLING PHASE */
                //Background Color
                $activeWorksheet
                ->getStyle($cellsRange)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('e4d00a');

                // Allignment
                $activeWorksheet
                ->getStyle($cellsRange)
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);

                // Font Size
                $activeWorksheet
                ->getStyle($cellsRange)
                ->getFont()
                ->setSize('14');

                $activeWorksheet->getRowDimension($rowStart)->setRowHeight(30);


                $timelineA = DB::select("
                    SELECT
                        detail,
                        startdate,
                        enddate,
                        week_start,
                        CASE
                            WHEN count_week = 0 THEN 1
                            ELSE count_week
                        END as count_week
                    FROM (
                        SELECT
                            detail,
                            startdate,
                            enddate,
                            CEIL(DATEDIFF(startdate, '".$timeline->project->startdate."') / 7) as week_start,
                            CEIL(DATEDIFF(enddate, startdate) / 7) as count_week
                        FROM t_timelineA
                            where transactionnumber = '$id'
                                AND detail = '$task'
                        GROUP BY detail, startdate, enddate
                    ) a
                ")[0];

                $phaseColumn_start = 'C';
                for ($y=1; $y < $numberOfWeeks; $y++) {
                    $timelineA->week_start = $timelineA->week_start == 0 ? 1 : $timelineA->week_start;
                    if($timelineA->week_start == $y){
                        $num = $y+2;
                        $phaseColumn_start = Coordinate::stringFromColumnIndex($num);

                        for ($z=0; $z < $timelineA->count_week; $z++) {
                            $num_end = $num+($timelineA->count_week-1);
                            $phaseColumn_end = Coordinate::stringFromColumnIndex($num_end);
                            $timelineRange = $phaseColumn_start.$rowStart.':'.$phaseColumn_end.$rowStart;
                        }

                        $activeWorksheet
                            ->getStyle($timelineRange)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('5f6ff2');
                    }
                }
            }

            $rowStart++;
        }

        /** SET EXCEL TITLE */
        $project = Project::where('id', $timeline->project_id)->with('client')->first();
        $last_column = Coordinate::stringFromColumnIndex($numberOfWeeks+2);
        $range = 'A1:'.$last_column.'3';
        $activeWorksheet->mergeCells($range);
        $activeWorksheet->setCellValue('A1', "Timeline Project $project->name - ".$project->client->name);
        $activeWorksheet->getStyle($range)
                        ->getFont()
                        ->setSize('18')
                        ->setBold(true);
        $activeWorksheet->getStyle($range)
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $activeWorksheet->getRowDimension('4')->setRowHeight(30);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Timeline Project $project->name - ".$project->client->name;
        if($type == 'excel'){
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$fileName.'.xlsx"');
            $writer->save('php://output');
        } else {
            $path = public_path()."/document/timeline/".$fileName;
            $pdfWriter = IOFactory::createWriter($spreadsheet, 'Mpdf');

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$fileName.'.pdf"');
            $pdfWriter->save('php://output');
            // $pdfWriter->save($path.'.pdf');
            // $writer->save($path.'.xlsx');
        }
    }

    public function generateTemplate(Request $request)
    {
        try {
            $projectId = base64_decode($request->project_id);
            $project = Project::where('id', $projectId)->with('pc')->first();

            $phase = PhaseA::where('phase_id', $request->timeline_type)->orderBy('order', 'asc')->get();
            $spreadsheet = IOFactory::load(public_path().'/document/template_timeline_project.xlsx');
            $spreadsheet->setActiveSheetIndexByName('Timeline');

            /** Set Timeline Template Value */
            $timelineSheet = $spreadsheet->getActiveSheet();
            $timelineSheet->getCell('C3')->setValue($project->name . ' ('.$project->code.')');
            $timelineSheet->getCell('C4')->setValue($projectId);
            $timelineSheet->getCell('C5')->setValue($project->pc->name);
            $periodeProject = Carbon::parse($project->startdate)->translatedFormat('d F Y').' - '.Carbon::parse($project->enddate)->translatedFormat('d F Y');
            $timelineSheet->getCell('G3')->setValue($periodeProject);

            $cellStart = 8;
            foreach ($phase as $val) {
                $timelineSheet->getCell('A'.$cellStart++)->setValue($val->name);
            }

            /** Set Employee Data Value */
            $employee = User::where('name', '<>', 'Admin')->with('roles')->with('division')->orderby('name')->get();
            $spreadsheet->setActiveSheetIndexByName('Employee Data');
            $employeeSheet = $spreadsheet->getActiveSheet();
            $cellStart = 2;
            foreach($employee as $val){
                $nextCell = $cellStart++;
                $employeeSheet->setCellValue('A'.$nextCell, $val->id);
                $employeeSheet->setCellValue('B'.$nextCell, $val->name);
                $employeeSheet->setCellValue('D'.$nextCell, $val->roles->name);
                $employeeSheet->setCellValue('C'.$nextCell, $val->division->name);
            }

            $spreadsheet->setActiveSheetIndexByName('Timeline');
            $writer = new Xlsx($spreadsheet);
            $fileName = "Timeline Project $project->name - ".$project->client->name.'.xlsx';
            $writer->save(public_path().'/document/'.$fileName);

            return response()->json([
                'status' => [
                    'code' => 200,
                    'msg' => 'OK'
                ],
                'file' => $fileName
            ]);
        } catch (Exception $th) {
            return response()->json([
                'status' => [
                    'msg' => 'Gagal generate template',
                    'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ],
                'data' => null,
                'err_detail' => $th,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function downloadTemplate($filename)
    {
        $path = public_path()."/document/$filename";
        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function importTimeline(Request $request)
    {
        $filename = '';
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = date('y-m-d') . '_' . Str::random(10) . '.' . $file->getClientOriginalName();
            $file->move(public_path().'/document/timeline/', $filename);
        }

        $fullPath = public_path()."/document/timeline/$filename";
        try {
			$inputFileType = IOFactory::identify($fullPath);
			$objReader = IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objReader->setReadEmptyCells(false);
			$objPHPExcel = $objReader->load($fullPath);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($fullPath,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

        $sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestDataRow();

        $project_id = $sheet->getCell('C4')->getValue();
        $trans_number = DataAccessHelpers::generateTransactionNumber($project_id);

        DB::beginTransaction();
        try {
            $header['transactionnumber'] = $trans_number;
            $header['project_id'] = $project_id;
            $header['created_by'] = Auth::user()->name;
            $header['created_at'] = now();
            $header['deletestatus'] = 0;

            $t_header = Timeline::create($header);

            /**
             * Start Insert Detail
             */
            try {
                for ($i=8; $i <= $highestRow; $i++) {
                    $phase = $sheet->getCell('A'.$i)->getValue();
                    $work = $sheet->getCell('B'.$i)->getValue();
                    $start_date = $sheet->getCell('C'.$i)->getValue();
                    $start_date = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($start_date))->format('Y-m-d');

                    $end_date = $sheet->getCell('D'.$i)->getValue();
                    $end_date = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($end_date))->format('Y-m-d');

                    $bobot = $sheet->getCell('E'.$i)->getValue();
                    $employee = $sheet->getCell('F'.$i)->getValue();
                    $order = $sheet->getCell('G'.$i)->getValue();
                    $is_document = $sheet->getCell('H'.$i)->getValue() == 'Ya' ? 1 : 0;
                    TimelineA::create([
                        'transactionnumber' => $trans_number,
                        'fase' => $phase,
                        'detail' => $work,
                        'startdate' => $start_date,
                        'enddate' => $end_date,
                        'bobot' => $bobot,
                        'karyawan_id' => Str::replace('.', ',', $employee),
                        'order' => $order,
                        'closed' => 0,
                        'is_document' => $is_document
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

    function isEmptyRow($row) {
        foreach($row as $cell){
            if (null !== $cell) return false;
        }
        return true;
    }
}
