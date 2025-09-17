<?php

use Illuminate\Support\Facades\Route;

// Static pages (initial phase) under auth
Route::middleware(['web', 'auth'])->group(function ()
{
	Route::view('/app/access-roles', 'humano-access-control::content.apps.app-access-roles')->name('app-access-roles');
	Route::view('/app/access-permission', 'humano-access-control::content.apps.app-access-permission')->name('app-access-permission');
});


