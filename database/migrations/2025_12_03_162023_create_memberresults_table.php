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
        Schema::create('event_member_results', function (Blueprint $table) {
            $table->id('result_id');

            $table->foreignId('event_id')
                ->constrained('events', 'event_id')
                ->cascadeOnDelete();

            $table->foreignId('member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->cascadeOnDelete();

            $table->integer('score')->nullable();
            $table->integer('ranking')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'member_club_id']);
            $table->index('member_club_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_member_results');
    }
};
