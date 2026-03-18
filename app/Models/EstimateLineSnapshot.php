<?php

namespace App\Models;

use App\Enums\ContractLineType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimateLineSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'contract_line_id',
        'display_order',
        'row_type',
        'cost_center',
        'item_number',
        'concept_code',
        'description',
        'unit',
        'budget_quantity',
        'unit_price',
        'budget_amount',
        'addendum_quantity',
        'addendum_amount',
        'origin_plus_addendum_quantity',
        'origin_plus_addendum_amount',
        'previous_quantity',
        'previous_amount',
        'current_quantity',
        'current_amount',
        'accumulated_quantity',
        'accumulated_amount',
        'remaining_quantity',
        'remaining_amount',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'row_type' => ContractLineType::class,
            'budget_quantity' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'budget_amount' => 'decimal:2',
            'addendum_quantity' => 'decimal:4',
            'addendum_amount' => 'decimal:2',
            'origin_plus_addendum_quantity' => 'decimal:4',
            'origin_plus_addendum_amount' => 'decimal:2',
            'previous_quantity' => 'decimal:4',
            'previous_amount' => 'decimal:2',
            'current_quantity' => 'decimal:4',
            'current_amount' => 'decimal:2',
            'accumulated_quantity' => 'decimal:4',
            'accumulated_amount' => 'decimal:2',
            'remaining_quantity' => 'decimal:4',
            'remaining_amount' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function contractLine(): BelongsTo
    {
        return $this->belongsTo(ContractLine::class);
    }
}
