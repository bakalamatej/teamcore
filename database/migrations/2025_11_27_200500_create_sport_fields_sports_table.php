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
        Schema::create('sport_fields_sports', function (Blueprint $table) {
            $table->foreignId('sport_field_id')
                ->constrained('sport_fields', 'sport_field_id')
                ->cascadeOnDelete();

            $table->foreignId('sport_id')
                ->constrained('sports', 'sport_id')
                ->cascadeOnDelete();

            $table->primary(['sport_field_id', 'sport_id']);

            $table->index('sport_field_id');
            $table->index('sport_id');
        });

        DB::unprepared("
            CREATE TRIGGER trg_sport_fields_sports_delete
            BEFORE DELETE ON sport_fields_sports
            FOR EACH ROW
            BEGIN
                DECLARE v_event_count INT;
                
                SELECT COUNT(*) INTO v_event_count
                FROM events e
                JOIN event_types et ON et.event_type_id = e.event_type_id
                WHERE e.sport_field_id = OLD.sport_field_id
                AND et.sport_id = OLD.sport_id
                AND e.deleted_at IS NULL;
                
                IF v_event_count > 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'CANNOT REMOVE SPORT FROM FIELD WITH EXISTING EVENTS.';
                END IF;
            END
        ");
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_sport_fields_sports_delete");
        Schema::dropIfExists('sport_fields_sports');
    }
};
