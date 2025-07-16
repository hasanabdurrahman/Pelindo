<?php

namespace App\Http\Controllers\Module\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Client;
use \Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $rolesA = getPermission(Route::currentRouteName());
        return view('module.masterdata.client.index', compact('rolesA'));
    }

    /**
     * Get data and show as datatable
     */
    public function datatable(Request $request)
    {
        $query = Client::where('deleted_status',0)->latest()->get();
        $routePrevious = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $permission = getPermission($routePrevious);

        return DataTables::of($query)
            // ->editColumn('status', function ($query) {
            //     if ($query->deleted_status) {
            //         return '<span class="badge bg-light-danger" style="cursor:pointer" onclick="showDeletedDetail(this)" data-deleted_by="' . $query->deleted_by . '" data-deleted_at="' . $query->deleted_at . '">Inactive</span>';
            //     } else {
            //         return '<span class="badge bg-light-success">Active</span>';
            //     }
            // })
            ->editColumn('action', function ($query) use ($permission) {
                return self::renderAction($query->id, $permission, $query->deleted_status);
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'parent'])
            ->make(true);
    }

    public function renderAction($id, $permission, $deleted_status)
    {
        $blade = '';
        if ($permission->xupdate) {
            $blade .= "
                <a href='javascript:void(0)' onclick='renderView(`" . route('client.edit', $id) . "`)'  class='btn icon btn-sm btn-outline-primary rounded-pill'>
                    <i class='fas fa-edit'></i>
                </a>";
        }

        if ($permission->xdelete) {

            if ($deleted_status == 0) {
                $blade .= "
                <a href='#' onclick='deleteClient(`$id`)' class='btn icon btn-sm btn-outline-danger rounded-pill'>
                <i class='bi-regular bi bi-trash'></i>
                </a>";
            }

        }

        return $blade;
    }

    public function create()
    {
        return view('module.masterdata.client.add');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            // 'code' => 'required|max:3|unique:m_client,code',
            'code' => 'required', 'max:3', Rule::unique('m_client')->where(function ($query) use ($request) {
                return $query->where('code', $request->code)->where('deleted_status', 0);
            }),
            // 'name' => 'required|unique:m_client,name',
            'name' => 'required', Rule::unique('m_client')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)->where('deleted_status', 0);
            }),
            'contact_person' => 'required',
            'company_phone' => 'required',
            'email' => 'required|email|unique:m_client,email',
            'company_address' => 'required',
        ],[
            'code.unique' => 'Code Client sudah ada',
            'name.unique' => 'Nama Client sudah ada',
            'code.max' => 'Code Client tidak boleh lebih dari :max karakter'
        ]);

        try {

            $client = $request->all();
            $client['created_by'] = auth()->user()->name;
            $client['deleted_status'] = 0;

            $client = Client::create($client);

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => $client,
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

    public function destroy(string $id)
    {
        try {
            $client = Client::findOrFail($id);

            $data = [];

            $client['deleted_by'] = auth()->user()->name;
            $client['deleted_at'] = now();
            $client['deleted_status'] = 1;
            $client->update($data);

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
            $client = Client::findOrFail($id);

            $data = [];

            $client['updated_by'] = auth()->user()->name;
            $client['updated_at'] = now();
            $client['deleted_status'] = 0;
            $client->update($data);

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

    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        return view('module.masterdata.client.edit', compact('client'))->render();
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|unique:m_client,code,' . $request->id,
            'name' => 'required|unique:m_client,name,' . $request->id,
            'contact_person' => 'required',
            'company_phone' => 'required',
            'email' => 'required|email|unique:m_client,email,' . $request->id,
            'company_address' => 'required',
        ],[
            'code.unique' => 'Code Client sudah ada',
            'name.unique' => 'Nama Client sudah ada',
            'code.max' => 'Code Client tidak boleh lebih dari :max karakter'
        ]);

        try {
            $client = Client::findOrFail($request->id);

            $client['updated_by'] = auth()->user()->name;
            $client['updated_at'] = now();
            $client->update($request->all());

            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => $client,
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
}
