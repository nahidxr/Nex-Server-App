<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentProfile extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'width', 'height', 'video_bitrate', 'frame_rate', 'audio_bitrate'];
}