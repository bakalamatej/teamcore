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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('member_club_id');            
            $table->integer('events_attended')->default(0);
            $table->integer('training_sessions')->default(0);
            $table->integer('matches_played')->default(0);
            $table->integer('goals_scored')->default(0);

            $table->foreign('member_club_id')->references('id')->on('member_club')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberstats');
    }
};
