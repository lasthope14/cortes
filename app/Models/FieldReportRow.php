<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldReportRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_report_id',
        'contract_line_id',
        'estimate_id',
        'captured_by',
        'work_date',
        'sequence',
        'element',
        'axis',
        'level',
        'plan_reference',
        'length',
        'width',
        'height',
        'area',
        'volume',
        'weight',
        'quantity',
        'subtotal',
        'element_count',
        'observations',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'length' => 'decimal:4',
            'width' => 'decimal:4',
            'height' => 'decimal:4',
            'area' => 'decimal:4',
            'volume' => 'decimal:4',
            'weight' => 'decimal:4',
            'quantity' => 'decimal:4',
            'subtotal' => 'decimal:4',
            'metadata' => 'array',
        ];
    }

    public function fieldReport(): BelongsTo
    {
        return $this->belongsTo(FieldReport::class);
    }

    public function contractLine(): BelongsTo
    {
        return $this->belongsTo(ContractLine::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function captor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captured_by');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(Evidence::class);
    }
}
