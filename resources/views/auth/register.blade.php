@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush

@section('content')
<div class="register-page">
    <div class="register-header">
        <img src="{{ asset('images/dwell-logo.png') }}" alt="Dwell Logo">
    </div>

    <div class="register-container">
        <div class="card">
            <h2>Create Account</h2>
            <p class="subtitle">Start creating your account</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group input-icon">
                    <i class="fa fa-user"></i>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Full Name">
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group input-icon">
                    <i class="fa fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Email Address">
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group input-icon">
                    <i class="fa fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Password">
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group input-icon">
                    <i class="fa fa-lock"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Confirm Password">
                </div>

                <button type="submit" class="btn btn-primary">Sign Up</button>
            </form>

            <div class="social-login">
                <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="btn btn-google">
                    <img src="{{ asset('images/google.png') }}" alt="Google" style="width:20px; height:20px; margin-right:8px;">Sign up with Google
                </a>
                <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}" class="btn btn-facebook">
                    <img src="{{ asset('images/facebook.png') }}" alt="Facebook" style="width:20px; height:20px; margin-right:8px;">Sign up with Facebook
                </a>
            </div>

            <div class="register-link">
                <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
