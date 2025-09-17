<?php

use Illuminate\Support\Facades\Route;
use Idoneo\HumanoAccessControl\Http\Controllers\RoleController;

// Static pages (initial phase) under auth
Route::middleware(['web', 'auth'])->group(function ()
{
	Route::get('/app/access-roles', [RoleController::class, 'index'])->name('app-access-roles');
	Route::get('/app/access-roles/data', [RoleController::class, 'data'])->name('app-access-roles.data');
	Route::get('/app/access-roles/users-data', [RoleController::class, 'usersData'])->name('app-access-roles.users-data');
	Route::get('/app/access-roles/{role}/permissions', [RoleController::class, 'permissions'])->name('app-access-roles.permissions');
	Route::post('/app/access-roles/{role}', [RoleController::class, 'update'])->name('app-access-roles.update');
	Route::view('/app/access-permission', 'humano-access-control::content.apps.app-access-permission')->name('app-access-permission');
});


