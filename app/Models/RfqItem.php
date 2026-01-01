<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqItem extends Model
{
    /** @use HasFactory<\Database\Factories\RfqItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rfq_id',
        'material_id',
        'name',
        'qty',
        'price',
    ];

    /**
     * Get the RFQ that owns the item.
     */
    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    /**
     * Get the material associated with the item.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
