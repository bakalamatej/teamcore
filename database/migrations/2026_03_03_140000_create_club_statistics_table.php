<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_statistics', function (Blueprint $table) {
            $table->id('stat_id');

            $table->foreignId('club_id')
                ->constrained('clubs', 'club_id')
                ->restrictOnDelete();

            $table->unsignedInteger('active_members')->default(0);
            $table->unsignedInteger('total_coaches')->default(0);
            $table->unsignedInteger('matches_played')->default(0);
            $table->unsignedInteger('tournaments_attended')->default(0);
            $table->unsignedInteger('total_wins')->default(0);
            $table->unsignedInteger('total_losses')->default(0);
            $table->timestamps();

            $table->unique('club_id');
        });

        DB::unprepared("
            CREATE TRIGGER trg_club_statistics_event_finished_increment
            AFTER UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_event_type_name VARCHAR(50);

                IF OLD.status <> 'finished' AND NEW.status = 'finished' THEN
                    SELECT et.name INTO v_event_type_name
                    FROM event_types et
                    WHERE et.event_type_id = NEW.event_type_id;

                    INSERT INTO club_statistics (club_id, matches_played, tournaments_attended, created_at, updated_at)
                    SELECT
                        ec.club_id,
                        IF(v_event_type_name LIKE '%Match%', 1, 0),
                        IF(v_event_type_name LIKE '%Tournament%', 1, 0),
                        NOW(),
                        NOW()
                    FROM event_club ec
                    WHERE ec.event_id = NEW.event_id
                    ON DUPLICATE KEY UPDATE
                        matches_played = matches_played + IF(v_event_type_name LIKE '%Match%', 1, 0),
                        tournaments_attended = tournaments_attended + IF(v_event_type_name LIKE '%Tournament%', 1, 0),
                        updated_at = NOW();
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_club_statistics_event_finished_decrement
            AFTER UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_event_type_name VARCHAR(50);

                IF OLD.status = 'finished' AND NEW.status <> 'finished' THEN
                    SELECT et.name INTO v_event_type_name
                    FROM event_types et
                    WHERE et.event_type_id = OLD.event_type_id;

                    UPDATE club_statistics cs
                    JOIN event_club ec ON ec.club_id = cs.club_id
                    SET
                        cs.matches_played = GREATEST(cs.matches_played - IF(v_event_type_name LIKE '%Match%', 1, 0), 0),
                        cs.tournaments_attended = GREATEST(cs.tournaments_attended - IF(v_event_type_name LIKE '%Tournament%', 1, 0), 0),
                        cs.updated_at = NOW()
                    WHERE ec.event_id = OLD.event_id;
                END IF;
            END
        ");


        DB::unprepared("
            CREATE TRIGGER trg_club_statistics_result_insert
            AFTER INSERT ON event_club_results
            FOR EACH ROW
            BEGIN
                INSERT INTO club_statistics (club_id, total_wins, total_losses, created_at, updated_at)
                VALUES (
                    NEW.club_id,
                    IF(NEW.ranking = 1, 1, 0),
                    IF(NEW.ranking > 1, 1, 0),
                    NOW(), NOW()
                )
                ON DUPLICATE KEY UPDATE
                    total_wins = total_wins + IF(NEW.ranking = 1, 1, 0),
                    total_losses = total_losses + IF(NEW.ranking > 1, 1, 0),
                    updated_at = NOW();
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_club_statistics_result_update
            AFTER UPDATE ON event_club_results
            FOR EACH ROW
            BEGIN
                UPDATE club_statistics
                SET total_wins = total_wins 
                    - IF(OLD.ranking = 1, 1, 0) 
                    + IF(NEW.ranking = 1, 1, 0),
                    total_losses = total_losses 
                    - IF(OLD.ranking > 1, 1, 0) 
                    + IF(NEW.ranking > 1, 1, 0),
                    updated_at = NOW()
                WHERE club_id = NEW.club_id;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_club_statistics_result_delete
            AFTER DELETE ON event_club_results
            FOR EACH ROW
            BEGIN
                UPDATE club_statistics
                SET total_wins = GREATEST(total_wins - IF(OLD.ranking = 1, 1, 0), 0),
                    total_losses = GREATEST(total_losses - IF(OLD.ranking > 1, 1, 0), 0),
                    updated_at = NOW()
                WHERE club_id = OLD.club_id;
            END
        ");

    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_club_statistics_event_finished_decrement');
        DB::statement('DROP TRIGGER IF EXISTS trg_club_statistics_event_finished_increment');
        DB::statement('DROP TRIGGER IF EXISTS trg_club_statistics_result_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_club_statistics_result_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_club_statistics_result_delete');
        Schema::dropIfExists('club_statistics');
    }
};