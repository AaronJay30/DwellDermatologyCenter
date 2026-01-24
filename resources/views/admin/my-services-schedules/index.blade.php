@extends('layouts.dashboard')
@section('page-title', 'My Services Schedules')

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
<style>
    .slots-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        gap: 1rem;
    }

    .slots-header-left {
        flex: 1;
    }

    .slots-header-right {
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

    .slot-search-input {
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

    .slot-search-input:focus {
        outline: none;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    }

    .slot-search-input::placeholder {
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

    .slots-table-card {
        padding: 1rem;
        border: 1px solid #eef1f4;
        margin-bottom: 2rem;
    }

    .slots-table-header {
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

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 90px;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-booked {
        background-color: #3b82f6;
        color: #fff;
    }

    .status-pending {
        background-color: #fbbf24;
        color: #78350f;
    }

    .status-confirmed {
        background-color: #10b981;
        color: #fff;
    }

    .status-completed {
        background-color: #6b7280;
        color: #fff;
    }

    .patient-info {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .patient-name-link {
        color: #197a8c;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
    }

    .patient-name-link:hover {
        color: #145866;
    }

    /* Patient Information Modal Styles */
    .patient-modal {
        display: none;
        position: absolute;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .patient-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .patient-modal-content {
        background-color: #ffffff;
        margin: auto;
        padding: 0;
        border: 3px solid #FFD700;
        width: 90%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .patient-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem 2rem;
        border-bottom: 2px solid #e0e0e0;
        background-color: #ffffff;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .patient-modal-logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .patient-modal-logo img {
        width: 60px;
        height: 60px;
    }

    .patient-modal-logo-text {
        font-size: 1.2rem;
        font-weight: bold;
        color: #197a8c;
    }

    .patient-modal-title {
        font-size: 1.8rem;
        font-weight: bold;
        color: #000000;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
    }

    .patient-modal-close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .patient-modal-close:hover,
    .patient-modal-close:focus {
        color: #000;
    }

    .patient-modal-body {
        padding: 2rem;
    }

    .patient-form-section {
        background-color: #E6F3F5;
        border: 2px solid #FFD700;
        border-radius: 5px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .patient-section-header {
        background-color: #008080;
        color: #ffffff;
        padding: 0.75rem 1rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        font-weight: bold;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .patient-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .patient-form-group {
        margin-bottom: 1rem;
    }

    .patient-form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .patient-form-group input[type="text"],
    .patient-form-group input[type="email"],
    .patient-form-group input[type="date"],
    .patient-form-group textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 1rem;
        box-sizing: border-box;
        background-color: #ffffff;
    }

    .patient-form-group textarea {
        min-height: 80px;
        resize: vertical;
    }

    .patient-radio-group {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .patient-radio-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: normal;
        cursor: default;
    }

    .patient-checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .patient-checkbox-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: normal;
        cursor: default;
    }

    .patient-certification-section {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e0e0e0;
    }

    .patient-certification-text {
        margin-bottom: 1.5rem;
        font-size: 1rem;
        color: #333;
    }

    .patient-signature-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-top: 1rem;
    }

    .patient-signature-field {
        display: flex;
        flex-direction: column;
    }

    .patient-signature-field label {
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .patient-signature-display {
        border: 2px solid #ccc;
        border-radius: 5px;
        padding: 1rem;
        background: white;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .patient-signature-display img {
        max-width: 100%;
        max-height: 150px;
    }

    .patient-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-top: 2px solid #e0e0e0;
        background-color: #ffffff;
        position: sticky;
        bottom: 0;
    }

    .patient-modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .patient-modal-btn-cancel {
        background-color: #6c757d;
        color: white;
    }

    .patient-modal-btn-cancel:hover {
        background-color: #5a6268;
    }

    .patient-modal-btn-print {
        background-color: #008080;
        color: white;
    }

    .patient-modal-btn-print:hover {
        background-color: #006666;
    }

    .patient-modal-btn-download {
        background-color: #008080;
        color: white;
    }

    .patient-modal-btn-download:hover {
        background-color: #006666;
    }

    /* Modal Container Styles */
    .modal-container {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        align-items: center;
        justify-content: center;
        z-index: 1000;
        overflow-y: auto;
        padding: 20px;
        display: none;
    }

    .modal-container.active {
        display: flex;
    }

    .modal-content {
        background: #ffffff;
        border: 1px solid #eef1f4;
        border-radius: 8px;
        width: min(900px, 95vw);
        max-height: 90vh;
        overflow-y: auto;
        color: #000000;
    }

    .modal-header {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #000000;
    }

    .modal-body {
        padding: 1.5rem;
        color: #000000;
    }

    .modal-section {
        background-color: #E6F3F5;
        border: 2px solid #FFD700;
        border-radius: 5px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .modal-section-header {
        background-color: #008080;
        color: #ffffff;
        padding: 0.75rem 1rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        font-weight: bold;
        font-size: 1.1rem;
        text-transform: uppercase;
    }

    .bullet-list-container {
        margin-top: 1rem;
    }

    .bullet-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        color: #000000;
    }

    .bullet-item input {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        color: #000000;
        background: #ffffff;
    }

    .bullet-item input::placeholder {
        color: #999;
    }

    .add-bullet-btn {
        padding: 0.4rem 0.8rem;
        background-color: #197a8c;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .add-bullet-btn:hover {
        background-color: #145866;
    }

    .remove-bullet-btn {
        padding: 0.3rem 0.6rem;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.75rem;
    }

    .remove-bullet-btn:hover {
        background-color: #c82333;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #000000;
    }

    .form-group input[type="text"],
    .form-group input[type="date"],
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        color: #000000;
        background: #ffffff;
    }

    .form-group textarea {
        resize: vertical;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .form-check input[type="checkbox"] {
        width: auto;
    }

    .form-check label {
        margin: 0;
        color: #000000;
    }

    @media (max-width: 768px) {
        .slots-header {
            flex-direction: column;
            align-items: stretch;
        }

        .patient-modal {
            position: fixed;
        }

        .slots-header-right {
            flex-direction: column;
            width: 100%;
        }

        .search-wrapper,
        .slot-search-input {
            width: 100%;
        }

        .slot-search-input {
            padding-left: 40px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="slots-header">
        <div class="slots-header-left">
            <span class="management-label">{{ $pageTitle ?? 'MY SERVICES SCHEDULES' }}</span>
        </div>
        <div class="slots-header-right">
            <form method="GET" action="{{ route($searchRoute ?? 'admin.my-services-schedules') }}" class="search-wrapper" id="searchForm">
                <i data-feather="search" class="search-icon"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    class="slot-search-input"
                    placeholder="Search by patient, service, branch..."
                    aria-label="Search service schedules"
                    id="searchInput"
                >
            </form>
        </div>
    </div>

    <div class="card slots-table-card">
        <div class="slots-table-header">
            @if(!empty($search))
                <span class="search-summary">Showing results for "<strong>{{ $search }}</strong>"</span>
            @endif
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        @php
                            $patientProfile = $appointment->personalInformation;
                            $composedName = trim(($appointment->first_name ?? '') . ' ' . ($appointment->middle_initial ?? '') . ' ' . ($appointment->last_name ?? ''));
                            $patientName = $patientProfile?->full_name
                                ?? ($composedName !== '' ? $composedName : null)
                                ?? ($appointment->patient->name ?? 'N/A');

                            // Normalize date/time display using app timezone to avoid off-by-one issues
                            $tz = config('app.timezone');
                            // Try to honor the patient-selected preferred date; fall back safely
                            $preferredDate = null;
                            if (empty($appointment->scheduled_date) && !empty($appointment->notes)) {
                                // Look for "Preferred Date: <date>" in notes (patient booking flow)
                                if (preg_match('/Preferred Date:\s*([A-Za-z]{3,}\s+\d{1,2},\s*\d{4})/i', $appointment->notes, $m)) {
                                    try {
                                        $preferredDate = \Carbon\Carbon::parse($m[1], $tz)->timezone($tz)->format('M d, Y');
                                    } catch (\Exception $e) {
                                        $preferredDate = null;
                                    }
                                } elseif (preg_match('/Preferred Date:\s*([\d]{4}-[\d]{2}-[\d]{2})/', $appointment->notes, $m)) {
                                    try {
                                        $preferredDate = \Carbon\Carbon::parse($m[1], $tz)->timezone($tz)->format('M d, Y');
                                    } catch (\Exception $e) {
                                        $preferredDate = null;
                                    }
                                }
                            }

                            $displayDate = $appointment->scheduled_date
                                ? \Carbon\Carbon::parse($appointment->scheduled_date, $tz)->timezone($tz)->format('M d, Y')
                                : ($preferredDate ?? optional($appointment->created_at)->timezone($tz)->format('M d, Y'));

                            $displayTime = null;
                            if ($appointment->scheduled_time) {
                                try {
                                    $displayTime = \Carbon\Carbon::createFromFormat('H:i:s', $appointment->scheduled_time, $tz)
                                        ->timezone($tz)
                                        ->format('g:i A');
                                } catch (\Exception $e) {
                                    try {
                                        $displayTime = \Carbon\Carbon::createFromFormat('H:i', $appointment->scheduled_time, $tz)
                                            ->timezone($tz)
                                            ->format('g:i A');
                                    } catch (\Exception $e) {
                                        $displayTime = null;
                                    }
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <div class="patient-info">
                                    <span
                                        class="patient-name-link" data-appointment-id="{{ $appointment->id }}" onclick="openPatientModal({{ $appointment->id }})"
                                    >
                                        {{ $patientName }}
                                    </span>
                                </div>
                            </td>
                            <td>{{ $appointment->service->name ?? 'N/A' }}</td>
                            <td>{{ $appointment->branch->name ?? 'N/A' }}</td>
                            <td>
                                {{ $displayDate ?? 'N/A' }}
                                @if($displayTime)
                                    <br><small style="color: #6c757d;">{{ $displayTime }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = 'status-' . strtolower($appointment->status);
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td>
                                @php
                                    $appointmentDate = $appointment->scheduled_date
                                        ? \Carbon\Carbon::parse($appointment->scheduled_date)->startOfDay()
                                        : $appointment->created_at->startOfDay();
                                    $isPastDate = $appointmentDate < now()->startOfDay();
                                    $isPastPending = $appointment->status === 'pending' && $isPastDate;
                                    $isPastConfirmed = $appointment->status === 'confirmed' && $isPastDate;
                                @endphp
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    @if($isPastPending)
                                        <button type="button"
                                            class="delete-past-appointment-btn"
                                                data-appointment-id="{{ $appointment->id }}"
                                                data-patient-name="{{ json_encode($patientName) }}"
                                                data-appointment-date="{{ $appointmentDate->format('M d, Y') }}"
                                                data-appointment-time="{{ $displayTime ?? '' }}"
                                                style="padding: 0.4rem 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                                            Delete
                                        </button>
                                    @elseif($isPastConfirmed)
                                        <button onclick="addServiceResult({{ $appointment->id }})" style="padding: 0.4rem 0.8rem; background-color: #197a8c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">Add Result</button>
                                        <button type="button"
                                            class="drop-past-confirmed-btn"
                                                data-appointment-id="{{ $appointment->id }}"
                                                data-patient-name="{{ json_encode($patientName) }}"
                                                data-appointment-date="{{ $appointmentDate->format('M d, Y') }}"
                                                data-appointment-time="{{ $displayTime ?? '' }}"
                                                style="padding: 0.4rem 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                                            Drop
                                        </button>
                                    @elseif($appointment->status === 'pending')
                                        <button onclick="confirmServiceSchedule({{ $appointment->id }})" style="padding: 0.4rem 0.8rem; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">Confirm</button>
                                        <button onclick="cancelServiceSchedule({{ $appointment->id }})" style="padding: 0.4rem 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">Cancel</button>
                                    @elseif($appointment->status === 'confirmed')
                                        <button onclick="addServiceResult({{ $appointment->id }})" style="padding: 0.4rem 0.8rem; background-color: #197a8c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">Add Result</button>
                                        <button onclick="cancelServiceSchedule({{ $appointment->id }})" style="padding: 0.4rem 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">Cancel</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:2rem; color:#6b7280;">
                                @if(empty($search))
                                    No service schedules found.
                                @else
                                    No service schedules match your search.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $appointments->links() }}
        </div>
    </div>
</div>

<!-- Patient Information Modal -->
<div id="patientModal" class="patient-modal">
    <div class="patient-modal-content">
        <div class="patient-modal-header">
            <div class="patient-modal-logo">
                <img src="{{ asset('images/dwell-logo.png') }}" alt="Logo">
                <span class="patient-modal-logo-text">D'well</span>
            </div>
            <h1 class="patient-modal-title">NEW PATIENT INFORMATION SHEET</h1>
            <button class="patient-modal-close" onclick="closePatientModal()">&times;</button>
        </div>
        <div class="patient-modal-body">
            <!-- PERSONAL INFORMATION Section -->
            <div class="patient-form-section">
                <div class="patient-section-header">PERSONAL INFORMATION</div>
                
                <div class="patient-form-group">
                    <label for="modal-name">Name</label>
                    <input type="text" id="modal-name" readonly>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label for="modal-birthday">Birthday</label>
                        <input type="text" id="modal-birthday" readonly>
                    </div>
                    <div class="patient-form-group">
                        <label for="modal-address">Address</label>
                        <input type="text" id="modal-address" readonly>
                    </div>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label for="modal-contact-number">Contact No</label>
                        <input type="text" id="modal-contact-number" readonly>
                    </div>
                    <div class="patient-form-group">
                        <label for="modal-email">Email address</label>
                        <input type="email" id="modal-email" readonly>
                    </div>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label>Civil Status</label>
                        <div class="patient-radio-group">
                            <label>
                                <input type="radio" name="modal-civil-status" value="Single" disabled>
                                Single
                            </label>
                            <label>
                                <input type="radio" name="modal-civil-status" value="Married" disabled>
                                Married
                            </label>
                        </div>
                    </div>
                    <div class="patient-form-group">
                        <label>Sex</label>
                        <div class="patient-radio-group">
                            <label>
                                <input type="radio" name="modal-sex" value="male" disabled>
                                Male
                            </label>
                            <label>
                                <input type="radio" name="modal-sex" value="female" disabled>
                                Female
                            </label>
                        </div>
                    </div>
                </div>

                <div class="patient-form-group">
                    <label for="modal-preferred-pronoun">Preferred pronoun</label>
                    <input type="text" id="modal-preferred-pronoun" readonly>
                </div>
            </div>

            <!-- PERTINENT MEDICAL INFORMATION Section -->
            <div class="patient-form-section">
                <div class="patient-section-header">PERTINENT MEDICAL INFORMATION</div>
                
                <div class="patient-form-group">
                    <label>Comorbids</label>
                    <div class="patient-checkbox-group">
                        <label>
                            <input type="checkbox" id="modal-hypertension" disabled>
                            Hypertension
                        </label>
                        <label>
                            <input type="checkbox" id="modal-diabetes" disabled>
                            Diabetes
                        </label>
                    </div>
                    <div style="margin-top: 0.5rem;">
                        <input type="text" id="modal-comorbidities-others" placeholder="Others, please specify:" readonly style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 3px;">
                    </div>
                </div>

                <div class="patient-form-group">
                    <label>Allergics</label>
                    <div class="patient-checkbox-group">
                        <label>
                            <input type="checkbox" id="modal-allergies-medications" disabled>
                            Medications
                        </label>
                        <label>
                            <input type="checkbox" id="modal-allergies-anesthetics" disabled>
                            Anesthetics
                        </label>
                    </div>
                    <div style="margin-top: 0.5rem;">
                        <input type="text" id="modal-allergies-others" placeholder="Others, please specify:" readonly style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 3px;">
                    </div>
                </div>

                <div class="patient-form-group">
                    <label for="modal-previous-hospitalizations">Previous hospitalizations / surgeries</label>
                    <textarea id="modal-previous-hospitalizations" readonly></textarea>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label>Smoker?</label>
                        <div class="patient-radio-group">
                            <label>
                                <input type="radio" name="modal-smoker" value="Yes" disabled>
                                Yes
                            </label>
                            <label>
                                <input type="radio" name="modal-smoker" value="No" disabled>
                                No
                            </label>
                        </div>
                    </div>
                    <div class="patient-form-group">
                        <label>Alcoholic beverage drinker?</label>
                        <div class="patient-radio-group">
                            <label>
                                <input type="radio" name="modal-alcoholic-drinker" value="Yes" disabled>
                                Yes
                            </label>
                            <label>
                                <input type="radio" name="modal-alcoholic-drinker" value="No" disabled>
                                No
                            </label>
                        </div>
                    </div>
                </div>

                <div class="patient-form-group">
                    <label for="modal-known-family-illnesses">Known family illnesses</label>
                    <textarea id="modal-known-family-illnesses" readonly></textarea>
                </div>
            </div>

            <!-- PERSON TO CONTACT IN CASE OF EMERGENCY Section -->
            <div class="patient-form-section">
                <div class="patient-section-header">PERSON TO CONTACT IN CASE OF EMERGENCY</div>
                
                <div class="patient-form-group">
                    <label for="modal-emergency-name">Name</label>
                    <input type="text" id="modal-emergency-name" readonly>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label for="modal-emergency-relationship">Relationship</label>
                        <input type="text" id="modal-emergency-relationship" readonly>
                    </div>
                    <div class="patient-form-group">
                        <label for="modal-emergency-address">Address</label>
                        <input type="text" id="modal-emergency-address" readonly>
                    </div>
                </div>

                <div class="patient-form-group">
                    <label for="modal-emergency-contact-number">Contact No</label>
                    <input type="text" id="modal-emergency-contact-number" readonly>
                </div>
            </div>

            <!-- Certification and Signature Section -->
            <div class="patient-certification-section">
                <p class="patient-certification-text">I certify that all the information I wrote on this form are true and correct.</p>
                
                <div class="patient-signature-section">
                    <div class="patient-signature-field">
                        <label>Signature over Printed Name</label>
                        <div class="patient-signature-display" id="modal-signature-display">
                            <span style="color: #999;">No signature available</span>
                        </div>
                    </div>
                    <div class="patient-signature-field">
                        <label>Date</label>
                        <input type="text" id="modal-date" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="patient-modal-footer">
            <button class="patient-modal-btn patient-modal-btn-cancel" onclick="closePatientModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="patient-modal">
    <div class="patient-modal-content" style="max-width: 480px;">
        <div class="patient-modal-header">
            <h1 class="patient-modal-title" style="font-size: 1.3rem; color: #ef4444;">
                Confirm Deletion
            </h1>
            <button class="patient-modal-close" onclick="closeDeleteModal()">×</button>
        </div>
        <div class="patient-modal-body" style="padding: 1.5rem 2rem;">
            <p style="margin-bottom: 1rem; color: #374151; line-height: 1.5;">
                Are you sure you want to delete this item?<br>
                <strong>This action cannot be undone.</strong>
            </p>
            <p id="deleteTargetInfo" style="font-weight: 500; color: #1f2937; margin: 1.25rem 0;"></p>
            <div id="deleteErrorMsg" style="display:none; color:#ef4444; font-weight:600; margin-top:1rem;"></div>
        </div>
        <div class="patient-modal-footer" style="padding: 1rem 2rem; gap: 1rem;">
            <button class="patient-modal-btn patient-modal-btn-cancel" onclick="closeDeleteModal()">
                Cancel
            </button>
            <button id="confirmDeleteBtn" class="patient-modal-btn" style="background-color: #ef4444; color: white;">
                Yes, Delete
            </button>
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="modalContainer" class="modal-container">
    <!-- Confirm Service Schedule Modal -->
    <div id="confirmModal" class="modal-content" style="display:none; width:min(520px, 92vw);">
        <div class="modal-header">
            <span style="color: #000000;">Confirm Service Schedule</span>
            <button type="button" onclick="closeConfirmModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #000000;">&times;</button>
        </div>
        <div class="modal-body">
            <form id="confirmForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="doctor_id" value="{{ auth()->id() }}">
                
                <div class="form-group">
                    <label for="scheduled_time" style="color: #000000;">Time <span style="color: red;">*</span></label>
                    <input type="time" name="scheduled_time" id="scheduled_time" class="form-control" required>
                    <small style="color: #6c757d;">Please select the scheduled time for this service</small>
                </div>
                
                <div class="form-group">
                    <label for="scheduled_date" style="color: #000000;">Date (Optional - for rescheduling)</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" class="form-control" min="{{ date('Y-m-d') }}">
                    <small style="color: #6c757d;">Leave empty to use the original booking date</small>
                </div>
                
                <div style="display:flex; justify-content:flex-end; gap:.5rem; margin-top: 1rem;">
                    <button type="button" class="btn btn-accent" onclick="closeConfirmModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cancel Service Schedule Modal -->
    <div id="cancelModal" class="modal-content" style="display:none; width:min(520px, 92vw);">
        <div class="modal-header">
            <span style="color: #000000;">Cancel Service Schedule</span>
            <button type="button" onclick="closeCancelModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #000000;">&times;</button>
        </div>
        <div class="modal-body">
            <form id="cancelForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="cancellation_reason" style="color: #000000;">Reason for cancellation:</label>
                    <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="3" required placeholder="Please provide a reason for cancelling this service schedule..."></textarea>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:.5rem;">
                    <button type="button" class="btn btn-accent" onclick="closeCancelModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Cancel Service Schedule</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Result Modal -->
    <div id="addResultModal" class="modal-content" style="display:none;">
        <div class="modal-header">
            <span style="color: #000000;">Add Result</span>
            <button type="button" onclick="closeAddResultModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #000000;">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addResultForm" enctype="multipart/form-data">
                @csrf
                
                <!-- BEFORE CONDITION Section -->
                <div class="modal-section">
                    <div class="modal-section-header">BEFORE CONDITION</div>
                    
                    <div class="bullet-list-container" id="beforeConditionList">
                        <div class="bullet-item">
                            <span style="color: #000000;">•</span>
                            <input type="text" name="before_condition[]" placeholder="Enter before condition detail..." style="color: #000000;">
                            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)" style="display: none;">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="add-bullet-btn" onclick="addBulletItem('beforeConditionList', 'before_condition')">+ Add Bullet</button>
                </div>

                <!-- RESULT Section -->
                <div class="modal-section">
                    <div class="modal-section-header">RESULT</div>
                    
                    <div class="bullet-list-container" id="resultList">
                        <div class="bullet-item">
                            <span style="color: #000000;">•</span>
                            <input type="text" name="result[]" placeholder="Enter result detail..." style="color: #000000;">
                            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)" style="display: none;">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="add-bullet-btn" onclick="addBulletItem('resultList', 'result')">+ Add Bullet</button>
                </div>

                <!-- PROCEDURES Section -->
                <div class="modal-section">
                    <div class="modal-section-header">PROCEDURES</div>
                    
                    <div class="bullet-list-container" id="proceduresList">
                        <div class="bullet-item">
                            <span style="color: #000000;">•</span>
                            <input type="text" name="procedures[]" placeholder="Enter procedure detail..." style="color: #000000;">
                            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)" style="display: none;">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="add-bullet-btn" onclick="addBulletItem('proceduresList', 'procedures')">+ Add Bullet</button>
                </div>

                <!-- MEDICATION Section -->
                <div class="modal-section">
                    <div class="modal-section-header">MEDICATION</div>
                    
                    <div class="bullet-list-container" id="medicationList">
                        <div class="bullet-item">
                            <span style="color: #000000;">•</span>
                            <input type="text" name="medication[]" placeholder="Enter medication detail..." style="color: #000000;">
                            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)" style="display: none;">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="add-bullet-btn" onclick="addBulletItem('medicationList', 'medication')">+ Add Bullet</button>
                </div>

                <!-- FOLLOW-UP Section -->
                <div class="modal-section">
                    <div class="modal-section-header">FOLLOW-UP</div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="follow_up_required" id="follow_up_required" value="1" onchange="toggleFollowUpDate()">
                            <label for="follow_up_required" style="color: #000000;">Follow-up Required</label>
                        </div>
                    </div>
                    
                    <div class="form-group" id="followUpDateWrapper" style="display: none;">
                        <label for="follow_up_date" style="color: #000000;">Follow-up Date</label>
                        <input type="date" name="follow_up_date" id="follow_up_date" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                
                <div style="display:flex; justify-content:flex-end; gap:.5rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e9ecef;">
                    <button type="button" class="btn btn-accent" onclick="closeAddResultModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Result</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Past Pending Appointments Modal -->
<div id="pastPendingModal" class="patient-modal">
    <div class="patient-modal-content" style="max-width: 600px;">
        <div class="patient-modal-header">
            <h1 class="patient-modal-title" style="font-size: 1.2rem;">⚠️ Past Pending Appointments</h1>
            <button class="patient-modal-close" onclick="closePastPendingModal()">&times;</button>
        </div>
        <div class="patient-modal-body">
            <p style="margin-bottom: 1rem; color: #6c757d;">
                The following service appointments have dates in the past and are still pending. These appointments need to be dropped.
            </p>
            <div id="pastPendingList" style="max-height: 400px; overflow-y: auto;">
                @foreach($pastPendingAppointments ?? [] as $appointment)
                    @php
                        $patientProfile = $appointment->personalInformation;
                        $composedName = trim(($appointment->first_name ?? '') . ' ' . ($appointment->middle_initial ?? '') . ' ' . ($appointment->last_name ?? ''));
                        $patientName = $patientProfile?->full_name
                            ?? ($composedName !== '' ? $composedName : null)
                            ?? ($appointment->patient->name ?? 'N/A');
                        $appointmentDate = $appointment->scheduled_date
                            ? \Carbon\Carbon::parse($appointment->scheduled_date)->format('M d, Y')
                            : $appointment->created_at->format('M d, Y');
                        $appointmentTime = $appointment->scheduled_time
                            ? \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A')
                            : '';
                    @endphp
                    <div style="padding: 1rem; margin-bottom: 0.75rem; border: 2px solid #fbbf24; border-radius: 6px; background: #fffbeb;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                            <div>
                                <strong style="color: #78350f;">{{ $patientName }}</strong>
                                <div style="font-size: 0.9rem; color: #6c757d; margin-top: 0.25rem;">
                                    {{ $appointment->service->name ?? 'N/A' }} - {{ $appointmentDate }}@if($appointmentTime) at {{ $appointmentTime }}@endif
                                    @if($appointment->branch)
                                        - {{ $appointment->branch->name }}
                                    @endif
                                </div>
                            </div>
                                <button type="button"
                                    class="delete-past-appointment-btn"
                                    data-appointment-id="{{ $appointment->id }}"
                                    data-patient-name="{{ json_encode($patientName) }}"
                                    data-appointment-date="{{ $appointmentDate }}"
                                    data-appointment-time="{{ $appointmentTime }}"
                                    style="padding: 0.4rem 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                                Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="patient-modal-footer">
            <button class="patient-modal-btn patient-modal-btn-cancel" onclick="closePastPendingModal()">Close</button>
        </div>
    </div>
</div>

<!-- Past Confirmed Appointments Modal -->
<div id="pastConfirmedModal" class="patient-modal">
    <div class="patient-modal-content" style="max-width: 600px;">
        <div class="patient-modal-header">
            <h1 class="patient-modal-title" style="font-size: 1.2rem;">⚠️ Past Confirmed Appointments</h1>
            <button class="patient-modal-close" onclick="closePastConfirmedModal()">&times;</button>
        </div>
        <div class="patient-modal-body">
            <p style="margin-bottom: 1rem; color: #6c757d;">
                The following service appointments have dates in the past and are still confirmed. These appointments need to be dropped.
            </p>
            <div id="pastConfirmedList" style="max-height: 400px; overflow-y: auto;">
                @foreach($pastConfirmedAppointments ?? [] as $appointment)
                    @php
                        $patientProfile = $appointment->personalInformation;
                        $composedName = trim(($appointment->first_name ?? '') . ' ' . ($appointment->middle_initial ?? '') . ' ' . ($appointment->last_name ?? ''));
                        $patientName = $patientProfile?->full_name
                            ?? ($composedName !== '' ? $composedName : null)
                            ?? ($appointment->patient->name ?? 'N/A');
                        $appointmentDate = $appointment->scheduled_date
                            ? \Carbon\Carbon::parse($appointment->scheduled_date)->format('M d, Y')
                            : $appointment->created_at->format('M d, Y');
                        $appointmentTime = $appointment->scheduled_time
                            ? \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A')
                            : '';
                    @endphp
                    <div style="padding: 1rem; margin-bottom: 0.75rem; border: 2px solid #dc3545; border-radius: 6px; background: #fff5f5;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                            <div>
                                <strong style="color: #991b1b;">{{ $patientName }}</strong>
                                <div style="font-size: 0.9rem; color: #6c757d; margin-top: 0.25rem;">
                                    {{ $appointment->service->name ?? 'N/A' }} - {{ $appointmentDate }}@if($appointmentTime) at {{ $appointmentTime }}@endif
                                    @if($appointment->branch)
                                        - {{ $appointment->branch->name }}
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <button type="button"
                                        onclick="addServiceResult({{ $appointment->id }})"
                                        style="padding: 0.4rem 0.8rem; background-color: #197a8c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                                    Add Result
                                </button>
                                <button type="button"
                                    class="drop-past-confirmed-btn"
                                        data-appointment-id="{{ $appointment->id }}"
                                        data-patient-name="{{ json_encode($patientName) }}"
                                        data-appointment-date="{{ $appointmentDate }}"
                                        data-appointment-time="{{ $appointmentTime }}"
                                        style="padding: 0.4rem 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                                    Drop
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="patient-modal-footer">
            <button class="patient-modal-btn patient-modal-btn-cancel" onclick="closePastConfirmedModal()">Close</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.feather && typeof window.feather.replace === 'function') {
        window.feather.replace();
    }

    // Live search functionality
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                searchForm.submit();
            }, 500);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                searchForm.submit();
            }
        });
    }

    // Close patient modal when clicking outside
    const patientModal = document.getElementById('patientModal');
    if (patientModal) {
        patientModal.addEventListener('click', function(e) {
            if (e.target === patientModal) {
                closePatientModal();
            }
        });
    }
});

let currentServiceAppointmentId = null;

// Modal management functions
function showModal(modalId) {
    const container = document.getElementById('modalContainer');
    const modal = document.getElementById(modalId);
    
    // Hide all modals first
    container.querySelectorAll('.modal-content').forEach(m => {
        m.style.display = 'none';
    });
    
    // Show the requested modal
    modal.style.display = 'block';
    container.classList.add('active');
}

function hideModal() {
    const container = document.getElementById('modalContainer');
    container.classList.remove('active');
    container.querySelectorAll('.modal-content').forEach(m => {
        m.style.display = 'none';
    });
}

// Patient info modal functions
function openPatientModal(appointmentId) {
    const modal = document.getElementById('patientModal');
    modal.classList.add('active');
    console.log("AARON");
    // Clear previous data
    clearPatientModal();
    
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'modal-loading';
    loadingIndicator.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 100; background: white; padding: 2rem; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);';
    loadingIndicator.innerHTML = '<p>Loading patient information...</p>';
    modal.querySelector('.patient-modal-content').style.position = 'relative';
    modal.querySelector('.patient-modal-content').appendChild(loadingIndicator);
    
    // Fetch patient information
    fetch(`{{ url('/admin/appointments') }}/${appointmentId}/patient-info`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch patient information');
            }
            return response.json();
        })
        .then(data => {
            // Remove loading indicator
            const loading = document.getElementById('modal-loading');
            if (loading) loading.remove();
            
            populatePatientModal(data);
        })
        .catch(error => {
            console.error('Error fetching patient info:', error);
            const loading = document.getElementById('modal-loading');
            if (loading) {
                loading.innerHTML = '<p style="color: red;">Error loading patient information. Please try again.</p><button onclick="closePatientModal()" class="patient-modal-btn patient-modal-btn-cancel" style="margin-top: 1rem;">Close</button>';
            }
        });
}

function clearPatientModal() {
    // Clear all input values
    document.getElementById('modal-name').value = '';
    document.getElementById('modal-birthday').value = '';
    document.getElementById('modal-address').value = '';
    document.getElementById('modal-contact-number').value = '';
    document.getElementById('modal-email').value = '';
    document.getElementById('modal-preferred-pronoun').value = '';
    document.getElementById('modal-comorbidities-others').value = '';
    document.getElementById('modal-allergies-others').value = '';
    document.getElementById('modal-previous-hospitalizations').value = '';
    document.getElementById('modal-known-family-illnesses').value = '';
    document.getElementById('modal-emergency-name').value = '';
    document.getElementById('modal-emergency-relationship').value = '';
    document.getElementById('modal-emergency-address').value = '';
    document.getElementById('modal-emergency-contact-number').value = '';
    document.getElementById('modal-date').value = '';
    
    // Uncheck all checkboxes and radios
    document.getElementById('modal-hypertension').checked = false;
    document.getElementById('modal-diabetes').checked = false;
    document.getElementById('modal-allergies-medications').checked = false;
    document.getElementById('modal-allergies-anesthetics').checked = false;
    document.querySelectorAll('input[type="radio"]').forEach(radio => radio.checked = false);
    
    // Clear signature
    document.getElementById('modal-signature-display').innerHTML = '<span style="color: #999;">No signature available</span>';
}

function closePatientModal() {
    const modal = document.getElementById('patientModal');
    modal.classList.remove('active');
}

function populatePatientModal(data) {
    const personalInfo = data.personal_information || {};
    const medicalInfo = data.medical_information || {};
    const emergencyContact = data.emergency_contact || {};
    const patient = data.patient || {};
    const appointmentData = data.appointment || {};
    
    // Personal Information
    const appointmentName = appointmentData.first_name
        ? `${appointmentData.first_name} ${appointmentData.middle_initial || ''} ${appointmentData.last_name || ''}`.trim()
        : '';
    const computedName = personalInfo.full_name
        || `${personalInfo.first_name || ''} ${personalInfo.middle_initial || ''} ${personalInfo.last_name || ''}`.trim()
        || appointmentName
        || patient.name
        || 'N/A';
    document.getElementById('modal-name').value = computedName;
    
    if (personalInfo.birthday) {
        const birthday = new Date(personalInfo.birthday);
        document.getElementById('modal-birthday').value = birthday.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } else if (patient.date_of_birth) {
        const birthday = new Date(patient.date_of_birth);
        document.getElementById('modal-birthday').value = birthday.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } else {
        document.getElementById('modal-birthday').value = '';
    }
    
    document.getElementById('modal-address').value = personalInfo.address || appointmentData.address || patient.address || '';
    document.getElementById('modal-contact-number').value = personalInfo.contact_number || patient.phone || patient.contact_phone || '';
    document.getElementById('modal-email').value = patient.email || '';
    
    // Civil Status
    if (personalInfo.civil_status) {
        const civilStatusRadios = document.querySelectorAll('input[name="modal-civil-status"]');
        civilStatusRadios.forEach(radio => {
            if (radio.value === personalInfo.civil_status) {
                radio.checked = true;
            }
        });
    }
    
    // Sex
    if (patient.gender) {
        const sexRadios = document.querySelectorAll('input[name="modal-sex"]');
        sexRadios.forEach(radio => {
            if (radio.value.toLowerCase() === patient.gender.toLowerCase()) {
                radio.checked = true;
            }
        });
    }
    
    // Preferred pronoun
    document.getElementById('modal-preferred-pronoun').value = personalInfo.preferred_pronoun || '';
    
    // Medical Information
    document.getElementById('modal-hypertension').checked = Boolean(medicalInfo.hypertension);
    document.getElementById('modal-diabetes').checked = Boolean(medicalInfo.diabetes);
    document.getElementById('modal-comorbidities-others').value = medicalInfo.comorbidities_others || '';
    
    if (medicalInfo.allergies) {
        try {
            const allergies = Array.isArray(medicalInfo.allergies) ? medicalInfo.allergies : JSON.parse(medicalInfo.allergies);
            document.getElementById('modal-allergies-medications').checked = allergies.includes('Medications') || allergies.includes('medications');
            document.getElementById('modal-allergies-anesthetics').checked = allergies.includes('Anesthetics') || allergies.includes('anesthetics');
            document.getElementById('modal-allergies-others').value = allergies.filter(item => !['Medications', 'Anesthetics', 'medications', 'anesthetics'].includes(item)).join(', ');
        } catch (error) {
            console.error('Error parsing allergies:', error);
        }
    } else {
        document.getElementById('modal-allergies-medications').checked = false;
        document.getElementById('modal-allergies-anesthetics').checked = false;
        document.getElementById('modal-allergies-others').value = medicalInfo.anesthetics_others || '';
    }
    
    document.getElementById('modal-previous-hospitalizations').value = medicalInfo.previous_hospitalizations_surgeries || '';
    
    // Smoker and Alcoholic Drinker
    if (medicalInfo.smoker !== undefined && medicalInfo.smoker !== null) {
        const smokerRadios = document.querySelectorAll('input[name="modal-smoker"]');
        smokerRadios.forEach(radio => {
            if ((radio.value === 'Yes' && medicalInfo.smoker) || (radio.value === 'No' && !medicalInfo.smoker)) {
                radio.checked = true;
            }
        });
    }
    
    if (medicalInfo.alcoholic_drinker !== undefined && medicalInfo.alcoholic_drinker !== null) {
        const alcoholRadios = document.querySelectorAll('input[name="modal-alcoholic-drinker"]');
        alcoholRadios.forEach(radio => {
            if ((radio.value === 'Yes' && medicalInfo.alcoholic_drinker) || (radio.value === 'No' && !medicalInfo.alcoholic_drinker)) {
                radio.checked = true;
            }
        });
    }
    
    document.getElementById('modal-known-family-illnesses').value = medicalInfo.known_family_illnesses || '';
    
    // Emergency Contact
    document.getElementById('modal-emergency-name').value = emergencyContact.name || '';
    document.getElementById('modal-emergency-relationship').value = emergencyContact.relationship || '';
    document.getElementById('modal-emergency-address').value = emergencyContact.address || '';
    document.getElementById('modal-emergency-contact-number').value = emergencyContact.contact_number || '';
    
    // Signature
    const signatureDisplay = document.getElementById('modal-signature-display');
    const signatureSrc = personalInfo.signature || '';
    
    if (signatureSrc) {
        if (signatureSrc.startsWith('data:image')) {
            signatureDisplay.innerHTML = `<img src="${signatureSrc}" alt="Signature" style="max-width: 100%; max-height: 150px;" />`;
        } else {
            // If it's a stored path, generate the full URL
            const fullPath = signatureSrc.startsWith('http') ? signatureSrc : `{{ asset('storage') }}/${signatureSrc}`;
            signatureDisplay.innerHTML = `<img src="${fullPath}" alt="Signature" style="max-width: 100%; max-height: 150px;" />`;
        }
    } else {
        signatureDisplay.innerHTML = '<span style="color: #999;">No signature available</span>';
    }
    
    // Date - prefer scheduled_date, fallback to booking (created_at)
    let bookingDateStr = appointmentData.scheduled_date || appointmentData.created_at || null;
    if (bookingDateStr) {
        const bookingDate = new Date(bookingDateStr);
        const month = String(bookingDate.getMonth() + 1).padStart(2, '0');
        const day = String(bookingDate.getDate()).padStart(2, '0');
        const year = bookingDate.getFullYear();
        document.getElementById('modal-date').value = `${month}/${day}/${year}`;
    } else {
        document.getElementById('modal-date').value = '';
    }
}

function confirmServiceSchedule(appointmentId) {
    const form = document.getElementById('confirmForm');
    form.action = `{{ url('/admin/my-services-schedules') }}/${appointmentId}/confirm`;
    // Reset form
    document.getElementById('scheduled_time').value = '';
    document.getElementById('scheduled_date').value = '';
    showModal('confirmModal');
}

function cancelServiceSchedule(appointmentId) {
    const form = document.getElementById('cancelForm');
    form.action = `{{ url('/admin/my-services-schedules') }}/${appointmentId}/cancel`;
    showModal('cancelModal');
}

function addServiceResult(appointmentId) {
    currentServiceAppointmentId = appointmentId;
    const form = document.getElementById('addResultForm');
    form.action = `{{ url('/admin/my-services-schedules') }}/${appointmentId}/result`;
    // Reset form
    form.reset();
    // Reset bullet lists to initial state
    resetBulletLists();
    showModal('addResultModal');
}

function closeConfirmModal() {
    hideModal();
}

function closeCancelModal() {
    document.getElementById('cancellation_reason').value = '';
    hideModal();
}

function closeAddResultModal() {
    document.getElementById('addResultForm').reset();
    resetBulletLists();
    currentServiceAppointmentId = null;
    hideModal();
}

// Bullet point management functions
function addBulletItem(containerId, fieldName) {
    const container = document.getElementById(containerId);
    const bulletItem = document.createElement('div');
    bulletItem.className = 'bullet-item';
    bulletItem.innerHTML = `
        <span style="color: #000000;">•</span>
        <input type="text" name="${fieldName}[]" placeholder="Enter ${fieldName.replace('_', ' ')} detail..." style="color: #000000;">
        <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)">Remove</button>
    `;
    container.appendChild(bulletItem);
    
    // Show remove buttons if there's more than one item
    updateRemoveButtons(containerId);
}

function removeBulletItem(button) {
    const container = button.closest('.bullet-list-container');
    button.closest('.bullet-item').remove();
    updateRemoveButtons(container.id);
}

function updateRemoveButtons(containerId) {
    const container = document.getElementById(containerId);
    const items = container.querySelectorAll('.bullet-item');
    const removeButtons = container.querySelectorAll('.remove-bullet-btn');
    
    if (items.length > 1) {
        removeButtons.forEach(btn => btn.style.display = 'block');
    } else {
        removeButtons.forEach(btn => btn.style.display = 'none');
    }
}

function resetBulletLists() {
    // Reset before condition
    const beforeList = document.getElementById('beforeConditionList');
    beforeList.innerHTML = `
        <div class="bullet-item">
            <span style="color: #000000;">•</span>
            <input type="text" name="before_condition[]" placeholder="Enter before condition detail..." style="color: #000000;">
            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)" style="display: none;">Remove</button>
        </div>
    `;
    
    // Reset result
    const resultList = document.getElementById('resultList');
    resultList.innerHTML = `
        <div class="bullet-item">
            <span style="color: #000000;">•</span>
            <input type="text" name="result[]" placeholder="Enter result detail..." style="color: #000000;">
            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)" style="display: none;">Remove</button>
        </div>
    `;
    
    // Reset procedures
    const proceduresList = document.getElementById('proceduresList');
    proceduresList.innerHTML = `
        <div class="bullet-item">
            <span style="color: #000000;">•</span>
            <input type="text" name="procedures[]" placeholder="Enter procedure detail..." style="color: #000000;">
            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)" style="display: none;">Remove</button>
        </div>
    `;
    
    // Reset medication
    const medicationList = document.getElementById('medicationList');
    medicationList.innerHTML = `
        <div class="bullet-item">
            <span style="color: #000000;">•</span>
            <input type="text" name="medication[]" placeholder="Enter medication detail..." style="color: #000000;">
            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)" style="display: none;">Remove</button>
        </div>
    `;
    
    // Reset follow-up
    document.getElementById('follow_up_required').checked = false;
    document.getElementById('followUpDateWrapper').style.display = 'none';
    document.getElementById('follow_up_date').value = '';
}

function toggleFollowUpDate() {
    const checkbox = document.getElementById('follow_up_required');
    const dateWrapper = document.getElementById('followUpDateWrapper');
    if (checkbox.checked) {
        dateWrapper.style.display = 'block';
    } else {
        dateWrapper.style.display = 'none';
        document.getElementById('follow_up_date').value = '';
    }
}

// Handle Add Result form submission
document.getElementById('addResultForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentServiceAppointmentId) {
        alert('Error: No appointment selected');
        return;
    }
    
    const form = e.target;
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        
        if (data.success) {
            alert(data.message || 'Result added successfully!');
            closeAddResultModal();
            window.location.reload();
        } else {
            alert(data.message || 'An error occurred while saving the result.');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        console.error('Error:', error);
        alert('An error occurred while saving the result. Please try again.');
    });
});

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const container = document.getElementById('modalContainer');
    if (e.target === container) {
        hideModal();
    }
});

// Past Pending Modal Functions
function openPastPendingModal() {
    const modal = document.getElementById('pastPendingModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closePastPendingModal() {
    const modal = document.getElementById('pastPendingModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Handle delete past appointment button clicks
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-past-appointment-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            let patientName = this.getAttribute('data-patient-name');
            const appointmentDate = this.getAttribute('data-appointment-date');
            const appointmentTime = this.getAttribute('data-appointment-time');
            
            // Parse JSON if it's encoded
            try {
                patientName = JSON.parse(patientName);
            } catch (e) {
                // If not JSON, use as-is
            }
            
            const timeStr = appointmentTime ? ` at ${appointmentTime}` : '';
            openDeleteModal({
                appointmentId,
                btn: this,
                type: 'pending',
                message: `Are you sure you want to delete the appointment for <strong>${patientName}</strong> on <strong>${appointmentDate}${timeStr}</strong>? The patient will be notified that their appointment was declined because they did not show up.`
            });
        });
    });
    // Attach to all drop-past-confirmed-btn buttons
    document.querySelectorAll('.drop-past-confirmed-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            let patientName = this.getAttribute('data-patient-name');
            const appointmentDate = this.getAttribute('data-appointment-date');
            const appointmentTime = this.getAttribute('data-appointment-time');
            try { patientName = JSON.parse(patientName); } catch (e) {}
            const timeStr = appointmentTime ? ` at ${appointmentTime}` : '';
            openDeleteModal({
                appointmentId,
                btn: this,
                type: 'confirmed',
                message: `Are you sure you want to drop the appointment for <strong>${patientName}</strong> on <strong>${appointmentDate}${timeStr}</strong>? This will delete the appointment and notify the patient that their appointment was declined because they did not show up.`
            });
        });
    });
    // Confirm delete button in modal
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (!deleteContext) return closeDeleteModal();
            const { appointmentId, btn, type } = deleteContext;
            if (!appointmentId) return closeDeleteModal();
            confirmBtn.disabled = true;
            confirmBtn.textContent = (type === 'confirmed') ? 'Dropping...' : 'Deleting...';
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('/admin/appointments') }}/${appointmentId}`;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            form.appendChild(csrfInput);
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            document.body.appendChild(form);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfInput.value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(new FormData(form))
            })
            .then(response => response.json().catch(() => null))
            .then(data => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = (type === 'confirmed') ? 'Yes, Drop' : 'Yes, Delete';
                if (data && data.success) {
                    if (btn) {
                        // Remove the appointment from the modal list if it exists
                        let appointmentItem = null;
                        if (type === 'pending') {
                            appointmentItem = btn.closest('div[style*="border: 2px solid #fbbf24"]');
                        } else if (type === 'confirmed') {
                            appointmentItem = btn.closest('div[style*="border: 2px solid #dc3545"]');
                        }
                        if (appointmentItem) {
                            appointmentItem.style.transition = 'opacity 0.3s';
                            appointmentItem.style.opacity = '0';
                            setTimeout(() => {
                                appointmentItem.remove();
                                // Check if modal list is empty
                                if (type === 'pending') {
                                    const pastPendingList = document.getElementById('pastPendingList');
                                    if (pastPendingList && pastPendingList.children.length === 0) {
                                        closePastPendingModal();
                                    }
                                } else if (type === 'confirmed') {
                                    const pastConfirmedList = document.getElementById('pastConfirmedList');
                                    if (pastConfirmedList && pastConfirmedList.children.length === 0) {
                                        closePastConfirmedModal();
                                    }
                                }
                            }, 300);
                        }
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                    closeDeleteModal();
                } else {
                    // Show error in modal
                    const error = document.getElementById('deleteErrorMsg');
                    if (error) {
                        error.style.display = 'block';
                        error.textContent = (data && data.message) ? data.message : 'Failed to delete appointment. Please try again.';
                    }
                }
            })
            .catch(error => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = (type === 'confirmed') ? 'Yes, Drop' : 'Yes, Delete';
                const errorMsg = document.getElementById('deleteErrorMsg');
                if (errorMsg) {
                    errorMsg.style.display = 'block';
                    errorMsg.textContent = 'An error occurred while deleting the appointment. Please try again.';
                }
            })
            .finally(() => {
                if (form.parentNode) {
                    form.parentNode.removeChild(form);
                }
            });
        });
    }
});

// --- Delete Confirmation Modal logic ---
let deleteContext = null;
function openDeleteModal(context) {
    deleteContext = context;
    const modal = document.getElementById('deleteConfirmModal');
    const info = document.getElementById('deleteTargetInfo');
    const error = document.getElementById('deleteErrorMsg');
    if (info && context) {
        info.innerHTML = context.message;
    }
    if (error) {
        error.style.display = 'none';
        error.textContent = '';
    }
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}
function closeDeleteModal() {
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
    deleteContext = null;
}

// Past Confirmed Modal Functions
function openPastConfirmedModal() {
    const modal = document.getElementById('pastConfirmedModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closePastConfirmedModal() {
    const modal = document.getElementById('pastConfirmedModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Handle drop past confirmed appointment button clicks
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.drop-past-confirmed-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            let patientName = this.getAttribute('data-patient-name');
            const appointmentDate = this.getAttribute('data-appointment-date');
            const appointmentTime = this.getAttribute('data-appointment-time');
            try { patientName = JSON.parse(patientName); } catch (e) {}
            const timeStr = appointmentTime ? ` at ${appointmentTime}` : '';
            openDeleteModal({
                appointmentId,
                btn: this,
                type: 'confirmed',
                message: `Are you sure you want to drop the appointment for <strong>${patientName}</strong> on <strong>${appointmentDate}${timeStr}</strong>? This will delete the appointment and notify the patient that their appointment was declined because they did not show up.`
            });
        });
    });

    // Show past confirmed modal after pending modal is closed (if both exist)
    @if(isset($pastPendingAppointments) && $pastPendingAppointments->isNotEmpty() && isset($pastConfirmedAppointments) && $pastConfirmedAppointments->isNotEmpty())
        const pastPendingModal = document.getElementById('pastPendingModal');
        if (pastPendingModal) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (!pastPendingModal.classList.contains('active')) {
                        // Pending modal was closed, show confirmed modal
                        setTimeout(() => {
                            openPastConfirmedModal();
                        }, 300);
                        observer.disconnect();
                    }
                });
            });
            observer.observe(pastPendingModal, { attributes: true, attributeFilter: ['class'] });
        }
    @endif
});
</script>
@endsection
