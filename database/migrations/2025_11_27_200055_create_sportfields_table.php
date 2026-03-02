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
        Schema::create('sport_fields', function (Blueprint $table) {
            $table->id('sport_field_id');

            $table->foreignId('address_id')
                ->nullable()
                ->constrained('addresses', 'address_id')
                ->nullOnDelete();

            $table->string('name', 30);
            $table->string('field_type', 20);

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['address_id', 'name']);
            $table->index('address_id');
            $table->index('field_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sportfields');
    }
};
