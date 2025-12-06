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
        Schema::create('event_member_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('member_id');
            $table->integer('score')->nullable();   
            $table->integer('ranking')->nullable();
            $table->text('note')->nullable();
            
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');

            $table->unique(['event_id', 'member_id']);

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberresults');
    }
};
