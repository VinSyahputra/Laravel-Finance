<?php

namespace App\Http\Validations;

class RoleValidation
{
    public static function store()
    {
        return [
            'role_name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public static function update()
    {
        return [
            'role_name' => 'required', 'string', 'max:255', 'unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }
}
