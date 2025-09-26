<?php

use Idoneo\HumanoAccessControl\Http\Controllers\PermissionController;
use Idoneo\HumanoAccessControl\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Static pages (initial phase) under auth
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/app/access-roles', [RoleController::class, 'index'])->name('app-access-roles');
    Route::get('/app/access-roles/data', [RoleController::class, 'data'])->name('app-access-roles.data');
    Route::get('/app/access-roles/list-data', [RoleController::class, 'listData'])->name('app-access-roles.list-data');
    Route::post('/app/access-roles', [RoleController::class, 'store'])->name('app-access-roles.store');
    Route::get('/app/access-roles/users-data', [RoleController::class, 'usersData'])->name('app-access-roles.users-data');
    Route::get('/app/access-roles/{role}/permissions', [RoleController::class, 'permissions'])->name('app-access-roles.permissions');
    Route::get('/app/access-roles/permissions-template', [RoleController::class, 'permissionsTemplate'])->name('app-access-roles.permissions-template');
    Route::post('/app/access-roles/{role}', [RoleController::class, 'update'])->name('app-access-roles.update');
    Route::get('/app/access-permission', [PermissionController::class, 'index'])->name('app-access-permission');
    Route::get('/app/access-permission/data', [PermissionController::class, 'data'])->name('app-access-permission.data');
});
