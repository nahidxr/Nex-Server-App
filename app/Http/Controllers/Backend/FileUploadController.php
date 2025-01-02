<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Content;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Str;
use FFMpeg\Coordinate\TimeCode;
use Google\Cloud\Storage\StorageClient;
use App\Models\ContentProfile;
use Date;



class FileUploadController extends Controller
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
    public function create()
    {
        // Retrieve all content profiles
        $profiles = ContentProfile::all();
    
        // Pass the profiles to the view
        return view('backend.pages.content_management.create', compact('profiles'));
    }

    // public function create()
    // {
    //     return view('backend.pages.content_management.create');
    // }

    
    public function store(Request $request)
    {
        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return $this->saveFile($save->getFile());
        }

        // we are in chunk mode, lets send the current progress
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    /**
     * Saves the file to S3 server
     *
     * @param UploadedFile $file
     *
     * @return JsonResponse
     */
   

    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return JsonResponse
     */
    protected function saveFile(UploadedFile $file)
    {
        $fileName = $this->createFilename($file);
        $originalFileName = $file->getClientOriginalName();
        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());

        // Group files by the date (week
        $dateFolder = date("Y-m-W");

        // Build the file path
        $filePath = "upload/{$mime}/{$dateFolder}";
        $finalPath = storage_path("app/public/" . $filePath);

        // move the file name
        $file->move($finalPath, $fileName);

        return response()->json([
            'path' => asset('storage/public/' . $filePath),
            'name' => $fileName,
            'mime_type' => $mime,
            'originalFileName' => $originalFileName,
        ]);
    }
    




    protected function saveFileToBucket(Request $request)
    {
        $validated = $request->validate([
            'file_name' => 'required|string|max:255', // Validate file name
            'file_title' => 'required|string|max:255', // Validate file name
            'file_path' => 'required|string', // Validate file path
            'originalFileName' => 'required|string|max:255' ,
           
        ]);

            // Ensure that at least one of 'profiles' or 'selected_profiles' is provided
            // if (empty($request->input('profiles')) && empty($request->input('selected_profiles'))) {
            //     $message = 'Either profiles or selected_profiles must be provided.';

            //     return response()->json(['message' => $message, 'status' => 'danger'], 422);
            // }

            // Process and merge profiles into a single array
            // $finalProfiles = $this->mergeProfiles(
            //     $request->input('profiles', []),  // Default to an empty array if null
            //     $request->input('selected_profiles', [])
            // );

           


        // Get the uploaded file
        $originalFileName = $validated['originalFileName'];
        $fileName = $validated['file_name'];
        $filePath = 'upload/' . $validated['file_path'] . '/' .  $originalFileName; // Get the original file name
        $localFilePath = public_path("storage/public/upload/{$validated['file_path']}/{$validated['file_name']}");

        // Generate a unique filename
        $extension = pathinfo( $originalFileName, PATHINFO_EXTENSION);
        $uniqueFileId = Str::random(32);
        $uniqueFile =  $uniqueFileId . '.' . $extension; // Unique ID + extension
    
        // Define the destination path in the GCS bucket
        $destinationPath = 'uploads/' . $uniqueFile;
    
        // Prepare the StorageClient
        $storage = new StorageClient([
            'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'keyFilePath' => env('GOOGLE_CLOUD_KEY_FILE'),
        ]);
    
        $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
    
        try {

            if (file_exists($localFilePath)) {
                 //  Attempt to upload the file to GCS
                // $object = $bucket->upload(
                //     fopen($localFilePath, 'r'), // Open the local file for reading
                //     ['name' => $destinationPath] // The path in the GCS bucket
                // );
               
                $content = new Content();
                $content->original_file_name = pathinfo( $originalFileName, PATHINFO_FILENAME); // Store the original filename without extension
                $content->file_name = $validated['file_title'];
                $content->encoder_status = 0; // Update as needed
                $content->file_id = $uniqueFileId;
                $content->file_path = $destinationPath; // Path in the GCS bucket
                $content->folder = $validated['file_path']; // Store the folder path
                // $content->profiles = json_encode($finalProfiles);
                $fileDir = public_path("storage/public/upload/{$validated['file_path']}/{$validated['file_name']}");
                $content->media_details = $this->generateVideoDetails($fileDir);
                $content->save();
                unlink($localFilePath); // Delete the file
            
                // session()->flash('success', 'File has successfully uploaded!');
    
                $message = 'File uploaded successfully!';

                return response()->json(['message' => $message, 'status' => 'success'], 200);
            }else {
            // If the file doesn't exist, log an error
            Log::error('Local file not found for uploading: ' . $localFilePath);
            return response()->json(['error' => 'Local file not found.'], 404);
        }
    
        } catch (\Exception $e) {
            // Handle exceptions
            // session()->flash('error', 'File uploading failed!');
            $error = 'Failed to upload file.';

            return response()->json(['message' => $error, 'status' => 'error'], 500);
        }
    }
    private function mergeProfiles(?array $profiles = [], ?array $selectedProfiles = []): array
    {
        // Ensure both inputs are arrays
        $profiles = $profiles ?? [];
        $selectedProfiles = $selectedProfiles ?? [];

        // Convert selected profiles and merge with standard profiles
        $convertedSelectedProfiles = array_map(function ($profile) {
            return [
                'width' => (string) $profile['width'],
                'height' => (string) $profile['height'],
                'audio_bitrate' => (string) $this->extractBitrate($profile['audio_bitrate']),
                'video_bitrate' => (string) $this->extractBitrate($profile['video_bitrate']),
                'fps' => (string) $profile['fps'],
            ];
        }, $selectedProfiles);

        // Merge both profile arrays
        return array_merge($profiles, $convertedSelectedProfiles);
    }
    
    /**
     * Extract bitrate from string, converting Mbps to Kbps when necessary.
     */
    private function extractBitrate(string $bitrate): int
    {
        $bitrate = strtolower($bitrate);
    
        // Convert Mbps to Kbps if necessary
        if (str_contains($bitrate, 'mbps')) {
            return (int) ((float) filter_var($bitrate, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) * 1000);
        }
    
        // Extract and return integer value for Kbps
        return (int) filter_var($bitrate, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Create unique filename for uploaded file
    
    
    
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace("." . $extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }
    public function tmpDelete(Request $request)
    {
        // Extract the video path from the request
        $videoPath = $request->input('video_path');
    
        // The received video path is something like "storage/public/upload/..."
        // We need to convert it to "storage/app/public/upload/..." to delete the actual file
        $filePath = storage_path('app/public/' . str_replace('storage/public/', '', $videoPath));    
        // Check if the file exists in the storage path
        if (File::exists($filePath)) {
            // Delete the file
            File::delete($filePath);
            return response()->json(['message' => 'Video deleted successfully'], 200);
        }
    
        return response()->json(['message' => 'Video not found'], 404);
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
            $content = Content::findOrFail($id); // Ensure this is the correct model

            $content->delete();


            return redirect()->route('upload.index')->with('success', 'Content deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('upload.index')->with('error', 'An error occurred while deleting the content: ' . $e->getMessage());
        }
    }

    // public function showLog($id)
    // {
    //     // Query the Content model based on the provided id
    //     $content = Content::find($id);
        
    //     // Check if content is found
    //     if (!$content) {
    //         abort(404, 'Content not found.');
    //     }
    
    //     // Get the file_id from the Content model
    //     $fileId = $content->file_id;
    
    //     // File path for the log file
    //     $filePath = '/usr/local/nexdecade/nex-vod-processing/storage/logs/ffmpeg-status.log';
        
    //     // Check if the log file exists
    //     if (!File::exists($filePath)) {
    //         abort(404, 'Log file not found.');
    //     }
        
    //     // Read the entire log file content
    //     $logContent = File::get($filePath);
        
    //     // Filter log content based on the file_id (searching for occurrences of file_id in the log)
    //     $filteredLogContent = '';
        
    //     // If the file_id is found, extract the relevant lines from the log content
    //     if (strpos($logContent, $fileId) !== false) {
    //         // Split the log content into lines
    //         $logLines = explode("\n", $logContent);
            
    //         // Filter lines that contain the file_id
    //         foreach ($logLines as $line) {
    //             if (strpos($line, $fileId) !== false) {
    //                 $filteredLogContent .= $line . "\n";
    //             }
    //         }
    //     } else {
    //         abort(404, 'File ID not found in the log file.');
    //     }
    
    //     // Pass the filtered log content and content data to the view
    //     return view('backend.pages.content_management.showLog', compact('filteredLogContent', 'content'));
    // }


    public function showLog($id)
    {
        $content = Content::find($id);
        
        // Check if content is found
        if (!$content) {
            abort(404, 'Content not found.');
        }
    
        // Get the file_id from the Content model
        $fileId = $content->file_id;
    
        // File path for the log file
        $filePath = '/usr/local/nexdecade/nex-vod-processing/storage/logs/ffmpeg-status.log';
        
        // Check if the log file exists
        if (!File::exists($filePath)) {
            abort(404, 'Log file not found.');
        }
        
        // Read the entire log file content
        $logContent = File::get($filePath);
        
        // Filter log content based on the file_id (searching for occurrences of file_id in the log)
        $filteredLogContent = '';
        
        // If the file_id is found, extract the relevant lines from the log content
        if (strpos($logContent, $fileId) !== false) {
            // Split the log content into lines
            $logLines = explode("\n", $logContent);
            
            // Filter lines that contain the file_id
            foreach ($logLines as $line) {
                if (strpos($line, $fileId) !== false) {
                    $filteredLogContent .= $line . "\n";
                }
            }
        } else {
            abort(404, 'File ID not found in the log file.');
        }

        // Process the log content into an array of entries
        $logEntries = $this->processLogContent($filteredLogContent);
        
        // Pass the structured log entries to the view
        return view('backend.pages.content_management.showLog', compact('logEntries'));
    }

    private function processLogContent($logContent)
    {
        // Split the content by lines
        $lines = explode("\n", $logContent);
        $entries = [];

        // Parse each line into an entry
        foreach ($lines as $line) {
            if (preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) - (.*)/', $line, $matches)) {
                $entries[] = [
                    'time' => $matches[1],
                    'action' => $matches[2],
                ];
            }
        }
        
        return $entries;
    }





}
