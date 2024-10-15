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
        Schema::table('server_monitors', function (Blueprint $table) {
            $table->json('alerts')->nullable();
            $table->string('notification')->nullable();  // Field for notification type (e.g., Slack, Webhook)
            $table->string('project_name')->nullable();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('server_monitors', function (Blueprint $table) {
            $table->dropColumn('alerts'); 
            $table->dropColumn('notification');
            $table->dropColumn('project_name');
        });
    }
};
