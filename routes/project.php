<?php
use App\Http\Controllers\Module\MasterData\PhaseController;
use App\Http\Controllers\Module\MasterData\ClientController;
use App\Http\Controllers\Module\MasterData\ProjectController;
use App\Http\Controllers\Module\MasterData\EmployeeController;
use App\Http\Controllers\Module\MasterData\DivisionController;
use App\Http\Controllers\Module\Transaction\AdditionalTimelineController;
use App\Http\Controllers\Module\Transaction\TerminController;
use App\Http\Controllers\Module\Transaction\TimelineController;
use Illuminate\Support\Facades\Route;

## Client
Route::middleware(['auth', 'ajax'])->prefix('client')->group(function () {
    Route::get('/add', [ClientController::class, 'create'])->name('client.add');
    Route::get('/edit/{id}', [ClientController::class, 'edit'])->name('client.edit');
    Route::get('/', [ClientController::class, 'index'])->name('master-project.client')
        ->middleware('permission:read');
    Route::post('/datatable', [ClientController::class, 'datatable'])->name('client.datatable')
        ->middleware('permission:datatable');
    Route::post('/store', [ClientController::class, 'store'])->name('client.store')
        ->middleware('permission:add');
    Route::post('/update', [ClientController::class, 'update'])->name('client.update')
        ->middleware('permission:update');
    Route::delete('/delete/{id}', [ClientController::class, 'destroy'])->name('client.delete')
        ->middleware('permission:delete');
    Route::post('/active/{id}', [ClientController::class, 'active'])->name('client.active')
        ->middleware('permission:delete');
});

## Project
Route::middleware(['auth', 'ajax'])->prefix('project')->group(function () {
    Route::get('/add', [ProjectController::class, 'create'])->name('project.add');
    Route::get('/edit/{id}', [ProjectController::class, 'edit'])->name('project.edit');

    Route::get('/', [ProjectController::class, 'index'])->name('master-project.project')
    ->middleware('permission:read');
    Route::post('/datatable', [ProjectController::class, 'datatable'])->name('project.datatable')
    ->middleware('permission:datatable');
    Route::post('/store', [ProjectController::class, 'store'])->name('project.store')
    ->middleware('permission:add');
    Route::post('/update', [ProjectController::class, 'update'])->name('project.update')
    ->middleware('permission:update');
    Route::delete('/delete/{id}', [ProjectController::class, 'destroy'])->name('project.delete')
    ->middleware('permission:delete');
    Route::post('/active/{id}', [ProjectController::class, 'active'])->name('Project.active')
        ->middleware('permission:delete');

    Route::post('import', [ProjectController::class, 'import'])->name('project.import')->middleware('permission:add');;
});

##Timeline
Route::middleware(['auth', 'ajax'])->prefix('timeline')->group(function () {
    Route::get('/add/{project_id}', [TimelineController::class, 'create'])->name('timeline.add');
    Route::get('/edit/{trans_number}', [TimelineController::class, 'edit'])->name('timeline.edit');
    Route::get('/getTimeline/{id}', [TimelineController::class, 'renderTimeline'])->name('timeline.renderTimeline');
    Route::get('/render-form/{project_id}', [TimelineController::class, 'renderForm'])->name('timeline.renderForm');
    Route::get('/set-phase/{type}/{project_id}', [TimelineController::class, 'setPhase'])->name('timeline.setPhase');
    Route::get('/chart/{id}', [TimelineController::class, 'getDataChart'])->name('timeline.chart');

    Route::get('/', [TimelineController::class, 'index'])->name('master-project.timeline')->middleware('permission:read');
    Route::post('/store', [TimelineController::class, 'store'])->name('timeline.store')->middleware('permission:add');
    Route::post('/datatable', [TimelineController::class, 'datatable'])->name('timeline.datatable')->middleware('permission:datatable');
    Route::post('/update', [TimelineController::class, 'update'])->name('timeline.update')->middleware('permission:update');
    Route::get('/approve/{id}', [TimelineController::class, 'approve'])->name('timeline.approve')->middleware('permission:approve');
    Route::get('/close_action/{id}', [TimelineController::class, 'close_action'])->name('timeline.close_action');
    Route::get('/print/{type}/{id}', [TimelineController::class, 'exportFile'])->name('timeline.print')->middleware('permission:print');

    Route::post('/generate-template', [TimelineController::class, 'generateTemplate']);
    Route::get('/download-file/{filename}', [TimelineController::class, 'downloadTemplate']);
    Route::post('/import-timeline', [TimelineController::class, 'importTimeline']);
});

##Additional Timeline
Route::middleware(['auth', 'ajax'])->prefix('additional-timeline')->group(function () {
    Route::get('/add/{project_id}', [AdditionalTimelineController::class, 'create'])->name('additional-timeline.add');
    Route::get('/edit/{trans_number}', [AdditionalTimelineController::class, 'edit'])->name('additional-timeline.edit');
    Route::get('/getTimeline/{id}', [AdditionalTimelineController::class, 'renderTimeline'])->name('additional-timeline.renderTimeline');
    Route::get('/render-form/{project_id}', [AdditionalTimelineController::class, 'renderForm'])->name('additional-timeline.renderForm');
    Route::get('/set-phase/{type}/{project_id}', [AdditionalTimelineController::class, 'setPhase'])->name('additional-timeline.setPhase');
    Route::get('/chart/{id}', [AdditionalTimelineController::class, 'getDataChart'])->name('additional-timeline.chart');
    Route::get('show/{id}', [AdditionalTimelineController::class, 'show'])->name('additional-timeline.show');
    Route::post('/show-current-datatable', [AdditionalTimelineController::class, 'showCurrentDatatable'])->name('additional-timeline.current-datatable');

    Route::get('/', [AdditionalTimelineController::class, 'index'])->name('master-project.additional-timeline')->middleware('permission:read');
    Route::post('/store', [AdditionalTimelineController::class, 'store'])->name('additional-timeline.store')->middleware('permission:add');
    Route::post('/datatable', [AdditionalTimelineController::class, 'datatable'])->name('additional-timeline.datatable')->middleware('permission:datatable');
    Route::post('/update', [AdditionalTimelineController::class, 'update'])->name('additional-timeline.update')->middleware('permission:update');
    Route::get('/approve/{id}', [AdditionalTimelineController::class, 'approve'])->name('additional-timeline.approve')->middleware('permission:approve');
    Route::get('/set-default/{id}', [AdditionalTimelineController::class, 'setDefault'])->name('additional-timeline.set-default');
});


# Termin
Route::middleware(['auth', 'ajax'])->prefix('termin')->group(function(){
    Route::get('/', [TerminController::class, 'index'])->name('master-project.termin')->middleware('permission:read');
    Route::post('/data', [TerminController::class, 'datatable'])->name('termin.datatable')->middleware('permission:datatable');
    Route::post('/detail/{tn}', [TerminController::class, 'detailData'])->name('termin.detailData');

    Route::get('/add', [TerminController::class, 'create'])->name('termin.add');
    Route::get('/project-detail/{project_id}', [TerminController::class, 'projectDetail'])->name('termin.project-detail');
    Route::get('/render-form-add/{project_id}', [TerminController::class, 'renderFormAdd'])->name('termin.renderFormAdd');
    Route::post('/store', [TerminController::class, 'store'])->name('termin.store')->middleware('permission:add');
});

## Phase
Route::middleware(['auth', 'ajax'])->prefix('phase')->group(function(){
    Route::get('/', [PhaseController::class, 'index'])->name('master-project.phase')->middleware('permission:read');
    Route::get('/show/{id}', [PhaseController::class, 'show'])->name('phase.show');
    Route::get('/add', [PhaseController::class, 'create'])->name('phase.add');
    Route::post('/datatable', [PhaseController::class, 'datatable'])->name('phase.datatable')->middleware('permission:datatable');
    Route::post('/store', [PhaseController::class, 'store'])->name('phase.store')->middleware('permission:add');
    Route::get('/edit/{id}', [PhaseController::class, 'edit'])->name('phase.edit');
    Route::post('/update', [PhaseController::class, 'update'])->name('phase.update')->middleware('permission:update');
    Route::delete('/delete/{id}', [PhaseController::class, 'destroy'])->name('phase.delete')->middleware('permission:delete');
});
