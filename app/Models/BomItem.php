<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    /** @use HasFactory<\Database\Factories\BomItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'material_id',
        'quantity_per_unit',
        'total_required',
        'allocated',
        'realized',
    ];

    /**
     * Get the project item that owns the BOM item.
     */
    public function item()
    {
        return $this->belongsTo(ProjectItem::class, 'item_id');
    }

    /**
     * Get the material associated with the BOM item.
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
