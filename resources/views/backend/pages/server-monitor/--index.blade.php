@extends('backend.layouts.master')

@section('title')
    Server Monitor Page - Admin Panel
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
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
                        <li><span>All Server</span></li>
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
                        <h4 class="header-title float-left">Server List</h4>
                        <p class="float-right mb-2">
                            <a class="btn btn-primary text-white" href="#">Create New Server</a>
                        </p>
                        <div class="clearfix"></div>
                        <div class="data-tables">
                            @include('backend.layouts.partials.messages')
                            <!-- Progress Table start -->

                            <table class="table table-hover progress-table text-center">
                                <thead class="text-uppercase">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Server Name</th>
                                        <th scope="col">CPU Usage</th>
                                        <th scope="col">RAM Usage</th>
                                        <th scope="col">Disk Usage</th>
                                        <th scope="col">Uptime</th>
                                        <th scope="col">Last Log</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($servers as $server)
                                        @php
                                            // Decode the server_data column (JSON)
                                            $data = json_decode($server->server_data, true);
                                        @endphp
                                        <tr>
                                            <th scope="row">{{ $server->id }}</th>
                                            <td>{{ $server->server_name }}</td>
                                            <td>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $data['cpu_usage'] ?? 0 }}%;" 
                                                         aria-valuenow="{{ $data['cpu_usage'] ?? 0 }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" 
                                                         style="width: {{ $data['ram_usage'] ?? 0 }}%;" 
                                                         aria-valuenow="{{ $data['ram_usage'] ?? 0 }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $data['disk_usage'] ?? 0 }}%;" 
                                                         aria-valuenow="{{ $data['disk_usage'] ?? 0 }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $uptimeInSeconds = isset($data['uptime']) ? $data['uptime'] : 0;
                                                    $uptimeInMinutes = floor($uptimeInSeconds / 60);
                                                    $uptimeInHours = floor($uptimeInMinutes / 60);
                                                    $uptimeInDays = floor($uptimeInHours / 24);
                                                    $uptimeInMonths = floor($uptimeInDays / 30);
                                                    $uptimeInYears = floor($uptimeInMonths / 12);
                                            
                                                    // Calculate the remainder for more precise output
                                                    $remainingDays = $uptimeInDays % 30;
                                                    $remainingHours = $uptimeInHours % 24;
                                                    $remainingMinutes = $uptimeInMinutes % 60;
                                            
                                                    // Construct the uptime string
                                                    $uptimeParts = [];
                                            
                                                    if ($uptimeInYears > 0) {
                                                        $uptimeParts[] = "{$uptimeInYears} year" . ($uptimeInYears > 1 ? 's' : '');
                                                    }
                                                    if ($remainingDays > 0) {
                                                        $uptimeParts[] = "{$remainingDays} day" . ($remainingDays > 1 ? 's' : '');
                                                    }
                                                    if ($remainingHours > 0) {
                                                        $uptimeParts[] = "{$remainingHours} hour" . ($remainingHours > 1 ? 's' : '');
                                                    }
                                                    if ($remainingMinutes > 0) {
                                                        $uptimeParts[] = "{$remainingMinutes} minute" . ($remainingMinutes > 1 ? 's' : '');
                                                    }
                                            
                                                    // Join the parts with a space
                                                    $uptimeString = implode(' ', $uptimeParts) ?: 'N/A';
                                                @endphp
                                            
                                                <span>{{ $uptimeString }}</span>
                                            </td>
                                            <td><span>{{ $server->updated_at->diffForHumans() }}</span></td>
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <a href="#" class="text-secondary">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="text-danger">
                                                            <i class="ti-trash"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
                            

                            <!-- Progress Table end -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- data table end -->

        </div>
    </div>
@endsection


@section('scripts')
@endsection
