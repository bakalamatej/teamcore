<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_files', function (Blueprint $table) {
            $table->foreignId('club_id')
                ->constrained('clubs', 'club_id')
                ->cascadeOnDelete();

            $table->foreignId('file_id')
                ->constrained('files', 'file_id')
                ->cascadeOnDelete();

            $table->string('file_category', 30);
            $table->timestamps();

            $table->primary(['club_id', 'file_id']);
            $table->index(['club_id', 'file_category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_files');
    }
};
