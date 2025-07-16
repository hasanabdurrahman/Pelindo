<?php

namespace App\Http\Controllers\Module\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\RolesA;
use \Yajra\DataTables\Facades\DataTables;
class RolesDetailController extends Controller
{
    public function index()
    {
        $data = RolesA::selectRaw(
                'm_rolesA.*, 
        (SELECT name FROM users WHERE id = m_rolesA.updated_by) AS updated_by,
        (SELECT name FROM users WHERE id = m_rolesA.created_by) AS created_by'
            )
            ->get();
        return view('module.setting.permission.index', compact('data'));
    }

    public function datatable()
    {
        $data = RolesA::latest()->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $onclick = "onclick=\"openModal('$row->slug')\"";
                $btn = '<div class="text-center">';
                $btn .= '<button style="margin-right:10px" type="button" ' . $onclick . ' class="btn btn-primary btn-sm" data-toggle="modal" data-target="#EditModal" data-toggle="tooltip" data-placement="top" title="Edit Kursus"><i class="fas fa-edit"></i></button>';
                $btn .= '<a style="margin-right:10px"  href="/panel/course/view/' . $row->slug . '" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Tambah Materi"><i class="fas fa-plus-circle"></i></a>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
