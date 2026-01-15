@extends('layouts.dashboard')
@section('page-title', 'Promotions')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<style>
    .promos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        gap: 1rem;
        width: 100%;
        box-sizing: border-box;
    }

    .promos-header-left {
        flex: 1;
        min-width: 0;
    }

    .promos-header-right {
        flex: 0 1 auto;
        min-width: 0;
    }

    .promos-header h1 {
        color: var(--primary-color);
        margin: 0;
        font-size: 1.5rem;
    }

    .btn-create-promo {
        background: var(--primary-color);
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
        box-sizing: border-box;
    }

    .btn-create-promo i {
        width: 18px;
        height: 18px;
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 0.75rem;
        }

        .promos-header {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 1.5rem;
        }

        .promos-header-left {
            width: 100%;
        }

        .promos-header h1 {
            font-size: 1.25rem;
        }

        .promos-header-right {
            width: 100%;
        }

        .btn-create-promo {
            width: 100%;
            justify-content: center;
            min-height: 44px;
        }

        .card {
            padding: 0.75rem;
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

        .status-badge {
            align-self: flex-start;
        }

        .action-buttons {
            width: 100%;
            flex-direction: row;
            gap: 0.5rem;
        }

        .action-btn {
            min-height: 44px;
            min-width: 44px;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding: 0 0.5rem;
        }

        .promos-header {
            margin-bottom: 1rem;
        }

        .promos-header h1 {
            font-size: 1.1rem;
        }

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

        .status-badge {
            padding: 6px 12px;
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@section('content')
<div class="container">
    <div class="promos-header">
        <div class="promos-header-left">
            <h1>Promotions</h1>
        </div>
        <div class="promos-header-right">
            <a href="{{ route('admin.promos.create') }}" class="btn-create-promo">
                <i data-feather="plus"></i>
                Create Promotion
            </a>
        </div>
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
                                    <a href="{{ route('admin.promos.edit', $promo) }}" class="action-btn" title="Edit">
                                        <i data-feather="edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.promos.destroy', $promo) }}" onsubmit="return confirm('Delete this promotion?');" style="display: inline;">
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
                                No promotions found. <a href="{{ route('admin.promos.create') }}">Create one now</a>
                            </td>
                        </tr>
                    @endforelse
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

