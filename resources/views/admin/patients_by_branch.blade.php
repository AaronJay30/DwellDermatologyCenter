@extends('layouts.dashboard')
@section('page-title', 'Patients')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
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
                        <th>Name</th>/
                        <th>Email</th>
                        <th>Phone</th>
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


