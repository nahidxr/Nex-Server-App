@extends('backend.layouts.master')

@section('title')
Admin Edit - Admin Panel
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
    .custom-button {
        padding: 5px 10px; /* Adjust the vertical and horizontal padding as needed */
        font-size: 14px;    /* Optionally adjust the font size */
    }
</style>
@endsection

@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Server Edit</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('server-monitor.index') }}">All Server</a></li>
                    <li><span>Edit Server</span></li>
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
                        <h4 class="mb-0">Edit Server Monitor</h4>
                    </div>
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('server-monitor.update', $serverMonitor->id) }}" method="POST">
                        @csrf
                        @method('POST') <!-- This line is essential for updating -->
                        <div class="form-row">
                            <div class="col-md-12 col-sm-12">
                                <!-- Server Name -->
                                <div class="form-group">
                                    <label for="name"><i class="fa fa-desktop"></i> Server Name</label>
                                    <input type="text" class="form-control" id="name" name="server_name" placeholder="Enter Server Name" value="{{ old('server_name', $serverMonitor->server_name) }}" required>
                                </div>
                            </div>
                        
                            <div class="col-md-12 col-sm-12">
                                <!-- Identifier -->
                                <div class="form-group">
                                    <label for="server_ip"><i class="fa fa-link"></i> Identifier</label>
                                    <input type="text" class="form-control" id="server_ip" name="identifier" placeholder="Enter the IP or Domain" value="{{ old('identifier', $serverMonitor->identifier) }}" required>
                                </div>
                            </div>
                        
                            <div class="col-md-12 col-sm-12">
                                <!-- Check Interval -->
                                <div class="form-group">
                                    <label for="check_interval" class="col-form-label"><i class="fa fa-retweet"></i> Check Interval</label>
                                    <select class="form-control" id="check_interval" name="check_interval" style="height: 44.44px;" required>
                                        <option value="" disabled>Select Check Interval</option>
                                        <option value="5" {{ $serverMonitor->check_interval == 5 ? 'selected' : '' }}>5 minutes</option>
                                        <option value="10" {{ $serverMonitor->check_interval == 10 ? 'selected' : '' }}>10 minutes</option>
                                        <option value="15" {{ $serverMonitor->check_interval == 15 ? 'selected' : '' }}>15 minutes</option>
                                        <option value="30" {{ $serverMonitor->check_interval == 30 ? 'selected' : '' }}>30 minutes</option>
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
                       <!-- Inside your form, where the alert-container is located -->
                            <div id="alert-container">
                                <label for="notification"><i class="fa fa-exclamation-triangle"></i> Alerts</label>
                                @foreach($serverMonitor->alerts as $alert)
                                    <div class="alert-row" style="display: block;">
                                        <div class="form-row align-items-center">
                                            <div class="col-md-3 mb-3">
                                                <label for="metric">Metric</label>
                                                <select class="form-control" name="metric[]" style="height: 44.44px;">
                                                    <option value="cpu_usage" {{ $alert['metric'] == 'cpu_usage' ? 'selected' : '' }}>CPU Usage</option>
                                                    <option value="memory_usage" {{ $alert['metric'] == 'memory_usage' ? 'selected' : '' }}>Memory Usage</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="alert_rule">Rule</label>
                                                <select class="form-control" name="alert_rule[]" style="height: 44.44px;">
                                                    <option value="lower_than" {{ $alert['rule'] == 'lower_than' ? 'selected' : '' }}>Lower Than</option>
                                                    <option value="higher_than" {{ $alert['rule'] == 'higher_than' ? 'selected' : '' }}>Higher Than</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="value"><i class="fa fa-dollar-sign"></i> Value</label>
                                                <input type="number" class="form-control" name="value[]" value="{{ $alert['value'] }}" placeholder="Enter Value">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="trigger_after_x"><i class="fa fa-clock"></i> Trigger After X Checks</label>
                                                <input type="number" class="form-control" name="trigger_after_x[]" value="{{ $alert['trigger_after_checks'] }}" placeholder="Enter Number of Checks">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger w-100 delete-alert custom-button mb-4" title="Delete Alert">
                                            <i class="fa fa-trash"> Delete</i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-outline-success w-100 custom-button" id="add-alert">
                                <i class="fa fa-plus-circle"></i> Add new alert
                            </button>

                            <div class="form-group mt-4">
                                <label for="notification"><i class="fa fa-bell"></i> Notifications</label>
                                <select class="form-control" id="notification" name="notification" style="height: 44.44px;">
                                    <option value="" disabled>Select Notification Handler</option>
                                    @foreach($notificationHandlers as $handler)
                                        <option value="{{ $handler->id }}" {{ $serverMonitor->notification == $handler->id ? 'selected' : '' }}>{{ $handler->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                    
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Update Server</button>
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

<script>
    document.getElementById('add-alert').addEventListener('click', function() {
        var alertContainer = document.getElementById('alert-container');
        var newAlert = `
            <div class="alert-row" style="display: block;">
                <div class="form-row align-items-center">
                    <div class="col-md-3 mb-3">
                        <label for="metric">Metric</label>
                        <select class="form-control" name="metric[]" style="height: 44.44px;">
                            <option value="cpu_usage">CPU Usage</option>
                            <option value="memory_usage">Memory Usage</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="alert_rule">Rule</label>
                        <select class="form-control" name="alert_rule[]" style="height: 44.44px;">
                            <option value="lower_than">Lower Than</option>
                            <option value="higher_than">Higher Than</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="value"><i class="fa fa-dollar-sign"></i> Value</label>
                        <input type="number" class="form-control" name="value[]" placeholder="Enter Value">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trigger_after_x"><i class="fa fa-clock"></i> Trigger After X Checks</label>
                        <input type="number" class="form-control" name="trigger_after_x[]" placeholder="Enter Number of Checks">
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger w-100 delete-alert custom-button mb-4" title="Delete Alert">
                    <i class="fa fa-trash"> Delete</i>
                </button>
            </div>`;
        alertContainer.insertAdjacentHTML('beforeend', newAlert);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-alert')) {
            e.target.closest('.alert-row').remove();
        }
    });
</script>
@endsection
