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
            $table->id('event_id');

            $table->foreignId('parent_event_id')
                ->nullable()
                ->constrained('events', 'event_id')
                ->nullOnDelete();

            $table->foreignId('sport_field_id')
                ->constrained('sport_fields', 'sport_field_id')
                ->restrictOnDelete();

            $table->foreignId('event_type_id')
                ->constrained('event_types', 'event_type_id')
                ->restrictOnDelete();

            $table->string('title', 80);
            $table->longText('description')->nullable();

            $table->enum('status', ['scheduled', 'canceled', 'finished', 'ongoing'])
                ->default('scheduled');

            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_event_id');
            $table->index('sport_field_id');
            $table->index('event_type_id');
            $table->index('status');
            $table->index('start_date');
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
