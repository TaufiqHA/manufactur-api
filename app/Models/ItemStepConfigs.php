<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemStepConfigs extends Model
{
    /** @use HasFactory<\Database\Factories\ItemStepConfigsFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'item_step_configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'step',
        'sequence',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sequence' => 'integer',
    ];

    /**
     * Get the item that owns the step config.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ProjectItem::class, 'item_id', 'id');
    }
}
