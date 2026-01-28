@extends('layouts.dashboard')
@section('page-title', 'Categories')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <!-- First Line: CATEGORY MANAGEMENT with Search and Add Button -->
    <div class="categories-header">
        <div class="categories-header-left">
            <span class="management-label">CATEGORY MANAGEMENT</span>
        </div>
        <div class="categories-header-right">
            <div class="search-wrapper">
                <i data-feather="search" class="search-icon"></i>
                <input type="text" id="categorySearch" class="category-search-input" placeholder="Search categories...">
            </div>
            <a href="{{ route('doctor.categories.create') }}" class="add-category-btn">
                <span class="add-icon">+</span>
                <span>Add Category</span>
            </a>
        </div>
    </div>

    <!-- Second Line: Filters -->
    <div class="categories-filters">
        <form method="GET" action="{{ route('doctor.categories') }}" id="filterForm" class="filter-form">
            <div class="filter-group">
                <label for="branch_id" class="filter-label">Clinic / Branch</label>
                <select id="branch_id" name="branch_id" class="filter-select">
                    <option value="all" {{ !request('branch_id') || request('branch_id') == 'all' ? 'selected' : '' }}>All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (string)request('branch_id') === (string)$branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
    
    <div class="card" style="padding: 1rem; border: 1px solid #eef1f4;">
        @php
            $selectedBranch = null;
            if (request('branch_id')) {
                $selectedBranch = $branches->firstWhere('id', (int)request('branch_id'));
            }
        @endphp
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem;">
            <h2 style="margin:0; color: var(--primary-color); font-size:1.15rem;">
                Categories — {{ $selectedBranch?->name ?? 'All Branches' }}
            </h2>
            @if($selectedBranch)
                <a href="{{ route('doctor.categories') }}" class="btn btn-accent">Reset</a>
            @endif
        </div>
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem;" id="categoriesGrid">
            @forelse($categories as $category)
                <div class="category-card" 
                     data-name="{{ strtolower($category->name) }}" 
                     data-description="{{ strtolower($category->description ?? '') }}"
                     data-branch="{{ strtolower($category->branch->name ?? '') }}"
                     style="background: #fff; border:1px solid #e9ecef; border-radius: 12px; overflow:hidden; display:flex; flex-direction:column; box-shadow: 0 4px 12px rgba(0,0,0,0.15), 0 2px 4px rgba(0,0,0,0.1); transition: opacity 0.3s ease, transform 0.3s ease;">
                    <div style="position:relative; background:#f8f9fa; display:flex; align-items:center; justify-content:center; height:140px;">
                        @if($category->image_path)
                            <img src="{{ asset('storage/'.$category->image_path) }}" alt="{{ $category->name }}" style="max-height: 100%; max-width: 100%; object-fit: cover;">
                        @else
                            <div style="font-size: 3rem; color:#adb5bd;">🗂️</div>
                        @endif
                    </div>
                    <div style="padding: 1rem; display:flex; flex-direction:column; gap:.5rem;">
                        <div style="font-weight:600; font-size:1rem;">{{ $category->name }}</div>
                        <div style="color:#6c757d; font-size:.9rem; min-height: 2.6em;">{{ Str::limit($category->description, 80) ?? '—' }}</div>
                        @if($category->branch)
                            <div style="font-size:.8rem; color:#495057;">Branch: <strong>{{ $category->branch->name }}</strong></div>
                        @endif
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:.25rem;">
                            <span style="font-size:.85rem; color:#6c757d;">{{ $category->services_count }} services</span>
                            <div style="display:flex; gap:.5rem;">
                                <a href="{{ route('doctor.categories.edit', $category) }}" class="btn btn-accent" style="padding:.35rem .6rem;">Edit</a>
                                <button class="btn btn-danger" style="padding:.35rem .6rem;" data-open-delete="{{ $category->id }}">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div id="deleteModal-{{ $category->id }}" class="modal delete-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); align-items:center; justify-content:center; z-index:1000;">
                    <div class="modal-content delete-modal-content">
                        <div class="modal-header">
                            <h3 style="margin:0; font-size:1.25rem; font-weight:600; color:#000000;">Delete Category</h3>
                        </div>
                        <div class="modal-body">
                            <p style="margin:0; color:#000000;">Are you sure you want to delete <strong style="color:#000000;">{{ $category->name }}</strong>? This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <form method="POST" action="{{ route('doctor.categories.destroy', $category) }}" style="display:flex; justify-content:flex-end; gap:.75rem; width:100%;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-cancel-red" data-close-delete="{{ $category->id }}">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="color:#6c757d;">No categories found.</div>
            @endforelse
        </div>

        <div class="pagination-wrapper">{{ $categories->links() }}</div>
    </div>
</div>

<style>
    .categories-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .categories-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .categories-header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .management-label {
        font-family: 'Figtree', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        width: 18px;
        height: 18px;
        color: #9ca3af;
        pointer-events: none;
        z-index: 1;
    }

    .category-search-input {
        width: 300px;
        height: 40px;
        padding: 0 12px 0 40px;
        border: 2px solid #FFD700;
        background: #ffffff;
        font-family: 'Figtree', sans-serif;
        font-size: 0.95rem;
        color: #2c3e50;
        box-shadow: 0 2px 4px rgba(255, 215, 0, 0.2);
        transition: box-shadow 0.3s ease;
        border-radius: 0;
    }

    .category-search-input:focus {
        outline: none;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    }

    .category-search-input::placeholder {
        color: #9ca3af;
        font-style: italic;
    }

    .add-category-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 40px;
        padding: 0 20px;
        background: #197a8c;
        color: #ffffff;
        text-decoration: none;
        font-family: 'Figtree', sans-serif;
        font-size: 0.95rem;
        font-weight: 500;
        border: none;
        border-radius: 0;
        cursor: pointer;
        transition: background-color 0.3s ease;
        white-space: nowrap;
    }

    .add-category-btn:hover {
        background: #1a6b7a;
    }

    .add-icon {
        font-size: 1.2rem;
        font-weight: 300;
        color: #ffffff;
        line-height: 1;
    }

    /* Filter Row Styling */
    .categories-filters {
        margin-bottom: 1.5rem;
        padding: 0.5rem;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid #e9ecef;
    }

    .filter-form {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: nowrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        flex: 0 0 auto;
        min-width: 180px;
    }

    .filter-label {
        font-family: 'Figtree', sans-serif;
        font-size: 0.7rem;
        color: #6c757d;
        font-weight: 500;
    }

    .filter-select,
    .filter-input {
        width: 100%;
        height: 28px;
        padding: 0 8px;
        border: 2px solid #e9ecef;
        background: #ffffff;
        font-family: 'Figtree', sans-serif;
        font-size: 0.8rem;
        color: #2c3e50;
        border-radius: 0;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .filter-select:focus,
    .filter-input:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 2px 4px rgba(255, 215, 0, 0.2);
    }

    .filter-select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239ca3af' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    .category-card.hidden {
        display: none;
    }

    .modal {
        color: #1f2937;
    }

    .modal .card {
        background: #ffffff;
        color: inherit;
        box-shadow: 0 12px 24px rgba(17, 24, 39, 0.18);
    }

    .modal .card p,
    .modal .card label {
        color: inherit;
    }

    /* Delete Modal Container Styling */
    .delete-modal-content {
        background-color: #ffffff;
        margin: auto;
        padding: 0;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .delete-modal .modal-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background-color: #ffffff;
    }

    .delete-modal .modal-body {
        padding: 1.5rem;
        color: #000000;
    }

    .delete-modal .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        background-color: #ffffff;
    }

    .btn-cancel-red {
        background-color: #dc3545 !important;
        color: #ffffff !important;
        border: none !important;
        padding: 0.75rem 1.5rem !important;
        border-radius: 4px !important;
        cursor: pointer !important;
        font-weight: 500 !important;
        font-size: 0.95rem !important;
        transition: background-color 0.3s ease !important;
    }

    .btn-cancel-red:hover {
        background-color: #c82333 !important;
        color: #ffffff !important;
    }

    @media (max-width: 768px) {
        .categories-header {
            flex-direction: column;
            align-items: stretch;
        }

        .categories-header-left {
            flex-direction: column;
            gap: 0.75rem;
        }

        .categories-header-right {
            flex-direction: column;
            gap: 0.75rem;
        }

        .search-wrapper {
            width: 100%;
        }

        .category-search-input {
            width: 100%;
        }

        .add-category-btn {
            width: 100%;
        }

        .filter-form {
            flex-direction: column;
        }

        .filter-group {
            min-width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Live search functionality (client-side filtering)
        const searchInput = document.getElementById('categorySearch');
        const categoryCards = document.querySelectorAll('.category-card');

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                categoryCards.forEach(function(card) {
                    const name = card.getAttribute('data-name') || '';
                    const description = card.getAttribute('data-description') || '';
                    const branch = card.getAttribute('data-branch') || '';
                    
                    if (name.includes(searchTerm) || 
                        description.includes(searchTerm) || 
                        branch.includes(searchTerm)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            });
        }

        // Live filter functionality (server-side filtering)
        const filterForm = document.getElementById('filterForm');
        const branchSelect = document.getElementById('branch_id');

        // Live filtering on change
        if (branchSelect) {
            branchSelect.addEventListener('change', function() {
                filterForm.submit();
            });
        }

        // Initialize Feather icons
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }

        // Re-initialize icons after search icon is added
        setTimeout(function() {
            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }
        }, 100);
    });

    // Modal open/close handlers
    document.addEventListener('click', function(e) {
        const openDeleteId = e.target.getAttribute('data-open-delete');
        if (openDeleteId) {
            const modal = document.getElementById('deleteModal-' + openDeleteId);
            if (modal) modal.style.display = 'flex';
        }
        const closeDeleteId = e.target.getAttribute('data-close-delete');
        if (closeDeleteId) {
            const modal = document.getElementById('deleteModal-' + closeDeleteId);
            if (modal) modal.style.display = 'none';
        }
    });
</script>
@endsection

