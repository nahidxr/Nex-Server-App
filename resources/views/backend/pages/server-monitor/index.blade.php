@extends('backend.layouts.master')

@section('title')
    Server Monitor Page - Admin Panel
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
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
                        <li><span>All Servers</span></li>
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
                            <a class="btn btn-primary text-white" href="{{ route('server-monitor.create') }}">Create New
                                Server</a>
                        </p>
                        <div class="clearfix"></div>
                        <div class="data-tables">
                            @include('backend.layouts.partials.messages')
                            <table id="dataTable" class="text-center cell-border" style="width: 100%;">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="15%">Server Name</th>
                                        <th width="15%">CPU Usage</th>
                                        <th width="15%">RAM Usage</th>
                                        <th width="15%">Disk Usage</th>
                                        <th width="15%">UpTime</th>
                                        <th width="15%">Last Log</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($servers as $server)
                                        @php
                                            $data = json_decode($server->server_data, true);
                                        @endphp
                                        <tr>
                                            <td>{{ $server->id }}</td>
                                            <td>{{ $server->server_name }}</td>
                                            <td>
                                                <div class="usage-box" style="background-color: rgba(76, 175, 80, 0.2);">
                                                    <span>{{ $data['cpu_usage'] ?? 0 }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="usage-box" style="background-color: rgba(3, 169, 244, 0.2);">
                                                    <span>{{ $data['ram_usage'] ?? 0 }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="usage-box" style="background-color: rgba(189, 189, 189, 0.2);">
                                                    <span>{{ $data['disk_usage'] ?? 0 }}%</span>
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
                                                        $uptimeParts[] =
                                                            "{$uptimeInYears} year" . ($uptimeInYears > 1 ? 's' : '');
                                                    }
                                                    if ($remainingDays > 0) {
                                                        $uptimeParts[] =
                                                            "{$remainingDays} day" . ($remainingDays > 1 ? 's' : '');
                                                    }
                                                    if ($remainingHours > 0) {
                                                        $uptimeParts[] =
                                                            "{$remainingHours} hour" . ($remainingHours > 1 ? 's' : '');
                                                    }
                                                    if ($remainingMinutes > 0) {
                                                        $uptimeParts[] =
                                                            "{$remainingMinutes} minute" .
                                                            ($remainingMinutes > 1 ? 's' : '');
                                                    }

                                                    // Join the parts with a space
                                                    $uptimeString = implode(' ', $uptimeParts) ?: '-';
                                                @endphp

                                                <span>{{ $uptimeString }}</span>
                                            </td>
                                            <td>
                                                <span>
                                                    {{ $server->server_data ? ($server->updated_at ? $server->updated_at->diffForHumans() : '-') : '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <a href="{{ route('server-monitor.show', $server->id) }}"
                                                            class="text-secondary" title="View Server">
                                                            <i class="fa fa-eye"></i> <!-- Font Awesome eye icon -->
                                                        </a>
                                                    </li>
                                                    <li class="mr-3">
                                                        <a href="{{ route('server-monitor.edit', $server->id) }}"
                                                            class="text-secondary" title="Edit Server Monitor">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="text-danger"
                                                            onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this server?')) { document.getElementById('delete-form-{{ $server->id }}').submit(); }">
                                                            <i class="ti-trash"></i>
                                                        </a>

                                                        <form id="delete-form-{{ $server->id }}"
                                                            action="{{ route('server-monitor.destroy', $server->id) }}"
                                                            method="POST" style="display: none;">
                                                            @method('DELETE')
                                                            @csrf
                                                        </form>
                                                    </li>
                                                </ul>
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
@endsection

@section('scripts')
    <!-- Start datatable js -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

    <script>
        /*================================
           datatable active
           ==================================*/
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                responsive: true
            });
        }
    </script>
@endsection
