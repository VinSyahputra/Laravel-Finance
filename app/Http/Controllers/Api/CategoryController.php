<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Http\Validations\CategoryValidation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

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
    public function show(string $serviceId)
    {
        // try {
        //     $service = Service::where('id', $serviceId)->first();
        //     if (!$service)
        //         return $this->responseError(null, 'Not found', Response::HTTP_NOT_FOUND);

        //     return $this->responseSuccess(new ServiceResource($service), 'Get detail');
        // } catch (\Exception $e) {
        //     return $this->responseError(null, 'Not found', Response::HTTP_NOT_FOUND);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $categoryId)
    {
        try {
            // Find the category by its ID
            $category = Category::where('id', $categoryId)->first();

            if (!$category) {
                return response()->json([
                    "code" => 404,
                    "status" => "FAILED",
                    'message' => 'Not Found',
                    'errors'  => 'ID not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Perform validation
            $validated = Validator::make($request->all(), CategoryValidation::update());

            if ($validated->fails()) {
                // Return all validation errors, not just the first one
                return response()->json([
                    'message' => 'Validation failed.',
                    'status' => 'FAILED',
                    'errors' => $validated->errors()->first(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Update the category with validated data
            $category->update($validated->validated()); // Use validated data, not the raw request

            // Return success response
            return response()->json([
                'data' => $category,
                'message' => 'Data updated successfully',
                'status' => Response::HTTP_OK // Changed to HTTP_OK for success
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            // Return internal server error on exceptions
            return response()->json([
                "code" => 500,
                "status" => "FAILED",
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $categoryId)
    {
        try {
            DB::transaction(function () use ($categoryId) {
                $category = Category::where('id', $categoryId)->first();

                if (!$category) {
                    throw new \Exception('Category not found');
                }

                $category->delete();
            });

            // Return success response
            return response()->json([
                'message' => 'Data deleted successfully',
                'status' => Response::HTTP_OK // Changed to HTTP_OK for success
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            // Return internal server error on exceptions
            return response()->json([
                "code" => 500,
                "status" => "FAILED",
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
