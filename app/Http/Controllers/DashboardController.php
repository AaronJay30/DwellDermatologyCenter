<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Service;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Redirect users to their appropriate dashboards
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isDoctor()) {
            return redirect()->route('doctor.dashboard');
        } else {
            // For patients, show the main dashboard
            $branches = Branch::all();
            $categories = Category::with('branch')->get();
            $services = Service::with(['images', 'category', 'promoServices.promotion'])
                ->where('is_active', true)
                ->paginate(5)
                ->withQueryString();
            
            // Get active promotions with images and services
            // Try less strict query first to catch all active promotions
            $activePromos = \App\Models\Promotion::with(['images', 'promoServices.service'])
                ->where('status', 'active')
                ->where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('ends_at')
                      ->orWhere('ends_at', '>=', now());
                })
                ->latest()
                ->get();
            
            // If no promotions found, try even less strict (for debugging)
            if ($activePromos->count() === 0) {
                $activePromos = \App\Models\Promotion::with(['images', 'promoServices.service'])
                    ->where('status', 'active')
                    ->where('is_active', true)
                    ->latest()
                    ->get();
            }
            
            // Ensure it's always a collection
            if (!$activePromos) {
                $activePromos = collect();
            }
            
            return view('dashboard', compact('branches', 'categories', 'services', 'activePromos'));
        }
    }

    public function about()
    {
        return view('about');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ];

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $userData['profile_photo'] = $path;
        }

        $user->update($userData);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    /**
     * Get categories for a specific branch
     */
    public function getCategoriesByBranch($branchId)
    {
        $categories = Category::when($branchId && $branchId != '0', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with('branch')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Get services for a specific category
     */
    public function getServicesByCategory($categoryId)
    {
        $services = Service::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with(['category.branch', 'images', 'promoServices.promotion'])
            ->orderBy('name')
            ->get()
            ->each(function ($service) {
                $service->append('pricing');
            });

        return response()->json([
            'success' => true,
            'services' => $services
        ]);
    }

    /**
     * Get all services for a specific branch
     */
    public function getServicesByBranch($branchId)
    {
        $query = Service::where('is_active', true)
            ->with(['category.branch', 'images', 'promoServices.promotion']);
            
        // If branchId is not 0, filter by branch
        if ($branchId != 0) {
            $query->whereHas('category', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }
        
        $services = $query->orderBy('name')->get()->each(function ($service) {
            $service->append('pricing');
        });

        return response()->json([
            'success' => true,
            'services' => $services
        ]);
    }
}
