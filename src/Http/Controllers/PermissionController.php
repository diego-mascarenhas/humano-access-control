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
        $query = Permission::query()
            ->with('roles:id,name')
            ->select(['id', 'name', 'guard_name', 'created_at']);

        return DataTables::of($query)
            ->addColumn('assigned_to', function (Permission $permission) {
                if ($permission->roles->isEmpty()) {
                    return '<span class="text-muted">-</span>';
                }

                /** @var \Illuminate\Support\Collection<int,\Spatie\Permission\Models\Role> $roles */
                $roles = $permission->roles;

                return $roles->map(function (\Spatie\Permission\Models\Role $role) {
                    return '<span class="badge bg-label-secondary me-1">'.e($role->name).'</span>';
                })->implode(' ');
            })
            ->addColumn('actions', function (Permission $permission) {
                return view('humano-access-control::components.permissions.actions', compact('permission'))->render();
            })
            ->rawColumns(['assigned_to', 'actions'])
            ->toJson();
    }
}
