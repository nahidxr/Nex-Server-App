<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerMonitor extends Model
{
    use HasFactory;

    protected $table = 'server_monitors';

    // Specify the fields that can be mass-assigned
    protected $fillable = [
        'server_name',      
        'identifier',      // IP, domain, or server name
        'check_interval',  // Check interval in minutes
        'api_key',         // API key
        'server_data',     // JSON data (server status, metrics, etc.)
        'status',     // JSON data (server status, metrics, etc.)
    ];

    // Specify any casts for the model
    // protected $casts = [
    //     'server_data' => 'array',  // Automatically cast server_data as an array when retrieved
    // ];
}
