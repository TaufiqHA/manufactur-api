<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAssembly extends Model
{
    /** @use HasFactory<\Database\Factories\SubAssemblyFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'name',
        'qty_per_parent',
        'total_needed',
        'completed_qty',
        'total_produced',
        'consumed_qty',
        'material_id',
        'processes',
        'step_stats',
        'is_locked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'processes' => 'array',
        'step_stats' => 'array',
    ];

    /**
     * Get the project item that owns the sub assembly.
     */
    public function item()
    {
        return $this->belongsTo(ProjectItem::class, 'item_id');
    }

    /**
     * Get the material associated with the sub assembly.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
