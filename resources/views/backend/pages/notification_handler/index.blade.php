@extends('backend.layouts.master')

@section('title')
    Notification Handlers - Admin Panel
@endsection

@section('styles')
    <!-- Datatable CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endsection

@section('admin-content')
    <!-- Page Title Area Start -->
    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="breadcrumbs-area clearfix">
                    <h4 class="page-title pull-left">Notification Handlers</h4>
                    <ul class="breadcrumbs pull-left">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><span>All Handlers</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-6 clearfix">
                @include('backend.layouts.partials.logout')
            </div>
        </div>
    </div>
    <!-- Page Title Area End -->

    <div class="main-content-inner">
        <div class="row">
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title float-left">Notification Handler List</h4>
                        <p class="float-right mb-2">
                            <a class="btn btn-primary text-white" href="{{ route('notification-handler.create') }}">Create New Handler</a>
                        </p>
                        <div class="clearfix"></div>
                        <div class="data-tables">
                            @include('backend.layouts.partials.messages')
                            <table id="dataTable" class="text-center cell-border" style="width: 100%;">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>ID</th>
                                        <th>Handler Name</th>
                                        <th>Handler Type</th>
                                        <th>Link</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($handlers as $handler)
                                        <tr>
                                            <td>{{ $handler->id }}</td>
                                            <td>{{ $handler->name }}</td>
                                            <td>{{ ucfirst($handler->notification_type) }}</td>
                                            <td>{{ $handler->url }}</td>
                                            <td>{{ $handler->created_at->diffForHumans() }}</td>
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <a href="{{ route('notification-handler.edit', $handler->id) }}" 
                                                           class="text-secondary" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="text-danger"
                                                           onclick="event.preventDefault(); 
                                                           if(confirm('Are you sure you want to delete this handler?')) {
                                                               document.getElementById('delete-form-{{ $handler->id }}').submit();
                                                           }">
                                                            <i class="ti-trash"></i>
                                                        </a>
                                                        <form id="delete-form-{{ $handler->id }}" 
                                                              action="{{ route('notification-handler.destroy', $handler->id) }}" 
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
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                responsive: true
            });
        });
    </script>
@endsection
