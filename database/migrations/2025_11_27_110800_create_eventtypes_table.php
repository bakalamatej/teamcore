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
        Schema::create('event_types', function (Blueprint $table) {
            $table->id('event_type_id');

            $table->foreignId('sport_id')
                ->constrained('sports', 'sport_id')
                ->restrictOnDelete();

            $table->string('name', 50);

            $table->unique(['sport_id', 'name']);
            $table->index('sport_id');
        });

        DB::unprepared("
            CREATE TRIGGER trg_event_type_sport_update
            BEFORE UPDATE ON event_types
            FOR EACH ROW
            BEGIN
                DECLARE v_event_count INT;
                
                IF OLD.sport_id != NEW.sport_id THEN
                    SELECT COUNT(*) INTO v_event_count
                    FROM events
                    WHERE event_type_id = OLD.event_type_id
                    AND deleted_at IS NULL;
                    
                    IF v_event_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'CANNOT CHANGE SPORT FOR EVENT TYPE WITH EXISTING EVENTS.';
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
        DB::unprepared("DROP TRIGGER IF EXISTS trg_event_type_sport_update");
        Schema::dropIfExists('event_types');
    }
};
