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
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sport_field_id')->nullable();
            $table->unsignedBigInteger('event_type_id')->nullable();
            $table->string('title', 80);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('scheduled');
            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->foreign('sport_field_id')->references('id')->on('sport_fields')->onDelete('set null');
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
