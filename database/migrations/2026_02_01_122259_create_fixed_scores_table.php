<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fixed_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('overall_ranking_component_id')->constrained('overall_ranking_components')->onDelete('cascade');
            $table->foreignId('contestant_id')->constrained('contestants')->onDelete('cascade');
            $table->decimal('score', 8, 2);
            $table->text('notes')->nullable();
            $table->foreignId('entered_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure one score per contestant per component
            $table->unique(['overall_ranking_component_id', 'contestant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_scores');
    }
};
