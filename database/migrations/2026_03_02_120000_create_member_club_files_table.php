<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_club_files', function (Blueprint $table) {
            $table->foreignId('member_club_id')
                ->constrained('member_club', 'member_club_id')
                ->cascadeOnDelete();

            $table->foreignId('file_id')
                ->constrained('files', 'file_id')
                ->cascadeOnDelete();

            $table->foreignId('file_category_id')
                ->constrained('file_categories', 'file_category_id')
                ->restrictOnDelete();
            $table->timestamps();

            $table->primary(['member_club_id', 'file_id']);
            $table->index(['member_club_id', 'file_category_id']);
        });

        DB::unprepared("
            CREATE TRIGGER trg_member_stats_event_member_insert
            AFTER INSERT ON event_member
            FOR EACH ROW
            BEGIN
                INSERT INTO member_statistics (member_club_id, events_attended, created_at, updated_at)
                VALUES (NEW.member_club_id, 1, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    events_attended = events_attended + 1,
                    updated_at = NOW();
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_member_stats_event_member_delete
            AFTER DELETE ON event_member
            FOR EACH ROW
            BEGIN
                UPDATE member_statistics
                SET events_attended = GREATEST(events_attended - 1, 0),
                    updated_at = NOW()
                WHERE member_club_id = OLD.member_club_id;
            END
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_member_stats_event_member_delete');
        DB::statement('DROP TRIGGER IF EXISTS trg_member_stats_event_member_insert');
        Schema::dropIfExists('member_club_files');
    }
};
