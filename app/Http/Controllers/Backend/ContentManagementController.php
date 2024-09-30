<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\TemporaryFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Support\Facades\Log;
use FFMpeg\FFMpeg;
use Date;

class ContentManagementController extends Controller
{

    public function index()
    {
        $contents = Content::all()->map(function ($content) {
            // Decode the all_details JSON string
            $content->media_details = json_decode($content->media_details, true);
            return $content;
        });

       
        return view('backend.pages.content_management.index', compact('contents'));
    }


    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'file_name' => 'required|string|max:255',
            'video' => 'required|string', // The folder name
        ]);
    
        // Look for the temporary file using the video folder name from the request
        $tmp_file = TemporaryFile::where('folder', $request->video)->first();
    
        if ($tmp_file) {
            // Move the file from the temporary folder to the final destination
            Storage::copy('videos/tmp/' . $tmp_file->folder . '/' . $tmp_file->file, 'videos/' . $tmp_file->folder . '/' . $tmp_file->file);
    
            // Delete the temporary folder
            Storage::deleteDirectory('videos/tmp/' . $tmp_file->folder);
    
            // Delete the temporary file record
            $tmp_file->delete();

    
            // Save content in the database
            $content = new Content();
            $content->file_name = $request->file_name;
            $content->encoder_status = 0; // Update as needed
            $content->file_id = Str::random(10);
            $content->file_path = $tmp_file->folder . '/' . $tmp_file->file; // Path saved in the DB
            $content->folder = $tmp_file->folder; // Path saved in the DB

            $fileDir = public_path("storage/videos/$content->file_path");
            $content->media_details=$this->generateVideoDetails($fileDir);

    
            $content->save();
    
            return response()->json(['success' => 'Content uploaded successfully.']);
        }
    
        return response()->json(['error' => 'File upload failed.'], 422);
    }
    
    

    public function tmpUpload(Request $request)
    {
        // Check if the request has a video file
        if ($request->hasFile('video')) {
            $file = $request->file('video'); // Correct the variable name to $file
            $file_name = $file->getClientOriginalName(); // Get the original name of the file
            // $folder = uniqid() . '-' . now()->timestamp; // Generate a unique folder name
            $folder = uniqid('video' ,true); // Generate a unique folder name
    
            // Store the file in the uploads/tmp directory
            $video = $file->storeAs('videos/tmp/' . $folder, $file_name); // Corrected path with '/'
    
            // Save the temporary file info to the database
            TemporaryFile::create([
                'folder' => $folder,
                'file' => $file_name
            ]);
    
            return $folder; 
        }
    
        // If no file was uploaded, return a response or error
        return response()->json(['error' => 'No video file found'], 400);
    }
   
    
    public function tmpDelete()
    {
        // Retrieve the folder name directly from the request content
        $folderName = request()->getContent();

        dd($folderName);
    
        // Retrieve the temporary file based on the folder name
        $tem_file = TemporaryFile::where('folder', $folderName)->first();
    
        if ($tem_file) {
            // Delete the directory containing the uploaded file (corrected path)
            Storage::deleteDirectory('videos/tmp/' . $tem_file->folder);
    
            // Delete the database record of the temporary file
            $tem_file->delete();
    
            // Return a 200 response on successful deletion
            return response('', 200);
        }
    
        // Return a 404 response if the temporary file was not found
        return response()->json(['error' => 'Temporary file not found'], 404);
    }
    public function generateVideoDetails($filePath)
    {
        try {
            // Ensure the path is constructed correctly
            $fullPath = $filePath;
    
            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($fullPath);
    
            // Get video streams
            $streams = $video->getStreams();
    
            // Get the first video stream
            $videoStream = $streams->videos()->first();
    
            // Get video dimensions
            $videoDimensions = $videoStream->getDimensions();
            $width = $videoDimensions->getWidth();
            $height = $videoDimensions->getHeight();
    
            // File size (in bytes)
            $fileSize = filesize($fullPath);
    
            // Duration (in seconds)
            $duration = $video->getFormat()->get('duration');
    
            // Aspect ratio
            $aspectRatio = $this->calculateAspectRatio($width, $height);
    
            // Video codec
            $videoCodec = $videoStream->get('codec_name');
    
            // Video bitrate (in bits per second)
            $videoBitrate = $videoStream->get('bit_rate');
    
            // Frame rate (frames per second)
            $frameRate = $videoStream->get('r_frame_rate');
    
            // Get the first audio stream
            $audioStream = $streams->audios()->first();
    
            // Audio codec
            $audioCodec = $audioStream ? $audioStream->get('codec_name') : 'No audio stream';
            // Audio bitrate (in bits per second)
            $audioBitrate = $audioStream ? $audioStream->get('bit_rate') : 'No audio stream';
    
            // Sample rate (in Hz)
            $sampleRate = $audioStream ? $audioStream->get('sample_rate') : 'No audio stream';
    
            // Structure the details
            $fileDetails = [
                'width' => (string)$width,
                'height' => (string)$height,
                'file_size' => (string)$fileSize,
                'duration' => number_format((float)$duration, 2, '.', ''),
                'aspect_ratio' => $aspectRatio,
                'video_codec' => strtoupper($videoCodec), // Capitalize the codec name
                'video_bitrate' => (string)round($videoBitrate / 1000), // Convert to Kbps
                'frame_rate' => (string)$frameRate,
                'audio_codec' => strtoupper($audioCodec), // Capitalize the codec name
                'audio_bitrate' => (string)round($audioBitrate / 1000), // Convert to Kbps
                'sample_rate' => (string)$sampleRate,
                'all_details' => [
                    'width' => [
                        'title' => 'Width',
                        'value' => (string)$width,
                        'unit' => 'Pixel',
                        'display' => "{$width} Pixel",
                    ],
                    'height' => [
                        'title' => 'Height',
                        'value' => (string)$height,
                        'unit' => 'Pixel',
                        'display' => "{$height} Pixel",
                    ],
                    'dimensions' => [
                        'title' => 'Dimensions',
                        'value' => null,
                        'unit' => 'Pixel',
                        'display' => "{$width}x{$height}",
                    ],
                    'file_size' => [
                        'title' => 'File Size',
                        'value' => (string)$fileSize,
                        'unit' => 'MB',
                        'display' => number_format($fileSize / 1048576, 1) . " MB", // Convert bytes to MB
                    ],
                    'duration' => [
                        'title' => 'Duration',
                        'value' => number_format((float)$duration, 1), // Format duration to 1 decimal place
                        'unit' => 'sec',
                        'display' => number_format((float)$duration, 1) . " sec",
                    ],
                    'aspect_ratio' => [
                        'title' => 'Aspect Ratio',
                        'value' => $aspectRatio,
                        'unit' => '',
                        'display' => $aspectRatio,
                    ],
                    'video_codec' => [
                        'title' => 'Video Codec',
                        'value' => strtoupper($videoCodec),
                        'unit' => '',
                        'display' => strtoupper($videoCodec),
                    ],
                    'video_bitrate' => [
                        'title' => 'Video Bitrate',
                        'value' => (string)round($videoBitrate / 1000), // Convert bps to Kbps
                        'unit' => 'Kbps',
                        'display' => number_format($videoBitrate / 1000) . " Kbps",
                    ],
                    'frame_rate' => [
                        'title' => 'Frame Rate',
                        'value' => (string)$frameRate,
                        'unit' => 'fps',
                        'display' => (string)$frameRate . " fps",
                    ],
                    'audio_codec' => [
                        'title' => 'Audio Codec',
                        'value' => strtoupper($audioCodec),
                        'unit' => '',
                        'display' => strtoupper($audioCodec),
                    ],
                    'audio_bitrate' => [
                        'title' => 'Audio Bitrate',
                        'value' => (string)round($audioBitrate / 1000),
                        'unit' => 'Kbps',
                        'display' => number_format($audioBitrate / 1000) . " Kbps",
                    ],
                    'sample_rate' => [
                        'title' => 'Sample Rate',
                        'value' => (string)number_format($sampleRate / 1000, 1), // Convert Hz to kHz
                        'unit' => 'kHz',
                        'display' => number_format($sampleRate / 1000, 1) . " kHz",
                    ],
                ],
            ];
    
            // Return as JSON string, ensuring it's not double-encoded
            return json_encode($fileDetails);
        } catch (\Exception $e) {
            Log::error('Error generating video details: ' . $e->getMessage());
            return null;
        }
    }
    
    
    // Define calculateAspectRatio as a method
    private function calculateAspectRatio($width, $height)
    {
        // Find the greatest common divisor of width and height
        $gcd = $this->gcd($width, $height);

        // Divide both width and height by their GCD to get the ratio
        $aspectRatioWidth = $width / $gcd;
        $aspectRatioHeight = $height / $gcd;

        // Return the aspect ratio as a string
        return $aspectRatioWidth . ':' . $aspectRatioHeight;
    }

    // Define gcd as a method
    private function gcd($a, $b)
    {
        // Euclidean algorithm to find the greatest common divisor
        return ($b == 0) ? $a : $this->gcd($b, $a % $b);
    }

    public function destroy($id)
    {
        try {
        // Find the content by ID
        $content = Content::findOrFail($id);
    
        // Get the filename from the database
        $fileName = $content->file_path;
    
        // Construct the full storage path
        $fullPath = storage_path('app/public/videos/' . $fileName);
    
        // Delete the content from the database
        $content->delete();
    
        // Delete the file from storage
        if (file_exists($fullPath)) {

            Storage::deleteDirectory('videos/' . $content->folder);
            // unlink($fullPath); // Use unlink to delete the file
        }

        // Redirect back with a success message
        return redirect()->route('content.index'); // Adjust this to your index route

        } catch (\Exception $e) {
            return redirect()->route('content.index'); // Adjust this to your index route
        }
    }
    public function transferVideos()
    {
        // Define remote servers (server => port)
        $remoteServers = [
            ['server' => 'root@live-cdn.nexdecade.com:/video/', 'port' => 60424],
            ['server' => 'root@150.242.104.122:/video/', 'port' => 22],
        ];

        $totalServers = count($remoteServers);
        $counter = 0;

        // Fetch contents that have not been transferred yet
        $contents = Content::where('transferred', false)->get();

        if ($contents->isEmpty()) {
            Log::info('No videos to transfer.');
            return response()->json(['message' => 'No videos to transfer.'], 200);
        }

        foreach ($contents as $content) {
            try {
                $filePath = storage_path('app/public/videos/' . $content->file_path);

                if (!file_exists($filePath)) {
                    Log::warning("File not found: $filePath");
                    continue; // Skip to the next file
                }

                $remoteServer = $remoteServers[$counter]['server'];
                $sshPort = $remoteServers[$counter]['port'];

                Log::info("Transferring {$content->file_name} to {$remoteServer} on port {$sshPort}");

                // Prepare the SCP command
                // Use double quotes to allow variable expansion
                // escapeshellarg to prevent shell injection
                $scpCommand = sprintf(
                    'scp -P %d %s %s',
                    $sshPort,
                    escapeshellarg($filePath),
                    escapeshellarg($remoteServer)
                );
                

                // Execute the SCP command
                // exec($scpCommand, $output, $returnVar);

                if ($returnVar === 0) {
                    Log::info("Successfully transferred {$content->file_name} to {$remoteServer}");

                    // Optionally, mark as transferred
                    $content->transferred = true;
                    // $content->transferred_at = now(); // If using timestamp
                    $content->save();
                    
                } else {
                    Log::error("Failed to transfer {$content->file_name} to {$remoteServer}. Return code: $returnVar");
                }

                // Move to the next server using round-robin
                $counter = ($counter + 1) % $totalServers;

            } catch (Exception $e) {
                Log::error("Error transferring {$content->file_name}: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Video transfer process completed.'], 200);
    }




    
    
}
