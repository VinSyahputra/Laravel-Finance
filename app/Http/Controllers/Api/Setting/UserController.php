<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Validations\UserValidation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

    /**
     * Display the specified resource.
     */
    public function show(string $userId)
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $userId)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $userId)
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
}
