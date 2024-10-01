@extends('backend.layouts.master')

@section('title')
Admin Create - Admin Panel
@endsection

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
                <h4 class="page-title pull-left">Server Create</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.admins.index') }}">All Server</a></li>
                    <li><span>Create Server</span></li>
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
                        <i class="fa fa-server mr-2"></i>
                        <h4 class="mb-0">Create a new server monitor</h4>
                    </div>
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('server-monitor.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 col-sm-12">
                                <!-- Server Name -->
                                <div class="form-group">
                                    <label for="name"><i class="fa fa-desktop"></i> Server Name</label>
                                    <input type="text" class="form-control" id="name" name="server_name" placeholder="Enter Server Name" required>
                                </div>
                            </div>
                        
                            <div class="col-md-12 col-sm-12">
                                <!-- Server IP -->
                                <div class="form-group">
                                    <label for="server_ip"><i class="fa fa-link"></i> Identifier</label>
                                    <input type="text" class="form-control" id="server_ip" name="identifier" placeholder="12.85.103.987" required>
                                </div>
                            </div>
                        
                            <div class="col-md-12 col-sm-12">
                                <!-- Check Interval -->
                                <div class="form-group">
                                    <label for="check_interval" class="col-form-label"><i class="fa fa-retweet"></i> Check Interval</label>
                                    <select class="form-control" id="check_interval" name="check_interval" style="height: 44.44px;" required>
                                        <option value="" disabled selected>Select Check Interval</option>
                                        <option value="5">5 minutes</option>
                                        <option value="10">10 minutes</option>
                                        <option value="15">15 minutes</option>
                                        <option value="30">30 minutes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Save Server</button>
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
