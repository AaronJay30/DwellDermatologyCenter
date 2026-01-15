@extends('layouts.dashboard')
@section('page-title', 'Admin Dashboard')

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem; font-size: 1.875rem; font-weight: 700;">Admin Dashboard</h1>

    <div style="display: grid; gap: 1.5rem; grid-template-columns: repeat(1, minmax(0, 1fr));" class="lg-grid-cols-12">
        
        <div class="lg-col-span-8" style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <div class="card hero-flex" style="display:flex; align-items:center; justify-content:space-between; gap:1.5rem; background: linear-gradient(135deg, #197a8c, #1a6b7a); color:#fff; padding: 1.5rem; border-radius: 12px;">
                <div class="hero-text">
                    <h2 style="margin-bottom:.5rem; font-size: 1.5rem; font-weight: 600;">View all appointments today</h2>
                    <p style="opacity:.9; font-size: 0.875rem;">Quick access to today's schedule and patient details.</p>
                    <a href="{{ route('admin.appointments') }}" class="btn" style="display: inline-block; margin-top: 1rem; background:#fff; color:#197a8c; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600;">Open Schedule</a>
                </div>
                <img src="{{ asset('images/dwell-logo.png') }}" 
                     alt="Derma" 
                     class="hero-img"
                     style="width:150px; height:auto; border-radius:10px; object-fit:cover;">
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:1rem;">
                @foreach($branches as $branch)
                    <a href="{{ route('admin.branch.patients', $branch) }}" class="card" style="text-decoration:none; color:inherit; background: linear-gradient(135deg, #eef8fb, #ffffff); padding: 1.25rem; border-radius: 12px; display: block;">
                        <div style="font-weight:600; color:#197a8c; font-size: 1.1rem;">{{ $branch->name }}</div>
                        <div style="margin-top:.5rem; font-size:.85rem; color:#6c757d; min-height: 2.5rem;">{{ $branch->address }}</div>
                        <div style="margin-top:.75rem; font-size:.9rem;">Patients: <strong>{{ $branch->users_count }}</strong></div>
                    </a>
                @endforeach
            </div>

            <div class="card" style="padding: 1.25rem; border-radius: 12px; background: #fff;">
                <div class="table-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap: wrap; gap: 0.5rem;">
                    <h3 style="color: var(--primary-color); font-weight: 600;">Today's Schedule</h3>
                    <div style="display:flex; gap:.5rem;">
                        <a href="{{ route('admin.appointments') }}" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.4rem 0.8rem;">View All</a>
                        <a href="{{ route('admin.patients') }}" class="btn btn-accent" style="font-size: 0.75rem; padding: 0.4rem 0.8rem;">Patients</a>
                    </div>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width:100%; border-collapse:collapse; min-width: 400px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding: .75rem; text-align:left; border-bottom:2px solid #e9ecef; font-size: 0.875rem;">Time</th>
                                <th style="padding: .75rem; text-align:left; border-bottom:2px solid #e9ecef; font-size: 0.875rem;">Patient</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todaysSchedule as $appt)
                                <tr style="border-bottom:1px solid #e9ecef;">
                                    <td style="padding:.75rem; font-size: 0.875rem;">{{ optional($appt->doctorSlot)->start_time?->format('H:i') }} - {{ optional($appt->doctorSlot)->end_time?->format('H:i') }}</td>
                                    <td style="padding:.75rem; font-size: 0.875rem;">{{ $appt->patient->name ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr><td style="padding:.75rem; font-size: 0.875rem;" colspan="2">No appointments today.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="lg-col-span-4" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card" style="text-align:center; padding: 1.5rem; border-radius: 12px; background: #fff;">
                <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : asset('images/doctor-placeholder.jpg') }}" 
                     alt="Admin" 
                     style="width:100px; height:100px; border-radius:50%; object-fit:cover; margin:0 auto 10px; border: 3px solid #f0fdfa;">
                <div style="font-weight:700; color:#197a8c;">{{ auth()->user()->name }}</div>
                <a href="{{ route('admin.profile') }}" class="btn btn-primary" style="margin-top:.75rem; display: block; width: 100%;">Profile</a>
            </div>

            <div class="card" style="padding: 1.25rem; border-radius: 12px; background: #fff;">
                <h3 style="color: var(--primary-color); margin-bottom:.75rem; font-size: 1rem; font-weight: 600;">Calendar</h3>
                <input type="date" style="width:100%; padding:.6rem; border:1px solid #e9ecef; border-radius:8px; outline: none;" value="{{ now()->toDateString() }}">
            </div>

            <div class="card" style="padding: 1.25rem; border-radius: 12px; background: #fff;">
                <h3 style="color: var(--primary-color); margin-bottom:.75rem; font-size: 1rem; font-weight: 600;">Upcoming</h3>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:.5rem;">
                    @forelse($upcomingAppointments as $u)
                        <li style="display:flex; justify-content:space-between; align-items:center; padding:.6rem .8rem; border-radius:10px; background: linear-gradient(135deg, #1d8fa5, #197a8c); color:#fff; font-size: 0.85rem;">
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px;">{{ $u->patient->name ?? 'N/A' }}</span>
                            <span style="font-size: 0.75rem;">
                                {{ optional($u->doctorSlot)->slot_date?->format('M d') }} | {{ optional($u->doctorSlot)->start_time?->format('H:i') }}
                            </span>
                        </li>
                    @empty
                        <li style="font-size: 0.875rem; color: #9ca3af;">No upcoming appointments.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    /* Desktop layout logic (Min-width 1024px) */
    @media (min-width: 1024px) {
        .lg-grid-cols-12 {
            grid-template-columns: repeat(12, minmax(0, 1fr)) !important;
        }
        .lg-col-span-8 {
            grid-column: span 8 / span 8 !important;
        }
        .lg-col-span-4 {
            grid-column: span 4 / span 4 !important;
        }
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
        .hero-flex {
            flex-direction: column !important;
            text-align: center;
        }
        .hero-img {
            width: 120px !important;
            margin: 0 auto;
        }
        .table-header {
            justify-content: center !important;
        }
    }
</style>
@endsection