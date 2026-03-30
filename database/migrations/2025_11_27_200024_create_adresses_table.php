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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->string('country', 255);
            $table->string('city', 255);
            $table->string('street', 255)->nullable();
            $table->string('zip_code', 20)->nullable();
            
            $table->timestamps();

            $table->unique(['country', 'city', 'street']);
            $table->index(['city', 'country']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
