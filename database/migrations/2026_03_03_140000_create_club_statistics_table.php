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
        Schema::create('club_statistics', function (Blueprint $table) {
            $table->id('stat_id');

            $table->foreignId('club_id')
                ->unique()
                ->constrained('clubs', 'club_id')
                ->cascadeOnDelete();

            $table->unsignedInteger('active_members')->default(0);
            $table->unsignedInteger('total_coaches')->default(0);
            $table->unsignedInteger('total_events')->default(0);
            $table->unsignedInteger('total_wins')->default(0);
            $table->unsignedInteger('total_loses')->default(0);
            $table->unsignedInteger('total_draws')->default(0);
            $table->timestamps();

            $table->index('club_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_statistics');
    }
};
