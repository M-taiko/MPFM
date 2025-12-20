@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 col-lg-6 mx-auto">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="text-center mb-5">
                        <a href="{{ route('register') }}">
                            <img src="{{ asset('assets/img/brand/logo.png') }}" class="header-brand-img desktop-logo" alt="logo">
                        </a>
                        <div class="mt-4">
                            <h4 class="mb-1">Create Account</h4>
                            <p class="text-muted">Sign up to create an account</p>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter your name">

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter your password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">Already have an account? <a href="{{ route('login') }}" class="text-primary ml-1">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
