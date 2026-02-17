<?php

namespace App\Http\Controllers;

use App\Models\Branch;
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
        $branches = Branch::all();

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
            'branches' => $branches,
        ]);
    }

    public function show(Service $service)
    {
        $service->load([
            'category.branch',
            'category.services.images',
            'category.services.promoServices.promotion',
            'images',
            'promoServices.promotion',
        ]);
        $branches = Branch::all();
        return view('services.show', compact('service', 'branches'));
    }
}
