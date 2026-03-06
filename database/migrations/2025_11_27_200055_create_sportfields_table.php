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
                ->constrained('addresses', 'address_id')
                ->restrictOnDelete();

            $table->string('name', 50);
            $table->foreignId('field_type_id')
                ->constrained('field_types', 'field_type_id')
                ->restrictOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['address_id', 'name']);
            $table->index('address_id');
            $table->index('field_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_fields');
    }
};
