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
        Schema::create('sport_fields_sports', function (Blueprint $table) {
            $table->foreignId('sport_field_id')
                ->constrained('sport_fields', 'sport_field_id')
                ->cascadeOnDelete();

            $table->foreignId('sport_id')
                ->constrained('sports', 'sport_id')
                ->cascadeOnDelete();

            $table->primary(['sport_field_id', 'sport_id']);

            $table->index('sport_field_id');
            $table->index('sport_id');
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_fields_sports');
    }
};
