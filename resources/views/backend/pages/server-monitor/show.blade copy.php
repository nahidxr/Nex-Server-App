@extends('backend.layouts.master')

@section('title')
Admin Server Monitor - Admin Panel
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .card-header {
        background-color: #1d1f2b; /* Dark background for header */
        color: white;
        padding: 20px; /* Add padding for better spacing */
    }

    .status-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #28a745; /* Green status */
        display: inline-block;
        margin-right: 10px;
    }

    .server-title {
        font-size: 24px;
        font-weight: bold;
    }

    .ip-address {
        color: #ccc; /* Lighter color for IP */
        margin-top: 5px;
        font-size: 14px;
    }

    .btn-green {
        background-color: transparent; /* No background initially */
        color: #28a745; /* Green text for the install button */
        border: 1px solid #28a745; /* Green border for the install button */
        transition: background-color 0.3s, color 0.3s; /* Smooth transition for hover effects */
    }

    .btn-green:hover {
        background-color: #28a745; /* Green background on hover */
        color: white; /* White text on hover */
    }

    .btn-outline {
        background-color: transparent; /* No background initially */
        border: 1px solid #6c757d; /* Gray border for uninstall button */
        color: #6c757d; /* Gray text for uninstall button */
        transition: background-color 0.3s, color 0.3s; /* Smooth transition for hover effects */
    }

    .btn-outline:hover {
        background-color: #6c757d; /* Gray background on hover */
        color: white; /* White text on hover */
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
                    <div class="d-flex align-items-center">
                        <div class="status-icon"></div>
                        <div class="server-title">Toffee Server Monitor</div>
                    </div>
                    <div class="ip-address">192.168.5.150</div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between ">
                        <button class="btn btn-green w-100 mr-4">Install Code</button>
                        <button class="btn btn-outline w-100">Uninstall Code</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- server monitor end -->
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
