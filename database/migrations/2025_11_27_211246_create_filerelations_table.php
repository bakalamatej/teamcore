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
        Schema::create('file_relations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('file_id');
            $table->string('fileable_type', 50);
            $table->unsignedBigInteger('fileable_id');
            $table->string('file_category', 30);

            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');

            $table->unique(['file_id', 'fileable_id', 'fileable_type']);
            $table->index(['fileable_id', 'fileable_type']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filerelations');
    }
};
