<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('excel_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('contract_lines')->nullOnDelete();
            $table->unsignedInteger('display_order');
            $table->unsignedInteger('excel_row')->nullable();
            $table->string('row_type');
            $table->string('cost_center')->nullable();
            $table->string('item_number')->nullable();
            $table->string('concept_code')->nullable();
            $table->text('description');
            $table->string('unit')->nullable();
            $table->decimal('budget_quantity', 18, 4)->nullable();
            $table->decimal('unit_price', 18, 2)->nullable();
            $table->decimal('budget_amount', 18, 2)->nullable();
            $table->boolean('allows_field_capture')->default(true);
            $table->json('measurement_schema')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'display_order']);
            $table->index(['project_id', 'concept_code']);
            $table->index(['project_id', 'row_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_lines');
    }
};
