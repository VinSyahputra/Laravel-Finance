<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        return view('contents.transaction.index', [
            'user' => Auth::user()
        ]);
    }
}
