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
        Schema::create('overall_ranking_category_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('overall_ranking_component_id')
                ->constrained('overall_ranking_components', 'id', 'orcw_component_fk')
                ->onDelete('cascade');
            $table->foreignId('category_id')
                ->constrained('categories', 'id', 'orcw_category_fk')
                ->onDelete('cascade');
            $table->decimal('percentage', 5, 2);
            $table->timestamps();
            
            $table->unique(['overall_ranking_component_id', 'category_id'], 'orcw_component_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overall_ranking_category_weights');
    }
};
