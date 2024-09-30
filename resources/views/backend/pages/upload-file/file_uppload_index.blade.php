
@extends('backend.layouts.master')

@section('title')
Role Page - Admin Panel
@endsection

@section('styles')
@endsection


@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Roles</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><span>All Roles</span></li>
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
    <div class="row justify-content-center">

        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Upload File</h5>
                </div>

                <div class="card-body">
                    <div id="upload-container" class="text-center">
                        <button id="browseFile" class="btn btn-primary">Browse File</button>
                    </div>
                    <div style="display: none" class="progress mt-3" style="height: 25px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%; height: 100%">75%</div>
                    </div>
                </div>

                <div class="card-footer p-4" style="display: none">
                    <img id="imagePreview" src="" style="width: 100%; height: auto; display: none" alt="img"/>
                    <video id="videoPreview" src="" controls style="width: 100%; height: auto; display: none"></video>
                </div>
            </div>
        </div>



    </div>
</div>
@endsection


@section('scripts')
 <!-- Include required scripts -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
 <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

 <script type="text/javascript">
     let browseFile = $('#browseFile');
     let resumable = new Resumable({
         target: '{{ route('upload.store') }}',
         query: {_token: '{{ csrf_token() }}'},
         fileType: ['png', 'jpg', 'jpeg', 'mp4'],
         chunkSize: 10 * 1024 * 1024, // Adjust chunk size based on server limit
         headers: {
             'Accept': 'application/json'
         },
         testChunks: false,
         throttleProgressCallbacks: 1,
     });

     resumable.assignBrowse(browseFile[0]);

     resumable.on('fileAdded', function (file) {
         showProgress();
         resumable.upload();
     });

     resumable.on('fileProgress', function (file) {
         updateProgress(Math.floor(file.progress() * 100));
     });

     resumable.on('fileSuccess', function (file, response) {
         response = JSON.parse(response);

         if (response.mime_type.includes("image")) {
             $('#imagePreview').attr('src', response.path + '/' + response.name).show();
         }

         if (response.mime_type.includes("video")) {
             $('#videoPreview').attr('src', response.path + '/' + response.name).show();
         }

         $('.card-footer').show();
     });

     resumable.on('fileError', function (file, response) {
         alert('file uploading error.');
     });

     let progress = $('.progress');

     function showProgress() {
         progress.find('.progress-bar').css('width', '0%');
         progress.find('.progress-bar').html('0%');
         progress.find('.progress-bar').removeClass('bg-success');
         progress.show();
     }

     function updateProgress(value) {
         progress.find('.progress-bar').css('width', `${value}%`);
         progress.find('.progress-bar').html(`${value}%`);

         if (value === 100) {
             progress.find('.progress-bar').addClass('bg-success');
         }
     }

     function hideProgress() {
         progress.hide();
     }
 </script>
@endsection