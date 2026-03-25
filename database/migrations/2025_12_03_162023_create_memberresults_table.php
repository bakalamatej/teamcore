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

            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('member_club_id');

            $table->string('value', 20)->nullable();
            $table->enum('result_type', array_map(fn(\App\Enums\ResultType $type) => $type->value, \App\Enums\ResultType::cases()))
                ->default(\App\Enums\ResultType::SCORE->value);
            $table->unsignedSmallInteger('ranking')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'member_club_id']);
            $table->index('member_club_id');

             // Composite FK → event_member 
            $table->foreign(['member_club_id', 'event_id'])
                ->references(['member_club_id', 'event_id'])
                ->on('event_member')
                ->cascadeOnDelete();
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
