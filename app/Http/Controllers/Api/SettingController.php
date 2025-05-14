<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Validations\RoleValidation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettingController extends Controller
{
    public function getRoles(Request $request)
    {
        $allowedSortFields = ['name', 'created_at', 'updated_at'];

        $categories = Role::when($request->input('search'), function ($query) use ($request) {
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

        return RoleResource::collection($categories);
    }

    public function getPermissions(Request $request)
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

    public function getPermissionsByRole(Request $request, $roleId)
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


    public function storeRole(Request $request)
    {
        // if (!$request->user()->can('create role')) {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        // Validate the input
        $validated = Validator::make($request->all(), RoleValidation::store());

        if ($validated->fails()) return $this->responseError($validated->errors(), 'The given parameter was invalid', Response::HTTP_UNPROCESSABLE_ENTITY);

        // Create the role
        $role = Role::create([
            'name' => $request->role_name,
        ]);
        $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
        // Assign permissions using IDs
        $role->syncPermissions($permissionNames);

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Role and permissions created successfully',
            'data' => [
                'role' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ],
        ], Response::HTTP_CREATED);
    }

    public function updateRole(Request $request, string $roleId)
    {
        $role = Role::findById($roleId);
        if (!$role) {
            return response()->json([
                "code" => 404,
                "status" => "FAILED",
                'message' => 'Not Found',
                'errors'  => 'ID not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = Validator::make($request->all(), RoleValidation::update());

        if ($validated->fails()) return $this->responseError($validated->errors(), 'The given parameter was invalid', Response::HTTP_UNPROCESSABLE_ENTITY);

        // Find the role
        $role = Role::findById($request->roleId);

        if (!$role) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Role not found',
            ], Response::HTTP_NOT_FOUND);
        }

        // Update the role
        $role->name = $request->role_name;
        $role->save();

        $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
        $role->syncPermissions($permissionNames);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Role updated successfully',
            'data' => [
                'role' => $role,
            ],
        ], Response::HTTP_OK);
    }

    public function deleteRole(string $roleId)
    {
        try {
            DB::beginTransaction();
    
            // Use find() instead of findById() for better compatibility
            $role = Role::find($roleId);
            
            if (!$role) {
                return response()->json([
                    "code" => 404,
                    "status" => "FAILED",
                    'message' => 'Role not found',
                    'errors' => ['id' => 'Role ID not found']
                ], Response::HTTP_NOT_FOUND);
            }
    
            // First detach all permissions
            $role->permissions()->detach();
    
            // Then detach from all users
            $role->users()->detach();
    
            // Finally delete the role
            $role->delete();
    
            DB::commit();
            
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Role deleted successfully',
            ], Response::HTTP_OK);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                "code" => 500,
                "status" => "ERROR",
                'message' => 'Failed to delete role',
                'errors' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
