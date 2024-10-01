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
    public function upload_index() {
        return view('backend.pages.upload-file.index');
    }

    // public function uploadLarge(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:mp4', // Limit file size to 20MB
    //     ]);

    //     $file = $request->file('file');
    //     $path = $file->store('videos', 'public'); // Store the file in the 'public/videos' directory

    //     return response()->json(['path' => Storage::url($path)]);
    //     }

    //     // public function uploadLarge(Request $request)
    //     // {
    //     //     $request->validate([
    //     //         'file' => 'required|file|mimes:mp4|max:20480', // Limit file size to 20MB
    //     //     ]);

    //     //     $file = $request->file('file');
    //     //     $filePath = $file->store('videos', 'public'); // Store the file in the 'public/videos' directory
    //     //     $fullPath = Storage::path($filePath); // Get the full path for generating video details

    //     //     // Generate video details
    //     //     $videoDetails = $this->generateVideoDetails($fullPath);
            
    //     //     // Decode the JSON response from generateVideoDetails
    //     //     // $videoDetailsArray = json_decode($videoDetails, true);
    //     //     $staticVideoDetails = [
    //     //         'duration' => '00:00:30', // Example duration
    //     //         'resolution' => '1920x1080', // Example resolution
    //     //         'bitrate' => '4500kbps', // Example bitrate
    //     //         'codec' => 'H.264', // Example codec
    //     //         'fps' => 30, // Frames per second
    //     //         // Add other static fields as needed
    //     //     ];
    //     //     // Create a new Content record
    //     //     try {
    //     //         Content::create([
    //     //             'file_name' => $file->getClientOriginalName(), // Original file name
    //     //             'file_path' => $filePath, // Path stored in the filesystem
    //     //             'folder' => 'videos', // Specify the folder or use as needed
    //     //             'file_id' => null, // You can assign a value or logic here if needed
    //     //             'media_details' => $staticVideoDetails, // Store video details as JSON
    //     //         ]);
    //     //     } catch (\Exception $e) {
    //     //         Log::error('Error saving video details: ' . $e->getMessage());
    //     //         return response()->json(['error' => 'Failed to save video details.'], 500);
    //     //     }

    //     //     return response()->json(['path' => Storage::url($filePath)]);
    //     // }


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
    



    // protected function saveFileToBucket(Request $request)
    // {
    //     // Validate the incoming request
    //     $validated = $request->validate([
    //         'file_path' => 'required|string',
    //         'file_name' => 'required|string',
    //     ]);

    //     // Full path to the temporary file
    //     $tempFilePath = storage_path('app/public/upload/' . $validated['file_path'] . '/' . $validated['file_name']);

    //     // Check if file exists
    //     if (!file_exists($tempFilePath)) {
    //         return response()->json(['error' => 'File not found.'], 404);
    //     }

    //     // Define the destination path in the GCS bucket
    //     $destinationPath = 'uploads/' . $validated['file_name'];
    //     $filePath = 'upload/' . $validated['file_path'] . '/' . $validated['file_name'];

    //     try {
          

    //         // Save file information in the database
    //         $content = new Content();
    //         $content->file_name = pathinfo($request->originalFileName, PATHINFO_FILENAME);
    //         $content->encoder_status = 0; // Update as needed
    //         $extension = pathinfo($validated['file_name'], PATHINFO_EXTENSION); // Get the file extension
    //         $uniqueFileId = Str::random(10) . '.' . $extension; // Unique ID + extension
    //         $content->file_id = $uniqueFileId;
    //         // $content->file_id = Str::random(10); // Random file ID
    //         $content->file_path = $filePath; // Path in the GCS bucket
    //         $content->folder = $validated['file_path']; // Store the folder path
    //         // If you need to extract media details, ensure you implement the logic
    //         $fileDir = public_path("storage/public/upload/{$validated['file_path']}/{$validated['file_name']}");
    //         $content->media_details = $this->generateVideoDetails($fileDir);


    //         // Upload the file to GCS
    //         $storage = new StorageClient([
    //             'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
    //             'keyFilePath' => env('GOOGLE_CLOUD_KEY_FILE'),
    //         ]);

    //         $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
    //         $bucket->upload(
    //             fopen($tempFilePath, 'r'),
    //             ['name' => $content->file_id]
    //         );

    //         $content->save();


    //         // Delete the temporary file after uploading to GCS
    //         // Storage::delete($tempFilePath);
    //         session()->flash('success', 'File has Sucessfully Uploaded !!');
    //         // Return success response
    //         return response()->json(['message' => 'File saved to Google Cloud Storage successfully and saved in database!', 'path' => $destinationPath], 200);
    //     } catch (\Exception $e) {
    //         // Handle exceptions
    //         session()->flash('error', 'File uploading failed !');
    //         return response()->json(['error' => 'Failed to upload file to Google Cloud Storage.', 'exception' => $e->getMessage()], 500);
    //     }
    // }

    protected function saveFileToBucket(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'file_path' => 'required|string',
            'file_name' => 'required|string',
        ]);
    

        // Full path to the temporary file
        $tempFilePath = storage_path('app/public/upload/' . $validated['file_path'] . '/' . $validated['file_name']);

        // Check if file exists
        if (!file_exists($tempFilePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        // $numericId = rand(1000000, 9999999);
        //  // Generate a UUID
        // $uuid = (string) Str::uuid(); 
        // $uniqueFileId = "{$numericId}_{$uuid}";
        // $uniqueFile= "$uniqueFileId.{$extension}";



        $extension = pathinfo($validated['file_name'], PATHINFO_EXTENSION); // Get the file extension
        $uniqueFileId = Str::random(32);
        $uniqueFile =  $uniqueFileId . '.' . $extension; // Unique ID + extension
        // Generate the unique file ID in the desired format


     

        // Define the destination path in the GCS bucket
        $destinationPath = 'uploads/'. $uniqueFile;
        // $destinationPath = $uniqueFileId;
        $filePath = 'upload/' . $validated['file_path'] . '/' . $validated['file_name'];

        // Prepare the StorageClient
        $storage = new StorageClient([
            'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'keyFilePath' => env('GOOGLE_CLOUD_KEY_FILE'),
        ]);

        $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));

        try {
            // Attempt to upload the file to GCS
            $object = $bucket->upload(
                fopen($tempFilePath, 'r'),
                ['name' => $destinationPath]
            );

            // If the upload is successful, create a new Content instance
            if ($object) {
            // if (true) {
                $content = new Content();
                $content->file_name = pathinfo($request->originalFileName, PATHINFO_FILENAME);
                $content->encoder_status = 0; // Update as needed
                $content->file_id = $uniqueFileId;
                $content->file_path = $filePath; // Path in the GCS bucket
                $content->folder = $validated['file_path']; // Store the folder path

                // If you need to extract media details, ensure you implement the logic
                $fileDir = public_path("storage/public/upload/{$validated['file_path']}/{$validated['file_name']}");
                $content->media_details = $this->generateVideoDetails($fileDir);

                // Save file information in the database
                $content->save();

                // Delete the temporary file after uploading to GCS
                // Storage::delete($tempFilePath);
                session()->flash('success', 'File has successfully uploaded!');

                // Return success response
                return response()->json(['message' => 'File saved to Google Cloud Storage successfully and saved in database!', 'path' => $destinationPath], 200);
            }

        } catch (\Exception $e) {
            // Handle exceptions
            session()->flash('error', 'File uploading failed!');
            return response()->json(['error' => 'Failed to upload file to Google Cloud Storage.', 'exception' => $e->getMessage()], 500);
        }
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

            // Get the file path stored in the database
            $filePath = $content->file_path; // e.g., public/upload/video-mp4/2024-09-39/bkash_ads_6d2400385e80d9db68e218e61a12f8f9.mp4

            // Debug: Check the file path
            // dd($filePath);

            // Delete the associated file from storage
            // Specify 'public' disk as your files are stored there
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            } else {
                return redirect()->route('upload.index')->with('error', 'File does not exist.');
            }

            // Delete the content from the database
            $content->delete();

            return redirect()->route('upload.index')->with('success', 'Content deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('upload.index')->with('error', 'An error occurred while deleting the content: ' . $e->getMessage());
        }
    }





}
