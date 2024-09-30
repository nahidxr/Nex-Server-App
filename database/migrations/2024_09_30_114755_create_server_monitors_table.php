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
        Schema::create('server_monitors', function (Blueprint $table) {
            $table->id();
            $table->string('server_name');
            $table->string('identifier');  // Can be IP, domain, or name
            $table->integer('check_interval');  // Interval in minutes
            $table->string('api_key')->unique();  // API Key, unique to each server
            $table->json('server_data')->nullable();  // JSON field to store server data
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_monitors');
    }
};
