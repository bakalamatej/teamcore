<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\MemberClubRole;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_club', function (Blueprint $table) {
            $table->id('member_club_id');

            $table->foreignId('member_id')
                ->constrained('members', 'member_id')
                ->restrictOnDelete();

            $table->foreignId('club_id')
                ->constrained('clubs', 'club_id')
                ->restrictOnDelete();

            $table->foreignId('sport_id')
                ->constrained('sports', 'sport_id')
                ->restrictOnDelete();

            $table->enum('role', array_map(fn(MemberClubRole $role) => $role->value, MemberClubRole::cases()))->default(MemberClubRole::PLAYER->value);
            $table->date('joined_at');
            $table->date('left_at')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'club_id']);
            $table->index(['club_id', 'role']);
            $table->index(['club_id', 'sport_id']);
            $table->index('left_at'); 
        });

        DB::unprepared("
            CREATE TRIGGER trg_member_club_active
            BEFORE INSERT ON member_club
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count 
                FROM member_club 
                WHERE member_id = NEW.member_id 
                AND club_id = NEW.club_id 
                AND left_at IS NULL;
                IF v_count > 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'MEMBER IS ALREADY AN ACTIVE MEMBER OF THIS CLUB.';
                END IF;
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_club_stats_member_insert
            AFTER INSERT ON member_club
            FOR EACH ROW
            BEGIN
                INSERT INTO club_statistics (club_id, active_members, total_coaches, created_at, updated_at)
                VALUES (NEW.club_id, 1, IF(NEW.role = 'coach', 1, 0), NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    active_members = active_members + 1,
                    total_coaches = total_coaches + IF(NEW.role = 'coach', 1, 0),
                    updated_at = NOW();
            END
        ");
        DB::unprepared("
            CREATE TRIGGER trg_club_stats_member_leave
            AFTER UPDATE ON member_club
            FOR EACH ROW
            BEGIN
                IF OLD.left_at IS NULL AND NEW.left_at IS NOT NULL THEN
                    UPDATE club_statistics
                    SET active_members = GREATEST(active_members - 1, 0),
                        total_coaches = GREATEST(total_coaches - IF(NEW.role = 'coach', 1, 0), 0),
                        updated_at = NOW()
                    WHERE club_id = NEW.club_id;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_club_stats_member_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_club_stats_member_leave');
        DB::statement('DROP TRIGGER IF EXISTS trg_member_club_active');
        Schema::dropIfExists('member_club');
    }
};
