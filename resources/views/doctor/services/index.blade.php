@extends('layouts.dashboard')
@section('page-title', 'Services')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <!-- First Line: SERVICE MANAGEMENT with Search and Add Button -->
    <div class="services-header">
        <div class="services-header-left">
            <span class="management-label">SERVICE MANAGEMENT</span>
        </div>
        <div class="services-header-right">
            <div class="search-wrapper">
                <i data-feather="search" class="search-icon"></i>
                <input type="text" id="serviceSearch" class="service-search-input" placeholder="Search services...">
            </div>
            <a href="{{ request('branch_id') ? route('doctor.services.create', ['branch_id' => request('branch_id')]) : route('doctor.services.create') }}" class="add-service-btn">
                <span class="add-icon">+</span>
                <span>Add Service</span>
            </a>
        </div>
    </div>

    <!-- Second Line: Filters -->
    <div class="services-filters">
        <form method="GET" action="{{ route('doctor.services') }}" id="filterForm" class="filter-form">
            <div class="filter-group">
                <label for="branch_id" class="filter-label">Clinic / Branch</label>
                <select id="branch_id" name="branch_id" class="filter-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (string)request('branch_id') === (string)$branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="category_id" class="filter-label">Category</label>
                <select id="category_id" name="category_id" class="filter-select">
                    <option value="">All Categories</option>
                    @isset($categories)
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string)request('category_id') === (string)$category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    @endisset
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
                Services — {{ $selectedBranch?->name ?? 'All Branches' }}
            </h2>
            @if($selectedBranch || request('category_id'))
                <a href="{{ route('doctor.services') }}" class="btn btn-accent">Reset</a>
            @endif
        </div>
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem;" id="servicesGrid">
            @forelse($services as $service)
                @php($firstImage = $service->images->first())
                <div class="service-card" 
                     data-name="{{ strtolower($service->name) }}" 
                     data-description="{{ strtolower($service->description ?? '') }}"
                     data-category="{{ strtolower($service->category->name ?? '') }}"
                     data-branch="{{ strtolower($service->category->branch->name ?? '') }}">
                    <div style="position:relative; background:#f8f9fa; display:flex; align-items:center; justify-content:center; height:160px; overflow:hidden;">
                        @if($firstImage)
                            <img src="{{ asset('storage/'.$firstImage->image_path) }}" alt="{{ $service->name }}" style="max-height: 100%; max-width: 100%; object-fit: cover; width:100%; height:100%;" onerror="this.onerror=null; this.parentElement.innerHTML='<div style=\'font-size: 3rem; color:#adb5bd;\'>💆</div>';">
                        @else
                            <div style="font-size: 3rem; color:#adb5bd;">💆</div>
                        @endif
                    </div>
                    <div style="padding: 1rem; display:flex; flex-direction:column; gap:.5rem;">
                        <div style="display:flex; justify-content:space-between; align-items:start; gap:.5rem;">
                            <div style="font-weight:600; font-size:1rem;">{{ $service->name }}</div>
                            <span style="background: var(--primary-color); color:#fff; padding:.15rem .5rem; border-radius:6px; font-size:.75rem; white-space:nowrap;">{{ $service->category->name ?? 'Uncategorized' }}</span>
                        </div>
                        <div style="color:#6c757d; font-size:.9rem; min-height: 2.6em;">{{ Str::limit($service->description, 90) ?? '—' }}</div>
                        @if($service->category && $service->category->branch)
                            <div style="font-size:.8rem; color:#495057;">Branch: <strong>{{ $service->category->branch->name }}</strong></div>
                        @endif
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:.25rem;">
                            <div style="font-weight:600; color: var(--primary-color);">₱ {{ number_format($service->price, 2) }}</div>
                            <div style="display:flex; gap:.5rem;">
                                <a href="{{ route('doctor.services.edit', $service) }}" class="btn btn-accent" style="padding:.35rem .6rem;">Edit</a>
                                <form method="POST" action="{{ route('doctor.services.destroy', $service) }}" onsubmit="return confirm('Delete this service?');" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit" style="padding:.35rem .6rem; background-color:#dc3545; border-color:#dc3545; color:#fff;">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="color:#6c757d;">No services found.</div>
            @endforelse
        </div>
        <div class="pagination-wrapper">{{ $services->links() }}</div>
    </div>
</div>

<style>
    
    .services-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .services-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .services-header-right {
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

    .service-search-input {
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

    .service-search-input:focus {
        outline: none;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    }

    .service-search-input::placeholder {
        color: #9ca3af;
        font-style: italic;
    }

    .add-service-btn {
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

    .add-service-btn:hover {
        background: #1a6b7a;
    }

    .add-icon {
        font-size: 1.2rem;
        font-weight: 300;
        color: #ffffff;
        line-height: 1;
    }

    /* Filter Row Styling */
    .services-filters {
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

    .service-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15), 0 2px 4px rgba(0,0,0,0.1);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .service-card.hidden {
        display: none;
    }

    @media (max-width: 768px) {
        .services-header {
            flex-direction: column;
            align-items: stretch;
        }

        .services-header-left {
            flex-direction: column;
            gap: 0.75rem;
        }

        .services-header-right {
            flex-direction: column;
            gap: 0.75rem;
        }

        .search-wrapper {
            width: 100%;
        }

        .service-search-input {
            width: 100%;
        }

        .add-service-btn {
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
        const searchInput = document.getElementById('serviceSearch');
        const serviceCards = document.querySelectorAll('.service-card');

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                serviceCards.forEach(function(card) {
                    const name = card.getAttribute('data-name') || '';
                    const description = card.getAttribute('data-description') || '';
                    const category = card.getAttribute('data-category') || '';
                    const branch = card.getAttribute('data-branch') || '';
                    
                    if (name.includes(searchTerm) || 
                        description.includes(searchTerm) || 
                        category.includes(searchTerm) || 
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
        const categorySelect = document.getElementById('category_id');

        // Live filtering on change
        if (branchSelect) {
            branchSelect.addEventListener('change', function() {
                filterForm.submit();
            });
        }

        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
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
</script>
@endsection

