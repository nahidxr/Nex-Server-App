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
        'identifier',
        'check_interval',
        'api_key',
        'alerts',
        'notification',
        'project_name',
    ];

    protected $casts = [
        'alerts' => 'array', // This will automatically handle JSON encoding/decoding
    ];

    // // Alternatively, if you want to manually decode:
    // public function getAlertsAttribute($value)
    // {
    //     return json_decode($value, true); // true to return as an associative array
    // }
}
