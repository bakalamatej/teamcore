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
                ->restrictOnDelete();

            $table->unsignedInteger('total_participants')->default(0);
            $table->unsignedInteger('total_teams')->default(0);
            $table->timestamps();
        });

        DB::unprepared("
            CREATE TRIGGER trg_event_statistics_event_member_insert
            AFTER INSERT ON event_member
            FOR EACH ROW
            BEGIN
                INSERT INTO event_statistics (event_id, total_participants, total_teams, created_at, updated_at)
                VALUES (NEW.event_id, 1, 0, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    total_participants = total_participants + 1,
                    updated_at = NOW();
            END
        ");
 
        DB::unprepared("
            CREATE TRIGGER trg_event_statistics_event_member_delete
            AFTER DELETE ON event_member
            FOR EACH ROW
            BEGIN
                UPDATE event_statistics
                SET total_participants = GREATEST(total_participants - 1, 0),
                    updated_at = NOW()
                WHERE event_id = OLD.event_id;
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
