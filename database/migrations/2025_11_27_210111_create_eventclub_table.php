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
        Schema::create('event_club', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('club_id');

            $table->primary(['club_id', 'event_id']);

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventclub');
    }
};
