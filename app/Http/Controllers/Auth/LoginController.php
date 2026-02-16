<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Redirect to dashboard if user is already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        // Clear any old session data when showing login form
        // This ensures a clean state when accessing login page
        $request->session()->forget('_old_input');
        $request->session()->forget('url.intended');
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // If user is already logged in with a different account, logout and clear session first
        if (Auth::check()) {
            $currentUser = Auth::user();
            // If logging in with a different email, logout current user first
            if ($currentUser->email !== $request->email) {
                Auth::logout();
                $request->session()->flush();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Regenerate session ID for security (creates new session ID, keeps auth data)
            $request->session()->regenerate();
            
            // Clear any intended URL to prevent redirect issues
            $request->session()->forget('url.intended');
            
            // Clear any old flash messages that might persist
            $request->session()->forget('_old_input');
            
            return redirect()->route('dashboard');
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    public function logout(Request $request)
    {
        // Logout the user
        Auth::logout();
        
        // Invalidate the session (this clears all session data)
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        // Redirect to login page
        return redirect()->route('login')->with('status', 'You have been logged out successfully.');
    }
}
