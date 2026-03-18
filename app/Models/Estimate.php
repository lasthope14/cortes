<?php

namespace App\Models;

use App\Enums\EstimateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'estimate_number',
        'period_start',
        'period_end',
        'prepared_at',
        'status',
        'notes',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'prepared_at' => 'date',
            'locked_at' => 'datetime',
            'status' => EstimateStatus::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fieldReportRows(): HasMany
    {
        return $this->hasMany(FieldReportRow::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(EstimateLineSnapshot::class);
    }
}
