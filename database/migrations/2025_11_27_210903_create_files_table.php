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
        Schema::create('files', function (Blueprint $table) {
            $table->id('file_id');

            $table->foreignId('uploaded_by_user_id')
                ->constrained('users', 'user_id')
                ->restrictOnDelete();

            $table->string('file_name', 300);     // original name
            $table->string('file_path', 500);     // stored path
            $table->string('file_type', 50);      // mime type
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->index('uploaded_by_user_id');
            $table->index('file_type');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
