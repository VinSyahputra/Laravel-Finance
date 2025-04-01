<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticController extends Controller
{
    //

    public function getDataThisYear(Request $request)
    {
        $userId = Auth::user()->id;

        $dataLastYear = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->subYear()->year)
            ->sum('amount'); // Get total amount from last year

        $dataCurrent = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->year)
            ->sum('amount'); // Get total amount from this year

        if ($dataLastYear == 0) {
            $percentageChange = $dataCurrent > 0 ? 100 : 0; // If last year was 0, avoid division by zero
            $status = $dataCurrent > 0 ? 'up' : '-';
        } else {
            $percentageChange = (($dataCurrent - $dataLastYear) / abs($dataLastYear)) * 100;
            $status = ($dataCurrent > $dataLastYear) ? 'up' : 'down';
        }

        // Remove decimal places if percentage is a whole number
        $formattedPercentage = fmod($percentageChange, 1) == 0
            ? intval($percentageChange) // Convert to integer if whole number
            : number_format($percentageChange, 2, '.', ''); // Otherwise, keep 2 decimals

        $result = [
            'total_amount' => $dataCurrent,
            'percentage' => $formattedPercentage,
            'status' => $status, // Added status field
        ];

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $result,
        ], 200);
    }



    public function getDataRecentTransactions(Request $request)
    {
        $userId = Auth::user()->id;

        $transactions = Transaction::with('category', 'user')
            ->where('input_by', $userId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $transactions,
        ], 200);
    }
}
