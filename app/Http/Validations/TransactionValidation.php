<?php

namespace App\Http\Validations;

use Illuminate\Validation\Rule;

class TransactionValidation
{
    public static function store()
    {
        return [
            // 'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ];
    }

    public static function update()
    {
        return [
            'description'   => ['required', 'string', 'max:255'],
            'date'          => ['required'],
            'category'      => ['required', 'string', 'max:255', Rule::exists('categories', 'id')],
            'amount'        => ['required', 'numeric', 'min:0'],
            'user_id'       => ['required', 'string', 'max:255', Rule::exists('users', 'id')],
        ];
    }
}
