@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 col-lg-4 mx-auto">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="text-center mb-5">
                        <a href="{{ route('login') }}">
                            <img src="https://all-pribors.ru/pics/large/60560-15-0000.jpg" class="header-brand-img desktop-logo" alt="logo">
                        </a>
                        <div class="mt-4">
                            <h4 class="mb-1">Welcome Back!</h4>
                            <p class="text-muted">Sign in to continue to MPFM System</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span class="custom-control-label">Remember me</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a href="{{ route('password.request') }}" class="text-primary">Forgot Password?</a>
                            </div>
                        @endif
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-primary ml-1">Sign Up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
