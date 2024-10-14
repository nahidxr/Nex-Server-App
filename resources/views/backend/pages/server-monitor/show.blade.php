@extends('backend.layouts.master')

@section('title')
    Admin Server Monitor - View Server
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .card-header {
            background-color: #1d1f2b;
            /* Dark background for header */
            color: white;
            padding: 20px;
            /* Add padding for better spacing */
        }

        .status-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #28a745;
            /* Green status */
            display: inline-block;
            margin-right: 10px;
        }

        .server-title {
            font-size: 20px;
            /* Font size for the title */
            font-weight: bold;
            margin-left: 10px;
        }

        .ip-address {
            font-size: 14px;
            /* Font size for the IP address */
            color: #6c757d;
            /* Space above the IP address */
            margin-left: 10px;
            /* Indent the IP address under the title */
        }

    

        .btn-green {
            background-color: transparent;
            /* No background initially */
            color: #28a745;
            /* Green text for the install button */
            border: 1px solid #28a745;
            /* Green border for the install button */
            transition: background-color 0.3s, color 0.3s;
            /* Smooth transition for hover effects */
        }

        .btn-green:hover {
            background-color: #28a745;
            /* Green background on hover */
            color: white;
            /* White text on hover */
        }

        .btn-outline {
            background-color: transparent;
            /* No background initially */
            border: 1px solid #6c757d;
            /* Gray border for uninstall button */
            color: #6c757d;
            /* Gray text for uninstall button */
            transition: background-color 0.3s, color 0.3s;
            /* Smooth transition for hover effects */
        }

        .btn-outline:hover {
            background-color: #6c757d;
            /* Gray background on hover */
            color: white;
            /* White text on hover */
        }

        .check-icon {
            width: 50px;
            /* Adjust the size */
            height: 50px;
            /* Adjust the size */
            border-radius: 50%;
            /* Make it circular */
            background-color: #3c9a71;
            /* Green background color */
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Add shadow if needed */
        }

        .check-icon i {
            font-size: 30px;
            /* Adjust size of the checkmark */
            color: white;
            /* Color of the checkmark */
        }
    </style>
@endsection

@section('admin-content')
    <!-- page title area start -->
    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="breadcrumbs-area clearfix">
                    <h4 class="page-title pull-left">Server Monitor</h4>
                    <ul class="breadcrumbs pull-left">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.admins.index') }}">All Servers</a></li>
                        <li><span>View Server</span></li>
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
            <!-- server monitor start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-column align-items-start">
                            <div class="d-flex align-items-center">
                                <div class="check-icon mr-2">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                                <div class="server-info">
                                    <div class="server-title">{{ $serverMonitor->server_name }} Server Monitor</div>
                                    <div class="ip-address">{{ $serverMonitor->identifier }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between ">
                            <button class="btn btn-green w-100 mr-4" data-toggle="modal" data-target="#installModal">Install
                                Code</button>
                            <button class="btn btn-outline w-100" data-toggle="modal"
                                data-target="#uninstallModal">Uninstall Code</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- server monitor end -->
        </div>
    </div>

    <!-- Install Modal -->
    <div class="modal fade" id="installModal" tabindex="-1" role="dialog" aria-labelledby="installModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="installModalLabel"><i class="fa fa-code"></i> Install Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-justify">
                        You must copy and run this code in the terminal of your Linux server.
                        Your server language must be set to English for the code to work properly.
                    </p>
                    <div class="mb-3">
                        <textarea id="uninstallCode" class="form-control" readonly rows="3"
                            style="overflow-x: auto; overflow-y: hidden; white-space: nowrap; resize: none; width: 100%;">
                            {{ $installScript }}                 
                            </textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn w-100"
                        style="background-color: #10b77f; border-color: #10b77f; color: white; font-size: 16px;"
                        onclick="copyToClipboard('#installCode')">Copy to clipboard</button>

                </div>
            </div>
        </div>
    </div>

    <!-- Uninstall Modal -->
    <div class="modal fade" id="uninstallModal" tabindex="-1" role="dialog" aria-labelledby="uninstallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uninstallModalLabel"><i class="fa fa-code"></i> Uninstall Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-justify">
                        You must copy and run this code in the terminal of your Linux server.
                    </p>
                    <div class="mb-3">
                        <textarea id="uninstallCode" class="form-control" readonly rows="3"
                            style="overflow-x: auto; overflow-y: hidden; white-space: nowrap; resize: none; width: 100%;">
                            {{ $uninstallScript }}    
                            </textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn w-100"
                        style="background-color: #10b77f; border-color: #10b77f; color: white; font-size: 16px;"
                        onclick="copyToClipboard('#installCode')">Copy to clipboard</button>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        function copyToClipboard(selector) {
            const textarea = document.querySelector(selector);
            textarea.select();
            document.execCommand('copy');
            alert('Code copied to clipboard!');
        }
    </script>
    <script>
        document.getElementById('confirmUninstall').addEventListener('click', function() {
            // Add the uninstall logic here, e.g., sending an AJAX request to your uninstall route
            alert('Uninstall action triggered for ' + '{{ $serverMonitor->server_name }}');
            $('#uninstallModal').modal('hide'); // Close the modal after action
        }); <
    </script>

    </script>
@endsection
