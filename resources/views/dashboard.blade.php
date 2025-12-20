@extends('layouts.master')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Custom styles for the modern dashboard */
    body {
        background-color: #f8f9fa; /* Lighter background for less contrast */
    }
    .main-content {
        padding-top: 1.5rem; /* Reduce top padding */
    }
    .dashboard-header {
        background: linear-gradient(to right, #4e73df 0%, #667eea 100%);
        color: white;
        padding: 1.5rem 1.5rem; /* Reduced padding */
        border-radius: 15px;
        margin-bottom: 1.5rem; /* Reduced margin */
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .dashboard-header h1 {
        font-size: 2rem; /* Reduced font size */
        font-weight: 600;
    }
    .stat-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 1.5rem; /* Added margin between cards */
        height: 100%; /* Make cards in a row have equal height */
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.12);
    }
    .stat-card .card-body {
        padding: 1.5rem; /* Reduced padding */
        display: flex;
        align-items: center;
        height: 100%;
    }
    .stat-card .stat-icon {
        width: 50px; /* Reduced icon size */
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.25rem;
    }
    .stat-card .stat-info h3 {
        font-size: 1.75rem; /* Reduced font size */
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .quick-actions .btn {
        margin-bottom: 0.5rem; /* Add small margin between buttons on small screens */
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1>MPFM System Dashboard</h1>
        <p class="mb-0">Monitor and manage your well testing operations efficiently.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row row-sm">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fe fe-file-text"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $totalTests }}</h3>
                        <p class="text-muted mb-0">Total Tests</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fe fe-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $totalUsers }}</h3>
                        <p class="text-muted mb-0">Total Users</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="fe fe-trending-up"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $recentTests }}</h3>
                        <p class="text-muted mb-0">Tests in Last 7 Days</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fe fe-activity"></i>
                    </div>
                    <div class="stat-info">
                        <h3>98.5%</h3>
                        <p class="text-muted mb-0">System Health</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row row-sm mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('upload.form') }}" class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                                <i class="fe fe-upload me-2"></i> Upload New Test
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('well_tests.index') }}" class="btn btn-outline-primary btn-block d-flex align-items-center justify-content-center">
                                <i class="fe fe-database me-2"></i> View Archive
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-block d-flex align-items-center justify-content-center">
                                <i class="fe fe-users me-2"></i> Manage Users
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-success btn-block d-flex align-items-center justify-content-center">
                                <i class="fe fe-lock me-2"></i> Permissions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
