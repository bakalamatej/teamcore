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
            $table->id('club_id');

            $table->foreignId('address_id')
                ->constrained('addresses', 'address_id')
                ->restrictOnDelete();

            $table->foreignId('sport_id')
                ->constrained('sports', 'sport_id')
                ->restrictOnDelete();

            $table->string('name', 50);
            $table->string('phone', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('webpage', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('sport_id');
            $table->index('address_id');
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
