<?php

namespace App\Http\Controllers\Module\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Division;
use App\Models\MasterData\Employee;
use App\Models\Setting\Roles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use \Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $rolesA = getPermission(Route::currentRouteName());
        return view('module.masterdata.employee.index', compact('rolesA'))->render();
    }

    public function datatable(Request $request)
    {
        // Permission
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        $query = Employee::select('m_employee.*', 'm_division.name AS division_name', 'm_roles.name AS roles_name')
        ->join('m_division', 'm_employee.divisi_id', '=', 'm_division.id')
        ->join('m_roles', 'm_employee.roles_id', '=', 'm_roles.id')
        // ->where('m_employee.deleted_status',0)
        ->orderBy('m_employee.deleted_status')
        ->latest()->get();

        return DataTables::of($query)
            ->editColumn('status', function ($query) {
                if ($query->active != 'Active') {
                    return '<span class="badge bg-light-danger" style="cursor:pointer" onclick="showDeletedDetail(this)" data-deleted_by="' . $query->deleted_by . '" data-deleted_at="' . $query->deleted_at . '">Inactive</span>';
                } else {
                    return '<span class="badge bg-light-success">Active</span>';
                }
            })
            ->editColumn('action', function ($query) use ($permission) {
                return self::renderAction($query->id, $permission, $query->deleted_status);
            })
            ->editColumn('parent', function ($query) {
                $parentEmployee = $query->where('id', $query->parent_id)->get();
                $parentEmployee = count($parentEmployee) > 0 ? $parentEmployee[0]->name : '';
                return "
                    <span>$parentEmployee</span>
                ";
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'parent', 'status'])
            ->make(true);
    }
    public function renderAction($id, $permission, $deleted_status)
    {
        $blade = '';
        if ($permission->xupdate) {
            $blade .= "
                <a href='javascript:void(0)' onclick='renderView(`" . route('employee.edit', $id) . "`)'  class='btn icon btn-sm btn-outline-primary rounded-pill'>
                    <i class='fas fa-edit'></i>
                </a>
            ";
        }

        if ($permission->xdelete) {

            if ($deleted_status == 0) {
                $blade .= "
                <a href='javascript:void(0)' onclick='deleteEmployee(`$id`)' class='btn icon btn-sm btn-outline-danger rounded-pill'>
                <i class='bi-regular bi bi-trash'></i>
                </a>";
            }
        }

        {
            $blade .= "
                <a href='javascript:void(0)' onclick='resetEmployee(`$id`)'  class='btn icon btn-sm btn-outline-primary rounded-pill'>
                    <i class='fas fa-screwdriver-wrench'></i>
                </a>
            ";
        }

        return $blade;
    }


    public function reset(string $id)
    {
        $employee = Employee::findOrFail($id);
        $data = [];
        try {
            // $employee = Employee::findOrFail($request->id);
            $employee['updated_by'] = auth()->user()->name;
            $employee['updated_at'] = now();
            $employee['password'] = Hash::make('password');
            $employee->update($data);
            // dd($data);

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 'data' => $employee,
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

    /**
     * Show form to add employee
     */
    public function create()
    {
        $division = Division::where('deleted_status',0)->latest()->get();
        $roles = Roles::where('deleted_status',0)->latest()->get();
        return view('module.masterdata.employee.add', compact('division', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            // 'code' => 'required|unique:m_employee,code',
            'code' => [
                'required',
                    Rule::unique('m_employee')->where(function ($query) use ($request) {
                        return $query->where('code', $request->code)->where('deleted_status', 0);
                    }),
            ],
            'name' => 'required',
            // 'email' => 'required|email|unique:m_employee,email',
            'email' => 'required', 'email', Rule::unique('m_employee')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)->where('deleted_status', 0);
            }),
            'divisi_id' => 'required|integer|max_digits:11',
            'roles_id' => 'required|integer|max_digits:11',
            'phone' => 'required|numeric|max_digits:11',
            'address' => 'required',
        ]);
        try {
            $employee = $request->all();
            $employee['created_by'] = auth()->user()->name;
            $employee['created_at'] = now();
            $employee['deleted_status'] = 0;
            $employee['active'] = 'Active';
            $employee['password'] = Hash::make('password');

            $employee = Employee::create($employee);
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 'data' => $employee,
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
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        $division = Division::where('deleted_status',0)->get();
        $roles = Roles::where('deleted_status',0)->get();
        return view('module.masterdata.employee.edit', compact('employee','division', 'roles'))->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|numeric|unique:m_employee,code,' . $request->id,
            'name' => 'required|unique:m_employee,name,' . $request->id,
            'email' => 'required|email|unique:m_employee,email,' . $request->id,
            'divisi_id' => 'required|integer|max_digits:11',
            'roles_id' => 'required|integer|max_digits:11',
            'phone' => 'required|numeric',
            'address' => 'required',
        ]);
        try {
            $employee = Employee::findOrFail($request->id);
            $employee['updated_by'] = auth()->user()->name;
            $employee['updated_at'] = now();
            if ($request->input('active') == 'Active') {
                $employee['active']= 'Active';
                $employee['deleted_status'] = 0;
            } if($request->input('active') == 'Inactive'){
                $employee['active'] = 'Inactive';
                $employee['deleted_status'] = 1;
            }
            $employee->update($request->all());

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ], 'data' => $employee,
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $data = [];

            $employee['deleted_by'] = auth()->user()->name;
            $employee['deleted_at'] = now();
            $employee['deleted_status'] = 1;
            $employee['active'] = 'Inactive';
            $employee->update($data);

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

    // public function active(string $id)
    // {
    //     try {
    //         $employee = Employee::findOrFail($id);

    //         $data = [];

    //         $employee['updated_by'] = auth()->user()->name;
    //         $employee['updated_at'] = now();
    //         $employee['deleted_status'] = 0;
    //         $employee['active'] = 'Active';
    //         $employee->update($data);

    //         return response()->json([
    //             'status' => [
    //                 'msg' => 'OK',
    //                 'code' => JsonResponse::HTTP_OK,
    //             ],
    //             'data' => null,
    //         ], JsonResponse::HTTP_OK);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => [
    //                 'msg' => 'Err',
    //                 'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
    //             ],
    //             'data' => null,
    //             'err_detail' => $th,
    //         ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }
}
