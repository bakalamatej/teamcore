<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_statistics', function (Blueprint $table) {
            $table->id('stat_id');

            $table->foreignId('member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->restrictOnDelete();

            $table->unsignedInteger('events_attended')->default(0);
            $table->unsignedInteger('training_sessions')->default(0);
            $table->unsignedInteger('matches_played')->default(0);
            $table->unsignedInteger('tournaments_attended')->default(0);
            $table->unsignedInteger('total_wins')->default(0);
            $table->timestamps();

            $table->unique('member_club_id');
        });

        DB::unprepared("
            CREATE TRIGGER trg_member_statistics_event_finished_increment
            AFTER UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_event_type_name VARCHAR(50);

                IF OLD.status <> 'finished' AND NEW.status = 'finished' THEN
                    SELECT et.name INTO v_event_type_name
                    FROM event_types et
                    WHERE et.event_type_id = NEW.event_type_id;

                    INSERT INTO member_statistics (
                        member_club_id,
                        events_attended,
                        training_sessions,
                        matches_played,
                        tournaments_attended,
                        created_at,
                        updated_at
                    )
                    SELECT
                        em.member_club_id,
                        1,
                        IF(v_event_type_name LIKE '%Training%', 1, 0),
                        IF(v_event_type_name LIKE '%Match%', 1, 0),
                        IF(v_event_type_name LIKE '%Tournament%', 1, 0),
                        NOW(),
                        NOW()
                    FROM event_member em
                    WHERE em.event_id = NEW.event_id
                    ON DUPLICATE KEY UPDATE
                        events_attended = events_attended + 1,
                        training_sessions = training_sessions + IF(v_event_type_name LIKE '%Training%', 1, 0),
                        matches_played = matches_played + IF(v_event_type_name LIKE '%Match%', 1, 0),
                        tournaments_attended = tournaments_attended + IF(v_event_type_name LIKE '%Tournament%', 1, 0),
                        updated_at = NOW();
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_member_statistics_event_finished_decrement
            AFTER UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_event_type_name VARCHAR(50);

                IF OLD.status = 'finished' AND NEW.status <> 'finished' THEN
                    SELECT et.name INTO v_event_type_name
                    FROM event_types et
                    WHERE et.event_type_id = OLD.event_type_id;

                    UPDATE member_statistics ms
                    JOIN event_member em ON em.member_club_id = ms.member_club_id
                    SET
                        ms.events_attended = GREATEST(ms.events_attended - 1, 0),
                        ms.training_sessions = GREATEST(ms.training_sessions - IF(v_event_type_name LIKE '%Training%', 1, 0), 0),
                        ms.matches_played = GREATEST(ms.matches_played - IF(v_event_type_name LIKE '%Match%', 1, 0), 0),
                        ms.tournaments_attended = GREATEST(ms.tournaments_attended - IF(v_event_type_name LIKE '%Tournament%', 1, 0), 0),
                        ms.updated_at = NOW()
                    WHERE em.event_id = OLD.event_id;
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_member_statistics
            AFTER INSERT ON event_member_results
            FOR EACH ROW
            BEGIN
                INSERT INTO member_statistics (member_club_id, total_wins, created_at, updated_at)
                VALUES (NEW.member_club_id, IF(NEW.ranking = 1, 1, 0), NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    total_wins = total_wins + IF(NEW.ranking = 1, 1, 0),
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
                UPDATE member_statistics
                SET total_wins = GREATEST(total_wins - IF(OLD.ranking = 1, 1, 0), 0),
                    updated_at = NOW()
                WHERE member_club_id = OLD.member_club_id;
            END
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_member_statistics_event_finished_increment');
        DB::statement('DROP TRIGGER IF EXISTS trg_member_statistics_event_finished_decrement');
        DB::statement('DROP TRIGGER IF EXISTS trg_member_stats_delete');
        DB::statement('DROP TRIGGER IF EXISTS trg_member_statistics_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_member_statistics');
        Schema::dropIfExists('member_statistics');
    }
};