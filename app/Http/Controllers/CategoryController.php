<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {

        if (!$request->user()->can('view categories')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view categories.');
        }

        $user = Auth::user();
        $user->token = session('auth_token'); // Attach token dynamically

        return view('contents.category.index', compact('user'));
    }
}
