<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use App\Enums\ReservationStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');

            $table->foreignId('sport_field_id')
                ->constrained('sport_fields', 'sport_field_id')
                ->restrictOnDelete();

            $table->foreignId('created_by_member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->restrictOnDelete();

            $table->string('title', 255);
            $table->longText('description')->nullable();

            $table->enum('status', array_map(fn(ReservationStatus $status) => $status->value, ReservationStatus::cases()))
                ->default(ReservationStatus::APPROVED->value);

            $table->dateTime('start_date', 0);
            $table->dateTime('end_date', 0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('sport_field_id');
            $table->index('start_date');
            $table->index('status');
        });

        DB::unprepared("CREATE TRIGGER trg_reservation_overlap
            BEFORE INSERT ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;

                IF NEW.status NOT IN ('canceled', 'converted') THEN
                    SELECT COUNT(*) INTO v_count
                    FROM reservations
                    WHERE sport_field_id = NEW.sport_field_id
                    AND deleted_at IS NULL
                    AND status NOT IN ('canceled', 'converted')
                    AND start_date < NEW.end_date
                    AND end_date > NEW.start_date;

                    IF v_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'FIELD IS ALREADY RESERVED AT THIS TIME.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_reservation_overlap_update
            BEFORE UPDATE ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;

                IF NEW.status NOT IN ('canceled', 'converted') THEN
                    SELECT COUNT(*) INTO v_count
                    FROM reservations
                    WHERE sport_field_id = NEW.sport_field_id
                    AND deleted_at IS NULL
                    AND status NOT IN ('canceled', 'converted')
                    AND start_date < NEW.end_date
                    AND end_date > NEW.start_date
                    AND reservation_id != NEW.reservation_id;

                    IF v_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'FIELD IS ALREADY RESERVED AT THIS TIME.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_reservation_event_overlap
            BEFORE INSERT ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;

                IF NEW.status NOT IN ('canceled', 'converted') THEN
                    SELECT COUNT(*) INTO v_count
                    FROM events
                    JOIN event_types ON event_types.event_type_id = events.event_type_id
                    WHERE events.sport_field_id = NEW.sport_field_id
                    AND events.deleted_at IS NULL
                    AND events.status NOT IN ('canceled', 'finished')
                    AND event_types.name NOT LIKE '%Tournament%'
                    AND events.start_date < NEW.end_date
                    AND events.end_date > NEW.start_date;

                    IF v_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'FIELD HAS AN EVENT AT THIS TIME.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_reservation_event_overlap_update
            BEFORE UPDATE ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;

                IF NEW.status NOT IN ('canceled', 'converted') THEN
                    SELECT COUNT(*) INTO v_count
                    FROM events
                    JOIN event_types ON event_types.event_type_id = events.event_type_id
                    WHERE events.sport_field_id = NEW.sport_field_id
                    AND events.deleted_at IS NULL
                    AND events.status NOT IN ('canceled', 'finished')
                    AND event_types.name NOT LIKE '%Tournament%'
                    AND events.start_date < NEW.end_date
                    AND events.end_date > NEW.start_date;

                    IF v_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'FIELD HAS AN EVENT AT THIS TIME.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_reservation_valid_dates_insert
            BEFORE INSERT ON reservations
            FOR EACH ROW
            BEGIN
                IF NEW.end_date <= NEW.start_date THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'END DATE MUST BE LATER THAN START DATE.';
                END IF;
            END"
        );

        DB::unprepared("CREATE TRIGGER trg_reservation_valid_dates_update
            BEFORE UPDATE ON reservations
            FOR EACH ROW
            BEGIN
                IF NEW.end_date <= NEW.start_date THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'END DATE MUST BE LATER THAN START DATE.';
                END IF;
            END"
        );

        DB::unprepared("CREATE TRIGGER trg_reservation_active_member_club_insert
            BEFORE INSERT ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;

                SELECT COUNT(*) INTO v_count
                FROM member_club
                WHERE member_club_id = NEW.created_by_member_club_id
                AND left_at IS NULL;

                IF v_count = 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'RESERVATION MUST BE CREATED BY AN ACTIVE MEMBER OF A CLUB.';
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_reservation_field_supports_club_sport_insert
            BEFORE INSERT ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;

                SELECT COUNT(*) INTO v_count
                FROM member_club mc
                JOIN clubs c ON c.club_id = mc.club_id
                JOIN sport_fields_sports sfs ON sfs.sport_id = c.sport_id
                WHERE mc.member_club_id = NEW.created_by_member_club_id
                AND mc.left_at IS NULL
                AND sfs.sport_field_id = NEW.sport_field_id;

                IF v_count = 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'FIELD DOES NOT SUPPORT THE CLUB SPORT.';
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_reservation_field_supports_club_sport_update
            BEFORE UPDATE ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;

                SELECT COUNT(*) INTO v_count
                FROM member_club mc
                JOIN clubs c ON c.club_id = mc.club_id
                JOIN sport_fields_sports sfs ON sfs.sport_id = c.sport_id
                WHERE mc.member_club_id = NEW.created_by_member_club_id
                AND mc.left_at IS NULL
                AND sfs.sport_field_id = NEW.sport_field_id;

                IF v_count = 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'FIELD DOES NOT SUPPORT THE CLUB SPORT.';
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
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_overlap_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_event_overlap');
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_event_overlap_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_valid_dates_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_valid_dates_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_active_member_club_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_field_supports_club_sport_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_reservation_field_supports_club_sport_update');

        Schema::dropIfExists('reservations');
    }
};