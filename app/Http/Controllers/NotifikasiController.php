<?php

namespace App\Http\Controllers;


use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        // Fetch the latest three notifications
        $latestNotifikasi = Notifikasi::latest()
        ->take(3)
        ->where('receiver', auth()->user()->id)
        ->whereNull('deleted_at')
        ->whereNull('deleted_by')
        ->get();

        $allNotifikasi = Notifikasi::where('receiver', auth()->user()->id)
        ->whereNull('deleted_at')
        ->whereNull('deleted_by')
        ->latest()
        ->get();

        $newNotifications = Notifikasi::where('receiver', auth()->user()->id)
            ->where('status_read', 0)
            ->get();
            return view('includes.navbar', [
                'latestNotifikasi' => $latestNotifikasi,
                'allNotifikasi' => $allNotifikasi,
                'newNotifications' => $newNotifications,
            ]);
    }

    public function markAllAsRead()
    {
        $userId = auth()->user()->id;

        // Update status_read for all notifications of the authenticated user
        DB::table('notifikasi')
            ->where('receiver', $userId)
            ->update(['status_read' => 1,'read_at' =>now(),'read_by' => auth()->user()->name]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function clearNotifications()
    {
        $userId = auth()->user()->id;

        DB::table('notifikasi')
            ->where('receiver', $userId)
            ->update([
                'deleted_at' => now(),
                'deleted_by' => auth()->user()->name,
                'status_read' => 1,'read_at' =>now(),'read_by' => auth()->user()->name
            ]);


        return response()->json(['message' => 'All notifications cleared']);
    }

}
