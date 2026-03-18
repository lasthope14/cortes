<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_line_addendums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_line_id')->constrained()->cascadeOnDelete();
            $table->date('approved_at')->nullable();
            $table->decimal('quantity', 18, 4);
            $table->decimal('amount', 18, 2)->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_line_addendums');
    }
};
