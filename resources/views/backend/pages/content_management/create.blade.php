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
            border: 2px solid #ffffff;
            /* Change border style during upload */
            padding: 0;
            /* Remove padding to fit the video */
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

        .profile-header {
            cursor: pointer;
        }

        #statusMessage {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 10px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #28a745;
            color: white;
        }

        .alert-danger {
            background-color: #dc3545;
            color: white;
        }

        .icon-button {
            display: inline-flex;
            /* Align the icon centrally */
            align-items: center;
            /* Center vertically */
            justify-content: center;
            /* Center horizontally */
            padding: 5px;
            /* Add padding for the button */
            margin-left: 5px;
            /* Space between label and button */
            border: 1px solid #007bff;
            /* Border color */
            border-radius: 5px;
            /* Rounded corners */
            background-color: #f8f9fa;
            /* Light background color */
            color: #007bff;
            /* Icon color */
            cursor: pointer;
            /* Change cursor on hover */
            transition: background-color 0.2s, color 0.2s;
            /* Animation for hover effect */
        }

        .icon-button:hover {
            background-color: #e2e6ea;
            /* Darker background on hover */
            color: #0056b3;
            /* Darker color on hover */
        }

        .icon-button i {
            font-size: 1.5em;
            /* Adjust the icon size */
        }


        .profiles-list {
            margin-top: 20px;
            /* Space above the profile list */
        }

        .list-group-item {
            padding: 15px;
            /* Padding for each item */
            border: 1px solid #e0e0e0;
            /* Border for each item */
            border-radius: 5px;
            /* Rounded corners */
            margin-bottom: 10px;
            /* Space between items */
        }

        .badge-info {
            background-color: #17a2b8;
            /* Bootstrap info color for profile labels */
            color: #fff;
            /* White text for contrast */
        }

        .list-group {
            list-style: none;
            /* Remove default list styling */
            padding: 0;
            /* Remove padding */
        }
        .selected {
            background-color: #cce5ff; /* Light blue background for selected */
        }
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

                        {{-- @include('backend.layouts.partials.messages') --}}
                        <div id="flashMessagesContainer">
                            {{-- @include('backend.layouts.partials.messages')  --}}
                        </div>


                        <form id="uploadForm" method="POST" action="{{ route('upload.saveBucket') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- File Name -->
                            <div class="form-group">
                                <label for="fileName">File Name:</label>
                                <input type="text" class="form-control" id="fileName" name="file_name"
                                    placeholder="Enter File Name" required>
                            </div>


                            {{-- <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Profiles</h5>
                                    <input type="hidden" id="selectedProfiles" name="selectedProfiles">
                                    <div class="profiles-list" id="profilesList">
                                        <div class="row">
                                            @foreach ($profiles as $profile)
                                                <div class="col-md-4">
                                                    <ul class="list-group">
                                                        <li class="list-group-item profile-item mt-2" data-profile="profile_{{ $profile->id }}">
                                                            <span class="profile-header badge badge-info mb-2" data-id="{{ $profile->id }}">
                                                                {{ $profile->name }}
                                                            </span><br>
                                                            <strong class="profile-text">Regulation {{ $profile->width }}x{{ $profile->height }}</strong> - 
                                                            <span class="bitrate-text">Video bitrate {{ $profile->video_bitrate }}</span> - 
                                                            <span class="fps-text">{{ $profile->frame_rate }}</span>
                                                            <span class="audio-text">Audio bitrate {{ $profile->audio_bitrate }}</span>
                                                            
                                                            <!-- Container for dotted line and input fields -->
                                                            <div class="profile-inputs-container" style="display: none;">
                                                                <hr style="border: none; border-top: 1px dotted #ccc; margin: 10px 0;">
                                                                <div class="profile-inputs">
                                                                    <div class="form-group">
                                                                        <label for="regulationHeight_{{ $profile->id }}">Regulation Height</label>
                                                                        <input type="number" class="form-control" id="regulationHeight_{{ $profile->id }}" value="{{ $profile->height }}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="width_{{ $profile->id }}">Width</label>
                                                                        <input type="number" class="form-control" id="width_{{ $profile->id }}" value="{{ $profile->width }}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="videoBitrate_{{ $profile->id }}">Video Bitrate (Kbps)</label>
                                                                        <input type="text" class="form-control" id="videoBitrate_{{ $profile->id }}" value="{{ $profile->video_bitrate }}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="audioBitrate_{{ $profile->id }}">Audio Bitrate (Kbps)</label>
                                                                        <input type="text" class="form-control" id="audioBitrate_{{ $profile->id }}" value="{{ $profile->audio_bitrate }}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="fps_{{ $profile->id }}">FPS</label>
                                                                        <input type="number" class="form-control" id="fps_{{ $profile->id }}" value="{{ (int) $profile->frame_rate }}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Profiles</h5>
                                    <input type="hidden" id="selectedProfiles" name="selectedProfiles">
                                    <div class="profiles-list" id="profilesList">
                                        <div class="row">
                                            @foreach ($profiles as $profile)
                                                <div class="col-md-4">
                                                    <ul class="list-group">
                                                        <li class="list-group-item profile-item mt-2" 
                                                            data-profile-id="{{ $profile->id }}">
                                                            <span class="profile-header badge badge-info mb-2" data-id="{{ $profile->id }}">
                                                                {{ $profile->name }}
                                                                <i class="fa fa-edit ml-1" aria-hidden="true" title="Edit"></i>
                                                            </span><br>
                                                            <strong class="profile-text">Regulation {{ $profile->width }}x{{ $profile->height }}</strong> - 
                                                            <span class="bitrate-text">Video bitrate {{ $profile->video_bitrate }}</span> - 
                                                            <span class="fps-text">{{ $profile->frame_rate }}</span> - 
                                                            <span class="audio-text">Audio bitrate {{ $profile->audio_bitrate }}</span>
                            
                                                            <!-- Container for input fields (hidden initially) -->
                                                            <div class="profile-inputs-container" style="display: none;">
                                                                <hr style="border: none; border-top: 1px dotted #ccc; margin: 10px 0;">
                                                                <div class="profile-inputs">
                                                                    <div class="form-group">
                                                                        <label for="regulationHeight_{{ $profile->id }}"> Height</label>
                                                                        <input type="number" class="form-control" id="regulationHeight_{{ $profile->id }}" 
                                                                               value="{{ $profile->height }}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="width_{{ $profile->id }}">Width</label>
                                                                        <input type="number" class="form-control" id="width_{{ $profile->id }}" 
                                                                               value="{{ $profile->width }}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="videoBitrate_{{ $profile->id }}">Video Bitrate</label>
                                                                        <input type="text" class="form-control" id="videoBitrate_{{ $profile->id }}" 
                                                                               value="{{ $profile->video_bitrate }}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="audioBitrate_{{ $profile->id }}">Audio Bitrate</label>
                                                                        <input type="text" class="form-control" id="audioBitrate_{{ $profile->id }}" 
                                                                               value="{{ $profile->audio_bitrate }}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="fps_{{ $profile->id }}">FPS</label>
                                                                        <input type="number" class="form-control" id="fps_{{ $profile->id }}" 
                                                                               value="{{ (int) $profile->frame_rate }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                            
                            




                            <!-- Profile Section -->
                            <div class="form-group mt-2" id="profileSection">
                                <div id="profileContainer">
                                    <!-- No profiles will be displayed initially -->
                                </div>
                                <button type="button" id="addProfileButton" class="btn btn-info mt-2">+ Add
                                    Custom Profile</button>
                            </div>




                            <div class="row">
                                <div class="col-6">

                                </div class="col-6">
                                <div>

                                </div>

                            </div>
                            <!-- File Upload Section -->
                            <div class="form-group">
                                <label for="file-upload">Upload File:</label>
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
                                    <video id="videoPreview" controls
                                        style="display: none; width: 100%; height: 400px; margin-top: 10px;"></video>

                                </div>

                                <!-- Progress bar (initially hidden) -->
                                <div id="progress-content" class="upload-progress-content">
                                    <div class="progress mt-3" id="progressBarContainer" style="display: none;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                            style="width: 0%;">0%
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-4" id="saveButton">Save Content</button>
                            <button type="button" class="btn btn-secondary mt-4" id="cancelButton">Cancel</button>

                            <div class="spinner-border d-none" role="status" id="loadingSpinner">
                                <span class="sr-only">Loading...</span>
                            </div>
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
                    target: '{{ route('upload.store') }}',
                    query: {
                        _token: '{{ csrf_token() }}'
                    },
                    fileType: ['png', 'jpg', 'jpeg', 'mp4'],
                    chunkSize: 10 * 1024 * 1024,
                    headers: {
                        'Accept': 'application/json'
                    },
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
                        url: '{{ route('upload.delete') }}',
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            video_path: relativeVideoPath
                        },
                        success: function(response) {
                            console.log('Video deleted successfully');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting the video:', error);
                        }
                    });
                }
            }

            // Toggle the 'selected' class on click
            $('.profile-item').on('click', function () {
                    $(this).toggleClass('selected');
                });
        

            $('#saveButton').on('click', function() {

                // Disable save button and show spinner
                $('#saveButton').prop('disabled', true);
                spinner.show();

                let profilesData = [];

                // Gather the profile data
                $('.profile').each(function() {
                    const width = $(this).find('input[name*="[width]"]').val();
                    const height = $(this).find('input[name*="[height]"]').val();
                    const videoBitrate = $(this).find('input[name*="[video_bitrate]"]').val();
                    const audioBitrate = $(this).find('input[name*="[audio_bitrate]"]').val();
                    const fps = $(this).find('input[name*="[fps]"]').val();
                   

                    profilesData.push({
                        width: width,
                        height: height,
                        video_bitrate: videoBitrate,
                        audio_bitrate: audioBitrate,
                        fps: fps
                    });
                });

                let selectedProfilesData = [];

                // Gather data only from selected profile items
                $('.profile-item.selected').each(function () {
                    const profileId = $(this).data('profile-id');
                    const width = $(this).find('input[id^="width_"]').val();
                    const height = $(this).find('input[id^="regulationHeight_"]').val();
                    const videoBitrate = $(this).find('input[id^="videoBitrate_"]').val();
                    const audioBitrate = $(this).find('input[id^="audioBitrate_"]').val();
                    const fps = $(this).find('input[id^="fps_"]').val();

                    selectedProfilesData.push({
                        id: profileId,
                        width: width,
                        height: height,
                        video_bitrate: videoBitrate,
                        audio_bitrate: audioBitrate,
                        fps: fps,
                    });
                });


    

                // Get the file name from the input field
                const fileTitle = $('#fileName').val();
                // const selectedProfiles = $('#selectedProfiles').val();


                const dataToSend = {
                    _token: '{{ csrf_token() }}',
                    profiles: profilesData,
                    file_name: fileName, // Include the file name here
                    file_path: uploadedVideoPath.split('/').slice(-3, -1).join('/'),
                    originalFileName: originalFileName,
                    selected_profiles: selectedProfilesData,
                };


                // Log the data to the console
                console.log('Data to be sent:', dataToSend);
                // Send the collected data via AJAX
                $.ajax({
                    url: '{{ route('upload.saveBucket') }}', // Update with your route
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        profiles: profilesData,
                        file_title: fileTitle, // Include the file name here
                        file_name: uploadedVideoPath.split('/').pop(), // Include the file name here
                        file_path: uploadedVideoPath.split('/').slice(-3, -1).join('/'),
                        originalFileName: originalFileName,
                        selected_profiles: selectedProfilesData,
                    },
                    success: function(response) {
                        // Call function to show the success message dynamically
                        showFlashMessage('success', response.message);
                    },
                    error: function(xhr) {
                        // Handle error response and display error message
                        const response = xhr.responseJSON;
                        if (response && response.message) {
                            showFlashMessage('danger', response.message);
                        }
                    },
                    complete: function() {
                        // Re-enable the Save button and hide the spinner after the request is done
                        $('#saveButton').prop('disabled', false);
                        spinner.hide();
                    }
                });
            });

            // Handle Cancel button click
            $('#cancelButton').on('click', function() {
                window.location.href =
                    '{{ route('content.index') }}'; // Redirect to the content index page
            });

            function showFlashMessage(type, message) {
                const flashMessageHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                $('#flashMessagesContainer').html(flashMessageHtml); // Insert the flash message into the container

                // Automatically hide the flash message after 5 seconds
                setTimeout(() => {
                    $('.alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 5000);
            }
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
                    const name = input.name.replace(/\[\d+\]/,
                        `[${index}]`); // Replace index in name attribute
                    input.name = name;
                });
            });
        }

        document.getElementById('addProfileButton').addEventListener('click', function() {
            profileCount++;
            const profileContainer = document.getElementById('profileContainer');

            // Create a new profile div
            const newProfile = document.createElement('div');
            newProfile.classList.add('profile', 'mt-2');
            newProfile.innerHTML = `
                <h5 data-toggle="collapse" class="profile-header" aria-expanded="false" aria-controls="collapseProfile${profileCount}"> Custom Profile ${profileCount}</h5>
                <div id="collapseProfile${profileCount}" class="collapse mt-4">
                    <div class="row">
                        <div class="col">
                            <label for="scaleX">Regulation Width</label>
                            <input type="number" class="form-control" name="profiles[${profileCount}][width]" placeholder="Enter width" required>
                        </div>
                        <div class="col">
                            <label for="scaleY">Regulation Height</label>
                            <input type="number" class="form-control" name="profiles[${profileCount}][height]" placeholder="Enter height" required>
                        </div>
                    </div>
                
                    <div class="row mt-3">
                        <div class="col">
                            <label for="videoBitrate">Video Bitrate(Kbps)</label>
                            <input type="number" class="form-control" name="profiles[${profileCount}][video_bitrate]" placeholder="Enter Video Bitrate" required>
                        </div>
                        <div class="col">
                          <label for="audioBitrate">Audio Bitrate(Kbps)</label>
                            <input type="number" class="form-control" name="profiles[${profileCount}][audio_bitrate]" placeholder="Enter Audio Bitrate" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="fps">Fps</label>
                            <input type="number" class="form-control" name="profiles[${profileCount}][fps]" placeholder="Enter Video fps" required>
                        </div>
                        <div class="col">
                           
                        </div>
                   
                    </div>
                    <button type="button" class="btn btn-danger mt-3 remove-profile">
                        <i class="ti-trash"></i> <!-- Trash icon for removing profile -->
                    </button>
                </div>
            `;

            // Append the new profile to the container
            profileContainer.appendChild(newProfile);

            // Collapse previous profiles
            const previousProfiles = profileContainer.querySelectorAll('.collapse');
            previousProfiles.forEach(collapseElement => {
                if (collapseElement !== newProfile.querySelector('.collapse')) {
                    collapseElement.classList.remove('show'); // Collapse previous profiles
                }
            });

            // Update profile headers
            updateProfileHeaders();
        });




        // Event delegation to handle remove buttons
        document.getElementById('profileContainer').addEventListener('click', function(e) {
            if (e.target && (e.target.classList.contains('remove-profile') || e.target.closest(
                    '.remove-profile'))) {
                e.target.closest('.profile').remove();
                // Update profile headers after removal
                updateProfileHeaders();
            }
        });

        // Event delegation to handle profile header clicks for toggling visibility
        document.getElementById('profileContainer').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('profile-header')) {
                const collapseElement = e.target.nextElementSibling; // Get the corresponding collapse div
                const isExpanded = collapseElement.classList.contains('show'); // Check if it is currently expanded

                // Collapse all profiles first
                const allCollapses = document.querySelectorAll('.collapse');
                allCollapses.forEach(collapse => {
                    collapse.classList.remove('show');
                });

                // If it was not expanded, then expand the clicked one
                if (!isExpanded) {
                    collapseElement.classList.add('show');
                }
            }
        });
    </script>




<script>
 $(document).ready(function() {
    let selectedProfiles = []; // Array to hold selected profile objects

    // Click event for the profile header (badge)
    $('.profile-header').on('click', function(event) {
        const profileContainer = $(this).closest('li').find('.profile-inputs-container');
        profileContainer.toggle(); // Toggle the visibility of the input fields
        event.stopPropagation(); // Prevent the event from bubbling up to the profile item
    });

    // Click event for the profile inputs (prevent selection)
    $('.profile-inputs-container').on('click', function(event) {
        event.stopPropagation(); // Prevent event from bubbling to parent profile item
    });

    // Click event for the profile item
    $('.profile-item').on('click', function(event) {
        const profileId = $(this).find('.profile-header').data('id'); // Get the profile ID from the badge

        // Toggle selection
        const index = selectedProfiles.findIndex(profile => profile.id === profileId);
        if (index > -1) {
            // If already selected, deselect it
            selectedProfiles.splice(index, 1); // Remove from array
            $(this).removeClass('selected'); // Remove selected style
            console.log('Deselected profile ID:', profileId);
        } else {
            // If not selected, select it
            const regulationHeight = $('#regulationHeight_' + profileId).val();
            const width = $('#width_' + profileId).val();
            const videoBitrate = $('#videoBitrate_' + profileId).val();
            const audioBitrate = $('#audioBitrate_' + profileId).val();
            const fps = $('#fps_' + profileId).val();

            selectedProfiles.push({
                id: profileId,
                width: width,
                height: regulationHeight,
                videoBitrate: videoBitrate,
                audioBitrate: audioBitrate,
                fps: fps
            }); // Add object to array
            $(this).addClass('selected'); // Add selected style
            console.log('Selected profile ID:', profileId);
        }

        // Log the current state of selectedProfiles array
        console.log('Current selected profiles:', selectedProfiles);

        // Update the hidden input field with selected profiles
        $('#selectedProfiles').val(JSON.stringify(selectedProfiles)); // Convert array to JSON string
    });

    $('.profile-inputs input').on('input', function() {
        const profileId = $(this).closest('.profile-inputs-container').siblings('.profile-item').data('profile').split('_')[1];
        
        // Get the current values from the inputs
        const regulationHeight = $('#regulationHeight_' + profileId).val();
        const width = $('#width_' + profileId).val();
        const videoBitrate = $('#videoBitrate_' + profileId).val();
        const audioBitrate = $('#audioBitrate_' + profileId).val();
        const fps = $('#fps_' + profileId).val();

        // Update the corresponding text elements
        const profileItem = $(this).closest('.profile-inputs-container').siblings('.profile-item');
        profileItem.find('.profile-text').html(`Regulation ${width}x${regulationHeight}`);
        profileItem.find('.bitrate-text').html(`Video bitrate ${videoBitrate}`);
        profileItem.find('.fps-text').html(`${fps}`);
        profileItem.find('.audio-text').html(`Audio bitrate ${audioBitrate}`);
    });
});

</script>




    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            let isValid = true;

            // Iterate through each dynamically added profile
            document.querySelectorAll('.collapse').forEach(function(profile) {
                const scaleX = profile.querySelector('input[name*="[scale_x]"]');
                const scaleY = profile.querySelector('input[name*="[scale_y]"]');
                const height = profile.querySelector('input[name*="[height]"]');
                const width = profile.querySelector('input[name*="[width]"]');
                const audioBitrate = profile.querySelector('input[name*="[audio_bitrate]"]');
                const videoBitrate = profile.querySelector('input[name*="[video_bitrate]"]');

                // Check if any of the fields are empty
                if (
                    !scaleX.value.trim() ||
                    !scaleY.value.trim() ||
                    !height.value.trim() ||
                    !width.value.trim() ||
                    !audioBitrate.value.trim() ||
                    !videoBitrate.value.trim()
                ) {
                    isValid = false;

                    // Add an error message if any field is empty
                    alert('All fields in each profile must be filled out.');

                    // Optionally, focus on the first empty field
                    if (!scaleX.value.trim()) scaleX.focus();
                    else if (!scaleY.value.trim()) scaleY.focus();
                    else if (!height.value.trim()) height.focus();
                    else if (!width.value.trim()) width.focus();
                    else if (!audioBitrate.value.trim()) audioBitrate.focus();
                    else if (!videoBitrate.value.trim()) videoBitrate.focus();

                    // Stop further iteration
                    return;
                }
            });

            // Prevent form submission if validation failed
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
@endsection
