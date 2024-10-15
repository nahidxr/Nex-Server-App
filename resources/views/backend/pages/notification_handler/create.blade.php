@extends('backend.layouts.master')

@section('title', 'Create Notification Handler')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection

@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Create Notification Handler</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><span>Notification Handler</span></li>
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
                    <div class="d-flex align-items-center header-title">
                        <i class="fa fa-bell mr-2"></i>
                        <h4 class="mb-0">Create a new Notification Handler</h4>
                    </div>
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('notification-handler.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 col-sm-12">
                                <!-- Handler Name -->
                                <div class="form-group">
                                    <label for="name"><i class="fa fa-hand-paper"></i> Handler Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Handler Name" required>
                                </div>
                            </div>
                            
                            <div class="col-md-12 col-sm-12">
                                <!-- Notification Type -->
                                <div class="form-group">
                                    <label for="notification_type"><i class="fa fa-bell"></i> Notification Type</label>
                                    <select class="form-control" id="type" name="notification_type" required>
                                        <option value="" disabled selected>Select Notification Type</option>
                                        <option value="webhook">Webhook</option>
                                        <option value="slack">Slack</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 col-sm-12">
                                <!-- Link -->
                                <div class="form-group">
                                    <label for="link"><i class="fa fa-link"></i> Link</label>
                                    <input type="url" class="form-control" id="url" name="url" placeholder="Enter Link" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Create Handler</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection
