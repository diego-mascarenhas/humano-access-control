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
		$all = Permission::query()->orderBy('name')->pluck('name');
		$assigned = $role->permissions()->pluck('name');

		// Group by module prefix (before the first dot)
		$modules = [];
		foreach ($all as $perm)
		{
			$parts = explode('.', $perm);
			if (count($parts) < 2)
			{
				continue;
			}
			$module = $parts[0];
			$action = $parts[1];

			$modules[$module] = $modules[$module] ?? [
				'key' => $module,
				'readPerms' => [],
				'writePerms' => [],
				'createPerms' => [],
			];

			if (in_array($action, ['show', 'index', 'list', 'view']))
			{
				$modules[$module]['readPerms'][] = $perm;
			}
			if (in_array($action, ['show', 'edit', 'update', 'store']))
			{
				$modules[$module]['writePerms'][] = $perm;
			}
			if (in_array($action, ['create']))
			{
				$modules[$module]['createPerms'][] = $perm;
			}
		}

		// Compute checked flags
		$modules = array_values(array_map(function (array $m) use ($assigned)
		{
			$m['readChecked'] = ! empty($m['readPerms']) && collect($m['readPerms'])->every(fn ($p) => $assigned->contains($p));
			$m['writeChecked'] = ! empty($m['writePerms']) && collect($m['writePerms'])->every(fn ($p) => $assigned->contains($p));
			$m['createChecked'] = ! empty($m['createPerms']) && collect($m['createPerms'])->every(fn ($p) => $assigned->contains($p));
			return $m;
		}, $modules));

		return response()->json([
			'role' => [
				'id' => $role->id,
				'name' => $role->name,
			],
			'modules' => $modules,
		]);
	}

	public function update(Request $request, Role $role): JsonResponse
	{
		$data = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'modules' => ['array'],
			'modules.*.read' => ['boolean'],
			'modules.*.write' => ['boolean'],
			'modules.*.create' => ['boolean'],
			'modules.*.readPerms' => ['array'],
			'modules.*.writePerms' => ['array'],
			'modules.*.createPerms' => ['array'],
		]);

		$role->name = $data['name'];
		$role->save();

		$permissionsToSync = [];
		foreach (($data['modules'] ?? []) as $module)
		{
			if (! empty($module['read']))
			{
				$permissionsToSync = array_merge($permissionsToSync, $module['readPerms'] ?? []);
			}
			if (! empty($module['write']))
			{
				$permissionsToSync = array_merge($permissionsToSync, $module['writePerms'] ?? []);
			}
			if (! empty($module['create']))
			{
				$permissionsToSync = array_merge($permissionsToSync, $module['createPerms'] ?? []);
			}
		}

		$role->syncPermissions(array_values(array_unique($permissionsToSync)));

		return response()->json(['success' => true]);
	}
}


