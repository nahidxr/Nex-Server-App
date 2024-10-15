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
    .alert-settings {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-top: 20px;
        display: none; /* Initially hide the advanced settings */
    }
    .alert-settings-header {
        cursor: pointer;
        background: #f8f9fa;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-top: 10px;
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
                    <li><a href="{{ route('server-monitor.index') }}">All Server</a></li>
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

                        <!-- Advanced Settings Header -->
                        <div class="alert-settings-header" onclick="toggleAdvancedSettings()">
                            <h5>Advanced Settings <i class="fa fa-chevron-down" id="toggle-icon"></i></h5>
                        </div>

                       
                      <!-- Advanced Settings Section -->
                        <div class="alert-settings" id="advanced-settings">
                            <div class="form-row align-items-center">
                                <div class="col-md-3 mb-3">
                                    <label for="metric"><i class="fa fa-bell"></i> Alert Metric</label>
                                    <select class="form-control" id="metric" name="metric" style="height: 44.44px;">
                                        <option value="cpu_usage">CPU Usage</option>
                                        <option value="memory_usage">Memory Usage</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="alert_rule"><i class="fa fa-exclamation-triangle"></i> Rule</label>
                                    <select class="form-control" id="alert_rule" name="alert_rule" style="height: 44.44px;">
                                        <option value="lower_than">Lower Than</option>
                                        <option value="higher_than">Higher Than</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="value"><i class="fa fa-dollar-sign"></i> Value</label>
                                    <input type="number" class="form-control" id="value" name="value" placeholder="Enter Value">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="trigger_after_x"><i class="fa fa-clock"></i> Trigger After X Checks</label>
                                    <input type="number" class="form-control" id="trigger_after_x" name="trigger_after_x" placeholder="Enter Number of Checks" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="notification"><i class="fa fa-bell"></i> Notifications</label>
                                <select class="form-control" id="notification" name="notification" style="height: 44.44px;">
                                    <option value="" disabled selected>Select Notification Handler</option>
                                    @foreach($notificationHandlers as $handler)
                                        <option value="{{ $handler->id }}">{{ $handler->name }}</option>
                                    @endforeach
                                </select>
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

    function toggleAdvancedSettings() {
        var advancedSettings = document.getElementById('advanced-settings');
        var toggleIcon = document.getElementById('toggle-icon');

        if (advancedSettings.style.display === 'none' || advancedSettings.style.display === '') {
            advancedSettings.style.display = 'block';
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        } else {
            advancedSettings.style.display = 'none';
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
        }
    }
</script>
@endsection
