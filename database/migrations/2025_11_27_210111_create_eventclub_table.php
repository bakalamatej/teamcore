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
            CREATE TRIGGER trg_event_statistics_club_insert
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
 
        DB::unprepared("
            CREATE TRIGGER trg_event_statistics_club_delete
            AFTER DELETE ON event_club
            FOR EACH ROW
            BEGIN
                UPDATE event_statistics
                SET total_teams = GREATEST(total_teams - 1, 0),
                    updated_at = NOW()
                WHERE event_id = OLD.event_id;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_event_club_sport_must_match_event_sport
            BEFORE INSERT ON event_club
            FOR EACH ROW
            BEGIN
                DECLARE v_club_sport_id BIGINT UNSIGNED;
                DECLARE v_event_sport_id BIGINT UNSIGNED;

                SELECT c.sport_id
                INTO v_club_sport_id
                FROM clubs c
                WHERE c.club_id = NEW.club_id;

                SELECT et.sport_id
                INTO v_event_sport_id
                FROM events e
                JOIN event_types et ON et.event_type_id = e.event_type_id
                WHERE e.event_id = NEW.event_id;

                IF v_club_sport_id <> v_event_sport_id THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'CLUB SPORT MUST MATCH EVENT SPORT.';
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_event_club_child_must_belong_to_parent_insert
            BEFORE INSERT ON event_club
            FOR EACH ROW
            BEGIN
                DECLARE v_parent_event_id BIGINT UNSIGNED;
                DECLARE v_count INT;

                SELECT parent_event_id
                INTO v_parent_event_id
                FROM events
                WHERE event_id = NEW.event_id;

                IF v_parent_event_id IS NOT NULL THEN
                    SELECT COUNT(*)
                    INTO v_count
                    FROM event_club
                    WHERE event_id = v_parent_event_id
                    AND club_id = NEW.club_id;

                    IF v_count = 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'CHILD EVENT CLUB MUST BELONG TO THE PARENT TOURNAMENT.';
                    END IF;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_event_statistics_club_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_statistics_club_delete');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_club_child_must_belong_to_parent_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_club_sport_must_match_event_sport');
        Schema::dropIfExists('event_club');
    }
};
