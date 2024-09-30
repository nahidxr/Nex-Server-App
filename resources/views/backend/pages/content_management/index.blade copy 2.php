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
                                        <a class="btn btn-primary btn-sm text-white" href="{{ url('/content/' . $content->id . '/edit') }}">Update</a>
                                        <a class="btn btn-danger btn-sm ml-1 text-white" href="{{ url('/content/' . $content->id) }}" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $content->id }}').submit();">Delete</a>
                                        <form id="delete-form-{{ $content->id }}" action="{{ url('/content/' . $content->id) }}" method="POST" style="display: none;">
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
                    <div class="form-group">
                        <label for="fileNameInput">File Name</label>
                        <input type="text" class="form-control" id="fileNameInput" name="file_name" required>
                    </div>
                    <!-- Role-based file upload form -->
                    <div id="upload-container" class="text-center">
                        <button id="browseFile" class="btn btn-primary">Browse File</button>
                    </div>
                    <video id="videoPreview" controls style="display: none; width: 100%; height: auto; margin-top: 10px;"></video>

                    <div style="display: none" class="progress mt-3" style="height: 25px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%; height: 100%">75%</div>
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
