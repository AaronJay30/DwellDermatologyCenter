<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // Redirect to dashboard if user is already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'patient', // All registrations are patients
        ]);

        // Send welcome notification + email to new account
        NotificationService::sendNotification(
            'Welcome to Dwell Dermatology Center',
            'Your patient account has been created successfully. You can now log in and book consultations or services.',
            'system',
            $user->id
        );

        return redirect()->route('login')->with('success', 'Registration successful! Please login to continue.');
    }
}
