<?php

namespace Idoneo\HumanoAccessControl\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController
{
    public function index(): View
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

        return view('humano-access-control::content.apps.app-access-roles', [
            'roles' => $roles,
        ]);
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

	public function usersData(): JsonResponse
	{
		$users = User::query()
			->with('roles:id,name')
			->orderBy('name')
			->get()
			->map(function (User $user)
			{
				$status = 'Pending';
				if ($user->deleted_at)
				{
					$status = 'Inactive';
				}
				elseif ($user->email_verified_at)
				{
					$status = 'Active';
				}

				return [
					'name' => $user->name,
					'email' => $user->email,
					'role' => $user->roles->pluck('name')->first() ?? '-',
					'status' => $status,
				];
			});

		return response()->json([
			'data' => $users,
		]);
	}

	public function permissions(Role $role): JsonResponse
	{
		$allPermissions = Permission::query()->orderBy('name')->get()->pluck('name');
		$assigned = $role->permissions()->pluck('name');

		$items = $allPermissions->map(function ($name) use ($assigned)
		{
			return [
				'name' => $name,
				'assigned' => $assigned->contains($name),
			];
		});

		return response()->json([
			'role' => [
				'id' => $role->id,
				'name' => $role->name,
			],
			'permissions' => $items,
		]);
	}

	public function update(Request $request, Role $role): JsonResponse
	{
		$data = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'permissions' => ['array'],
			'permissions.*' => ['string'],
		]);

		$role->name = $data['name'];
		$role->save();
		$role->syncPermissions($data['permissions'] ?? []);

		return response()->json(['success' => true]);
	}
}


