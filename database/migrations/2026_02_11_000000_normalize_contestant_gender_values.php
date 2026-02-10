<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize all gender values to lowercase
        DB::table('contestants')
            ->whereNotNull('gender')
            ->update([
                'gender' => DB::raw('LOWER(gender)')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - data normalization is idempotent
    }
};
