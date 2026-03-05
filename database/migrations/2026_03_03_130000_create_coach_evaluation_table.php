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
        Schema::create('coach_evaluation', function (Blueprint $table) {
            $table->id('evaluation_id');

            $table->foreignId('coach_id')
                ->constrained('members', 'member_id')
                ->cascadeOnDelete();

            $table->foreignId('evaluated_by_member_id')
                ->constrained('members', 'member_id')
                ->restrictOnDelete();

            $table->foreignId('reservation_id')
                ->constrained('reservations', 'reservation_id')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->longText('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('coach_id');
            $table->index('evaluated_by_member_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coach_evaluation');
    }
};
