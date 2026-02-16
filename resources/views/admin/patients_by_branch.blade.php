@extends('layouts.dashboard')
@section('page-title', 'Patients')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<style>
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        white-space: nowrap;
    }
    .btn-view-history {
        display: inline-block;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s ease;
        white-space: nowrap;
        background: #197a8c;
        color: #ffffff;
        border: 1px solid #197a8c;
    }
    .btn-view-history:hover {
        background: #1a6b7a;
        border-color: #1a6b7a;
        color: #ffffff;
    }
</style>
@endpush

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 1rem;">{{ $branch->name }} Patients</h1>
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $p)
                        @php
                            $initials = collect(explode(' ', trim($p->name)))->filter()->map(fn($name) => mb_substr($name, 0, 1))->take(2)->join('');
                            if ($initials === '') { $initials = 'P'; }
                        @endphp
                        <tr>
                            <td>
                                <div class="profile-icon">{{ $initials }}</div>
                                <span class="primary-column-text">{{ $p->name }}</span>
                            </td>
                            <td>{{ $p->email }}</td>
                            <td>{{ $p->phone ?? '—' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.patients.history', ['patient' => $p->id]) }}" class="btn-view-history">
                                        View History
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No patients in this branch.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


