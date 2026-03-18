<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('client_name')->nullable();
            $table->string('contractor_name')->nullable();
            $table->string('location')->nullable();
            $table->string('contract_number')->nullable();
            $table->string('currency', 3)->default('COP');
            $table->decimal('advance_percentage', 8, 5)->default(0);
            $table->decimal('scheduled_progress', 12, 8)->nullable();
            $table->decimal('actual_progress', 12, 8)->nullable();
            $table->date('start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
