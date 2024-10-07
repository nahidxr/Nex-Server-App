@extends('backend.layouts.master')

@section('title')
Content Create - Content Panel
@endsection

@section('styles')
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

    <style>
    .profile {
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .upload-container {
        margin-bottom: 20px;
    }

    .upload-button {
        cursor: pointer;
    }
</style>




</style>
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

                    <form id="uploadForm" method="POST" action="{{ route('upload.saveBucket') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- File Name -->
                        <div class="form-group">
                            <label for="fileName">File Name</label>
                            <input type="text" class="form-control" id="fileName" name="file_name" placeholder="Enter File Name" required>
                        </div>

                          <!-- Profile Section -->

<!-- Profile Section -->
<div class="form-group" id="profileSection">
    <label>Profiles</label>
    <div id="profileContainer">
        <!-- Profile Template -->
        <div class="profile">
            <h5>Profile 1</h5> <!-- Dynamic Profile Header -->
            <div class="row">
                <div class="col">
                    <label for="scaleX">Scale X</label>
                    <input type="number" class="form-control" name="profiles[0][scale_x]" placeholder="Enter Scale X" required>
                </div>
                <div class="col">
                    <label for="scaleY">Scale Y</label>
                    <input type="number" class="form-control" name="profiles[0][scale_y]" placeholder="Enter Scale Y" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label for="height">Height</label>
                    <input type="number" class="form-control" name="profiles[0][height]" placeholder="Enter Height" required>
                </div>
                <div class="col">
                    <label for="width">Width</label>
                    <input type="number" class="form-control" name="profiles[0][width]" placeholder="Enter Width" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label for="audioBitrate">Audio Bitrate</label>
                    <input type="number" class="form-control" name="profiles[0][audio_bitrate]" placeholder="Enter Audio Bitrate" required>
                </div>
                <div class="col">
                    <label for="videoBitrate">Video Bitrate</label>
                    <input type="number" class="form-control" name="profiles[0][video_bitrate]" placeholder="Enter Video Bitrate" required>
                </div>
            </div>
            <button type="button" class="btn btn-danger mt-3 remove-profile">Remove Profile</button>
        </div>
    </div>
    <button type="button" id="addProfileButton" class="btn btn-success mt-4">+ Add Profile</button>
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
                                {{-- <video id="videoPreview" controls style="display: none; width: 100%; height: auto; margin-top: 10px;"></video> --}}
                                <video id="videoPreview" controls style="display: none; width: 100%; height: 400px; margin-top: 10px;"></video>

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

        $('#saveButton').on('click', function () {
            $.ajax({
                url: '{{ route("upload.saveBucket") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    file_path: uploadedVideoPath.split('/').slice(-3, -1).join('/'),
                    file_name: uploadedVideoPath.split('/').pop(),
                    originalFileName: originalFileName,
                },
                success: function (response) {
                    $('#statusMessage').text('File successfully saved to bucket.');
                },
                error: function (xhr, status, error) {
                    console.log('Error saving file to bucket.');
                }
            });
        });
         // Handle Cancel button click
         $('#cancelButton').on('click', function() {
            window.location.href = '{{ route("content.index") }}';  // Redirect to the content index page
        });
    });
</script>

<script>
    let profileCount = 0; // Initialize count of profiles

    function updateProfileHeaders() {
        // Update profile headers with the current profile count
        const profiles = document.querySelectorAll('.profile');
        profiles.forEach((profile, index) => {
            const header = profile.querySelector('h5');
            header.textContent = `Profile ${index + 1}`;
            // Update input names accordingly
            const inputs = profile.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.name.replace(/\[\d+\]/, `[${index}]`); // Replace index in name attribute
                input.name = name;
            });
        });
    }

    document.getElementById('addProfileButton').addEventListener('click', function() {
        profileCount++;
        const profileContainer = document.getElementById('profileContainer');

        // Create a new profile div
        const newProfile = document.createElement('div');
        newProfile.classList.add('profile', 'mt-4');
        newProfile.innerHTML = `
            <h5>Profile ${profileCount + 1}</h5> <!-- Dynamic Profile Header -->
            <div class="row">
                <div class="col">
                    <label for="scaleX">Scale X</label>
                    <input type="number" class="form-control" name="profiles[${profileCount}][scale_x]" placeholder="Enter Scale X" required>
                </div>
                <div class="col">
                    <label for="scaleY">Scale Y</label>
                    <input type="number" class="form-control" name="profiles[${profileCount}][scale_y]" placeholder="Enter Scale Y" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label for="height">Height</label>
                    <input type="number" class="form-control" name="profiles[${profileCount}][height]" placeholder="Enter Height" required>
                </div>
                <div class="col">
                    <label for="width">Width</label>
                    <input type="number" class="form-control" name="profiles[${profileCount}][width]" placeholder="Enter Width" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label for="audioBitrate">Audio Bitrate</label>
                    <input type="number" class="form-control" name="profiles[${profileCount}][audio_bitrate]" placeholder="Enter Audio Bitrate" required>
                </div>
                <div class="col">
                    <label for="videoBitrate">Video Bitrate</label>
                    <input type="number" class="form-control" name="profiles[${profileCount}][video_bitrate]" placeholder="Enter Video Bitrate" required>
                </div>
            </div>
            <button type="button" class="btn btn-danger mt-3 remove-profile">Remove Profile</button>
        `;

        // Append the new profile to the container
        profileContainer.appendChild(newProfile);

        // Update profile headers
        updateProfileHeaders();
    });

    // Event delegation to handle remove buttons
    document.getElementById('profileContainer').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-profile')) {
            e.target.parentElement.remove();
            // Update profile headers after removal
            updateProfileHeaders();
        }
    });
</script>

@endsection