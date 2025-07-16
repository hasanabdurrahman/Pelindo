<?php

namespace App\Http\Controllers\Module\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Project;
use App\Models\MasterData\Client;
use App\Models\MasterData\Employee;
use App\Models\Transaction\Timeline;
use Carbon\Carbon;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use \Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $rolesA = getPermission(Route::currentRouteName());
        return view('module.masterdata.project.index', compact('rolesA'));
    }

    public function datatable(Request $request)
    {

        $query = Project::select('m_project.*')
            ->selectRaw('m_client.name AS client_name')
            ->selectRaw('pc_employee.name AS pc_name')
            ->selectRaw('sales_employee.name AS sales_name')
            ->join('m_client', 'm_client.id', '=', 'm_project.id_client')
            ->leftJoin('m_employee AS pc_employee', 'pc_employee.id', '=', 'm_project.pc_id')
            ->leftJoin('m_employee AS sales_employee', 'sales_employee.id', '=', 'm_project.sales_id')
            ->where('m_project.deleted_status', 0)
            ->get();

        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);
        return DataTables::of($query)
            ->editColumn('project', function ($query) {
                $html = "Code : <b> $query->code </b><br>";
                $html .= "Project Name : <b>" . $query->name . "</b><br>";
                $html .= "Client : " . $query->client_name . "<br>";
                $html .= "Contract No. : <b>" . $query->contract_number . "</b><br>";
                $html .= "Value : <b>Rp. " . number_format($query->value, 2) ."</b>";
                return $html;
            })
            ->editColumn('project_date', function ($query) {
                $html = "<b>Start Date : ". Carbon::parse($query->startdate)->translatedFormat('d M Y') ." </b><br>";
                $html .= "<b>End Date : ". Carbon::parse($query->enddate)->translatedFormat('d M Y') ."</b>";
                return $html;
            })
            ->editColumn('project_pic', function ($query) {
                $html = "PC : ". $query->pc_name ." <br>";
                $html .= "Sales : ". $query->sales_name;
                return $html;
            })
            ->editColumn('action', function ($query) use ($permission) {
                return self::renderAction($query->id, $permission, $query->deleted_status);
            })
            ->editColumn('parent', function ($query) {
                $parentProject = $query->where('id', $query->parent_id)->get();
                $parentProject = count($parentProject) > 0 ? $parentProject[0]->name : '';
                return "
                    <span>$parentProject</span>
                ";
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'parent', 'project', 'project_date', 'project_pic'])
            ->make(true);
    }
    public function renderAction($id, $permission, $deleted_status)
    {
        $blade = '';
        if ($permission->xupdate) {
            if ($deleted_status == 0) {
                $blade .= "
                <a href='javascript:void(0)' onclick='renderView(`" . route('project.edit', $id) . "`)'  class='btn icon btn-sm btn-outline-primary rounded-pill'>
                    <i class='fas fa-edit'></i>
                </a>";
            }
        }

        if ($permission->xdelete) {

            if ($deleted_status == 0) {
                $blade .= "
                <a href='javascript:void(0)' onclick='deleteProject(`$id`)' class='btn icon btn-sm btn-outline-danger rounded-pill'>
                <i class='fa-regular fa-trash-can'></i>
                </a>";
            } else {
                $blade .= "
                <a href='javascript:void(0)' onclick='ActiveProject(`$id`)' class='btn icon btn-sm btn-outline-danger rounded-pill'>
                    <i class='fa-solid fa-eye'></i>
                </a>";
            }
        }

        return $blade;
    }
    /**
     * Show form to add Project
     */
    public function create()
    {
        $employeesWithRoles = Employee::where('deleted_status', 0)->with('roles')->get();
        $client = Client::where('deleted_status', 0)->latest()->get();

        $roleCodeArrays = [
            'prg' => [],
            'pc' => [],
            'sls' => [],
        ];

        foreach ($employeesWithRoles as $employee) {
            $roleCode = $employee->roles->code;

            if (array_key_exists($roleCode, $roleCodeArrays)) {
                $roleCodeArrays[$roleCode][] = $employee;
            }
        }

        $prg = $roleCodeArrays['prg'];
        $pc = $roleCodeArrays['pc'];
        $sls = $roleCodeArrays['sls'];

        return view('module.masterdata.project.add', compact('prg', 'pc', 'sls', 'client'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'id_client' => 'required|integer|max:99999999999',
            'contract_number' => 'required',
            'value' => [
                'required',
                'regex:/^\d{1,3}(,\d{3})*(\.\d{1,2})?$/',
                'max:99999999.99',
            ],
            'startdate' => 'required|date',
            'enddate' => 'required|date|after:startdate',
            'pc_id' => 'required|integer|max:99999999999',
            'sales_id' => 'required|integer|max:99999999999',
            'description' => 'required',
            'xtype' => 'required'
        ]);

        // Ambil tahun, bulan, dan nomor urutan proyek
        $projectYear = date('Y'); // Tahun saat ini (format 4 digit)
        $projectMonth = date('m'); // Bulan saat ini

        // Mencari jumlah proyek yang telah dibuat pada tahun ini
        $projectCount = Project::whereYear('startdate', '=', $projectYear)->count();
        $projectCount++; // Menambahkan 1 ke nomor urutan proyek

        // Format ulang nomor urutan proyek menjadi 4 digit dengan angka nol di depan jika perlu
        $formattedCounter = str_pad($projectCount, 4, '0', STR_PAD_LEFT);

        // Dapatkan kode client dari request
        $clientCode = Client::find($request->input('id_client'))->code;

        // Buat kode proyek dengan format yang diinginkan
        $projectCode = "{$clientCode}/{$projectMonth}/{$projectYear}/{$formattedCounter}";
        // dd($request);
        try {
            $project = new Project();
            $project->code = $projectCode;
            $project->name = $request->input('name');
            $project->id_client = $request->input('id_client');
            $project->contract_number = $request->input('contract_number');
            $project->value = str_replace(',', '', $request->input('value'));
            $project->startdate = $request->input('startdate');
            $project->enddate = $request->input('enddate');
            $project->pc_id = $request->input('pc_id');
            $project->sales_id = $request->input('sales_id');
            $project->xtype = $request->input('xtype');
            $project->description = $request->input('description');
            $project->created_at = now();
            $project->created_by = auth()->user()->name;
            $project->deleted_status = 0;

            DB::transaction(function () use ($project) {
                $project->save();
            });

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ],
                'data' => $project,
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::find($id);
        $employeesWithRoles = Employee::where('deleted_status', 0)->with('roles')->get();
        $client = Client::where('deleted_status', 0)->latest()->get();
        $types = Project::all();
        $roleCodeArrays = [
            'prg' => [],
            'pc' => [],
            'sls' => [],
        ];

        foreach ($employeesWithRoles as $employee) {
            $roleCode = $employee->roles->code;

            if (array_key_exists($roleCode, $roleCodeArrays)) {
                $roleCodeArrays[$roleCode][] = $employee;
            }
        }

        $prg = $roleCodeArrays['prg'];
        $pc = $roleCodeArrays['pc'];
        $sls = $roleCodeArrays['sls'];
        // dd($project);
        return view('module.masterdata.project.edit', compact('project', 'prg', 'pc', 'sls', 'client', 'types'))->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|unique:m_project,code,' . $request->id,
            'name' => 'required',
            'id_client' => 'required',
            'contract_number' => 'required',
            'value' => [
                'required',
                'regex:/^\d{1,3}(,\d{3})*(\.\d{1,2})?$/',
                'max:99999999.99',
            ],
            'startdate' => 'required',
            'enddate' => 'required',
            'pc_id' => 'required',
            'sales_id' => 'required',
            'xtype' => 'required',
            'description' => 'required',
        ]);
        // dd($request);
        try {
            $project = Project::findOrFail($request->id);
            $project['updated_by'] = auth()->user()->name;
            $project['updated_at'] = now();
            $project['name'] = $request->input('name');
            $project['id_client'] = $request->input('id_client');
            $project['contract_number'] = $request->input('contract_number');
            $project['pc_id'] = $request->input('pc_id');
            $project['sales_id'] = $request->input('sales_id');
            $project['startdate'] = $request->input('startdate');
            $project['enddate'] = $request->input('enddate');
            $project['description'] = $request->input('description');
            $project->value = str_replace(',', '', $request->input('value'));
            $project['xtype'] = $request->input('xtype');

            DB::transaction(function () use ($project) {
                $project->save();
            });

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => 200,
                ],
                'data' => $project,
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
            /** Check if this project has timeline */
            $timeline = Timeline::where('project_id', $id)->count();
            if ($timeline == 0) {
                $project = Project::findOrFail($id);

                $data = [];

                $project['deleted_by'] = auth()->user()->name;
                $project['deleted_at'] = now();
                $project['deleted_status'] = 1;
                $project->update($data);

                return response()->json([
                    'status' => [
                        'msg' => 'OK',
                        'code' => 200,
                    ],
                    'data' => null,
                ], 200);
            } else {
                throw new Exception('Tidak dapat menghapus data project ini karena sudah memiliki timeline', 409);
            }
        } catch (Exception $th) {
            return response()->json([
                'status' => [
                    'msg' => $th->getMessage() != '' ? $th->getMessage() : 'Err',
                    'code' => $th->getCode() != '' ? $th->getCode() : 500,
                ],
                'data' => null,
                'err_detail' => $th,
                'message' => $th->getMessage() != '' ? $th->getMessage() : 'Terjadi Kesalahan Saat Hapus Data, Harap Coba lagi!'
            ], 500);
        }
    }

    public function import(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->hasFile('project_data')) {
                $documentFile = $request->file('project_data');
                $filename = $documentFile->getClientOriginalName();
                $uploadPath = $documentFile->move(public_path('document/project_import'), $filename)->getRealPath();

                $inputFileType = IOFactory::identify($uploadPath);
                $objReader = IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($uploadPath);

                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $rowData = $sheet->rangeToArray(
                        'B' . $row . ':' . $highestColumn . $row,
                        NULL,
                        TRUE,
                        FALSE
                    );

                    $code = $rowData[0][0];
                    $idClient = Client::where('code', $code)->first();
                    $idClient = $idClient->id != null ? $idClient->id : null;
                    $project = $rowData[0][1];
                    $contract = $rowData[0][2];
                    $start = Date::excelToDateTimeObject($rowData[0][3]);
                    $end = Date::excelToDateTimeObject($rowData[0][4]);
                    $value = $rowData[0][5];
                    $pc = Employee::where('name', 'like', '%' . $rowData[0][6] . '%')->first();
                    $pc = $pc != null ? $pc->id : null;
                    $sales = Employee::where('name', 'like', '%' . $rowData[0][7] . '%')->first();
                    $sales = $sales != null ? $sales->id : null;
                    $type = $rowData[0][8];
                    $desc = $rowData[0][9];

                    $projectYear = date('Y');
                    $projectMonth = date('m');
                    $projectCount = Project::whereYear('startdate', '=', $projectYear)->count();
                    $projectCount++; // Menambahkan 1 ke nomor urutan proyek
                    $formattedCounter = str_pad($projectCount, 4, '0', STR_PAD_LEFT);
                    $clientCode = $code;
                    $projectCode = "{$clientCode}/{$projectMonth}/{$projectYear}/{$formattedCounter}";

                    $isExists = Project::where('id_client', $idClient)->where('contract_number', $contract)->where('name', $project)->exists();
                    if (!$isExists) {
                        Project::create([
                            'code' => $projectCode,
                            'name' => $project,
                            'id_client' => $idClient,
                            'contract_number' => $contract,
                            'value' => $value,
                            'startdate' => $start,
                            'enddate' => $end,
                            'pc_id' => $pc,
                            'sales_id' => $sales,
                            'xtype' => $type,
                            'description' => $desc,
                            'created_at' => now(),
                            'created_by' => auth()->user()->name,
                            'deleted_status' => 0,
                        ]);
                    }
                }

                DB::commit();
                return response()->json([
                    'status' => [
                        'msg' => 'OK',
                        'code' => 200,
                    ],
                    'data' => null,
                ], 200);
            } else {
                throw new Exception('Gagal upload file, harap coba lagi', 500);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => [
                    'msg' => $th->getMessage() != '' ? $th->getMessage() : 'Err',
                    'code' => $th->getCode() != '' ? $th->getCode() : 500,
                ],
                'err_detail' => $th,
                'message' => $th->getMessage() != '' ? $th->getMessage() : 'Terjadi Kesalahan Saat Import Data, Harap Coba lagi!'
            ]);
        }
    }
}
