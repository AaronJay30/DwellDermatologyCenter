@extends('layouts.dashboard')
@section('page-title', 'Promotions')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 2rem;">Promotions</h1>
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('doctor.promotions.create') }}" style="background: var(--primary-color); color: #fff; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none;">+ Add Promotion</a>
    </div>
    
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Discount</th>
                        <th>Status</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promotions as $promo)
                        @php
                            $initials = mb_substr($promo->name, 0, 1);
                            if ($initials === '') { $initials = 'P'; }
                        @endphp
                        <tr>
                            <td data-label="Name">
                                <div class="profile-icon">{{ $initials }}</div>
                                <span class="primary-column-text">{{ $promo->name }}</span>
                            </td>
                            <td data-label="Type">{{ ucfirst($promo->type) }}</td>
                            <td data-label="Discount">{{ $promo->discount_percent ? $promo->discount_percent.'%' : '-' }}</td>
                            <td data-label="Status">
                                <span class="status-badge status-{{ strtolower($promo->status) }}">
                                    {{ ucfirst($promo->status) }}
                                </span>
                            </td>
                            <td data-label="Start">{{ $promo->starts_at ? $promo->starts_at->format('Y-m-d') : '-' }}</td>
                            <td data-label="End">{{ $promo->ends_at ? $promo->ends_at->format('Y-m-d') : '-' }}</td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="{{ route('doctor.promotions.edit', $promo) }}" class="action-btn" title="Edit">
                                        <i data-feather="edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('doctor.promotions.destroy', $promo) }}" onsubmit="return confirm('Delete this promotion?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="action-btn" type="submit" title="Delete">
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
        <div style="margin-top: 1rem;">{{ $promotions->links() }}</div>
    </div>
</div>

<script>
    if (window.feather && typeof window.feather.replace === 'function') {
        window.feather.replace();
    }
</script>
@endsection

<style>
    /* Desktop Action Button Backgrounds */
    .action-btn {
        background: #197a8c !important;
        color: white !important;
        border: none !important;
    }

    .action-btn:hover {
        background: #1a6b7a !important;
        transform: scale(1.05);
    }

    /* Delete button specific styling */
    .action-buttons form button.action-btn {
        background: #dc3545 !important;
    }

    .action-buttons form button.action-btn:hover {
        background: #c82333 !important;
    }

    /* Mobile Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 0 0.75rem;
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

        /* Card Header - Promotion Name */
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
        table tbody td:nth-child(4),
        table tbody td:nth-child(5),
        table tbody td:nth-child(6) {
            display: flex;
            flex-direction: column;
            padding: 8px 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        }

        table tbody td:nth-child(2):before,
        table tbody td:nth-child(3):before,
        table tbody td:nth-child(4):before,
        table tbody td:nth-child(5):before,
        table tbody td:nth-child(6):before {
            content: attr(data-label);
            font-weight: 600;
            color: #2c3e50;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        /* Card Actions */
        table tbody td:last-child {
            display: flex;
            padding: 12px 16px 16px 16px;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            margin-top: 8px;
            border-bottom: none;
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
            background: #197a8c;
            color: white;
            border: none;
            cursor: pointer;
        }

        table tbody td:last-child .action-buttons .action-btn:hover {
            background: #1a6b7a;
            transform: translateY(-1px);
        }

        table tbody td:last-child .action-buttons form {
            flex: 1;
        }

        table tbody td:last-child .action-buttons form button {
            width: 100%;
            background: #dc3545;
            color: white;
        }

        table tbody td:last-child .action-buttons form button:hover {
            background: #c82333;
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

        /* Hide the data-label attribute on first child since it's the header */
        table tbody td:first-child::before {
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

        table tbody tr {
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }

        table tbody td {
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        table tbody td:first-child {
            padding: 12px 12px 8px 12px;
        }

        table tbody td:first-child .profile-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        table tbody td:first-child .primary-column-text {
            font-size: 1rem;
        }

        table tbody td:last-child {
            padding: 8px 12px 12px 12px;
        }

        table tbody td:last-child .action-buttons .action-btn,
        table tbody td:last-child .action-buttons form button {
            min-height: 40px;
            padding: 8px 12px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 400px) {
        table tbody td {
            font-size: 0.8rem;
        }

        table tbody td:first-child .primary-column-text {
            font-size: 0.9rem;
        }

        table tbody td:last-child .action-buttons .action-btn,
        table tbody td:last-child .action-buttons form button {
            padding: 6px 8px;
            font-size: 0.75rem;
            min-height: 36px;
        }
    }
</style>


