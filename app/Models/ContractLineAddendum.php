<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractLineAddendum extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_line_id',
        'approved_at',
        'quantity',
        'amount',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'date',
            'quantity' => 'decimal:4',
            'amount' => 'decimal:2',
        ];
    }

    public function contractLine(): BelongsTo
    {
        return $this->belongsTo(ContractLine::class);
    }
}
