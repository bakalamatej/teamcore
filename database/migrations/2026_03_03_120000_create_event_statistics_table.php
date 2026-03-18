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
            CREATE TRIGGER trg_event_statistics_event_member_insert
            AFTER INSERT ON event_member
            FOR EACH ROW
            BEGIN
                DECLARE v_total_participants INT DEFAULT 0;
                DECLARE v_total_teams INT DEFAULT 0;

                SELECT COUNT(*) INTO v_total_participants
                FROM event_member
                WHERE event_id = NEW.event_id;

                SELECT COUNT(DISTINCT mc.club_id) INTO v_total_teams
                FROM event_member em
                JOIN member_club mc ON mc.member_club_id = em.member_club_id
                WHERE em.event_id = NEW.event_id;

                INSERT INTO event_statistics (event_id, total_participants, total_teams, created_at, updated_at)
                VALUES (NEW.event_id, v_total_participants, v_total_teams, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    total_participants = v_total_participants,
                    total_teams = v_total_teams,
                    updated_at = NOW();
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_event_statistics_event_member_delete
            AFTER DELETE ON event_member
            FOR EACH ROW
            BEGIN
                DECLARE v_total_participants INT DEFAULT 0;
                DECLARE v_total_teams INT DEFAULT 0;

                SELECT COUNT(*) INTO v_total_participants
                FROM event_member
                WHERE event_id = OLD.event_id;

                SELECT COUNT(DISTINCT mc.club_id) INTO v_total_teams
                FROM event_member em
                JOIN member_club mc ON mc.member_club_id = em.member_club_id
                WHERE em.event_id = OLD.event_id;

                INSERT INTO event_statistics (event_id, total_participants, total_teams, created_at, updated_at)
                VALUES (OLD.event_id, v_total_participants, v_total_teams, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    total_participants = v_total_participants,
                    total_teams = v_total_teams,
                    updated_at = NOW();
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_event_statistics_event_member_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_statistics_event_member_delete');
        Schema::dropIfExists('event_statistics');
    }
};
