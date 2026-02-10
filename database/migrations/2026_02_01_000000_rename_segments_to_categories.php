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
        // Rename segments table to categories
        Schema::rename('segments', 'categories');

        // Rename judge_segment table to judge_category
        Schema::rename('judge_segment', 'judge_category');

        // Update foreign key in criteria table
        Schema::table('criteria', function (Blueprint $table) {
            $table->renameColumn('segment_id', 'category_id');
        });

        // Update foreign key in scores table
        Schema::table('scores', function (Blueprint $table) {
            $table->renameColumn('segment_id', 'category_id');
        });

        // Update judge_category table column names
        Schema::table('judge_category', function (Blueprint $table) {
            $table->renameColumn('segment_id', 'category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the renames
        Schema::rename('categories', 'segments');
        Schema::rename('judge_category', 'judge_segment');

        Schema::table('criteria', function (Blueprint $table) {
            $table->renameColumn('category_id', 'segment_id');
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->renameColumn('category_id', 'segment_id');
        });

        Schema::table('judge_segment', function (Blueprint $table) {
            $table->renameColumn('category_id', 'segment_id');
        });
    }
};
