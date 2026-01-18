<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
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

    public function index(Request $request)
    {
        $branch = $this->getAdminBranch();
        $search = trim((string) $request->query('q', ''));

        $categoriesQuery = Category::with(['branch'])
            ->withCount('services')
            ->where('branch_id', $branch->id);

        if ($search !== '') {
            $categoriesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $categoriesQuery
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.categories.index', [
            'branch' => $branch,
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $branch = $this->getAdminBranch();

        return view('admin.categories.create', [
            'branch' => $branch,
        ]);
    }

    public function edit(Category $category)
    {
        $branch = $this->getAdminBranch();

        if ((int) $category->branch_id !== (int) $branch->id) {
            abort(403);
        }

        return view('admin.categories.edit', [
            'branch' => $branch,
            'category' => $category,
        ]);
    }

    public function store(Request $request)
    {
        $branch = $this->getAdminBranch();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'branch_id' => $branch->id,
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()
            ->route('admin.categories')
            ->with('success', 'Category created successfully for your branch.');
    }

    public function update(Request $request, Category $category)
    {
        $branch = $this->getAdminBranch();

        if ((int) $category->branch_id !== (int) $branch->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }

            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $branch = $this->getAdminBranch();

        if ((int) $category->branch_id !== (int) $branch->id) {
            abort(403);
        }

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories')
            ->with('success', 'Category deleted successfully.');
    }
}

