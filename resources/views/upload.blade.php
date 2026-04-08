@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2 class="main-content-title tx-24 mg-b-5">Upload Well Test Log</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Well Testing</a></li>
                <li class="breadcrumb-item active">Upload VX Log</li>
            </ol>
        </div>
    </div>

    <!-- Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Well Test Information</h6>
                </div>

                <div class="card-body">
                    <form action="{{ route('process.log') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Manual Inputs -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Client</label>
                                    <input type="text" name="client" class="form-control" placeholder="Client Name" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Field</label>
                                    <input type="text" name="field" class="form-control" placeholder="Upper Zakum" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Well Name</label>
                                    <input type="text" name="well" class="form-control" placeholder="ZK-23" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Choke Size (1/64")</label>
                                    <input type="number" name="choke_size" class="form-control" placeholder="32" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Operator Name</label>
                                    <input type="text" name="operator" class="form-control" placeholder="Operator Name" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Test Date</label>
                                    <input type="date" name="test_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Test Type</label>
                                    <select name="test_type" class="form-control">
                                        <option value="flow">Flow Test</option>
                                        <option value="cleanup">Clean Up</option>
                                        <option value="build-up">Build Up</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- File Upload -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>VX Log File</label>
                                    <input type="file" name="log_file" class="form-control" accept=".vxlog,.txt" required>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="row mt-4">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-upload me-2"></i> Upload & Analyze
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
