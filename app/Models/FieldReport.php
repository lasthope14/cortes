<?php

namespace App\Models;

use App\Enums\FieldReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'engineer_id',
        'report_date',
        'status',
        'source',
        'notes',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'submitted_at' => 'datetime',
            'status' => FieldReportStatus::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(FieldReportRow::class);
    }
}
