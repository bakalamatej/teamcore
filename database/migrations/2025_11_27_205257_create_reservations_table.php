<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\ReservationStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');

            $table->foreignId('sport_id')
                ->constrained('sports', 'sport_id')
                ->restrictOnDelete();

            $table->foreignId('sport_field_id')
                ->constrained('sport_fields', 'sport_field_id')
                ->restrictOnDelete();

            $table->foreignId('club_id')
                ->constrained('clubs', 'club_id')
                ->restrictOnDelete();

            $table->foreignId('created_by_member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->restrictOnDelete();

            $table->string('title', 255);
            $table->longText('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');

            $table->enum('status', array_map(fn(ReservationStatus $status) => $status->value, ReservationStatus::cases()))
                ->default(ReservationStatus::PENDING->value);

            $table->timestamps();
            $table->softDeletes();

            $table->index('sport_id');
            $table->index('club_id');
            $table->index('sport_field_id');
            $table->index('status');
            $table->index('start_date');
        });

        DB::unprepared("
            CREATE TRIGGER trg_reservation_overlap
            BEFORE INSERT ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count
                FROM reservations
                WHERE sport_field_id = NEW.sport_field_id
                AND status NOT IN ('canceled', 'rejected')
                AND start_date < NEW.end_date
                AND end_date > NEW.start_date;
                IF v_count > 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FIELD IS ALREADY RESERVED AT THIS TIME.';
                END IF;
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_reservation_sport_field
            BEFORE INSERT ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count
                FROM sport_fields_sports
                WHERE sport_field_id = NEW.sport_field_id
                AND sport_id = NEW.sport_id;
                IF v_count = 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FIELD DOES NOT SUPPORT THIS SPORT.';
                END IF;
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_reservation_sport_club
            BEFORE INSERT ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count
                FROM club_sport
                WHERE club_id = NEW.club_id
                AND sport_id = NEW.sport_id;
                IF v_count = 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'CLUB DOES NOT HAVE THIS SPORT ASSIGNED.';
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_overlap');
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_sport_club');
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_sport_field');
        Schema::dropIfExists('reservations');
    }
};
