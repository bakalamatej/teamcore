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
        Schema::create('member_event', function (Blueprint $table) {
            $table->foreignId('member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->cascadeOnDelete();

            $table->foreignId('event_id')
                ->constrained('events', 'event_id')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['member_club_id', 'event_id']);
            $table->index('event_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_event');
    }
};
