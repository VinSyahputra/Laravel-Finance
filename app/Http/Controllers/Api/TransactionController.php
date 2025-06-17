<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TransactionResource;
use App\Models\Category;
use App\Http\Validations\CategoryValidation;
use App\Http\Validations\TransactionValidation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_id = Auth::user()->id;

        $allowedSortFields = ['description', 'created_at', 'updated_at'];

        $transactions = Transaction::with('category')
            ->when($request->input('search'), function ($query) use ($request) {
                $search = $request->input('search');
                return $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%");
                });
            })
            // Filter by specific category if provided
            ->when($request->input('category_id'), function ($query) use ($request) {
                $categoryId = $request->input('category_id');
                return $query->where('category_id', $categoryId);
            })
            ->when($request->input('type'), function ($query) use ($request) {
                $type = $request->input('type');
                return $query->where('type', $type);
            })
            ->when($request->input('month'), function ($query) use ($request) {
                return $query->whereMonth('date', $request->input('month'));
            })
            ->when($request->input('year'), function ($query) use ($request) {
                return $query->whereYear('date', $request->input('year'));
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('input_by', $user_id);
            })
            ->when($request->input('sort_by') && in_array($request->input('sort_by'), $allowedSortFields), function ($query) use ($request) {
                $sortBy = $request->input('sort_by');
                $sortDirection = $request->input('sort_direction', 'asc');
                return $query->orderBy($sortBy, $sortDirection);
            }, function ($query) {
                return $query->orderBy('date', 'asc');
            })
            ->paginate($request->input('per_page', 10));

        return TransactionResource::collection($transactions);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = Validator::make($request->all(), TransactionValidation::store());
        if ($validated->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'status' => 'FAILED',
                'errors' => $validated->errors()->first(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category = Transaction::Create([
            'date' => $request->date,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'amount' => (int) str_replace('.', '', $request->amount),
            'input_by' => $request->user_id,
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
    public function update(Request $request, string $transactionId)
    {
        try {
            // Find the transaction by its ID
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                return response()->json([
                    "code" => 404,
                    "status" => "FAILED",
                    'message' => 'Not Found',
                    'errors'  => 'ID not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Prepare request data before validation
            $input = $request->all();
            if (isset($input['amount'])) {
                $input['amount'] = (int) str_replace('.', '', $input['amount']);
            }
            // Perform validation
            $validated = Validator::make($input, TransactionValidation::update());

            if ($validated->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'status' => 'FAILED',
                    'errors' => $validated->errors(), // Return all validation errors
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Update the transaction with validated data
            $transaction->update($validated->validated());

            return response()->json([
                'data' => $transaction,
                'message' => 'Data updated successfully',
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
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
    public function destroy(string $transactionId)
    {
        try {
            DB::transaction(function () use ($transactionId) {
                $transaction = Transaction::where('id', $transactionId)->first();

                if (!$transaction) {
                    throw new \Exception('Transaction not found');
                }

                $transaction->delete();
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

    public function getDataByMonth(string $userId)
    {
        try {
            $transaction = Transaction::where('id', $userId)->get();
            if (!$transaction)
                return $this->responseError(null, 'Not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->responseError(null, 'Not found', Response::HTTP_NOT_FOUND);
        }
    }
}
