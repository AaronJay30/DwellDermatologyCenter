<?php

namespace App\Http\Controllers; 

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function index()
    {
        $categories = Category::withCount('services')->get();

        $categoryId = request('category_id');

        $servicesQuery = Service::with(['category', 'images', 'promoServices.promotion']);
        if ($categoryId) {
            $servicesQuery->where('category_id', $categoryId);
        }

        $services = $servicesQuery->get();

        return view('services.index', [
            'categories' => $categories,
            'services' => $services,
            'selectedCategoryId' => $categoryId,
        ]);
    }

    public function show(Service $service)
    {
        $service->load([
            'category.services.images',
            'category.services.promoServices.promotion',
            'images',
            'promoServices.promotion',
        ]);
        return view('services.show', compact('service'));
    }
}
