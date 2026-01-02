<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoItem extends Model
{
    /** @use HasFactory<\Database\Factories\PoItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'po_id',
        'material_id',
        'name',
        'qty',
        'price',
    ];

    /**
     * Get the purchase order that owns the item.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    /**
     * Get the material associated with the item.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
