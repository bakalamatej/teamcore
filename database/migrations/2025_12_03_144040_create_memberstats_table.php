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
        Schema::create('member_statistics', function (Blueprint $table) {
            $table->id('stat_id');

            $table->foreignId('member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->cascadeOnDelete();

            $table->unsignedInteger('events_attended')->default(0);
            $table->unsignedInteger('training_sessions')->default(0);
            $table->unsignedInteger('matches_played')->default(0);
            $table->unsignedInteger('goals_scored')->default(0);
            $table->timestamps();

            $table->unique('member_club_id');
            $table->index('member_club_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_statistics');
    }
};
