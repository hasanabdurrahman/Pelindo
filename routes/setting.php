<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Module\Setting\MenuController;
use App\Http\Controllers\Module\Setting\PermissionController;
use App\Http\Controllers\Module\Setting\RolesController;
use App\Models\Setting\Permission;

#Menu
Route::middleware(['auth', 'ajax'])->prefix('menu')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('setting.menu');
    Route::post('/data', [MenuController::class, 'data'])->name('menu.data');
    Route::get('/add', [MenuController::class, 'create'])->name('menu.add');
    Route::post('/store', [MenuController::class, 'store'])->name('menu.store');
    Route::get('/edit/{id}', [MenuController::class, 'edit'])->name('menu.edit');
    Route::post('/update', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/delete/{id}', [MenuController::class, 'destroy'])->name('menu.delete');
});

#Roles
Route::middleware(['auth', 'ajax'])->prefix('roles')->group(function () {
    Route::get('/', [RolesController::class, 'index'])->name('setting.roles');
    Route::post('/datatable', [RolesController::class, 'datatable'])->name('roles.datatable');
    Route::post('/get-menu-data', [RolesController::class, 'getMenuData'])->name('roles.getMenuData');
    Route::get('/add', [RolesController::class, 'create'])->name('roles.add');
    Route::post('/store', [RolesController::class, 'store'])->name('roles.store');
    Route::get('/edit/{id}', [RolesController::class, 'edit'])->name('roles.edit');
    Route::post('/update', [RolesController::class, 'update'])->name('roles.update');
    Route::delete('/delete/{id}', [RolesController::class, 'destroy'])->name('roles.delete');
    Route::delete('/active/{id}', [RolesController::class, 'active'])->name('roles.active');
});

#permission
Route::middleware(['auth', 'ajax'])->prefix('permission')->group(function(){
    Route::get('/', [PermissionController::class, 'index'])->name('setting.permission');
    Route::get('/create', [PermissionController::class, 'create'])->name('permission.create');
    Route::post('/store', [PermissionController::class, 'store'])->name('permission.store');
    Route::post('/set-permission', [PermissionController::class, 'setPermission'])->name('permission.setPermission');
    // GET DATA SOURCE
    Route::get('/all-roles/{id_menu}', [PermissionController::class, 'getAllRoles'])->name('permission.getAllRoles');
});