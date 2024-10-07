<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name',
        'original_file_name',
        'file_path',
        'folder',
        'file_id',
        'transcoder_status',
        'media_details',
        'status',
        'flag',
        'profiles',
        'transferred',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'media_details' => 'json', // Cast media_details as JSON
    ];
}
