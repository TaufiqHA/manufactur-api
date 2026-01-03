<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ProjectItem;
use App\Models\SubAssembly;
use App\Models\ItemStepConfigs;

class StepStockBalance extends Model
{
    /** @use HasFactory<\Database\Factories\StepStockBalanceFactory> */
    use HasFactory;


    protected $fillable = [
        'item_id',
        'sub_assembly_id',
        'process_step_id',
        'total_produced',
        'total_consumed',
        'available_qty',
    ];

    protected $casts = [
        'total_produced' => 'integer',
        'total_consumed' => 'integer',
        'available_qty' => 'integer',
    ];

    public function projectItem()
    {
        return $this->belongsTo(ProjectItem::class, 'item_id');
    }

    public function subAssembly()
    {
        return $this->belongsTo(SubAssembly::class, 'sub_assembly_id');
    }

    public function itemStepConfig()
    {
        return $this->belongsTo(ItemStepConfigs::class, 'process_step_id');
    }
}
