<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

            $table->string('file_category', 30);
            $table->timestamps();

            $table->primary(['member_club_id', 'file_id']);
            $table->index(['member_club_id', 'file_category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_club_files');
    }
};
