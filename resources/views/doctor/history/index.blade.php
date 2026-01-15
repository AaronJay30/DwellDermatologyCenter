@extends('layouts.dashboard')
@section('page-title', 'Patient History')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <div class="patients-header">
        <div class="patients-header-left">
            <span class="management-label">PATIENT HISTORY</span>
        </div>
        <div class="patients-header-right">
            <form method="GET" action="{{ route('doctor.history') }}" class="search-wrapper" id="searchForm">
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

    @if($uniquePatients->isEmpty())
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
                        @foreach($uniquePatients as $patientData)
                            @php
                                $patient = $patientData['patient'];
                                $initials = collect(explode(' ', trim($patient->name)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                                if ($initials === '') { $initials = 'P'; }
                                $latestDate = $patientData['latest_date'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="profile-icon">{{ $initials }}</div>
                                    <span class="primary-column-text">{{ $patient->name }}</span>
                                </td>
                                <td>{{ $patient->email ?? 'No email' }}</td>
                                <td>
                                    @if($latestDate)
                                        {{ $latestDate instanceof \Carbon\Carbon ? $latestDate->format('M d, Y') : \Carbon\Carbon::parse($latestDate)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">No history available</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('doctor.history.patient', $patient->id) }}" class="btn-view-history">
                                            View History
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
        .patients-header {
            flex-direction: column;
            align-items: stretch;
        }

        .patients-header-right {
            flex-direction: column;
            width: 100%;
        }

        .search-wrapper,
        .patient-search-input {
            width: 100%;
        }

        .patient-search-input {
            padding-left: 40px;
        }

        .patients-table-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.25rem;
        }

        .btn-view-history,
        .btn-update-history {
            width: 100%;
            text-align: center;
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
