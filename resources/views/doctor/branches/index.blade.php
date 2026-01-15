@extends('layouts.dashboard')
@section('page-title', 'Branches')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <div class="branches-header">
        <div class="branches-header-left">
            <span class="management-label">ADMIN BRANCH MANAGEMENT</span>
        </div>
        <div class="branches-header-right">
            <div class="search-wrapper">
                <i data-feather="search" class="search-icon"></i>
                <input type="text" id="branchSearch" class="branch-search-input" placeholder="Search branches...">
            </div>
            <a href="{{ route('doctor.branches.create') }}" class="add-branch-btn">
                <span class="add-icon">+</span>
                <span>Add Branch</span>
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="table-wrapper">
            <table id="branchesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $branch)
                        @php
                            $initials = mb_substr($branch->name, 0, 1);
                            if ($initials === '') { $initials = 'B'; }
                            $adminUser = $branch->users()->where('role', 'admin')->first();
                            $adminPhoto = null;
                            if ($adminUser && $adminUser->profile_photo) {
                                $adminPhoto = asset('storage/' . $adminUser->profile_photo) . '?t=' . time();
                            }
                        @endphp
                        <tr class="branch-row" data-name="{{ strtolower($branch->name) }}" data-address="{{ strtolower($branch->address) }}" data-phone="{{ strtolower($branch->phone) }}" data-email="{{ strtolower($branch->email) }}">
                            <td data-label="Branch">
                                <div class="profile-icon">
                                    @if($adminPhoto)
                                        <img src="{{ $adminPhoto }}" alt="{{ $branch->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span style="display: none;">{{ $initials }}</span>
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>
                                <span class="primary-column-text">{{ $branch->name }}</span>
                            </td>
                            <td data-label="Address">{{ $branch->address }}</td>
                            <td data-label="Phone">{{ $branch->phone }}</td>
                            <td data-label="Email">{{ $branch->email }}</td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="{{ route('doctor.branches.edit', $branch) }}" class="action-btn" title="Edit">
                                        <i data-feather="edit"></i>
                                    </a>
                                    <form action="{{ route('doctor.branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this branch?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn" title="Delete">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">{{ $branches->links() }}</div>
    </div>
</div>

<style>
    .branches-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 1rem;
        width: 100%;
        box-sizing: border-box;
    }

    .branches-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
        min-width: 0;
    }

    .branches-header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 0;
    }

    .management-label {
        font-family: 'Figtree', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        letter-spacing: 0.5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        flex: 0 1 auto;
        min-width: 0;
        max-width: 100%;
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

    .branch-search-input {
        width: 300px;
        max-width: 100%;
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
        box-sizing: border-box;
    }

    .branch-search-input:focus {
        outline: none;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    }

    .branch-search-input::placeholder {
        color: #9ca3af;
        font-style: italic;
    }

    .add-branch-btn {
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
        flex-shrink: 0;
        box-sizing: border-box;
    }

    .add-branch-btn:hover {
        background: #1a6b7a;
    }

    .add-icon {
        font-size: 1.2rem;
        font-weight: 300;
        color: #ffffff;
        line-height: 1;
    }

    /* Responsive Design - Tablets */
    @media (max-width: 992px) {
        .container {
            padding: 0 1rem;
        }

        .branches-header {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 0.75rem;
        }

        .branches-header {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 1.5rem;
        }

        .branches-header-left {
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
        }

        .management-label {
            font-size: 0.9rem;
        }

        .branches-header-right {
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
        }

        .search-wrapper {
            width: 100%;
        }

        .branch-search-input {
            width: 100%;
            min-height: 44px;
        }

        .add-branch-btn {
            width: 100%;
            min-height: 44px;
        }

        /* Make table responsive - card layout */
        .table-wrapper {
            overflow-x: visible;
            width: 100%;
            padding: 0;
        }

        table {
            min-width: 100%;
            width: 100%;
            display: block;
            border-spacing: 0 12px;
        }

        table thead {
            display: none;
        }

        table tbody {
            display: block;
            width: 100%;
        }

        table tbody tr {
            display: block;
            width: 100%;
            margin-bottom: 16px;
            background: rgba(255, 250, 240, 0.9);
            border-radius: 16px;
            box-shadow:
                0 4px 16px rgba(0, 0, 0, 0.08),
                0 2px 8px rgba(255, 215, 0, 0.2),
                0 1px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            overflow: hidden;
            position: relative;
        }

        table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow:
                0 8px 24px rgba(0, 0, 0, 0.12),
                0 4px 12px rgba(255, 215, 0, 0.3),
                0 2px 8px rgba(0, 0, 0, 0.15);
        }

        table tbody tr:last-child {
            margin-bottom: 0;
        }

        /* Card Header - Branch Name and Profile */
        table tbody td:first-child {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 16px 12px 16px;
            background: linear-gradient(135deg, rgba(25, 122, 140, 0.05), rgba(255, 215, 0, 0.05));
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            margin-bottom: 8px;
        }

        table tbody td:first-child .profile-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #197a8c, #1a6b7a);
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(25, 122, 140, 0.3);
        }

        table tbody td:first-child .profile-icon img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        table tbody td:first-child .primary-column-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            flex: 1;
        }

        /* Card Content - Details */
        table tbody td:nth-child(2),
        table tbody td:nth-child(3),
        table tbody td:nth-child(4) {
            display: flex;
            flex-direction: column;
            padding: 8px 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        }

        table tbody td:nth-child(2):last-child,
        table tbody td:nth-child(3):last-child,
        table tbody td:nth-child(4):last-child {
            border-bottom: none;
        }

        /* Card Actions */
        table tbody td:last-child {
            display: flex;
            padding: 12px 16px 16px 16px;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            margin-top: 8px;
        }

        table tbody td:last-child .action-buttons {
            display: flex;
            gap: 8px;
            width: 100%;
        }

        table tbody td:last-child .action-buttons .action-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            min-height: 44px;
        }

        table tbody td:last-child .action-buttons .action-btn:first-child {
            background: #197a8c;
            color: white;
        }

        table tbody td:last-child .action-buttons .action-btn:first-child:hover {
            background: #1a6b7a;
            transform: translateY(-1px);
        }

        table tbody td:last-child .action-buttons .action-btn:last-child {
            background: #dc3545;
            color: white;
        }

        table tbody td:last-child .action-buttons .action-btn:last-child:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        /* Remove default table cell styles */
        table tbody td {
            border: none !important;
            border-radius: 0 !important;
            text-align: left !important;
            vertical-align: top !important;
            min-height: auto !important;
            position: relative !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Hide labels since we're using structured layout */
        table tbody td::before {
            display: none !important;
        }

        /* Empty state styling */
        table tbody td[colspan] {
            display: block;
            text-align: center;
            padding: 3rem 1rem !important;
            background: rgba(255, 250, 240, 0.9);
            border-radius: 16px;
            color: #6c757d;
            font-style: italic;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding: 0 0.5rem;
        }

        .branches-header {
            margin-bottom: 1rem;
        }

        .management-label {
            font-size: 0.85rem;
        }

        .branches-header-left,
        .branches-header-right {
            gap: 0.5rem;
        }

        table tbody tr {
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }

        table tbody td {
            padding: 0.5rem 0;
            font-size: 0.875rem;
        }

        table tbody td::before {
            font-size: 0.7rem;
            margin-bottom: 0.35rem;
        }

        table tbody td:last-child button,
        table tbody td:last-child a.btn {
            padding: 0.5rem;
            font-size: 0.8rem;
            min-height: 40px;
        }
    }

    @media (max-width: 400px) {
        table tbody td {
            font-size: 0.8rem;
        }

        table tbody td:last-child button,
        table tbody td:last-child a.btn {
            padding: 0.4rem;
            font-size: 0.75rem;
            min-height: 36px;
        }
    }
</style>

<script>
    // Live search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('branchSearch');
        const tableRows = document.querySelectorAll('.branch-row');

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                tableRows.forEach(function(row) {
                    const name = row.getAttribute('data-name') || '';
                    const address = row.getAttribute('data-address') || '';
                    const phone = row.getAttribute('data-phone') || '';
                    const email = row.getAttribute('data-email') || '';
                    
                    if (name.includes(searchTerm) || 
                        address.includes(searchTerm) || 
                        phone.includes(searchTerm) || 
                        email.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
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

