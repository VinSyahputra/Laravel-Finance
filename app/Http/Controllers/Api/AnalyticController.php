<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticController extends Controller
{
    //

    public function getBalance(Request $request)
    {
        $userId = Auth::user()->id;
        $type = $request->type;

        // Build base expense query
        $expenseQuery = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->year)
            ->where('type', 'expense');

        if ($type === 'month') {
            $expenseQuery->whereMonth('date', now()->month);
        }

        $dataExpense = $expenseQuery->sum('amount');

        // Build base income query
        $incomeQuery = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->year)
            ->where('type', 'income');

        if ($type === 'month') {
            $incomeQuery->whereMonth('date', now()->month);
        }

        $dataIncome = $incomeQuery->sum('amount');

        // Determine status
        if ($dataExpense == 0) {
            $status = $dataIncome > 0 ? 'high' : '-';
        } else {
            $status = ($dataIncome > $dataExpense) ? 'high' : 'low';
        }

        // Balance calculation
        $balance = $dataIncome - $dataExpense;

        // Percentage calculation with proper formatting
        if ($dataIncome > 0) {
            $rawPercentage = ($balance / $dataIncome) * 100;
        } else {
            $rawPercentage = $balance < 0 ? -100 : 0;
        }

        $percentage = fmod($rawPercentage, 1) == 0
            ? (int) $rawPercentage
            : number_format($rawPercentage, 2, '.', '');

        // Prepare result
        $result = [
            'total_balance' => $balance,
            'percentage' => $percentage,
            'status' => $status,
        ];

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $result,
        ], 200);
    }



    public function getDataExpenseThisYear(Request $request)
    {
        $userId = Auth::user()->id;

        $dataLastYear = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->subYear()->year)
            ->where('type', 'expense')
            ->sum('amount'); // Get total amount from last year

        $dataCurrent = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->year)
            ->where('type', 'expense')
            ->sum('amount'); // Get total amount from this year

        if ($dataLastYear == 0) {
            $percentageChange = $dataCurrent > 0 ? 100 : 0; // If last year was 0, avoid division by zero
            $status = $dataCurrent > 0 ? 'high' : '-';
        } else {
            $percentageChange = (($dataCurrent - $dataLastYear) / abs($dataLastYear)) * 100;
            $status = ($dataCurrent > $dataLastYear) ? 'high' : 'low';
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

    public function getDataIncomeThisYear(Request $request)
    {
        $userId = Auth::user()->id;

        $dataLastYear = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->subYear()->year)
            ->where('type', 'income')
            ->sum('amount'); // Get total amount from last year

        $dataCurrent = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->year)
            ->where('type', 'income')
            ->sum('amount'); // Get total amount from this year

        if ($dataLastYear == 0) {
            $percentageChange = $dataCurrent > 0 ? 100 : 0; // If last year was 0, avoid division by zero
            $status = $dataCurrent > 0 ? 'high' : '-';
        } else {
            $percentageChange = (($dataCurrent - $dataLastYear) / abs($dataLastYear)) * 100;
            $status = ($dataCurrent > $dataLastYear) ? 'high' : 'low';
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

    public function getDataExpenseThisMonth(Request $request)
    {
        $userId = Auth::user()->id;

        $dataLastMonth = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->subMonth()->month)
            ->where('type', 'expense')
            ->sum('amount'); // Get total amount from last year

        $dataThisMonth = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->where('type', 'expense')
            ->sum('amount'); // Get total amount from this year

        if ($dataLastMonth == 0) {
            $percentageChange = $dataThisMonth > 0 ? 100 : 0; // If last year was 0, avoid division by zero
            $status = $dataThisMonth > 0 ? 'high' : '-';
        } else {
            $percentageChange = (($dataThisMonth - $dataLastMonth) / abs($dataLastMonth)) * 100;
            $status = ($dataThisMonth > $dataLastMonth) ? 'high' : 'low';
        }

        // Remove decimal places if percentage is a whole number
        $formattedPercentage = fmod($percentageChange, 1) == 0
            ? intval($percentageChange) // Convert to integer if whole number
            : number_format($percentageChange, 2, '.', ''); // Otherwise, keep 2 decimals

        $result = [
            'total_amount' => $dataThisMonth,
            'percentage' => $formattedPercentage,
            'status' => $status, // Added status field
        ];

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $result,
        ], 200);
    }

    public function getDataIncomeThisMonth(Request $request)
    {
        $userId = Auth::user()->id;

        $dataLastYear = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->subYear()->year)
            ->where('type', 'income')
            ->sum('amount'); // Get total amount from last year

        $dataCurrent = Transaction::where('input_by', $userId)
            ->whereYear('date', now()->year)
            ->where('type', 'income')
            ->sum('amount'); // Get total amount from this year

        if ($dataLastYear == 0) {
            $percentageChange = $dataCurrent > 0 ? 100 : 0; // If last year was 0, avoid division by zero
            $status = $dataCurrent > 0 ? 'high' : '-';
        } else {
            $percentageChange = (($dataCurrent - $dataLastYear) / abs($dataLastYear)) * 100;
            $status = ($dataCurrent > $dataLastYear) ? 'high' : 'low';
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



    public function getDataRecentExpenses(Request $request)
    {
        $userId = Auth::user()->id;

        $transactions = Transaction::with('category', 'user')
            ->where('input_by', $userId)
            ->where('type', 'expense')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $transactions,
        ], 200);
    }

    public function getDataRecentIncomes(Request $request)
    {
        $userId = Auth::user()->id;

        $transactions = Transaction::with('category', 'user')
            ->where('input_by', $userId)
            ->where('type', 'income')
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
