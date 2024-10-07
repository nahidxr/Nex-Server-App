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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('file_name'); // Stores the name of the file
            $table->string('original_file_name'); // Stores the original file name
            $table->string('folder');
            $table->string('file_id')->unique(); // Stores the file path
            $table->string('file_path'); // Stores the file path
            $table->integer('encoder_status')->default(0); // 0 = checking, 1 = start
            $table->integer('status')->nullable(); 
            $table->integer('flag')->nullable(); 
            $table->json('media_details'); // JSON column to store media details
            $table->json('profiles')->nullable(); // JSON column to store profiles as an array
            $table->boolean('transferred')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
