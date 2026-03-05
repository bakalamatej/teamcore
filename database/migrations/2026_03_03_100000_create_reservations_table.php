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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');

            $table->foreignId('sport_field_id')
                ->constrained('sport_fields', 'sport_field_id')
                ->restrictOnDelete();

            $table->foreignId('club_id')
                ->constrained('clubs', 'club_id')
                ->cascadeOnDelete();

            $table->foreignId('created_by_member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->restrictOnDelete();

            $table->string('title', 100);
            $table->longText('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])
                ->default('pending');

            $table->timestamps();
            $table->softDeletes();

            $table->index('club_id');
            $table->index('sport_field_id');
            $table->index('status');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
