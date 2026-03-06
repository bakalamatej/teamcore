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
        Schema::create('club_sport', function (Blueprint $table) {
            $table->foreignId('club_id')
                ->constrained('clubs', 'club_id')
                ->cascadeOnDelete();

            $table->foreignId('sport_id')
                ->constrained('sports', 'sport_id')
                ->cascadeOnDelete();

            $table->primary(['club_id', 'sport_id']);

            $table->index('club_id');
            $table->index('sport_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_sport');
    }
};
