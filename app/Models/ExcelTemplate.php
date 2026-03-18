<?php

namespace App\Models;

use App\Enums\ExcelTemplateType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExcelTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'template_type',
        'sheet_name',
        'storage_path',
        'version',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'template_type' => ExcelTemplateType::class,
            'is_active' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contractLines(): HasMany
    {
        return $this->hasMany(ContractLine::class);
    }
}
