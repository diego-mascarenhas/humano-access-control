<?php

namespace Idoneo\HumanoAccessControl\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController
{
	public function index(): View
	{
		return view('humano-access-control::content.apps.app-access-roles');
	}

	public function data(): JsonResponse
	{
		$roles = Role::query()
			->withCount('permissions')
			->orderBy('name')
			->get()
			->map(function (Role $role)
			{
				$usersCount = DB::table('model_has_roles')
					->where('role_id', $role->id)
					->count();

				return [
					'id' => $role->id,
					'name' => $role->name,
					'users_count' => $usersCount,
					'permissions_count' => $role->permissions_count,
				];
			});

		return response()->json([
			'data' => $roles,
		]);
	}
}


