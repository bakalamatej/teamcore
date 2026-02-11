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
            $table->unsignedBigInteger('sport_field_id');
            $table->unsignedBigInteger('sport_id');

            $table->primary(['sport_field_id', 'sport_id']);

            $table->foreign('sport_field_id')->references('id')->on('sport_fields')->onDelete('cascade');
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');

            $table->timestamps();

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
