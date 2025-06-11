<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $allowedSortFields = ['name', 'created_at', 'updated_at'];

        $permissions = Permission::when($request->input('search'), function ($query) use ($request) {
            $search = $request->input('search');
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
            ->when($request->input('sort_by') && in_array($request->input('sort_by'), $allowedSortFields), function ($query) use ($request) {
                $sortBy = $request->input('sort_by');
                $sortDirection = $request->input('sort_direction', 'asc');
                return $query->orderBy($sortBy, $sortDirection);
            }, function ($query) {
                return $query->orderBy('name', 'asc');
            })
            ->paginate($request->input('per_page', 10));

        return RoleResource::collection($permissions);
    }

    public function show(Request $request, $roleId)
    {
        $role = Role::findById($roleId);

        if (!$role) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Role not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $allowedSortFields = ['name', 'created_at', 'updated_at'];

        $permissions = $role->permissions()->when($request->input('search'), function ($query) use ($request) {
            $search = $request->input('search');
            return $query->where('name', 'like', "%{$search}%");
        })
            ->when($request->input('sort_by') && in_array($request->input('sort_by'), $allowedSortFields), function ($query) use ($request) {
                $sortBy = $request->input('sort_by');
                $sortDirection = $request->input('sort_direction', 'asc');
                return $query->orderBy($sortBy, $sortDirection);
            }, function ($query) {
                return $query->orderBy('name', 'asc');
            })
            ->paginate($request->input('per_page', 10));

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Role and permissions retrieved successfully',
            'data' => [
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                ],
                'permissions' => RoleResource::collection($permissions),
            ],
        ], Response::HTTP_OK);
    }

}
