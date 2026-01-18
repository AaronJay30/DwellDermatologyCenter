<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\PromotionImage;
use App\Models\PromoService;
use App\Models\Service;
use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PromoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    public function index()
    {
        // Auto-expire promotions
        Promotion::whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired', 'is_active' => false]);

        $promotions = Promotion::with(['images', 'promoServices.service'])
            ->latest()
            ->paginate(5);

        return view('doctor.promos.index', compact('promotions'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('doctor.promos.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'promo_code' => 'nullable|string|max:50|unique:promotions,promo_code',
            'max_claims_per_patient' => 'nullable|integer|min:1',
            'images' => 'nullable|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'services' => 'nullable|array',
            'services.*.service_id' => 'nullable|exists:services,id',
            'services.*.promo_price' => 'nullable|numeric|min:0',
            'services.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ], [
            'images.min' => 'Please upload at least one promo image.',
        ]);

        // Filter out unselected services (those without service_id)
        $selectedServices = collect($validated['services'] ?? [])->filter(function ($serviceData) {
            return !empty($serviceData['service_id']);
        })->values()->all();

        // Ensure at least one service is selected
        if (count($selectedServices) === 0) {
            return back()->withErrors([
                'services' => 'Please select at least one service for this promotion.',
            ])->withInput();
        }

        // Ensure either promo_price or discount_percent is provided for each selected service
        foreach ($selectedServices as $index => $serviceData) {
            if (empty($serviceData['promo_price']) && empty($serviceData['discount_percent'])) {
                return back()->withErrors([
                    "services.{$index}.promo_price" => 'Either promo price or discount percent must be provided.'
                ])->withInput();
            }
        }

        // Replace validated services with filtered selected services
        $validated['services'] = $selectedServices;

        DB::beginTransaction();
        try {
            // Generate promo code if not provided
            if (empty($validated['promo_code'])) {
                $validated['promo_code'] = $this->generatePromoCode($validated['title']);
            }

            $promotion = Promotion::create([
                'title' => $validated['title'],
                'name' => $validated['title'], // Keep for backward compatibility
                'description' => $validated['description'],
                'starts_at' => Carbon::parse($validated['starts_at']),
                'ends_at' => Carbon::parse($validated['ends_at']),
                'promo_code' => $validated['promo_code'],
                'max_claims_per_patient' => $validated['max_claims_per_patient'] ?? null,
                'status' => 'active',
                'is_active' => true,
            ]);

            // Upload images
            if ($request->hasFile('images')) {
                $order = 0;
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('promotions', 'public');
                    PromotionImage::create([
                        'promotion_id' => $promotion->id,
                        'image_path' => $path,
                        'display_order' => $order++,
                    ]);
                }
            }

            // Create promo services with pricing
            foreach ($validated['services'] as $serviceData) {
                $service = Service::findOrFail($serviceData['service_id']);
                $originalPrice = $service->price;
                
                $promoPrice = null;
                $discountPercent = null;

                if (!empty($serviceData['promo_price'])) {
                    $promoPrice = $serviceData['promo_price'];
                    $discountPercent = (($originalPrice - $promoPrice) / $originalPrice) * 100;
                } elseif (!empty($serviceData['discount_percent'])) {
                    $discountPercent = $serviceData['discount_percent'];
                    $promoPrice = $originalPrice * (1 - ($discountPercent / 100));
                }

                PromoService::create([
                    'promotion_id' => $promotion->id,
                    'service_id' => $service->id,
                    'original_price' => $originalPrice,
                    'promo_price' => $promoPrice,
                    'discount_percent' => $discountPercent,
                ]);
            }

            // Send notifications to all patients
            $patients = User::where('role', 'patient')->get();
            foreach ($patients as $patient) {
                NotificationService::sendPromotion(
                    'New Promotion Available!',
                    "A new promo is now available! {$promotion->title}. Check the latest deals on the dashboard.",
                    $patient->id
                );
            }

            DB::commit();

            return redirect()->route('doctor.promos.index')
                ->with('success', 'Promotion created successfully! All patients have been notified.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create promotion: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Promotion $promo)
    {
        $promo->load(['images', 'promoServices.service']);
        $services = Service::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('doctor.promos.edit', compact('promo', 'services'));
    }

    public function update(Request $request, Promotion $promo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'promo_code' => 'nullable|string|max:50|unique:promotions,promo_code,' . $promo->id,
            'max_claims_per_patient' => 'nullable|integer|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'exists:promotion_images,id',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.promo_price' => 'nullable|numeric|min:0',
            'services.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $promo->update([
                'title' => $validated['title'],
                'name' => $validated['title'],
                'description' => $validated['description'],
                'starts_at' => Carbon::parse($validated['starts_at']),
                'ends_at' => Carbon::parse($validated['ends_at']),
                'promo_code' => $validated['promo_code'] ?? $promo->promo_code,
                'max_claims_per_patient' => $validated['max_claims_per_patient'] ?? $promo->max_claims_per_patient,
            ]);

            // Remove selected images
            if (!empty($validated['remove_image_ids'])) {
                $images = PromotionImage::whereIn('id', $validated['remove_image_ids'])
                    ->where('promotion_id', $promo->id)
                    ->get();
                foreach ($images as $img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }

            // Add new images
            if ($request->hasFile('images')) {
                $maxOrder = (int) $promo->images()->max('display_order') ?? -1;
                $order = $maxOrder + 1;
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('promotions', 'public');
                    PromotionImage::create([
                        'promotion_id' => $promo->id,
                        'image_path' => $path,
                        'display_order' => $order++,
                    ]);
                }
            }

            // Update services - remove old ones and add new
            $promo->promoServices()->delete();
            foreach ($validated['services'] as $serviceData) {
                $service = Service::findOrFail($serviceData['service_id']);
                $originalPrice = $service->price;
                
                $promoPrice = null;
                $discountPercent = null;

                if (!empty($serviceData['promo_price'])) {
                    $promoPrice = $serviceData['promo_price'];
                    $discountPercent = (($originalPrice - $promoPrice) / $originalPrice) * 100;
                } elseif (!empty($serviceData['discount_percent'])) {
                    $discountPercent = $serviceData['discount_percent'];
                    $promoPrice = $originalPrice * (1 - ($discountPercent / 100));
                }

                PromoService::create([
                    'promotion_id' => $promo->id,
                    'service_id' => $service->id,
                    'original_price' => $originalPrice,
                    'promo_price' => $promoPrice,
                    'discount_percent' => $discountPercent,
                ]);
            }

            DB::commit();

            return redirect()->route('doctor.promos.index')
                ->with('success', 'Promotion updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update promotion: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Promotion $promo)
    {
        DB::transaction(function () use ($promo) {
            // Delete images
            foreach ($promo->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }
            
            // Delete promo services
            $promo->promoServices()->delete();
            
            // Delete promotion
            $promo->delete();
        });

        return redirect()->route('doctor.promos.index')
            ->with('success', 'Promotion deleted successfully!');
    }

    private function generatePromoCode($title)
    {
        // Generate code from title (e.g., "Summer Sale" -> "SUMMER50")
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($title, 0, 6)));
        $code .= rand(10, 99);
        
        // Ensure uniqueness
        while (Promotion::where('promo_code', $code)->exists()) {
            $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($title, 0, 6)));
            $code .= rand(10, 99);
        }
        
        return $code;
    }
}
