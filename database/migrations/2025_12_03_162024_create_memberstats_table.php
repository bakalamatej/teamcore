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
        Schema::create('member_statistics', function (Blueprint $table) {
            $table->id('stat_id');

            $table->foreignId('member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->cascadeOnDelete();

            $table->unsignedInteger('events_attended')->default(0);
            $table->unsignedInteger('training_sessions')->default(0);
            $table->unsignedInteger('matches_played')->default(0);
            $table->unsignedInteger('tournaments_attended')->default(0);
            $table->unsignedInteger('total_wins')->default(0);
            $table->timestamps();

            $table->unique('member_club_id');
        });

        DB::unprepared("
            CREATE TRIGGER trg_member_statistics
            AFTER INSERT ON event_member_results
            FOR EACH ROW
            BEGIN
                DECLARE v_event_type_name VARCHAR(50);
                
                SELECT et.name INTO v_event_type_name
                FROM events e
                JOIN event_types et ON et.event_type_id = e.event_type_id
                WHERE e.event_id = NEW.event_id;

                INSERT INTO member_statistics (member_club_id, events_attended, total_wins, training_sessions, matches_played, tournaments_attended, created_at, updated_at)
                VALUES (
                    NEW.member_club_id, 1, 
                    IF(NEW.ranking = 1, 1, 0),
                    IF(v_event_type_name = 'Training', 1, 0),
                    IF(v_event_type_name = 'Match', 1, 0),
                    IF(v_event_type_name = 'Tournament', 1, 0),
                    NOW(), NOW()
                )
                ON DUPLICATE KEY UPDATE
                    events_attended = events_attended + 1,
                    total_wins = total_wins + IF(NEW.ranking = 1, 1, 0),
                    training_sessions = training_sessions + IF(v_event_type_name = 'Training', 1, 0),
                    matches_played = matches_played + IF(v_event_type_name = 'Match', 1, 0),
                    tournaments_attended = tournaments_attended + IF(v_event_type_name = 'Tournament', 1, 0),
                    updated_at = NOW();
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_member_statistics_update
            AFTER UPDATE ON event_member_results
            FOR EACH ROW
            BEGIN
                UPDATE member_statistics
                SET total_wins = total_wins 
                    - IF(OLD.ranking = 1, 1, 0) 
                    + IF(NEW.ranking = 1, 1, 0),
                    updated_at = NOW()
                WHERE member_club_id = NEW.member_club_id;
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_member_stats_delete
            AFTER DELETE ON event_member_results
            FOR EACH ROW
            BEGIN
                DECLARE v_event_type_name VARCHAR(50);
                
                SELECT et.name INTO v_event_type_name
                FROM events e
                JOIN event_types et ON et.event_type_id = e.event_type_id
                WHERE e.event_id = OLD.event_id;

                UPDATE member_statistics
                SET events_attended = GREATEST(events_attended - 1, 0),
                    total_wins = GREATEST(total_wins - IF(OLD.ranking = 1, 1, 0), 0),
                    training_sessions = GREATEST(training_sessions - IF(v_event_type_name = 'Training', 1, 0), 0),
                    matches_played = GREATEST(matches_played - IF(v_event_type_name = 'Match', 1, 0), 0),
                    tournaments_attended = GREATEST(tournaments_attended - IF(v_event_type_name = 'Tournament', 1, 0), 0),
                    updated_at = NOW()
                WHERE member_club_id = OLD.member_club_id;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_member_stats_delete');
        DB::statement('DROP TRIGGER IF EXISTS trg_member_statistics_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_member_statistics');
        Schema::dropIfExists('member_statistics');
    }
};
