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
        Schema::create('content_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->unsignedInteger('width'); 
            $table->unsignedInteger('height');
            $table->string('video_bitrate');
            $table->string('frame_rate'); 
            $table->string('audio_bitrate'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_profiles');
    }
};
