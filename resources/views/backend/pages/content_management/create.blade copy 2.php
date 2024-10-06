@extends('backend.layouts.master')

@section('title')
Content Create - Content Panel
@endsection

@section('styles')
<!-- Add any additional CSS here if needed -->
@endsection

@section('admin-content')

<!-- Page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Content Create</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('content.index') }}">All Content</a></li>
                    <li><span>Create Content</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- Page title area end -->

<div class="main-content-inner">
    <div class="row">
        <!-- Form start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Create New Content</h4>

                    @include('backend.layouts.partials.messages')

                    <form  method="POST" action="{{ route('upload.saveBucket') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- File Name -->
                        <div class="form-group">
                            <label for="fileName">File Name</label>
                            <input type="text" class="form-control" id="fileName" name="file_name" placeholder="Enter File Name" required>
                        </div>

                        <!-- Bitrate -->
                        <div class="form-group">
                            <label for="bitrate">Bitrate</label>
                            <input type="number" class="form-control" id="bitrate" name="bitrate" placeholder="Enter Bitrate (e.g. 320)" required>
                        </div>

                        <!-- Width -->
                        <div class="form-group">
                            <label for="width">Width</label>
                            <input type="number" class="form-control" id="width" name="width" placeholder="Enter Width in pixels" required>
                        </div>

                        <!-- Height -->
                        <div class="form-group">
                            <label for="height">Height</label>
                            <input type="number" class="form-control" id="height" name="height" placeholder="Enter Height in pixels" required>
                        </div>

                        <!-- File Upload Section -->
                        <div class="form-group">
                            <label for="file-upload">Upload File</label>
                            <div id="upload-container" class="upload-container text-center">
                                <div class="upload-icon">
                                    <i class="bi bi-cloud-upload-fill"></i>
                                </div>
                                <label class="btn upload-button" for="file-upload" id="browseFileButton">
                                    <i class="bi bi-folder2-open"></i> Browse Files
                                </label>
                                <input type="file" id="file-upload" name="file" style="display: none;" required>
                                <div class="upload-text" id="uploadText">
                                    Click to upload video
                                </div>

                                <!-- Video preview (initially hidden) -->
                                <video id="videoPreview" controls style="display: none; width: 100%; height: auto; margin-top: 10px;"></video>
                            </div>

                            <!-- Progress bar (initially hidden) -->
                            <div id="progress-content" class="upload-progress-content">
                                <div class="progress mt-3" id="progressBarContainer" style="display: none;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4" id="saveButton">Save Content</button>
                        <button type="button" class="btn btn-secondary mt-4" id="cancelButton">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
<script>
    $(document).ready(function() {
        let browseFile = $('#file-upload');
        let progressBarContainer = $('#progressBarContainer');
        let progressBar = $('.progress-bar');
        let videoPreview = $('#videoPreview');
        let uploadText = $('#uploadText');
        let browseFileButton = $('#browseFileButton');
        let uploadContainer = $('#upload-container');
        let spinner = $('#loadingSpinner');
        let uploadedVideoPath = '';
        let originalFileName = '';

        function initializeResumable() {
            let resumable = new Resumable({
                target: '{{ route("upload.store") }}',
                query: {_token: '{{ csrf_token() }}'},
                fileType: ['png', 'jpg', 'jpeg', 'mp4'],
                chunkSize: 10 * 1024 * 1024,
                headers: {'Accept': 'application/json'},
                testChunks: false,
                throttleProgressCallbacks: 1,
            });

            resumable.assignBrowse(browseFile[0]);

            resumable.on('fileAdded', function(file) {
                uploadContainer.addClass('active-upload');
                uploadText.hide();
                browseFileButton.hide();
                progressBarContainer.addClass('progress-bar-container-inside').show();
                spinner.show();
                resumable.upload();
            });

            resumable.on('fileProgress', function(file) {
                let progress = Math.floor(file.progress() * 100);
                progressBar.css('width', `${progress}%`);
                progressBar.text(`${progress}%`);
            });

            resumable.on('fileSuccess', function(file, response) {
                response = JSON.parse(response);
                if (response.mime_type.includes("video")) {
                    progressBarContainer.hide();
                    videoPreview.attr('src', response.path + '/' + response.name).show();
                    spinner.hide();
                    uploadContainer.css('border', 'none');
                    uploadedVideoPath = response.path + '/' + response.name;
                    originalFileName = response.originalFileName;
                }
            });

            resumable.on('fileError', function(file, response) {
                alert('File upload error.');
                resetUploadContainer();
            });
        }

        initializeResumable();

        function resetUploadContainer() {
            progressBarContainer.removeClass('progress-bar-container-inside').hide();
            videoPreview.hide();
            browseFileButton.show();
            uploadText.show();
            progressBar.css('width', '0%').text('0%');
            spinner.hide();
            uploadedVideoPath = '';
        }

        function deleteUploadedVideo() {
            if (uploadedVideoPath) {
                let relativeVideoPath = uploadedVideoPath.replace(window.location.origin + '/', '');

                $.ajax({
                    url: '{{ route("upload.delete") }}',
                    method: 'DELETE',
                    data: {_token: '{{ csrf_token() }}', video_path: relativeVideoPath},
                    success: function(response) {
                        console.log('Video deleted successfully');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting the video:', error);
                    }
                });
            }
        }

        // $('#saveButton').on('click', function () {
        //     $.ajax({
        //         url: '{{ route("upload.saveBucket") }}',
        //         method: 'POST',
        //         data: {
        //             _token: '{{ csrf_token() }}',
        //             file_path: uploadedVideoPath.split('/').slice(-3, -1).join('/'),
        //             file_name: uploadedVideoPath.split('/').pop(),
        //             originalFileName: originalFileName,
        //         },
        //         success: function (response) {
        //             $('#statusMessage').text('File successfully saved to bucket.');
        //         },
        //         error: function (xhr, status, error) {
        //             console.log('Error saving file to bucket.');
        //         }
        //     });
        // });
    });
</script>
@endsection
