<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update max_score to equal percentage for all existing criteria
        DB::table('criteria')->update([
            'max_score' => DB::raw('percentage')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert max_score back to 10 for all criteria
        DB::table('criteria')->update([
            'max_score' => 10
        ]);
    }
};
