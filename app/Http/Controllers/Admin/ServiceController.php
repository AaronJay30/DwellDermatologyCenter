<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    protected function getAdminBranch()
    {
        $branch = Auth::user()->branch;

        if (!$branch) {
            abort(403, 'You are not assigned to a branch.');
        }

        return $branch;
    }

    protected function ensureCategoryBelongsToBranch(int $categoryId, int $branchId): void
    {
        $belongs = Category::where('id', $categoryId)
            ->where('branch_id', $branchId)
            ->exists();

        if (!$belongs) {
            abort(403, 'You are not allowed to use this category.');
        }
    }

    public function index(Request $request)
    {
        $branch = $this->getAdminBranch();
        $selectedCategoryId = $request->query('category_id');
        $search = trim((string) $request->query('q', ''));

        $categories = Category::where('branch_id', $branch->id)
            ->orderBy('name')
            ->get();

        if ($selectedCategoryId) {
            $selectedCategory = $categories->firstWhere('id', (int) $selectedCategoryId);
            if (!$selectedCategory) {
                abort(403, 'Invalid category for this branch.');
            }
        }

        $servicesQuery = Service::with(['category.branch', 'images'])
            ->whereHas('category', function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            });

        if ($selectedCategoryId) {
            $servicesQuery->where('category_id', $selectedCategoryId);
        }

        if ($search !== '') {
            $servicesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $servicesQuery
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.services.index', [
            'branch' => $branch,
            'categories' => $categories,
            'services' => $services,
            'selectedCategoryId' => $selectedCategoryId ? (int) $selectedCategoryId : null,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $branch = $this->getAdminBranch();

        $categories = Category::where('branch_id', $branch->id)
            ->orderBy('name')
            ->get();

        return view('admin.services.create', [
            'branch' => $branch,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $branch = $this->getAdminBranch();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $this->ensureCategoryBelongsToBranch((int) $validated['category_id'], $branch->id);

        $isActive = $request->boolean('is_active', true);

        DB::transaction(function () use ($validated, $request, $isActive) {
            $service = Service::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_active' => $isActive,
            ]);

            if ($request->hasFile('images')) {
                $order = 0;
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('services', 'public');
                    ServiceImage::create([
                        'service_id' => $service->id,
                        'image_path' => $path,
                        'display_order' => $order++,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.services')
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $branch = $this->getAdminBranch();

        if (!$service->category || (int) $service->category->branch_id !== (int) $branch->id) {
            abort(403);
        }

        $service->load(['images', 'category.branch']);

        $categories = Category::where('branch_id', $branch->id)
            ->orderBy('name')
            ->get();

        return view('admin.services.edit', [
            'branch' => $branch,
            'service' => $service,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $branch = $this->getAdminBranch();

        if (!$service->category || (int) $service->category->branch_id !== (int) $branch->id) {
            abort(403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'integer|exists:service_images,id',
        ]);

        $this->ensureCategoryBelongsToBranch((int) $validated['category_id'], $branch->id);

        $isActive = $request->boolean('is_active', true);

        DB::transaction(function () use ($validated, $request, $service, $isActive) {
            $service->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_active' => $isActive,
            ]);

            if (!empty($validated['remove_image_ids'])) {
                $images = ServiceImage::whereIn('id', $validated['remove_image_ids'])
                    ->where('service_id', $service->id)
                    ->get();

                foreach ($images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                $maxOrder = (int) $service->images()->max('display_order');
                $order = $maxOrder + 1;

                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('services', 'public');
                    ServiceImage::create([
                        'service_id' => $service->id,
                        'image_path' => $path,
                        'display_order' => $order++,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.services')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $branch = $this->getAdminBranch();

        if (!$service->category || (int) $service->category->branch_id !== (int) $branch->id) {
            abort(403);
        }

        DB::transaction(function () use ($service) {
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            $service->delete();
        });

        return redirect()
            ->route('admin.services')
            ->with('success', 'Service deleted successfully.');
    }
}

