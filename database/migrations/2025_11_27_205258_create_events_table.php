<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\EventStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');

            $table->foreignId('parent_event_id')
                ->nullable()
                ->constrained('events', 'event_id')
                ->nullOnDelete();

            $table->foreignId('reservation_id')
                ->nullable()
                ->constrained('reservations', 'reservation_id')
                ->restrictOnDelete();

            $table->foreignId('sport_field_id')
                ->constrained('sport_fields', 'sport_field_id')
                ->restrictOnDelete();

            $table->foreignId('event_type_id')
                ->constrained('event_types', 'event_type_id')
                ->restrictOnDelete();

            $table->string('title', 255);
            $table->longText('description')->nullable();

            $table->enum('status', array_map(fn(EventStatus $status) => $status->value, EventStatus::cases()))
                ->default(EventStatus::SCHEDULED->value);

            $table->dateTime('start_date', 0);
            $table->dateTime('end_date', 0);
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_event_id');
            $table->index('sport_field_id');
            $table->index('event_type_id');
            $table->index('status');
            $table->index('start_date');
        });

        DB::unprepared("CREATE TRIGGER trg_event_no_self_parent_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                IF NEW.parent_event_id = NEW.event_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'EVENT CANNOT BE ITS OWN PARENT.';
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_no_self_parent_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                IF NEW.parent_event_id = NEW.event_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'EVENT CANNOT BE ITS OWN PARENT.';
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_sport_match_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_event_type_sport_id INT;
                DECLARE v_sport_field_match INT;
 
                SELECT sport_id INTO v_event_type_sport_id
                FROM event_types
                WHERE event_type_id = NEW.event_type_id;
 
                SELECT COUNT(*) INTO v_sport_field_match
                FROM sport_fields_sports
                WHERE sport_field_id = NEW.sport_field_id
                AND sport_id = v_event_type_sport_id;
 
                IF v_sport_field_match = 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'EVENT TYPE SPORT MUST MATCH SPORT FIELD SPORT.';
                END IF;
            END
        ");
 
        DB::unprepared("CREATE TRIGGER trg_event_sport_match_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_event_type_sport_id INT;
                DECLARE v_sport_field_match INT;
 
                SELECT sport_id INTO v_event_type_sport_id
                FROM event_types
                WHERE event_type_id = NEW.event_type_id;
 
                SELECT COUNT(*) INTO v_sport_field_match
                FROM sport_fields_sports
                WHERE sport_field_id = NEW.sport_field_id
                AND sport_id = v_event_type_sport_id;
 
                IF v_sport_field_match = 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'EVENT TYPE SPORT MUST MATCH SPORT FIELD SPORT.';
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_reservation_overlap_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                DECLARE v_is_tournament INT;

                SELECT COUNT(*) INTO v_is_tournament
                FROM event_types
                WHERE event_type_id = NEW.event_type_id
                AND name LIKE '%Tournament%';

                IF NEW.status NOT IN ('canceled', 'finished') AND v_is_tournament = 0 THEN
                    SELECT COUNT(*) INTO v_count
                    FROM reservations
                    WHERE sport_field_id = NEW.sport_field_id
                    AND deleted_at IS NULL
                    AND status NOT IN ('canceled', 'converted')
                    AND start_date < NEW.end_date
                    AND end_date > NEW.start_date
                    AND (NEW.reservation_id IS NULL OR reservation_id != NEW.reservation_id);

                    IF v_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'FIELD IS ALREADY RESERVED AT THIS TIME.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_reservation_overlap_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                DECLARE v_is_tournament INT;

                SELECT COUNT(*) INTO v_is_tournament
                FROM event_types
                WHERE event_type_id = NEW.event_type_id
                AND name LIKE '%Tournament%';

                IF NEW.status NOT IN ('canceled', 'finished') AND v_is_tournament = 0 THEN
                    SELECT COUNT(*) INTO v_count
                    FROM reservations
                    WHERE sport_field_id = NEW.sport_field_id
                    AND deleted_at IS NULL
                    AND status NOT IN ('canceled', 'converted')
                    AND start_date < NEW.end_date
                    AND end_date > NEW.start_date
                    AND (NEW.reservation_id IS NULL OR reservation_id != NEW.reservation_id);

                    IF v_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'FIELD IS ALREADY RESERVED AT THIS TIME.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_event_overlap_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                DECLARE v_is_tournament INT;

                SELECT COUNT(*) INTO v_is_tournament
                FROM event_types
                WHERE event_type_id = NEW.event_type_id
                AND name LIKE '%Tournament%';

                IF NEW.status NOT IN ('canceled', 'finished') AND v_is_tournament = 0 THEN
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
                        SET MESSAGE_TEXT = 'FIELD ALREADY HAS AN EVENT AT THIS TIME.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_event_overlap_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                DECLARE v_is_tournament INT;

                SELECT COUNT(*) INTO v_is_tournament
                FROM event_types
                WHERE event_type_id = NEW.event_type_id
                AND name LIKE '%Tournament%';

                IF NEW.status NOT IN ('canceled', 'finished') AND v_is_tournament = 0 THEN
                    SELECT COUNT(*) INTO v_count
                    FROM events
                    JOIN event_types ON event_types.event_type_id = events.event_type_id
                    WHERE events.sport_field_id = NEW.sport_field_id
                    AND events.deleted_at IS NULL
                    AND events.status NOT IN ('canceled', 'finished')
                    AND event_types.name NOT LIKE '%Tournament%'
                    AND events.start_date < NEW.end_date
                    AND events.end_date > NEW.start_date
                    AND events.event_id != NEW.event_id;

                    IF v_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'FIELD ALREADY HAS AN EVENT AT THIS TIME.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_valid_dates_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                IF NEW.end_date <= NEW.start_date THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'END DATE MUST BE LATER THAN START DATE.';
                END IF;
            END"
        );

        DB::unprepared("CREATE TRIGGER trg_event_valid_dates_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                IF NEW.end_date <= NEW.start_date THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'END DATE MUST BE LATER THAN START DATE.';
                END IF;
            END"
        );

        DB::unprepared("CREATE TRIGGER trg_event_reservation_consistency_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;

                IF NEW.reservation_id IS NOT NULL THEN
                    SELECT COUNT(*) INTO v_count
                    FROM reservations
                    WHERE reservation_id = NEW.reservation_id
                    AND deleted_at IS NULL
                    AND status NOT IN ('canceled', 'converted')
                    AND sport_field_id = NEW.sport_field_id
                    AND NEW.start_date >= start_date
                    AND NEW.end_date <= end_date;

                    IF v_count = 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'EVENT DOES NOT MATCH THE SELECTED RESERVATION.';
                    END IF;
                END IF;
            END"
        );

        DB::unprepared("CREATE TRIGGER trg_event_child_sport_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_parent_sport_id INT;
                DECLARE v_child_sport_id INT;

                IF NEW.parent_event_id IS NOT NULL THEN
                    SELECT et.sport_id INTO v_parent_sport_id
                    FROM events e
                    JOIN event_types et ON et.event_type_id = e.event_type_id
                    WHERE e.event_id = NEW.parent_event_id;

                    SELECT sport_id INTO v_child_sport_id
                    FROM event_types
                    WHERE event_type_id = NEW.event_type_id;

                    IF v_parent_sport_id != v_child_sport_id THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'CHILD EVENT SPORT MUST MATCH PARENT EVENT SPORT.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_child_sport_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_parent_sport_id INT;
                DECLARE v_child_sport_id INT;

                IF NEW.parent_event_id IS NOT NULL THEN
                    SELECT et.sport_id INTO v_parent_sport_id
                    FROM events e
                    JOIN event_types et ON et.event_type_id = e.event_type_id
                    WHERE e.event_id = NEW.parent_event_id;

                    SELECT sport_id INTO v_child_sport_id
                    FROM event_types
                    WHERE event_type_id = NEW.event_type_id;

                    IF v_parent_sport_id != v_child_sport_id THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'CHILD EVENT SPORT MUST MATCH PARENT EVENT SPORT.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_child_dates_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_parent_start DATETIME;
                DECLARE v_parent_end DATETIME;

                IF NEW.parent_event_id IS NOT NULL THEN
                    SELECT start_date, end_date INTO v_parent_start, v_parent_end
                    FROM events
                    WHERE event_id = NEW.parent_event_id;

                    IF NEW.start_date < v_parent_start OR NEW.end_date > v_parent_end THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'CHILD EVENT DATES MUST BE WITHIN PARENT EVENT INTERVAL.';
                    END IF;
                END IF;
            END
        ");

        DB::unprepared("CREATE TRIGGER trg_event_child_dates_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_parent_start DATETIME;
                DECLARE v_parent_end DATETIME;

                IF NEW.parent_event_id IS NOT NULL THEN
                    SELECT start_date, end_date INTO v_parent_start, v_parent_end
                    FROM events
                    WHERE event_id = NEW.parent_event_id;

                    IF NEW.start_date < v_parent_start OR NEW.end_date > v_parent_end THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'CHILD EVENT DATES MUST BE WITHIN PARENT EVENT INTERVAL.';
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
        DB::statement('DROP TRIGGER IF EXISTS trg_event_no_self_parent_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_no_self_parent_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_sport_match_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_sport_match_update');   
        DB::statement('DROP TRIGGER IF EXISTS trg_event_reservation_overlap_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_reservation_overlap_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_event_overlap_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_event_overlap_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_valid_dates_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_valid_dates_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_reservation_consistency_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_child_sport_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_child_sport_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_child_dates_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_child_dates_update');

        Schema::dropIfExists('events');
    }
};