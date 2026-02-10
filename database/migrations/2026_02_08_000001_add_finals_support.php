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
        // Add is_finals flag to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_finals')->default(false)->after('status');
            $table->index('is_finals');
        });

        // Add finalist tracking to contestants table
        Schema::table('contestants', function (Blueprint $table) {
            $table->boolean('is_finalist')->default(false)->after('is_active');
            $table->integer('preliminary_rank')->nullable()->after('is_finalist');
            $table->decimal('preliminary_score', 8, 2)->nullable()->after('preliminary_rank');
            
            $table->index('is_finalist');
            $table->index('preliminary_rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['is_finals']);
            $table->dropColumn('is_finals');
        });

        Schema::table('contestants', function (Blueprint $table) {
            $table->dropIndex(['is_finalist']);
            $table->dropIndex(['preliminary_rank']);
            $table->dropColumn(['is_finalist', 'preliminary_rank', 'preliminary_score']);
        });
    }
};
