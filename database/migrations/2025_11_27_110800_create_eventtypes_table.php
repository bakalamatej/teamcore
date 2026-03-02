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
        Schema::create('event_types', function (Blueprint $table) {
            $table->id('event_type_id');

            $table->foreignId('sport_id')
                ->constrained('sports', 'sport_id')
                ->restrictOnDelete();

            $table->string('name', 30);
            $table->timestamps();

            $table->unique(['sport_id', 'name']);
            $table->index('sport_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};
