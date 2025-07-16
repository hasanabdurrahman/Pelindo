<?php
use App\Http\Controllers\Module\Transaction\MonitoringController;

use App\Http\Controllers\Module\Transaction\AdditionalTimelineController;
use App\Http\Controllers\Module\Transaction\TasklistController;
use App\Http\Controllers\Module\Transaction\RequestController;
use App\Http\Controllers\Module\Transaction\TerminController;
use App\Http\Controllers\Module\Transaction\TicketController;
use App\Http\Controllers\Module\Transaction\TimelineController;
use Illuminate\Support\Facades\Route;

#TaskList
Route::middleware(['auth', 'ajax'])->prefix('tasklist')->group(function () {
    Route::get('/add', [TasklistController::class, 'create'])->name('tasklist.add');
    // Route::get('/add/{project_id}', [TasklistController::class, 'create'])->name('tasklist.add');
    Route::get('/edit/{id}', [TasklistController::class, 'edit'])->name('tasklist.edit');
    Route::get('/myTasklist', [TasklistController::class, 'myTasklist'])->name('tasklist.myTasklist');
    Route::get('/teamTasklist', [TasklistController::class, 'teamTasklist'])->name('tasklist.teamTasklist');

    Route::get('/', [TasklistController::class, 'index'])->name('transaction.tasklist')
    ->middleware('permission:read');
    Route::get('show/{id}', [TasklistController::class, 'show'])->name('tasklist.show');
    Route::post('/datatable', [TasklistController::class, 'datatable'])->name('tasklist.datatable')
    ->middleware('permission:datatable');
    Route::post('/datatableall', [TasklistController::class, 'datatableall'])->name('tasklist.datatableall')
    ->middleware('permission:datatable');
    Route::post('/datatable-rejected', [TasklistController::class, 'datatableRejected'])->name('tasklist.datatable-rejected')
    ->middleware('permission:datatable');
    Route::POST('/store', [TasklistController::class, 'store'])->name('tasklist.store')
    ->middleware('permission:add');
    Route::POST('/upload', [TasklistController::class, 'upload'])->name('tasklist.upload')
    ->middleware('permission:add');
    Route::post('/update', [TasklistController::class, 'update'])->name('tasklist.update')
    ->middleware('permission:update');
    Route::delete('/delete/{id}', [TasklistController::class, 'destroy'])->name('tasklist.delete')
    ->middleware('permission:delete');
    Route::post('/active/{id}', [TaskListController::class, 'active'])->name('tasklist.active')
    ->middleware('permission:delete');
    Route::post('/approve/{id}', [TaskListController::class, 'approve'])->name('tasklist.approve')
    ->middleware('permission:approve');
    Route::get('/get-timelinea/{Projectid}', [TasklistController::class, 'getTimelineAByProject'])->name('getTimelineAByProject');
    Route::get('/get-all-timelinea/{Projectid}', [TasklistController::class, 'getAllTimelineAByProject'])->name('getTimelineAByProject');

    Route::post('reject/{id}', [TasklistController::class, 'reject'])->name('tasklist.reject')->middleware('permission:approve');
    Route::post('request-approval/{id}', [TasklistController::class, 'requestApproval'])->name('tasklist.request-approval')->middleware('permission:approve');
    Route::get('history-approval/{id}', [TasklistController::class, 'showHistoryApproval'])->name('tasklist.history-approval');
});

#Monitoring
Route::middleware(['auth', 'ajax'])->prefix('monitoring')->group(function () {
    Route::get('/', [MonitoringController::class, 'index'])->name('transaction.monitoring')->middleware('permission:read');
    Route::get('/getMonitoring/{id}', [MonitoringController::class, 'renderMonitoring'])->name('monitoring.renderMonitoring');
    Route::post('/datatable', [MonitoringController::class, 'datatable'])->name('monitoring.datatable')->middleware('permission:datatable');
    Route::post('/datatableall', [MonitoringController::class, 'datatableall'])->name('monitoring.datatableall')->middleware('permission:datatable');
    Route::get('/progress/{id}', [MonitoringController::class, 'calculateProgress'])->name('monitoring.calculateProgress');
    Route::get('/chart/{id}', [MonitoringController::class, 'chart'])->name('monitoring.chart');
});

#Request
Route::middleware(['auth', 'ajax'])->prefix('request-team')->group(function () {
    Route::get('/add', [RequestController::class, 'create'])->name('request-team.add');
    Route::get('/edit/{id}', [RequestController::class, 'edit'])->name('request-team.edit');
    Route::get('/detail-pekerjaan/{karyawan_id}', [RequestController::class, 'getWorkDetail'])->name('request-team.detail-pekerjaan');
    Route::POST('/check-avail', [RequestController::class, 'checkAvail'])->name('request-team.check-avail');

    Route::get('/', [RequestController::class, 'index'])->name('transaction.request-team')->middleware('permission:read');
    Route::post('/datatable', [RequestController::class, 'datatable'])->name('request-team.datatable')->middleware('permission:datatable');
    Route::post('/reject/{id}', [RequestController::class, 'reject'])->name('request-team.reject')->middleware('permission:approve');
    Route::get('/approve/{id}', [RequestController::class, 'approve'])->name('request-team.approve')->middleware('permission:approve');
    Route::post('/store', [RequestController::class, 'store'])->name('request-team.store')->middleware('permission:add');
    Route::GET('/cancel/{id}', [RequestController::class, 'destroy'])->name('request-team.cancel')->middleware('permission:delete');
    Route::post('/update', [RequestController::class, 'update'])->name('request-team.update')->middleware('permission:update');
});

#Ticket
Route::middleware(['auth', 'ajax'])->prefix('request-ticket')->group(function () {
    Route::get('/add', [TicketController::class, 'create'])->name('request-ticket.add');
    Route::get('/edit/{id}', [TicketController::class, 'edit'])->name('request-ticket.edit');
    // Route::get('/detail-pekerjaan/{karyawan_id}', [TicketController::class, 'getWorkDetail'])->name('request-team.detail-pekerjaan');
    // Route::POST('/check-avail', [TicketController::class, 'checkAvail'])->name('request-ticket.check-avail');

    Route::get('/', [TicketController::class, 'index'])->name('transaction.request-ticket')->middleware('permission:read');
    Route::post('/datatable', [TicketController::class, 'datatable'])->name('request-ticket.datatable')->middleware('permission:datatable');
    Route::post('/datatable-reject', [TicketController::class, 'rejectedTicket'])->name('request-ticket.datatable')->middleware('permission:datatable');
    Route::post('/reject', [TicketController::class, 'reject'])->name('request-ticket.reject');
    Route::GET('/solved/{id}', [TicketController::class, 'solved'])->name('request-ticket.solved')->middleware('permission:approve');
    Route::post('/store', [TicketController::class, 'store'])->name('request-ticket.store')->middleware('permission:add');
    Route::GET('/cancel/{id}', [TicketController::class, 'destroy'])->name('request-ticket.cancel')->middleware('permission:delete');
    Route::post('/update', [TicketController::class, 'update'])->name('request-ticket.update')->middleware('permission:update');
    Route::get('/history-approval/{id}', [TicketController::class, 'showHistoryApproval']);
    Route::post('/request-approval/{id}', [TicketController::class, 'requestApproval'])->middleware('permission:approve');
});
