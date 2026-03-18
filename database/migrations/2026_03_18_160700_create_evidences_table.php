<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_report_row_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('storage_disk')->default('public');
            $table->string('storage_path');
            $table->string('original_name')->nullable();
            $table->text('caption')->nullable();
            $table->timestamp('taken_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['field_report_row_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidences');
    }
};
