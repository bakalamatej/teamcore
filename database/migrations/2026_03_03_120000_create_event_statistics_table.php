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
        Schema::create('event_statistics', function (Blueprint $table) {
            $table->id('stat_id');

            $table->foreignId('event_id')
                ->unique()
                ->constrained('events', 'event_id')
                ->cascadeOnDelete();

            $table->unsignedInteger('total_participants')->default(0);
            $table->unsignedInteger('total_teams')->default(0);
            $table->timestamps();

            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_statistics');
    }
};
