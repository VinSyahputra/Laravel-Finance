<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Validations\CategoryValidation;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $categoryId)
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


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $categoryId)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $categoryId)
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
