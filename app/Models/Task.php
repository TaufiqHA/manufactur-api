<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'project_name',
        'item_id',
        'item_name',
        'sub_assembly_id',
        'sub_assembly_name',
        'step',
        'machine_id',
        'target_qty',
        'daily_target',
        'completed_qty',
        'defect_qty',
        'status',
        'note',
        'total_downtime_minutes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'project_id' => 'string',
        'item_id' => 'string',
        'sub_assembly_id' => 'string',
        'machine_id' => 'string',
        'target_qty' => 'integer',
        'daily_target' => 'integer',
        'completed_qty' => 'integer',
        'defect_qty' => 'integer',
        'total_downtime_minutes' => 'integer',
    ];

    /**
     * Get the project that owns the task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the item that owns the task.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ProjectItem::class, 'item_id', 'id', 'project_items');
    }

    /**
     * Get the sub assembly that owns the task.
     */
    public function subAssembly(): BelongsTo
    {
        return $this->belongsTo(SubAssembly::class, 'sub_assembly_id');
    }

    /**
     * Get the machine that owns the task.
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }
}
