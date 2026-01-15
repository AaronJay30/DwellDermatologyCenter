<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'services' => [],
                'categories' => []
            ]);
        }

        // Search services
        $services = Service::with(['category', 'images', 'promoServices.promotion'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($service) {
                $pricing = $service->pricing;
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'price' => $service->price,
                    'pricing' => $pricing,
                    'discount_percentage' => data_get($pricing, 'discount_percent'),
                    'category' => $service->category->name,
                    'image' => $service->images->first() ? asset('storage/' . $service->images->first()->image_path) : null,
                    'url' => route('services.show', $service)
                ];
            });

        // Search categories
        $categories = Category::where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'url' => route('services.index', ['category_id' => $category->id])
                ];
            });

        return response()->json([
            'services' => $services,
            'categories' => $categories
        ]);
    }
}
