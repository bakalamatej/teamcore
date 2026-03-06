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
        Schema::create('event_club', function (Blueprint $table) {
            $table->foreignId('event_id')
                ->constrained('events', 'event_id')
                ->cascadeOnDelete();

            $table->foreignId('club_id')
                ->constrained('clubs', 'club_id')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['event_id', 'club_id']);
            $table->index('club_id');
        });
        DB::unprepared("
            CREATE TRIGGER trg_event_statistics_club
            AFTER INSERT ON event_club
            FOR EACH ROW
            BEGIN
                INSERT INTO event_statistics (event_id, total_teams, total_participants, created_at, updated_at)
                VALUES (NEW.event_id, 1, 0, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    total_teams = total_teams + 1,
                    updated_at = NOW();
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_event_statistics_club');
        Schema::dropIfExists('event_club');
    }
};
