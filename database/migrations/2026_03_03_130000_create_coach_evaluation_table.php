<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coach_evaluation', function (Blueprint $table) {
            $table->id('evaluation_id');

            // Coach — referencuje members, nie member_club
            $table->foreignId('coach_member_id')
                ->constrained('members', 'member_id')
                ->restrictOnDelete();

            // Člen ktorý bol ohodnotený
            $table->foreignId('evaluated_by_member_id')
                ->nullable()
                ->constrained('members', 'member_id')
                ->nullOnDelete();

            $table->decimal('rating', 2, 1);
            $table->longText('comment')->nullable();
            $table->timestamps();

            $table->index('coach_member_id');
            $table->index('evaluated_by_member_id');
            $table->index('rating');
        });

        DB::unprepared("
            CREATE TRIGGER trg_coach_evaluation_insert
            BEFORE INSERT ON coach_evaluation
            FOR EACH ROW
            BEGIN
                DECLARE v_is_coach INT;
                DECLARE v_coach_exists INT;
                DECLARE v_evaluated_exists INT;

                SELECT COUNT(*) INTO v_coach_exists
                FROM members
                WHERE member_id = NEW.coach_member_id
                AND deleted_at IS NULL;

                IF v_coach_exists = 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'COACH MEMBER DOES NOT EXIST.';
                END IF;

                SELECT COUNT(*) INTO v_is_coach
                FROM member_club
                WHERE member_id = NEW.coach_member_id
                AND role = 'coach'
                AND left_at IS NULL;

                IF v_is_coach = 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NOT A COACH.';
                END IF;

                IF NEW.evaluated_by_member_id IS NOT NULL THEN
                    SELECT COUNT(*) INTO v_evaluated_exists
                    FROM members
                    WHERE member_id = NEW.evaluated_by_member_id
                    AND deleted_at IS NULL;

                    IF v_evaluated_exists = 0 THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'EVALUATED MEMBER DOES NOT EXIST.';
                    END IF;
                END IF;

                IF NEW.coach_member_id = NEW.evaluated_by_member_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'COACH CANNOT EVALUATE THEMSELVES.';
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_coach_evaluation_insert');
        Schema::dropIfExists('coach_evaluation');
    }
};