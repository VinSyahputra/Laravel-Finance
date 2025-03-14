<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'id',
        'amount',
        'description',
        'category_id',
        'input_by',
        'date',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
