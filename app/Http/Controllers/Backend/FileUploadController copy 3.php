<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

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

class FileUploadController extends Controller
{
    public function index() {
        return view('backend.pages.upload-file.index');
    }


    public function uploadLargeFiles(Request $request)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            // File not uploaded, return error message
            return response()->json(['error' => 'File not uploaded.'], 400);
        }

        // Receive the file chunk
        $fileReceived = $receiver->receive();

        if ($fileReceived->isFinished()) {
            // File uploading is complete, move file to permanent storage
            $file = $fileReceived->getFile();
            $extension = $file->getClientOriginalExtension();
           
            $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()) . '_' . md5(time()) . '.' . $extension;
            // Store the file and get the path (ensure you're using the correct disk configuration)
            $disk = Storage::disk(config('filesystems.default'));

            $path = $disk->putFileAs('videos', $file, $fileName);
            // Check if the file was successfully moved to the desired location
            if ($path) {

                // Delete temporary chunked file
                unlink($file->getPathname());

                // Return the stored file's URL
                return response()->json([
                    'path' => Storage::url($path), // Use Storage::url() to return the correct public URL
                    'filename' => $fileName
                ]);
            } else {
                return response()->json(['error' => 'File could not be saved.'], 500);
            }
        }

        // Return upload progress if the file is not finished
        $handler = $fileReceived->handler();
        return response()->json([
            'done' => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

}
