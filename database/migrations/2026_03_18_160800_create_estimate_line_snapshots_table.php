<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimate_line_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_line_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('display_order');
            $table->string('row_type');
            $table->string('cost_center')->nullable();
            $table->string('item_number')->nullable();
            $table->string('concept_code')->nullable();
            $table->text('description');
            $table->string('unit')->nullable();
            $table->decimal('budget_quantity', 18, 4)->nullable();
            $table->decimal('unit_price', 18, 2)->nullable();
            $table->decimal('budget_amount', 18, 2)->nullable();
            $table->decimal('addendum_quantity', 18, 4)->default(0);
            $table->decimal('addendum_amount', 18, 2)->default(0);
            $table->decimal('origin_plus_addendum_quantity', 18, 4)->nullable();
            $table->decimal('origin_plus_addendum_amount', 18, 2)->nullable();
            $table->decimal('previous_quantity', 18, 4)->default(0);
            $table->decimal('previous_amount', 18, 2)->default(0);
            $table->decimal('current_quantity', 18, 4)->default(0);
            $table->decimal('current_amount', 18, 2)->default(0);
            $table->decimal('accumulated_quantity', 18, 4)->default(0);
            $table->decimal('accumulated_amount', 18, 2)->default(0);
            $table->decimal('remaining_quantity', 18, 4)->default(0);
            $table->decimal('remaining_amount', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['estimate_id', 'contract_line_id']);
            $table->index(['estimate_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimate_line_snapshots');
    }
};
