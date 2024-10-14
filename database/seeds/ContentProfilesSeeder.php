<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentProfile;

class ContentProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            [
                'name' => 'Profile 1',
                'width' => 1920,
                'height' => 1080,
                'video_bitrate' => '1.2 Mbps',
                'frame_rate' => '25 fps',
                'audio_bitrate' => '128 Kbps',
            ],
            [
                'name' => 'Profile 2',
                'width' => 1280,
                'height' => 720,
                'video_bitrate' => '1 Mbps',
                'frame_rate' => '25 fps',
                'audio_bitrate' => '128 Kbps',
            ],
            [
                'name' => 'Profile 3',
                'width' => 854,
                'height' => 480,
                'video_bitrate' => '856 Kbps',
                'frame_rate' => '25 fps',
                'audio_bitrate' => '128 Kbps',
            ],
            [
                'name' => 'Profile 4',
                'width' => 640,
                'height' => 360,
                'video_bitrate' => '512 Kbps',
                'frame_rate' => '25 fps',
                'audio_bitrate' => '128 Kbps',
            ],
            [
                'name' => 'Profile 5',
                'width' => 426,
                'height' => 240,
                'video_bitrate' => '360 Kbps',
                'frame_rate' => '25 fps',
                'audio_bitrate' => '128 Kbps',
            ],
            [
                'name' => 'Profile 6',
                'width' => 256,
                'height' => 144,
                'video_bitrate' => '180 Kbps',
                'frame_rate' => '25 fps',
                'audio_bitrate' => '128 Kbps',
            ],
        ];

        foreach ($profiles as $profile) {
            ContentProfile::create($profile);
        }
    }
}
