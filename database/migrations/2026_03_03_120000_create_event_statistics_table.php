<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_statistics', function (Blueprint $table) {
            $table->id('stat_id');

            $table->foreignId('event_id')
                ->unique()
                ->constrained('events', 'event_id')
                ->cascadeOnDelete();

            $table->unsignedInteger('total_participants')->default(0);
            $table->unsignedInteger('total_teams')->default(0);
            $table->timestamps();
        });

        DB::unprepared("
            CREATE TRIGGER trg_event_statistics
            AFTER INSERT ON event_member_results
            FOR EACH ROW
            BEGIN
                INSERT INTO event_statistics (event_id, total_participants, total_teams, created_at, updated_at)
                VALUES (NEW.event_id, 1, 0, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    total_participants = total_participants + 1,
                    updated_at = NOW();
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_event_statistics');
        Schema::dropIfExists('event_statistics');
    }
};
