<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Http\Validations\CategoryValidation;
use App\Http\Validations\RoleValidation;
use App\Http\Validations\UserValidation;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    public function getUsers(Request $request)
    {
        $allowedSortFields = ['name', 'email', 'created_at', 'updated_at', 'deleted_at'];

        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })
            ->when($request->input('search'), function ($query) use ($request) {
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

        return UserResource::collection($users);
    }

    public function storeUser(Request $request)
    {
        // if (!$request->user()->can('create user')) {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        // Validate the input
        $validator = Validator::make($request->all(), UserValidation::store());

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Create the user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $role = Role::findById($request->role_id);
        $user->assignRole($role->name);

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'User created successfully',
            'data' => [
                'user' => $user->only(['id', 'name', 'email']),
                'roles' => $user->getRoleNames(),
            ],
        ], Response::HTTP_CREATED);
    }

    public function getUserById(Request $request, string $userId)
    {
        // if (!$request->user()->can('view user')) {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        // Validate the input
        $validator = Validator::make(['user_id' => $userId], [
            'user_id' => ['required', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Find the user
        $user = User::findOrFail($userId);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'User retrieved successfully',
            'data' => new UserResource($user),
        ], Response::HTTP_OK);
    }

    public function updateUser(Request $request, string $userId)
    {
        // if (!$request->user()->can('update user')) {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        // Validate the input
        $validator = Validator::make($request->all(), UserValidation::update($userId));

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Find the user
        $user = User::findOrFail($userId);

        // Update the user
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->filled('role_id')) {
            $role = Role::findById($request->role_id);
            $user->syncRoles($role->name);
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'User updated successfully',
            'data' => new UserResource($user),
        ], Response::HTTP_OK);
    }

    public function deleteUser(string $userId)
    {
        // if (!$request->user()->can('delete user')) {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        // Validate the input
        $validator = Validator::make(['user_id' => $userId], [
            'user_id' => ['required', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Find the user
        $user = User::findOrFail($userId);

        // Delete the user
        $user->delete();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'User deleted successfully',
        ], Response::HTTP_OK);
    }

    public function getCategories(Request $request)
    {
        // if (!$request->user()->can('view categories')) {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        $allowedSortFields = ['name', 'created_at', 'updated_at'];

        $categories = Category::when($request->input('search'), function ($query) use ($request) {
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

        return CategoryResource::collection($categories);
    }

    public function storeCategory(Request $request)
    {
        $validated = Validator::make($request->all(), CategoryValidation::store());
        if ($validated->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'status' => 'FAILED',
                'errors' => $validated->errors()->first(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category = Category::Create([
            'name' => $request->name
        ]);

        return response()->json([
            'data' => $category,
            'message' => 'Data saved successfully',
            'status' => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);
    }

    public function getCategoryById(Request $request, string $categoryId)
    {
        // if (!$request->user()->can('view categories')) {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        // Validate the input
        $validator = Validator::make(['category_id' => $categoryId], [
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Find the category
        $category = Category::findOrFail($categoryId);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Category retrieved successfully',
            'data' => new CategoryResource($category),
        ], Response::HTTP_OK);
    }

    public function updateCategory(Request $request, string $categoryId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Find the category
        $category = Category::findOrFail($categoryId);

        // Update the category
        $category->name = $request->name;
        $category->save();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category),
        ], Response::HTTP_OK);
    }

    public function deleteCategory(string $categoryId)
    {
        // if (!$request->user()->can('delete categories')) {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        // Validate the input
        $validator = Validator::make(['category_id' => $categoryId], [
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Find the category
        $category = Category::findOrFail($categoryId);

        // Delete the category
        $category->delete();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Category deleted successfully',
        ], Response::HTTP_OK);
    }
}
