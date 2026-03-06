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
        Schema::create('coach_evaluation', function (Blueprint $table) {
            $table->id('evaluation_id');

            $table->foreignId('coach_member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->restrictOnDelete();

            $table->foreignId('evaluated_by_member_id')
                ->constrained('members', 'member_id')
                ->restrictOnDelete();

            $table->foreignId('reservation_id')
                ->constrained('reservations', 'reservation_id')
                ->restrictOnDelete();

            $table->decimal('rating', 3, 1);
            $table->longText('comment')->nullable();
            $table->timestamps();

            $table->index('coach_member_club_id');
            $table->index('evaluated_by_member_id');
            $table->index('rating');
        });

        DB::unprepared("
            CREATE TRIGGER trg_coach_evaluation_role
            BEFORE INSERT ON coach_evaluation
            FOR EACH ROW
            BEGIN
                DECLARE v_role VARCHAR(30);
                SELECT role INTO v_role FROM member_club WHERE member_club_id = NEW.coach_member_club_id;
                IF v_role != 'coach' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NOT A COACH.';
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_coach_evaluation_role');
        Schema::dropIfExists('coach_evaluation');
    }
};
