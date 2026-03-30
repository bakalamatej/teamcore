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
        Schema::create('event_member', function (Blueprint $table) {
            $table->foreignId('member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->cascadeOnDelete();

            $table->foreignId('event_id')
                ->constrained('events', 'event_id')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['member_club_id', 'event_id']);
            $table->index('event_id');
        });

        DB::unprepared("
            CREATE TRIGGER trg_event_member_club_must_belong_to_event
            BEFORE INSERT ON event_member
            FOR EACH ROW
            BEGIN
                DECLARE v_club_id BIGINT UNSIGNED;
                DECLARE v_count INT;

                SELECT club_id
                INTO v_club_id
                FROM member_club
                WHERE member_club_id = NEW.member_club_id;

                SELECT COUNT(*)
                INTO v_count
                FROM event_club
                WHERE event_id = NEW.event_id
                AND club_id = v_club_id;

                IF v_count = 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'MEMBER CLUB MUST BELONG TO EVENT CLUB.';
                END IF;
            END
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_event_member_club_must_belong_to_event');
        Schema::dropIfExists('event_member');
    }
};
