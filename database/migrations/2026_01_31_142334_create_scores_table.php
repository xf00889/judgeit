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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('judge_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('contestant_id')->constrained()->onDelete('cascade');
            $table->foreignId('criterion_id')->constrained('criteria')->onDelete('cascade');
            $table->foreignId('segment_id')->constrained()->onDelete('cascade');
            $table->decimal('score_value', 8, 2);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            $table->unique(['judge_id', 'contestant_id', 'criterion_id', 'segment_id'], 'unique_score_submission');
            $table->index(['judge_id', 'segment_id']);
            $table->index(['contestant_id', 'segment_id']);
            $table->index('segment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
