<?php

namespace App\Http\Validations;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password as RulesPassword;

class UserValidation
{
    public static function store()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
        ];
    }

    public static function update($userId)
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($userId),],
            'password' => ['nullable', 'confirmed', RulesPassword::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
        ];
    }
}
