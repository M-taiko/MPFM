@extends('layouts.master')

@section('title', 'Edit Test Vessel Test')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Test Vessel Test</h3>
                <div class="card-options">
                    <a href="{{ route('tv_tests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tv_tests.update', $tvTest) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('tv_tests._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
