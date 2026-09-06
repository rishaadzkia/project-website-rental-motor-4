<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motorcycle extends Model
{
    protected $fillable = [
        'name',
        'brand',
        'model',
        'plate_number',
        'year',
        'price_per_day',
        'status',
        'description',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'price_per_day' => 'decimal:2',
        ];
    }
}