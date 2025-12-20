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
                    <!-- عرض رسائل النجاح (إن وجدت) -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- عرض رسائل الخطأ العامة -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>There were some problems with your input:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('process.log') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Manual Inputs -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Client</label>
                                    <input type="text" name="client" class="form-control @error('client') is-invalid @enderror" placeholder="ADNOC" value="{{ old('client') }}" required>
                                    @error('client')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Field</label>
                                    <input type="text" name="field" class="form-control @error('field') is-invalid @enderror" placeholder="Upper Zakum" value="{{ old('field') }}" required>
                                    @error('field')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Well Name</label>
                                    <input type="text" name="well" class="form-control @error('well') is-invalid @enderror" placeholder="ZK-23" value="{{ old('well') }}" required>
                                    @error('well')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Choke Size (1/64")</label>
                                    <input type="number" name="choke_size" class="form-control" placeholder="32" value="{{ old('choke_size') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Operator Name</label>
                                    <input type="text" name="operator" class="form-control @error('operator') is-invalid @enderror" placeholder="Operator Name" value="{{ old('operator') }}" required>
                                    @error('operator')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Wellhead Tower</label>
                                    <input type="text" name="wellhead_tower" class="form-control" placeholder="Tower Name" value="{{ old('wellhead_tower') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Wellhead String</label>
                                    <input type="text" name="wellhead_string" class="form-control" placeholder="String Name" value="{{ old('wellhead_string') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Test Date</label>
                                    <input type="date" name="test_date" class="form-control @error('test_date') is-invalid @enderror" value="{{ old('test_date') }}" required>
                                    @error('test_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Test Type</label>
                                    <select name="test_type" class="form-control">
                                        <option value="flow" {{ old('test_type') == 'flow' ? 'selected' : '' }}>Flow Test</option>
                                        <option value="cleanup" {{ old('test_type') == 'cleanup' ? 'selected' : '' }}>Clean Up</option>
                                        <option value="build-up" {{ old('test_type') == 'build-up' ? 'selected' : '' }}>Build Up</option>
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
                                    <input type="file" name="logfile" class="form-control @error('logfile') is-invalid @enderror" accept=".vxlog,.txt" required>
                                    @error('logfile')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
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
