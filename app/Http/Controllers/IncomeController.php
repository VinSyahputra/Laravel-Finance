<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        // echo "<pre>";
        // var_dump(Auth::user()->getAllPermissions()->pluck('name'));
        // var_dump(Auth::user()->getRoleNames());
        // die();
        return view('contents.income.index', [
            'user' => Auth::user()
        ]);
    }
}
