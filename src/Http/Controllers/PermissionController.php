<?php

namespace Idoneo\HumanoAccessControl\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController
{
	public function index()
	{
		return view('humano-access-control::content.apps.app-access-permission');
	}

	public function data(Request $request)
	{
		$query = Permission::query()->select(['id', 'name', 'guard_name', 'created_at']);

		return DataTables::of($query)
			->addColumn('actions', function (Permission $permission)
			{
				return view('humano-access-control::components.permissions.actions', compact('permission'))->render();
			})
			->toJson();
	}
}


