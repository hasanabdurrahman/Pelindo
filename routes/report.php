<?php

use App\Http\Controllers\Module\Report\ProjectReportController;
use App\Http\Controllers\Module\Report\TimelineReportController;
use App\Http\Controllers\Module\Report\TasklistReportController;
use Illuminate\Support\Facades\Route;

#Project Report
Route::middleware(['auth', 'ajax'])->prefix('project-report')->group(function () {
    Route::get('/print/{filename}', [ProjectReportController::class, 'downloadFile']);
    Route::get('/', [ProjectReportController::class, 'index'])->name('report.project-report')->middleware('permission:read');
    Route::post('/data/{mode}', [ProjectReportController::class, 'getData'])->name('project-report.getData');
});

#Timeline Report
Route::middleware(['auth', 'ajax'])->prefix('timeline-report')->group(function () {
    Route::get('/print/{filename}', [TimelineReportController::class, 'downloadFile']);
    Route::get('/', [TimelineReportController::class, 'index'])->name('report.timeline-report')->middleware('permission:read');
    Route::post('/data/{mode}', [TimelineReportController::class, 'getData'])->name('timeline-report.getData');
});

#Tasklist Report
Route::middleware(['auth', 'ajax'])->prefix('tasklist-report')->group(function () {
    Route::get('/', [TasklistReportController::class, 'index'])->name('report.tasklist-report');
    Route::post('/', [TasklistReportController::class, 'preview'])->name('report.tasklist-preview');
    
});
Route::middleware(['auth'])->get('/export-pdf', [TasklistReportController::class, 'exportPdf'])->name('report.export-pdf');