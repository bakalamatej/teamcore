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
        Schema::create('clubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->unsignedBigInteger('sport_id')->nullable();
            $table->string('name', 30)->unique();
            $table->string('phone', 20)->unique();
            $table->string('email', 56)->unique();
            $table->string('webpage')->nullable ();

            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null');
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

            $table->index('address_id');
            $table->index('sport_id');
            $table->index('name');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
