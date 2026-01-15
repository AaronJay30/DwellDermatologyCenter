@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="login-page">
    <div class="login-header">
        <img src="{{ asset('images/dwell-logo.png') }}" alt="Dwell Logo">
    </div>

    <div class="login-container">
        <div class="card">
            <h2>Login</h2>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group input-icon">
                    <i class="fa fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="Email Address">
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
                   @if (Route::has('password.request'))
                    <div class="forgot-password">
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot Your Password?</a>
                    </div>
                @endif
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
<div class="social-login">
    <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="btn btn-google">
       <img src="{{ asset('images/google.png') }}" alt="Google" style="width:20px; height:20px; margin-right:8px;">Login with Google
    </a>
    <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}" class="btn btn-facebook">
         <img src="{{ asset('images/facebook.png') }}" alt="Facebook" style="width:20px; height:20px; margin-right:8px;">Login with Facebook
    </a>
</div>

            <div class="register-link">
                <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
            </div>
            
        </div>
    </div>
</div>
@endsection
