<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseOrderFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'date',
        'supplier_id',
        'rfq_id',
        'description',
        'status',
        'grand_total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'grand_total' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Relationship with Supplier model
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relationship with RFQ model
     */
    public function rfq()
    {
        return $this->belongsTo(Rfq::class, 'rfq_id');
    }

    /**
     * Relationship with ReceivingGood model
     */
    public function receivingGoods()
    {
        return $this->hasMany(ReceivingGood::class, 'po_id');
    }
}
