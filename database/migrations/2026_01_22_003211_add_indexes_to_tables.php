<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index(['name', 'email']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['name', 'surname']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->index('sport_field_id');
            $table->index('event_type_id');
            $table->index('status');
            $table->index('start_date');
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->index('address_id');
            $table->index('name');
        });

        Schema::table('member_club', function (Blueprint $table) {
            $table->index('member_id');
            $table->index('club_id');
        });

        Schema::table('event_club', function (Blueprint $table) {
            $table->index('event_id');
            $table->index('club_id');
        });

        Schema::table('member_event', function (Blueprint $table) {
            $table->index('member_id');
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['name', 'email']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['name', 'surname']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['sport_field_id']);
            $table->dropIndex(['event_type_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date']);
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropIndex(['address_id']);
            $table->dropIndex(['name']);
        });

        Schema::table('member_club', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
            $table->dropIndex(['club_id']);
        });

        Schema::table('event_club', function (Blueprint $table) {
            $table->dropIndex(['event_id']);
            $table->dropIndex(['club_id']);
        });

        Schema::table('member_event', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
            $table->dropIndex(['event_id']);
        });
    }
};
