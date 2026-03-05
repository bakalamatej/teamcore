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
            $table->string('country', 100);
            $table->string('city', 100);
            $table->string('street', 100)->nullable();
            $table->string('zip_code', 20)->nullable();
            
            $table->softDeletes();
            $table->timestamps();

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
