<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class SettingController extends Controller
{

    public function getRoles(Request $request)
    {
        return view('contents.setting.roles', [
            'user' => Auth::user()
        ]);
    }

    public function getUsers(Request $request)
    {
        $role = Role::get(['name', 'id']);
        return view('contents.setting.users', [
            'user' => Auth::user(),
            'roles' => $role
        ]);
    }

    public function getCategories(Request $request)
    {
        // if (!$request->user()->can('view categories')) {
        //     return redirect()->route('dashboard')->with('error', 'You do not have permission to view categories.');
        // }
        $category = Category::get(['name', 'id']);
        return view('contents.setting.categories', [
            'user' => Auth::user(),
        ]);
    }
}
