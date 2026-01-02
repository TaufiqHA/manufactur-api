<?php

namespace App\Models;

use App\Enums\FlowType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectItem extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'name',
        'dimensions',
        'thickness',
        'qty_set',
        'quantity',
        'unit',
        'is_bom_locked',
        'is_workflow_locked',
        'flow_type',
        'warehouse_qty',
        'shipped_qty',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_bom_locked' => 'boolean',
        'is_workflow_locked' => 'boolean',
        'qty_set' => 'integer',
        'quantity' => 'integer',
        'warehouse_qty' => 'integer',
        'shipped_qty' => 'integer',
        'flow_type' => FlowType::class,
    ];

    /**
     * Get the project that owns the project item.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
