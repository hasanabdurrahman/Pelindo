<?php
use App\Http\Controllers\Module\MasterData\PhaseController;
use App\Http\Controllers\Module\MasterData\ClientController;
use App\Http\Controllers\Module\MasterData\ProjectController;
use App\Http\Controllers\Module\MasterData\EmployeeController;
use App\Http\Controllers\Module\MasterData\DivisionController;
use Illuminate\Support\Facades\Route;

#employee
Route::middleware(['auth', 'ajax'])->prefix('employee')->group(function () {
    Route::get('/add', [EmployeeController::class, 'create'])->name('employee.add');
    Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('employee.edit');

    Route::get('/', [EmployeeController::class, 'index'])->name('masterdata.employee')
    ->middleware('permission:read');
    Route::post('/datatable', [EmployeeController::class, 'datatable'])->name('employee.datatable')
    ->middleware('permission:datatable');
    Route::post('/store', [EmployeeController::class, 'store'])->name('employee.store')
    ->middleware('permission:add');
    Route::post('/update', [EmployeeController::class, 'update'])->name('employee.update')
    ->middleware('permission:update');
    Route::delete('/delete/{id}', [EmployeeController::class, 'destroy'])->name('employee.delete')
    ->middleware('permission:delete');
    Route::post('/reset/{id}', [EmployeeController::class, 'reset'])->name('employee.reset');
    // ->middleware('permission:update');
    Route::post('/active/{id}', [EmployeeController::class, 'active'])->name('employee.active')
    ->middleware('permission:delete');
});

#Division
Route::middleware(['auth', 'ajax'])->prefix('division')->group(function () {
    Route::get('/add', [DivisionController::class, 'create'])->name('division.add');
    Route::get('/edit/{id}', [DivisionController::class, 'edit'])->name('division.edit');

    Route::get('/', [DivisionController::class, 'index'])->name('masterdata.division')
    ->middleware('permission:read');
    Route::post('/datatable', [DivisionController::class, 'datatable'])->name('division.datatable')
    ->middleware('permission:datatable');
    Route::post('/store', [DivisionController::class, 'store'])->name('division.store')
    ->middleware('permission:add');
    Route::post('/update', [DivisionController::class, 'update'])->name('division.update')
    ->middleware('permission:update');
    Route::delete('/delete/{id}', [DivisionController::class, 'destroy'])->name('division.delete')
    ->middleware('permission:delete');
    Route::post('/active/{id}', [DivisionController::class, 'active'])->name('division.active')
    ->middleware('permission:delete');
});

## Phase
Route::middleware(['auth', 'ajax'])->prefix('phase')->group(function(){
    Route::get('/', [PhaseController::class, 'index'])->name('masterdata.phase')->middleware('permission:read');
    Route::get('/show/{id}', [PhaseController::class, 'show'])->name('phase.show');
    Route::get('/add', [PhaseController::class, 'create'])->name('phase.add');
    Route::post('/datatable', [PhaseController::class, 'datatable'])->name('phase.datatable')->middleware('permission:datatable');
    Route::post('/store', [PhaseController::class, 'store'])->name('phase.store')->middleware('permission:add');
    Route::get('/edit/{id}', [PhaseController::class, 'edit'])->name('phase.edit');
    Route::post('/update', [PhaseController::class, 'update'])->name('phase.update')->middleware('permission:update');
    Route::delete('/delete/{id}', [PhaseController::class, 'destroy'])->name('phase.delete')->middleware('permission:delete');
});
