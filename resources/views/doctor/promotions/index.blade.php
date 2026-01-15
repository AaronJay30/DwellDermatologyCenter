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
                            <td>
                                <div class="profile-icon">{{ $initials }}</div>
                                <span class="primary-column-text">{{ $promo->name }}</span>
                            </td>
                            <td>{{ ucfirst($promo->type) }}</td>
                            <td>{{ $promo->discount_percent ? $promo->discount_percent.'%' : '-' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($promo->status) }}">
                                    {{ ucfirst($promo->status) }}
                                </span>
                            </td>
                            <td>{{ $promo->starts_at ? $promo->starts_at->format('Y-m-d') : '-' }}</td>
                            <td>{{ $promo->ends_at ? $promo->ends_at->format('Y-m-d') : '-' }}</td>
                            <td>
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


