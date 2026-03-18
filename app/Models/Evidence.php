<?php

namespace App\Models;

use App\Enums\EvidenceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidence extends Model
{
    use HasFactory;

    protected $table = 'evidences';

    protected $fillable = [
        'field_report_row_id',
        'type',
        'storage_disk',
        'storage_path',
        'original_name',
        'caption',
        'taken_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => EvidenceType::class,
            'taken_at' => 'datetime',
        ];
    }

    public function fieldReportRow(): BelongsTo
    {
        return $this->belongsTo(FieldReportRow::class);
    }
}
