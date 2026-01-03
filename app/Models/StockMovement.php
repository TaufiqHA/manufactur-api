<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Import related models
use App\Models\ProjectItem;
use App\Models\SubAssembly;
use App\Models\ItemStepConfig;
use App\Models\Task;
use App\Models\User;

class StockMovement extends Model
{
    /** @use HasFactory<\Database\Factories\StockMovementFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'sub_assembly_id',
        'source_step_id',
        'target_step_id',
        'task_id',
        'created_by',
        'quantity',
        'good_qty',
        'defect_qty',
        'movement_type',
        'shift',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'movement_type' => 'string',
        'shift' => 'string',
    ];

    /**
     * Relationship with ProjectItem model
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ProjectItem::class, 'item_id');
    }

    /**
     * Relationship with SubAssembly model
     */
    public function subAssembly(): BelongsTo
    {
        return $this->belongsTo(SubAssembly::class, 'sub_assembly_id');
    }

    /**
     * Relationship with ItemStepConfig model (source step)
     */
    public function sourceStep(): BelongsTo
    {
        return $this->belongsTo(ItemStepConfigs::class, 'source_step_id');
    }

    /**
     * Relationship with ItemStepConfig model (target step)
     */
    public function targetStep(): BelongsTo
    {
        return $this->belongsTo(ItemStepConfigs::class, 'target_step_id');
    }

    /**
     * Relationship with Task model
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Relationship with User model (created by)
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
