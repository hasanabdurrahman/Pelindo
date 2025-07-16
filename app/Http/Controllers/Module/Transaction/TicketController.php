<?php

namespace App\Http\Controllers\Module\Transaction;

use App\Helpers\DataAccessHelpers;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Employee;
use App\Models\MasterData\Project;
use App\Models\Setting\Roles;
use App\Models\Transaction\HistoryApprovalTicket;
use App\Models\Transaction\Ticket;
use App\Models\Transaction\TicketView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\DataTables;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rolesA = getPermission(Route::currentRouteName());
        $roles = Roles::where('id', Auth::user()->roles_id)->first();
        return view('module.transaction.request-ticket.index', compact('rolesA', 'roles'))->render();
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


        $data = [
            'project' => $project,
            'employee' => $employee
        ];

        return view('module.transaction.request-ticket.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $transNumber = DataAccessHelpers::generateRequestTicketTransNumber();
        $this->validate($request, [
            'project_id' => 'required',
            'karyawan_id' => 'required',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after:startdate',
            'issue' => 'required',
        ]);
        // dd($request);
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['requester'] = Auth::user()->id;
            $data['created_by'] =  Auth::user()->name;
            $data['created_at'] =  now();
            $data['status'] = 'process';
            $data['transactiondate'] =  now();
            $data['transactionnumber'] = $transNumber;

            $request = Ticket::create($data);

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


    /** Display data as datatable */
    public function datatable()
    {
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        $role = Roles::where('id', Auth::user()->roles_id)->first();


        if ($role->code != 'pc') {
            $query = TicketView::select(['id', 'transactionnumber', 'transactiondate', 'project_id', 'karyawan_id', 'startdate', 'enddate', 'issue', 'status', 'requester'])
                ->with('karyawan')->has('karyawan')->with('project')
                ->Where('karyawan_id', Auth::user()->id)
                ->where('status', '<>', 'rejected')
                ->groupBy(['id', 'transactionnumber', 'transactiondate', 'project_id', 'karyawan_id', 'startdate', 'enddate', 'issue', 'status', 'requester'])
                ->get();
        } else {
            $query = Ticket::with('karyawan')->has('karyawan')
                // where('deleted_status', 0)
                ->where('requester', Auth::user()->id)
                ->where('status', '<>', 'rejected')
                ->with('project')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return DataTables::of($query)
            ->editColumn('status', function ($query) {
                if ($query->status === 'solved') {
                    return '<span class="badge bg-light-success">Solved</span>';
                } else if ($query->status === 'process') {
                    return '<span class="badge bg-light-warning">Process</span>';
                } else if ($query->status === 'canceled') {
                    return '<span class="badge bg-light-danger">Canceled</span>';
                } else if($query->status === 'rejected'){
                    return '<span class="badge bg-light-info">Rejected</span>';
                }
            })
            // ->editColumn('approval_kadep', function($query){
            //     if($query->approval2 === 1){
            //         return '<span class="badge bg-light-success">Approved</span>';
            //     } else if ($query->approval2 === 0) {
            //         return '<span class="badge bg-light-danger" onclick="rejected_reason(`'.$query->reason2.'`)" style="cursor:pointer">Rejected</span>';
            //     } else if ($query->approval1 === 0) {
            //         return '<span class="badge bg-light-warning">Need Review</span>';
            //     } else {
            //         return '<span class="badge bg-light-warning">Need Approval</span>';
            //     }
            // })
            ->editColumn('action', function ($query) use ($permission, $role) {
                return self::renderAction($query, $permission, $role);
            })
            ->editColumn('startdate', function ($query) {
                return Carbon::parse($query->startdate)->translatedFormat('d-m-Y');
            })
            ->editColumn('enddate', function ($query) {
                return Carbon::parse($query->enddate)->translatedFormat('d-m-Y');
            })
            ->editColumn('transactiondate', function ($query) {
                return Carbon::parse($query->transactiondate)->translatedFormat('d-m-Y');
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function rejectedTicket()
    {
        $role = Roles::where('id', Auth::user()->roles_id)->first();
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        if ($role->code != 'pc') {
            $query = TicketView::select(['id', 'transactionnumber', 'transactiondate', 'project_id', 'karyawan_id', 'startdate', 'enddate', 'issue', 'status', 'requester'])
                ->with('karyawan')->has('karyawan')->with('project')
                ->Where('karyawan_id', Auth::user()->id)
                ->where('status', 'rejected')
                ->groupBy(['id', 'transactionnumber', 'transactiondate', 'project_id', 'karyawan_id', 'startdate', 'enddate', 'issue', 'status', 'requester'])
                ->get();
        } else {
            $query = Ticket::with('karyawan')->has('karyawan')
                // where('deleted_status', 0)
                ->where('requester', Auth::user()->id)
                ->where('status', 'rejected')
                ->with('project')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return DataTables::of($query)
            ->editColumn('status', function ($query) {
                if ($query->status === 'solved') {
                    return '<span class="badge bg-light-success">Solved</span>';
                } else if ($query->status === 'process') {
                    return '<span class="badge bg-light-warning">Process</span>';
                } else if ($query->status === 'canceled') {
                    return '<span class="badge bg-light-danger">Canceled</span>';
                } else if($query->status === 'rejected'){
                    return '<span class="badge bg-light-info">Rejected</span>';
                }
            })
            ->editColumn('action', function ($query) use ($permission, $role) {
                return self::renderAction($query, $permission, $role);
            })
            ->editColumn('startdate', function ($query) {
                return Carbon::parse($query->startdate)->translatedFormat('d-m-Y');
            })
            ->editColumn('enddate', function ($query) {
                return Carbon::parse($query->enddate)->translatedFormat('d-m-Y');
            })
            ->editColumn('transactiondate', function ($query) {
                return Carbon::parse($query->transactiondate)->translatedFormat('d-m-Y');
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /** Render Action data table */
    public function renderAction($query, $permission, $role)
    {
        $blade = '';
        $id = base64_encode($query->id);

        if ($query->status != 'canceled'&& $query->deleted_status == 0) {
            if ($permission->xdelete) {
                if ($query->requester == Auth::user()->id && ($query->status != 'solved' && $query->status != 'rejected')) {
                    $blade .= "
                            <a href='javascript:void(0)' onclick='cancelRequest(`$id`)' class='btn icon btn-sm btn-outline-danger rounded-pill me-2 mb-2' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Cancel Request'>
                                <i class='fas fa-cancel'></i>
                            </a>
                    ";
                } else if($query->status == 'rejected'){
                    $blade .= "
                            <a href='javascript:void(0)' onclick='historyReject(`$id`)'  class='btn icon btn-sm btn-outline-warning rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='History Approval'>
                                <i class='fas fa-clock'></i>
                            </a>
                        ";
                }
                // if($role->code != 'kdp'){
                // }
            }

            if ($permission->xupdate) {
                if ($query->requester == Auth::user()->id) {
                    if ($query->status != 'solved' && $query->status != 'rejected') {
                        $blade .= "
                                <a href='javascript:void(0)' onclick='renderView(`" . route('request-ticket.edit', $id) . "`)' class='btn icon btn-sm btn-outline-warning rounded-pill me-2 mb-2' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Edit Request'>
                                    <i class='fas fa-edit'></i>
                                </a>
                        ";
                    } else if($query->status == 'solved' ) {
                        $blade = "
                            <a href='javascript:void(0)' onclick='rejectSolvedTicket(`$id`)' class='btn icon btn-sm btn-outline-danger rounded-pill me-2 mb-2' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Reject Solved Request'>
                                <i class='bi bi-check-circle'></i>
                            </a>
                        ";
                        $countHistory = HistoryApprovalTicket::where('ticket_id', $query->id)->count();
                        if($countHistory > 0){
                            $blade .= "<a href='javascript:void(0)' onclick='historyReject(`$id`)'  class='btn icon btn-sm btn-outline-warning rounded-pill' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='History Approval'>
                                            <i class='fas fa-clock'></i>
                                        </a>";
                        }
                    }
                } else if($query->karyawan_id == Auth::user()->id){
                    if($query->status == 'process'){
                        $blade .= "
                            <a href='javascript:void(0)' onclick='solved(`$id`)' class='btn icon btn-sm btn-outline-success rounded-pill me-2 mb-2' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='Solved Request'>
                                <i class='bi bi-check-circle'></i>
                            </a>
                            ";
                    } else if ($query->status == 'rejected'){
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
            }
        }

        return $blade;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = base64_decode($id);

        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($id);

            $data['deleted_by'] = auth()->user()->name;
            $data['deleted_at'] = now();
            $data['deleted_status'] = 1;
            $data['status'] = 'canceled';

            $ticket->update($data);

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


    /**
     * Solved the specified resource from storage.
     */
    public function solved(string $id)
    {
        $id = base64_decode($id);

        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($id);

            $data['solved_by'] = auth()->user()->name;
            $data['solved_at'] = now();
            $data['status'] = 'solved';

            $ticket->update($data);

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
        $ticket = Ticket::find($id);

        $data = [
            'project' => $project,
            'employee' => $employee,
            'storedData' => $ticket
        ];

        return view('module.transaction.request-ticket.edit', $data);
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
            'issue' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $ticket = Ticket::find($request->id);
            $ticket['updated_by'] = Auth::user()->name;
            $ticket['updated_at'] = now();
            $ticket->update($request->all());

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

    /** Reject Request Ticket */
    public static function reject(Request $request)
    {
        $ticketId = base64_decode($request->ticket_id);
        DB::beginTransaction();
        try {
            // Update Tasklist status
            $tasklist = Ticket::where('id', $ticketId);
            $data['status'] = 'rejected';
            $tasklist->update($data);

            // Insert into history tasklist approval
            HistoryApprovalTicket::create([
                'ticket_id' => $ticketId,
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

    public static function showHistoryApproval($id)
    {
        $id = base64_decode($id);
        $history = HistoryApprovalTicket::where('ticket_id', $id)->with('employeeCreated')->get();

        $blade = view('module.transaction.request-ticket.historyApproval', compact('history'))->render();

        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => JsonResponse::HTTP_OK,
            ],
            'data' => $history,
            'blade' => $blade
        ], JsonResponse::HTTP_OK);
    }

    public static function requestApproval(Request $request, $id)
    {
        $id = base64_decode($id);
        DB::beginTransaction();
        try {
            // Update Tasklist status
            $tasklist = Ticket::where('id', $id);
            $data['status'] = 'solved';
            $tasklist->update($data);

            // Insert into history tasklist approval
            HistoryApprovalTicket::create([
                'ticket_id' => $id,
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
}
