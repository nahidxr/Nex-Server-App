@extends('backend.layouts.master')

@section('title')
Admins - Admin Panel
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">

    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
@endsection


@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Contents</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><span>All Contents</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title float-left">Content List</h4>
                    <p class="float-right mb-2">
                            <button class="btn btn-primary text-white" data-toggle="modal" data-target="#uploadModal">Add New Content</button>
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center cell-border" style="width: 100%">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th>Sl</th>
                                    <th>File Name</th>
                                    <th>File Size</th>
                                    <th>Resolution</th>
                                    <th>Aspect Ratio</th>
                                    <th>Audio Codec</th>
                                    <th>Audio Bitrate</th>
                                    <th>Video Codec</th>
                                    <th>Video Bitrate</th>
                                    <th>Frame Rate</th>
                                    <th>Sample Rate</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contents as $content)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $content->file_name }}</td>
                                    <td>{{ $content->media_details['all_details']['file_size']['display'] }}</td>
                                    <td>{{ $content->media_details['all_details']['width']['display'] }} x {{ $content->media_details['all_details']['height']['display'] }}</td>
                                    <td>{{ $content->media_details['all_details']['aspect_ratio']['display'] }}</td>
                                    <td>{{ $content->media_details['all_details']['audio_codec']['display'] }}</td>
                                    <td>{{ $content->media_details['all_details']['audio_bitrate']['display'] }}</td>
                                    <td>{{ $content->media_details['all_details']['video_codec']['display'] }}</td>
                                    <td>{{ $content->media_details['all_details']['video_bitrate']['display'] }}</td>
                                    <td>{{ $content->media_details['all_details']['frame_rate']['display'] }}</td>
                                    <td>{{ $content->media_details['all_details']['sample_rate']['display'] }}</td>
                                    <td>
                                        <a class="btn btn-danger btn-sm ml-1 text-white" 
                                           href="{{ url('/admin/upload/' . $content->id) }}" 
                                           onclick="event.preventDefault(); document.getElementById('delete-form-{{ $content->id }}').submit();">
                                           Delete
                                        </a>
                                        <form id="delete-form-{{ $content->id }}" action="{{ url('/admin/upload/' . $content->id) }}" method="POST" style="display: none;">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- data table end -->
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload New Content</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="topCloseButton">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    {{-- <div class="form-group">
                        <label for="fileNameInput">File Name</label>
                        <input type="text" class="form-control" id="fileNameInput" name="file_name" required>
                    </div> --}}
                    <!-- Drag-and-drop file upload container -->
                    <div id="upload-container" class="upload-container text-center">
                        <div class="upload-icon">
                            <i class="bi bi-cloud-upload-fill"></i>
                        </div>
                        <label class="btn upload-button" for="file-upload" id="browseFileButton">
                            <i class="bi bi-folder2-open"></i> Browse Files
                        </label>
                        <input type="file" id="file-upload" name="file" style="display: none;" required>
                        <div class="upload-text" id="uploadText">
                         click to upload video
                        </div>

                        <!-- Video preview (initially hidden) -->
                        <video id="videoPreview" controls style="display: none; width: 100%; height: auto; margin-top: 10px;"></video>

                        <!-- Spinner for loading state (initially hidden) -->
                        {{-- <div id="spinner" class="spinner-border text-primary" role="status" style="display: none;">
                            <span class="sr-only">Loading...</span>
                        </div> --}}
                    </div>

                    <!-- Progress bar (initially hidden) -->
                    <div id="progress-content" class="upload-progress-content">
                        <!-- Label changes to "Uploading..." when the upload starts -->
                        <label class="uploading-label" id="uploadingLabel" style="display: none;">Uploading...</label>
                        
                        <!-- Progress bar, shown while the video is uploading -->
                        <div class="progress mt-3" id="progressBarContainer" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%
                            </div>
                        </div>
                    </div>
                </form>
                <div id="statusMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeButton">Close</button>
                <button type="button" class="btn btn-primary" id="saveButton">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Styles for upload container -->
<style>
    .upload-container {
        border: 1px solid #bababa;
        border-radius: 8px;
        padding: 50px;
        background-color: #f9f9f9;
        color: #6c757d;
        margin-bottom: 20px;
        position: relative;
        transition: all 0.3s ease;
    }

    .upload-container.active-upload {
        border: 2px solid #ffffff; /* Change border style during upload */
        padding: 0; /* Remove padding to fit the video */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: #fff;
    }

    .upload-container .upload-icon {
        font-size: 50px;
        color: #000;
    }

    .upload-container .upload-button {
        background-color: #1d3557;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
    }

    .upload-container .upload-button:hover {
        background-color: #16344a;
    }

    .upload-container .upload-text {
        margin-top: 10px;
        font-size: 14px;
        color: #6c757d;
    }

    /* Full width video preview when active */
    .upload-container video {
        width: 100%;
        height: auto;
        border-radius: 8px;
        margin-top: 10px;
    }

    .progress-bar-container-inside {
        margin-top: 10px;
        width: 100%;
    }

    /* Spinner styles */
    .spinner-border {
        margin-top: 15px;
        display: block; /* Display block to center spinner */
    }
</style>

@endsection

@section('scripts')
     <!-- Start datatable js -->
     <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
     <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
     <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
     <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
     <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>

    <script>
        /*================================
       datatable active
       ==================================*/
       if ($('#dataTable').length) {
           $('#dataTable').DataTable({
               responsive: true
           });
       }

       $(document).ready(function () {
    let browseFile = $('#file-upload');
    let progressBarContainer = $('#progressBarContainer');
    let progressBar = $('.progress-bar');
    let videoPreview = $('#videoPreview');
    let uploadText = $('#uploadText');
    let browseFileButton = $('#browseFileButton');
    let uploadContainer = $('#upload-container');
    let spinner = $('#spinner');
    let uploadingLabel = $('#uploadingLabel');
    let uploadedVideoPath = ''; // Track uploaded video path
    let originalFileName = ''; // Track uploaded video path

    // Initialize Resumable.js for file uploading
    function initializeResumable() {
        let resumable = new Resumable({
            target: '{{ route("upload.store") }}', // Backend route for file upload
            query: {_token: '{{ csrf_token() }}'}, // Include CSRF token for security
            fileType: ['png', 'jpg', 'jpeg', 'mp4'], // Allow these file types
            chunkSize: 10 * 1024 * 1024, // 10MB chunk size
            headers: {
                'Accept': 'application/json'
            },
            testChunks: false, // Disable chunk testing
            throttleProgressCallbacks: 1, // Control progress callback rate
        });

        resumable.assignBrowse(browseFile[0]);

        // When a file is added
        resumable.on('fileAdded', function (file) {
            uploadContainer.addClass('active-upload'); // Add active state class
            uploadText.hide(); // Hide upload text
            browseFileButton.hide(); // Hide browse button
            progressBarContainer.addClass('progress-bar-container-inside').show(); // Show progress bar container
            uploadingLabel.show().text('Uploading...'); // Show uploading label
            spinner.show(); // Show spinner animation
            resumable.upload(); // Start the upload
        });

        // While file is uploading
        resumable.on('fileProgress', function (file) {
            let progress = Math.floor(file.progress() * 100); // Calculate percentage progress
            progressBar.css('width', `${progress}%`); // Update progress bar width
            progressBar.text(`${progress}%`); // Display percentage in progress bar
        });

        // When file upload is successful
        resumable.on('fileSuccess', function (file, response) {
            response = JSON.parse(response); // Parse the response JSON
            console.log('Upload successful, server response:', response); 

            if (response.mime_type.includes("video")) {
                progressBarContainer.hide(); // Hide progress bar when complete
                videoPreview.attr('src', response.path + '/' + response.name).show(); // Show video preview
                spinner.hide(); // Hide spinner
                uploadingLabel.hide(); // Hide uploading label
                uploadContainer.css('border', 'none'); // Remove border after upload
                uploadedVideoPath = response.path + '/' + response.name; // Store the uploaded video path
                originalFileName = response.originalFileName; // Store the uploaded video path

                console.log('Uploaded Video Path:', uploadedVideoPath);
                console.log('Original File Name:', originalFileName);
            }
        });

        // On file upload error
        resumable.on('fileError', function (file, response) {
            alert('File upload error.');
            resetUploadContainer(); // Reset upload container on error
        });
    }

    // Initialize the file upload on page load
    initializeResumable();

    // Function to reset the UI to the initial state after upload
    function resetUploadContainer() {
        progressBarContainer.removeClass('progress-bar-container-inside').hide(); // Hide progress bar container
        videoPreview.hide(); // Hide video preview
        browseFileButton.show(); // Show browse file button
        uploadText.show(); // Show upload text
        progressBar.css('width', '0%').text('0%'); // Reset progress bar
        spinner.hide(); // Hide spinner
        uploadingLabel.hide(); // Hide uploading label
        uploadedVideoPath = ''; // Clear the stored video path
    }

    // Function to delete the uploaded video from the server
    function deleteUploadedVideo() {
        if (uploadedVideoPath) {
            let relativeVideoPath = uploadedVideoPath.replace(window.location.origin + '/', ''); // Get relative path

            $.ajax({
                url: '{{ route("upload.delete") }}', // Backend route for deleting the video
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}', // Include CSRF token for security
                    video_path: relativeVideoPath // Send the relative file path to backend
                },
                success: function (response) {
                    console.log('Video deleted successfully');
                },
                error: function (xhr, status, error) {
                    console.error('Error deleting the video:', error); // Log error on failure
                }
            });
        }
    }

    
    $('#saveButton').on('click', function () {
    // Send the file details to the finalSave route
    $.ajax({
        url: '{{ route("upload.saveBucket") }}', // Backend route for final save
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}', // CSRF token for security
            file_path: uploadedVideoPath.split('/').slice(-3, -1).join('/'), // Extract temp file path
            file_name: uploadedVideoPath.split('/').pop(), // Extract file name from uploaded path
            originalFileName: originalFileName, // Extract file name from uploaded path
        },
        success: function (response) {
            console.log('File saved successfully:', response);
            $('#uploadModal').modal('hide'); // Close the modal
            location.reload(); // Reload page after save
        },
        error: function (xhr, status, error) {
            console.error('Error saving file:', error);
        }
    });
});



    // Event handler for close buttons to delete uploaded video and reset the modal
    $('#closeButton, #topCloseButton').on('click', function () {
        deleteUploadedVideo(); // Call function to delete the uploaded video
        resetUploadContainer(); // Reset the upload UI state
        $('#uploadModal').modal('hide'); // Close the modal window

        // Optionally reload the page after closing the modal (uncomment to enable)
        location.reload(); 
    });

});


    </script>
@endsection
