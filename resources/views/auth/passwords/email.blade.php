@extends('layouts.app')

@push('styles')
<style>
/* Background wrapper */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #fefcec;
}
.container {
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

/* Top section with logo */
.login-header {
    margin: 0;
    background: #197a8c;
    height: 500px;
    text-align: center;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.login-header img {
    margin-top: 6px;
    max-width: 200px;
    margin-bottom: 10rem;
}

/* Container */
.login-container {
    max-width: 980px;
    margin: -20rem auto 3rem auto; 
    padding: 1rem;
}

/* Card */
.login-container .card {
    background: #fff;
    padding: 5rem 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    text-align: center;
}

/* Heading */
.card h2 {
    color: #0f7c82;
    font-size: 1.5rem;
    font-weight: 750;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
}

/* Form groups */
.form-group {
    margin-bottom: 1.2rem;
    text-align: left;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #FFD700;
    border-radius: 6px;
    font-size: 1rem;
    transition: border 0.3s;
}

.form-control:focus {
    border-color: #FFD700;
    box-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
    outline: none;
}

/* Error messages */
.error-message {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

/* Button */
.button-container {
    display: flex;
    justify-content: center;
    margin-top: 1rem;
}

.btn-primary {
    width: 60%;
    padding: 0.9rem;
    background: #197a8c;
    color: white;
    border: none;
    border-radius: 25px; 
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
    text-transform: uppercase;
}

.btn-primary:hover {
    background: #0c6366;
}

/* Register / Back link area */
.register-link {
    text-align: center;
    margin-top: 15px;
    font-size: 0.9rem;
}

.register-link a {
    color: #197a8c;
    font-weight: 600;
    text-decoration: none;
}

.register-link a:hover {
    color: #dc3545;
    text-decoration: underline;
}
</style>
@endpush

@section('content')
<div class="login-page">
    <div class="login-header">
        <img src="{{ asset('images/dwell-logo.png') }}" alt="Dwell Logo">
    </div>

    <div class="login-container">
        <div class="card">
            <h2>Reset Password</h2>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" placeholder="Enter your email" class="form-control" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="button-container">
                    <button type="submit" class="btn-primary">Send Password Reset Link</button>
                </div>
            </form>

            <div class="register-link">
                <p><a href="{{ route('login') }}">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
