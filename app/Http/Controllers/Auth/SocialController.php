<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
     public function redirectToSocial($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404); 
        }

        // For Facebook, add required scopes to get email and public profile
        if ($provider === 'facebook') {
            return Socialite::driver($provider)
                ->scopes(['email', 'public_profile'])
                ->fields(['name', 'email', 'id']) // Explicitly request these fields
                ->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleSocialCallback($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            
            // Ensure email is available (required for account creation)
            if (empty($socialUser->email)) {
                return redirect()->route('register')->with('error', 'Unable to retrieve email from ' . ucfirst($provider) . '. Please ensure your account has an email address.');
            }
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect()->route('login')->with('error', 'Session expired. Please try again.');
        } catch (\Exception $e) {
            \Log::error('Social login error (' . $provider . '): ' . $e->getMessage());
            $errorMessage = $e->getMessage();
            
            // Check for Facebook app errors - "App not active" is a Facebook Developer Console issue
            if (str_contains(strtolower($errorMessage), 'app not active') || 
                str_contains(strtolower($errorMessage), 'app is not active') ||
                str_contains(strtolower($errorMessage), 'app developer is aware')) {
                return redirect()->route('login')->with('error', 
                    'Facebook app is not active. This is a Facebook Developer Console configuration issue, NOT a code error. ' .
                    'To fix: Go to https://developers.facebook.com/ → Your App → Roles → Test Users → Add yourself as a Test User, ' .
                    'OR switch your app to Live Mode in Settings → Basic → App Mode.'
                );
            }
            
            // Check for other common Facebook errors
            if (str_contains(strtolower($errorMessage), 'invalid oauth') || 
                str_contains(strtolower($errorMessage), 'redirect_uri')) {
                return redirect()->route('login')->with('error', 
                    'Facebook redirect URI mismatch. Check your .env FACEBOOK_REDIRECT_URL matches Facebook App Settings → Valid OAuth Redirect URIs.'
                );
            }
            
            return redirect()->route('login')->with('error', 
                ucfirst($provider) . ' authentication failed. Error: ' . $errorMessage . 
                ' Please check your Facebook app settings in Developer Console.'
            );
        }

        $user = User::where('email', $socialUser->email)->first();

        if ($user) {
            // CASE 1: same provider
            if ($user->provider === $provider) {
                Auth::login($user);
                session()->regenerate();
                return redirect()->route('dashboard');
            }

            // CASE 2: was manual (allow update)
            if ($user->provider === 'manual' || $user->provider === null) {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->id,
                ]);
                Auth::login($user);
                session()->regenerate();
                return redirect()->route('dashboard');
            }

            // CASE 3: already linked to another provider
            return redirect()->route('login')->with(
                'error',
                'This email is already linked to another account (' . ucfirst($user->provider ?? 'email') . '). Please login with that method instead.'
            );
        }

        // CASE 4: new user - create account
        $newUser = User::create([
            'name'    => $socialUser->name ?? $socialUser->nickname ?? 'User',
            'email'       => $socialUser->email,
            'password'    => bcrypt(str()->random(16)), // IMPORTANT!!!
            'provider'    => $provider,
            'provider_id' => $socialUser->id,
            'role'        => 'patient', // Default role for new registrations
        ]);

        Auth::login($newUser);
        session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }
}
