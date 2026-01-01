<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    /** @use HasFactory<\Database\Factories\MaterialFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'unit',
        'current_stock',
        'safety_stock',
        'price_per_unit',
        'category',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'current_stock' => 'integer',
        'safety_stock' => 'integer',
        'price_per_unit' => 'decimal:2',
        'category' => 'string',
    ];
}
