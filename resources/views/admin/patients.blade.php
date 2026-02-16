@extends('layouts.dashboard')
@section('page-title', 'Patient History')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
@endpush

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@section('content')
<div class="container">
    <div class="patients-header">
        <div class="patients-header-left">
            <span class="management-label">PATIENT HISTORY</span>
        </div>
        <div class="patients-header-right">
            <form method="GET" action="{{ route('admin.patients') }}" class="search-wrapper" id="searchForm">
                <i data-feather="search" class="search-icon"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    class="patient-search-input"
                    placeholder="Search patients..."
                    aria-label="Search patients"
                    id="searchInput"
                >
            </form>
        </div>
    </div>

    @if($patients->isEmpty())
        <div class="card patients-table-card">
            <div class="table-wrapper">
                <p class="text-center" style="padding: 2rem;">
                    @if(empty($search))
                        No patients with history found.
                    @else
                        No patients match your search.
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="card patients-table-card">
            @if(!empty($search))
                <div class="patients-table-header">
                    <span class="search-summary">Showing results for "<strong>{{ $search }}</strong>"</span>
                </div>
            @endif
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Latest History</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            @php
                                $initials = collect(explode(' ', trim($patient->name)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                                if ($initials === '') { $initials = 'P'; }
                                $latestHistory = $patient->patientHistory->sortByDesc('created_at')->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="profile-icon">{{ $initials }}</div>
                                    <span class="primary-column-text">{{ $patient->name }}</span>
                                </td>
                                <td>{{ $patient->email }}</td>
                                <td>
                                    @if($latestHistory)
                                        {{ $latestHistory->created_at->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">No history available</span>
                                    @endif
                                </td>
                                <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.patients.history', $patient->id) }}" class="btn-view-history">
                                                    View History
                                                </a>
                                            </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $patients->links() }}
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .patients-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }

    .patients-header-left {
        flex: 1;
    }

    .patients-header-right {
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

    .patient-search-input {
        width: 280px;
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

    .patient-search-input:focus {
        outline: none;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    }

    .patient-search-input::placeholder {
        color: #9ca3af;
        font-style: italic;
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

    .patients-table-card {
        padding: 1rem;
        border: 1px solid #eef1f4;
        margin-bottom: 2rem;
    }

    .patients-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .search-summary {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        white-space: nowrap;
    }

    .btn-view-history,
    .btn-update-history {
        display: inline-block;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .btn-view-history {
        background: #197a8c;
        color: #ffffff;
        border: 1px solid #197a8c;
    }

    .btn-view-history:hover {
        background: #1a6b7a;
        border-color: #1a6b7a;
        color: #ffffff;
    }

    .btn-update-history {
        background: #ffffff;
        color: #197a8c;
        border: 1px solid #197a8c;
    }

    .btn-update-history:hover {
        background: #197a8c;
        color: #ffffff;
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 0.75rem;
        }

        .patients-header {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 1.5rem;
        }

        .patients-header-left {
            width: 100%;
        }

        .management-label {
            font-size: 0.9rem;
        }

        .patients-header-right {
            flex-direction: column;
            width: 100%;
            gap: 0.75rem;
        }

        .search-wrapper,
        .patient-search-input {
            width: 100%;
        }

        .patient-search-input {
            padding-left: 40px;
            min-height: 44px;
        }

        .patients-table-card {
            padding: 0.75rem;
        }

        .patients-table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        /* Make table responsive - card layout */
        .table-wrapper {
            overflow-x: visible;
            width: 100%;
        }

        table {
            min-width: 100%;
            width: 100%;
            display: block;
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
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(255, 250, 240, 0.75);
            border-radius: 12px;
            box-shadow: 
                0 4px 12px rgba(0, 0, 0, 0.08),
                0 2px 6px rgba(255, 215, 0, 0.15);
            box-sizing: border-box;
        }

        table tbody tr:last-child {
            margin-bottom: 0;
        }

        table tbody td {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            text-align: left;
            min-height: auto;
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }

        table tbody td:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        table tbody td[colspan] {
            display: block;
            text-align: center;
            padding: 2rem 1rem !important;
            border-bottom: none;
        }

        table tbody td[colspan]::before {
            display: none;
        }

        table tbody td:nth-child(1)::before {
            content: "Name";
        }
        
        table tbody td:nth-child(2)::before {
            content: "Email";
        }
        
        table tbody td:nth-child(3)::before {
            content: "Latest History";
        }
        
        table tbody td:nth-child(4)::before {
            content: "Actions";
        }

        table tbody td::before {
            font-weight: 600;
            color: #2c3e50;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            flex-shrink: 0;
            text-align: left;
        }

        table tbody td:first-child {
            padding-top: 0;
        }

        table tbody td:first-child::before {
            display: none;
        }

        .profile-icon {
            align-self: flex-start;
        }

        .primary-column-text {
            width: 100%;
        }

        .action-buttons {
            width: 100%;
            flex-direction: column;
        }

        .btn-view-history,
        .btn-update-history {
            width: 100%;
            text-align: center;
            min-height: 44px;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding: 0 0.5rem;
        }

        .patients-header {
            margin-bottom: 1rem;
        }

        .management-label {
            font-size: 0.85rem;
        }

        .patients-table-card {
            padding: 0.5rem;
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

        .btn-view-history,
        .btn-update-history {
            padding: 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }

        // Live search functionality
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        let searchTimeout;

        if (searchInput && searchForm) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                // Debounce the search to avoid too many requests
                searchTimeout = setTimeout(function() {
                    searchForm.submit();
                }, 500);
            });

            // Allow Enter key to submit immediately
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    searchForm.submit();
                }
            });
        }
    });
</script>
@endpush
@endsection
