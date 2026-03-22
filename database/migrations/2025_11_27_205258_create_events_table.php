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

            $table->foreignId('sport_id')
                ->constrained('sports', 'sport_id')
                ->restrictOnDelete();

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

        DB::unprepared("
            CREATE TRIGGER trg_event_sport_field
            BEFORE INSERT ON events
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
            CREATE TRIGGER trg_event_no_self_parent_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                IF NEW.parent_event_id = NEW.event_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'EVENT CANNOT BE ITS OWN PARENT.';
                END IF;
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_event_reservation_overlap_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count
                FROM reservations
                WHERE sport_field_id = NEW.sport_field_id
                AND deleted_at IS NULL
                AND status NOT IN ('canceled', 'rejected')
                AND start_date < NEW.end_date
                AND end_date > NEW.start_date;
                IF v_count > 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FIELD IS ALREADY RESERVED AT THIS TIME.';
                END IF;
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_event_reservation_overlap_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count
                FROM reservations
                WHERE sport_field_id = NEW.sport_field_id
                AND deleted_at IS NULL
                AND status NOT IN ('canceled', 'rejected')
                AND start_date < NEW.end_date
                AND end_date > NEW.start_date;
                IF v_count > 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FIELD IS ALREADY RESERVED AT THIS TIME.';
                END IF;
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_event_event_overlap_insert
            BEFORE INSERT ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count
                FROM events
                WHERE sport_field_id = NEW.sport_field_id
                AND deleted_at IS NULL
                AND status NOT IN ('canceled', 'finished')
                AND start_date < NEW.end_date
                AND end_date > NEW.start_date;
                IF v_count > 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FIELD ALREADY HAS AN EVENT AT THIS TIME.';
                END IF;
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_event_event_overlap_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count
                FROM events
                WHERE sport_field_id = NEW.sport_field_id
                AND deleted_at IS NULL
                AND status NOT IN ('canceled', 'finished')
                AND start_date < NEW.end_date
                AND end_date > NEW.start_date
                AND event_id != NEW.event_id;
                IF v_count > 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FIELD ALREADY HAS AN EVENT AT THIS TIME.';
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_event_sport_field');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_no_self_parent_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_reservation_overlap_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_reservation_overlap_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_event_overlap_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_event_event_overlap_update');
        Schema::dropIfExists('events');
    }
};
