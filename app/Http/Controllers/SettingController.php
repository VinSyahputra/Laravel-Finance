<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{

    public function getRoles(Request $request)
    {
        return view('contents.setting.roles', [
            'user' => Auth::user()
        ]);
    }
}
