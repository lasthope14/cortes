<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_name',
        'contractor_name',
        'location',
        'contract_number',
        'currency',
        'advance_percentage',
        'scheduled_progress',
        'actual_progress',
        'start_date',
        'planned_end_date',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'advance_percentage' => 'decimal:5',
            'scheduled_progress' => 'decimal:8',
            'actual_progress' => 'decimal:8',
            'start_date' => 'date',
            'planned_end_date' => 'date',
            'metadata' => 'array',
        ];
    }

    public function excelTemplates(): HasMany
    {
        return $this->hasMany(ExcelTemplate::class);
    }

    public function contractLines(): HasMany
    {
        return $this->hasMany(ContractLine::class);
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class);
    }

    public function fieldReports(): HasMany
    {
        return $this->hasMany(FieldReport::class);
    }
}
