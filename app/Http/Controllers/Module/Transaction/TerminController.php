<?php

namespace App\Http\Controllers\Module\Transaction;

use App\Helpers\DataAccessHelpers;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Project;
use App\Models\Transaction\Termin;
use App\Models\Transaction\TerminA;
use App\Models\Transaction\Timeline;
use App\Models\Transaction\TimelineA;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\DataTables;

class TerminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rolesA = getPermission(Route::currentRouteName());

        return view('module.transaction.termin.index', compact('rolesA'));
    }

    /**
     * Display data on datatables
     */
    public function datatable(Request $request){
        $query = Termin::with('project_detail.pc', 'project_detail.sales')->get();

        return DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Display Termin detail data on data table
     */
    public function detailData(Request $request, $tn){
        $terminA = TerminA::where('transactionnumber', $tn)->get();

        return DataTables::of($terminA)
                ->addIndexColumn()
                ->editColumn('pekerjaan', function($terminA){
                    $timelineAID = explode(',', $terminA->timelineA_id);
                    $workData = TimelineA::whereIn('id', $timelineAID)->get();
                    $workText = '';

                    foreach ($workData as $val) {
                        $closed = $val->closed;
                        $notice = '';
                        if($closed == 0){
                            $notice = '<i class="fa-solid fa-circle-exclamation fa-beat-fade text-danger ms-1"></i>';
                        }
                        
                        $workText .= '<span class="badge bg-light-success me-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Pekerjaan Belum Diselesaikan">'.$val->detail.' '.$notice.'</span>';
                    }

                    return $workText;
                })
                ->editColumn('duedate', function($terminA){
                    $now = date('Y-m-d');
                    $due_date = date('Y-m-d', strtotime($terminA->due_date));
                    $diff = date_diff(date_create($now), date_create($due_date))->format("%R%a");

                    $dueText = '';
                    if($terminA->is_done == 0){
                        if($diff < 0 && $terminA){
                            $notif = ' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Termin ini sudah melewati masa due date"';
                            $dueText = '<span> '. Carbon::parse($due_date)->translatedFormat('d F Y') .' </span>';
                            $dueText .= '<i class="fa-solid fa-circle-exclamation fa-beat-fade text-danger ms-3" '. $notif .'></i>';
                        } else if($diff > 0 && $diff < 30){
                            $notif = ' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Due date untuk termin ini kurang dari 30 hari lagi"';
                            $dueText = '<span> '. Carbon::parse($due_date)->translatedFormat('d F Y') .' </span>';
                            $dueText .= '<i class="fa-solid fa-circle-exclamation fa-beat-fade text-warning ms-3" '. $notif .'></i>';
                        } else {
                            $dueText = '<span> '. Carbon::parse($due_date)->translatedFormat('d F Y') .' </span>';
                        }
                    } else {
                        $dueText = '<span> '. Carbon::parse($due_date)->translatedFormat('d F Y') .' </span>';
                    }

                    return $dueText;
                })
                ->rawColumns(['pekerjaan', 'duedate'])
                ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::with('timelines')->whereHas('timelines', function($timeline){
            $timeline->whereNotNull('approved_at');
        })->whereDoesntHave('termin')->get();

        return view('module.transaction.termin.add', compact('projects'));
    }

    /**
     * Show detail of project selected when create termin
     */
    public function projectDetail(string $project_id)
    {
        $project_id = base64_decode($project_id);
        $project = Project::where('id', $project_id)->with('pc')->with('sales')->first();

        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => JsonResponse::HTTP_OK,
            ],
            'data' => $project
        ], JsonResponse::HTTP_OK);
    }

    public function renderFormAdd(string $project_id)
    {
        $project_id = base64_decode($project_id);
        $project = Project::where('id', $project_id)->with('pc')->with('sales')->first();
        $timeline = Timeline::where('project_id', $project_id)->first();
        $timelineA = TimelineA::where('transactionnumber', $timeline->transactionnumber)->get();

        $blade = view('module.transaction.termin.formAdd', compact('timelineA', 'project'))->render();

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
        $transactionNumber = DataAccessHelpers::generateTerminTransactionNumber($request->project_id);

        DB::beginTransaction();
        try {
            /**
             * Insert Data to Header
             */
            $header['transactionnumber'] = $transactionNumber;
            $header['project_id'] = $request->project_id;
            $header['created_by'] = Auth::user()->name;
            $header['created_at'] = now();
            $header['deletestatus'] = 0;

            $t_header = Termin::create($header);

            /**
             * Start Insert Detail
             */
            try {
                for ($i=0; $i < count($request->name) ; $i++) {
                    TerminA::create([
                        'transactionnumber' => $transactionNumber,
                        'name' => $request->name[$i],
                        'percentage' => $request->percentage[$i],
                        'value' => str_replace(',', '', $request->value[$i]),
                        'timelineA_id' => $request->timelineA_id[$i],
                        'due_date' => $request->due_date[$i],
                        'order' => (int)$i+1,
                        'is_done' => 0
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
                        'msg' => 'Gagal Insert data Termin (detail), harap coba lagi',
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
                    'msg' => 'Gagal Insert data Termin (header), harap coba lagi',
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
