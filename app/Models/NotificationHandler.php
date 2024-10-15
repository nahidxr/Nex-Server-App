<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationHandler extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'notification_type', 'url'];
    
}
