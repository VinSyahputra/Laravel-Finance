<?php

namespace App\Http\Validations;

class CategoryValidation
{
    public static function store()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ];
    }

    public static function update()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ];
    }
}
