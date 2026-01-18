@extends('layouts.dashboard')
@section('page-title', 'Promotions')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
<style>
/* Responsive Design - Tablets */
@media (max-width: 992px) {
    /* Only target content container, not navbar */
}

@media (max-width: 768px) {
    /* Header section - only target the promotions header */
    .card {
        padding: 0.75rem;
    }
    
    /* Target only the specific header div with inline styles */
    div[style*="display: flex"][style*="justify-content: space-between"] {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 1rem;
    }

    div[style*="display: flex"][style*="justify-content: space-between"] h1 {
        text-align: center;
    }

    div[style*="display: flex"][style*="justify-content: space-between"] a[href*="promos.create"] {
        width: 100%;
        text-align: center;
        box-sizing: border-box;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
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

    /* Add labels for each field in mobile view */
    table tbody td:nth-child(1)::before {
        content: "Title";
    }

    table tbody td:nth-child(2)::before {
        content: "Promo Code";
    }

    table tbody td:nth-child(3)::before {
        content: "Services";
    }

    table tbody td:nth-child(4)::before {
        content: "Status";
    }

    table tbody td:nth-child(5)::before {
        content: "Start Date";
    }

    table tbody td:nth-child(6)::before {
        content: "End Date";
    }

    table tbody td:nth-child(7)::before {
        content: "Actions";
    }

    /* Action buttons in table */
    table tbody td:last-child .action-buttons {
        width: 100%;
        display: flex;
        flex-direction: row;
        gap: 0.5rem;
    }

    table tbody td:last-child .action-buttons a,
    table tbody td:last-child .action-buttons button {
        flex: 1;
        min-height: 44px;
        justify-content: center;
        display: flex;
        align-items: center;
    }

    table tbody td:last-child .action-buttons form {
        flex: 1;
        display: flex;
    }

    table tbody td:last-child .action-buttons form button {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .card {
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

    table tbody td:last-child .action-buttons a,
    table tbody td:last-child .action-buttons button {
        padding: 0.5rem;
        font-size: 0.8rem;
        min-height: 40px;
    }
}

@media (max-width: 400px) {
    table tbody td {
        font-size: 0.8rem;
    }

    table tbody td:last-child .action-buttons a,
    table tbody td:last-child .action-buttons button {
        padding: 0.4rem;
        font-size: 0.75rem;
        min-height: 36px;
    }
}
</style>
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--primary-color); margin: 0;">Promotions</h1>
        <a href="{{ route('doctor.promos.create') }}" style="background: var(--primary-color); color: #fff; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 500;">
            <i data-feather="plus" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 0.5rem;"></i>
            Create Promotion
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Promo Code</th>
                        <th>Services</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promo)
                        @php
                            $initials = mb_substr($promo->display_title, 0, 1);
                            if ($initials === '') { $initials = 'P'; }
                            $statusClass = strtolower($promo->status);
                            if ($promo->isActive()) {
                                $statusClass = 'active';
                            } elseif ($promo->ends_at && $promo->ends_at < now()) {
                                $statusClass = 'expired';
                            }
                        @endphp
                        <tr>
                            <td>
                                <div class="profile-icon">{{ $initials }}</div>
                                <span class="primary-column-text">{{ $promo->display_title }}</span>
                            </td>
                            <td>
                                @if($promo->promo_code)
                                    <code style="background: #f0f0f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">{{ $promo->promo_code }}</code>
                                @else
                                    <span style="color: #999;">-</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: #666; font-size: 0.9rem;">
                                    {{ $promo->promoServices->count() }} service(s)
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $statusClass }}">
                                    {{ ucfirst($statusClass) }}
                                </span>
                            </td>
                            <td>{{ $promo->starts_at ? $promo->starts_at->format('M d, Y H:i') : '-' }}</td>
                            <td>{{ $promo->ends_at ? $promo->ends_at->format('M d, Y H:i') : '-' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('doctor.promos.edit', $promo) }}" class="action-btn" title="Edit">
                                        <i data-feather="edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('doctor.promos.destroy', $promo) }}" onsubmit="return confirm('Delete this promotion?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="action-btn" type="submit" title="Delete">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: #999;">
                                No promotions found. <a href="{{ route('doctor.promos.create') }}">Create one now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $promotions->links() }}</div>
    </div>
</div>

<script>
    if (window.feather && typeof window.feather.replace === 'function') {
        window.feather.replace();
    }
</script>
@endsection
