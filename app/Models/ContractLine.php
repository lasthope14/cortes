<?php

namespace App\Models;

use App\Enums\ContractLineType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'excel_template_id',
        'parent_id',
        'display_order',
        'excel_row',
        'row_type',
        'cost_center',
        'item_number',
        'concept_code',
        'description',
        'unit',
        'budget_quantity',
        'unit_price',
        'budget_amount',
        'allows_field_capture',
        'measurement_schema',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'row_type' => ContractLineType::class,
            'budget_quantity' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'budget_amount' => 'decimal:2',
            'allows_field_capture' => 'boolean',
            'measurement_schema' => 'array',
            'metadata' => 'array',
        ];
    }

    public function scopeItems(Builder $query): Builder
    {
        return $query->where('row_type', ContractLineType::Item->value);
    }

    public function scopeGroups(Builder $query): Builder
    {
        return $query->where('row_type', ContractLineType::Group->value);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function excelTemplate(): BelongsTo
    {
        return $this->belongsTo(ExcelTemplate::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function addendums(): HasMany
    {
        return $this->hasMany(ContractLineAddendum::class);
    }

    public function fieldReportRows(): HasMany
    {
        return $this->hasMany(FieldReportRow::class);
    }

    public function estimateSnapshots(): HasMany
    {
        return $this->hasMany(EstimateLineSnapshot::class);
    }

    public function isGroup(): bool
    {
        return $this->row_type === ContractLineType::Group;
    }

    public function isItem(): bool
    {
        return $this->row_type === ContractLineType::Item;
    }
}
