<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingItem extends Model
{
    /** @use HasFactory<\Database\Factories\ReceivingItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'receiving_id',
        'material_id',
        'name',
        'qty',
    ];

    /**
     * Get the receiving that owns the receiving item.
     */
    public function receiving()
    {
        return $this->belongsTo(ReceivingGood::class, 'receiving_id');
    }

    /**
     * Get the material associated with the receiving item.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
