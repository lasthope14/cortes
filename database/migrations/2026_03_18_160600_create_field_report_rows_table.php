<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_report_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_line_id')->constrained()->restrictOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('captured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('work_date');
            $table->unsignedInteger('sequence')->default(1);
            $table->string('element')->nullable();
            $table->string('axis')->nullable();
            $table->string('level')->nullable();
            $table->string('plan_reference')->nullable();
            $table->decimal('length', 18, 4)->nullable();
            $table->decimal('width', 18, 4)->nullable();
            $table->decimal('height', 18, 4)->nullable();
            $table->decimal('area', 18, 4)->nullable();
            $table->decimal('volume', 18, 4)->nullable();
            $table->decimal('weight', 18, 4)->nullable();
            $table->decimal('quantity', 18, 4)->nullable();
            $table->decimal('subtotal', 18, 4)->nullable();
            $table->unsignedInteger('element_count')->nullable();
            $table->text('observations')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['contract_line_id', 'work_date']);
            $table->index(['estimate_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_report_rows');
    }
};
