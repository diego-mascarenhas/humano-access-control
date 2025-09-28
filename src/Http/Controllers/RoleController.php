<?php

namespace Idoneo\HumanoAccessControl\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class RoleController
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get()
            ->map(function (Role $role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'users_count' => $role->users_count,
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
            ->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get()
            ->map(function (Role $role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'users_count' => $role->users_count,
                    'permissions_count' => $role->permissions_count,
                ];
            });

        return response()->json([
            'data' => $roles,
        ]);
    }

    public function usersData(): JsonResponse
    {
        /** @var \Illuminate\Database\Eloquent\Builder $usersQuery */
        $usersQuery = \App\Models\User::query();
        $users = $usersQuery
            ->with('roles:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'deleted_at', 'email_verified_at'])
            ->map(function ($user): array {
                $deleted = data_get($user, 'deleted_at');
                $verified = data_get($user, 'email_verified_at');
                $status = $deleted ? 'Inactive' : ($verified ? 'Active' : 'Pending');

                return [
                    'name' => (string) data_get($user, 'name', ''),
                    'email' => (string) data_get($user, 'email', ''),
                    'role' => (string) data_get($user, 'roles.0.name', '-'),
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
        foreach ($all as $perm) {
            $parts = explode('.', $perm);
            if (count($parts) < 2) {
                continue;
            }
            $module = $parts[0];
            $action = $parts[1];

            // Compute human label (DB translations supported via app helper)
            $label = \App\Helpers\TranslationHelper::transGroup($module, 'modules');
            if ($label === 'modules.'.$module) {
                $label = ucfirst($module);
            }

            $modules[$module] = $modules[$module] ?? [
                'key' => $module,
                'label' => $label,
                'readPerms' => [],
                'writePerms' => [],
                'createPerms' => [],
                'deletePerms' => [],
            ];

            if (in_array($action, ['show', 'index', 'list', 'view'])) {
                $modules[$module]['readPerms'][] = $perm;
            }
            // Update (write): only edit/update
            if (in_array($action, ['edit', 'update'])) {
                $modules[$module]['writePerms'][] = $perm;
            }
            // Create: create + store
            if (in_array($action, ['create', 'store'])) {
                $modules[$module]['createPerms'][] = $perm;
            }
            if (in_array($action, ['destroy', 'delete', 'remove'])) {
                $modules[$module]['deletePerms'][] = $perm;
            }
        }

        // Compute checked flags
        $modules = array_values(array_map(function (array $m) use ($assigned) {
            $m['readChecked'] = ! empty($m['readPerms']) && collect($m['readPerms'])->every(fn ($p) => $assigned->contains($p));
            $m['writeChecked'] = ! empty($m['writePerms']) && collect($m['writePerms'])->every(fn ($p) => $assigned->contains($p));
            $m['createChecked'] = ! empty($m['createPerms']) && collect($m['createPerms'])->every(fn ($p) => $assigned->contains($p));
            $m['deleteChecked'] = ! empty($m['deletePerms']) && collect($m['deletePerms'])->every(fn ($p) => $assigned->contains($p));

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

    /**
     * Yajra DataTables: Roles list (name, users_count, permissions_count)
     */
    public function listData(Request $request)
    {
        $query = Role::query()
            ->select(['id', 'name'])
            ->withCount(['permissions', 'users'])
            ->orderBy('name');

        return DataTables::of($query)->toJson();
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
            'modules.*.delete' => ['boolean'],
            'modules.*.deletePerms' => ['array'],
        ]);

        $role->name = $data['name'];
        $role->save();

        $permissionsToSync = [];
        foreach (($data['modules'] ?? []) as $module) {
            if (! empty($module['read'])) {
                $permissionsToSync = array_merge($permissionsToSync, $module['readPerms'] ?? []);
            }
            if (! empty($module['write'])) {
                $permissionsToSync = array_merge($permissionsToSync, $module['writePerms'] ?? []);
            }
            if (! empty($module['create'])) {
                $permissionsToSync = array_merge($permissionsToSync, $module['createPerms'] ?? []);
            }
            if (! empty($module['delete'])) {
                $permissionsToSync = array_merge($permissionsToSync, $module['deletePerms'] ?? []);
            }
        }

        $role->syncPermissions(array_values(array_unique($permissionsToSync)));

        return response()->json(['success' => true]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ]);

        $role = Role::create(['name' => $data['name']]);

        return response()->json(['success' => true, 'id' => $role->id]);
    }

    public function permissionsTemplate(): JsonResponse
    {
        // Reuse permissions() grouping but without a specific role (empty assigned)
        $all = Permission::query()->orderBy('name')->pluck('name');
        $assigned = collect();
        $modules = [];
        foreach ($all as $perm) {
            $parts = explode('.', $perm);
            if (count($parts) < 2) {
                continue;
            }
            $module = $parts[0];
            $action = $parts[1];
            $modules[$module] = $modules[$module] ?? ['key' => $module, 'readPerms' => [], 'writePerms' => [], 'createPerms' => []];
            if (in_array($action, ['show', 'index', 'list', 'view'])) {
                $modules[$module]['readPerms'][] = $perm;
            }
            if (in_array($action, ['show', 'edit', 'update', 'store'])) {
                $modules[$module]['writePerms'][] = $perm;
            }
            if ($action === 'create') {
                $modules[$module]['createPerms'][] = $perm;
            }
        }
        $modules = array_values(array_map(function (array $m) {
            $m['readChecked'] = false;
            $m['writeChecked'] = false;
            $m['createChecked'] = false;

            return $m;
        }, $modules));

        return response()->json(['modules' => $modules]);
    }
}
