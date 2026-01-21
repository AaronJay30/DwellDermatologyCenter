@extends('layouts.dashboard')
@section('page-title', 'Appointments')

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

    .slots-table-header h2 {
        margin: 0;
        color: var(--primary-color);
        font-size: 1.15rem;
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

    /* Modal Styles */
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

    /* Add Result Modal Styles */
    .result-modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .result-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .result-modal-content {
        background-color: #ffffff;
        margin: auto;
        padding: 0;
        border: 3px solid #FFD700;
        width: 90%;
        max-width: 1000px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        border-radius: 5px;
    }

    .result-modal-header {
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

    .result-modal-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #197a8c;
        margin: 0;
    }

    .result-modal-close {
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

    .result-modal-close:hover,
    .result-modal-close:focus {
        color: #000;
    }

    .result-modal-body {
        padding: 2rem;
    }

    .result-form-section {
        background-color: #E6F3F5;
        border: 2px solid #FFD700;
        border-radius: 5px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .result-section-header {
        background-color: #008080;
        color: #ffffff;
        padding: 0.75rem 1rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        font-weight: bold;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 5px 5px 0 0;
    }

    .result-form-group {
        margin-bottom: 1.5rem;
    }

    .result-form-group:last-child {
        margin-bottom: 0;
    }

    .result-form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .result-form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 1rem;
        box-sizing: border-box;
        background-color: #ffffff;
        font-family: inherit;
        resize: vertical;
    }

    .file-upload-area {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .file-upload-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn-upload,
    .btn-camera {
        padding: 0.6rem 1.2rem;
        border: 2px solid #008080;
        background-color: #ffffff;
        color: #008080;
        border-radius: 5px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-upload:hover,
    .btn-camera:hover {
        background-color: #008080;
        color: #ffffff;
    }

    .file-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .file-preview-item {
        position: relative;
        border: 2px solid #e0e0e0;
        border-radius: 5px;
        overflow: hidden;
        background: #f5f5f5;
    }

    .file-preview-item img,
    .file-preview-item video {
        width: 100%;
        height: 120px;
        object-fit: cover;
        display: block;
    }

    .file-preview-item .file-name {
        padding: 0.5rem;
        font-size: 0.75rem;
        color: #333;
        word-break: break-all;
        text-align: center;
    }

    .file-preview-item .remove-file {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }

    .result-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-top: 2px solid #e0e0e0;
        background-color: #ffffff;
        position: sticky;
        bottom: 0;
    }

    .result-modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .result-modal-btn-cancel {
        background-color: #6c757d;
        color: white;
    }

    .result-modal-btn-cancel:hover {
        background-color: #5a6268;
    }

    .result-modal-btn-submit {
        background-color: #008080;
        color: white;
    }

    .result-modal-btn-submit:hover {
        background-color: #006666;
    }

    /* Bullet Notes Styles */
    .bullet-note-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .bullet-point {
        color: #008080;
        font-size: 1.2rem;
        font-weight: bold;
        flex-shrink: 0;
    }

    .bullet-input {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 1rem;
    }

    .btn-remove-bullet {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
        flex-shrink: 0;
    }

    .btn-remove-bullet:hover {
        background-color: #c82333;
    }

    .btn-add-bullet {
        font-size: 0.9rem;
    }

    .btn-add-bullet:hover {
        background-color: #006666 !important;
    }

    @media (max-width: 768px) {
        .slots-header {
            flex-direction: column;
            align-items: stretch;
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

        .slots-table-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="slots-header">
        <div class="slots-header-left">
            <span class="management-label">MY APPOINTMENT</span>
        </div>
        <div class="slots-header-right">
            <form method="GET" action="{{ route('admin.appointments') }}" class="search-wrapper" id="searchForm">
                <i data-feather="search" class="search-icon"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    class="slot-search-input"
                    placeholder="Search by patient, service, date..."
                    aria-label="Search appointments"
                    id="searchInput"
                >
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        @php
                            $patientName = $appointment->patient->name ?? trim(($appointment->first_name ?? '') . ' ' . ($appointment->middle_initial ?? '') . ' ' . ($appointment->last_name ?? '')) ?? 'N/A';
                        @endphp
                        <tr>
                            <td>
                                <div class="patient-info">
                                    <span class="patient-name-link" data-appointment-id="{{ $appointment->id }}" onclick="openPatientModal({{ $appointment->id }})">{{ $patientName }}</span>
                                </div>
                            </td>
                            <td>{{ $appointment->service->name ?? ($appointment->consultation_type ?? 'N/A') }}</td>
                            <td>
                                @if($appointment->timeSlot)
                                    {{ $appointment->timeSlot->date->format('M d, Y') }}
                                @elseif($appointment->doctorSlot && $appointment->doctorSlot->slot_date)
                                    {{ $appointment->doctorSlot->slot_date->format('M d, Y') }}
                                @else
                                    {{ $appointment->created_at->format('M d, Y') }}
                                @endif
                            </td>
                            <td>
                                @if($appointment->timeSlot)
                                    {{ $appointment->timeSlot->start_time }} - {{ $appointment->timeSlot->end_time }}
                                @elseif($appointment->doctorSlot)
                                    {{ optional($appointment->doctorSlot)->start_time?->format('H:i') ?? 'N/A' }} - {{ optional($appointment->doctorSlot)->end_time?->format('H:i') ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-booked">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button onclick="openAddResultModal({{ $appointment->id }})" class="btn-action btn-add-result" style="padding: 0.4rem 0.8rem; background-color: #008080; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">Add Result</button>
                                    <button onclick="deleteAppointment({{ $appointment->id }})" class="btn-action btn-delete" style="padding: 0.4rem 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:2rem; color:#6b7280;">
                                @if(empty($search))
                                    No booked appointments found.
                                @else
                                    No appointments match your search.
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
                                <input type="radio" name="modal-sex" value="Male" disabled>
                                Male
                            </label>
                            <label>
                                <input type="radio" name="modal-sex" value="Female" disabled>
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
            
            <button class="patient-modal-btn patient-modal-btn-download" onclick="downloadPatientInfo()">Download</button>
        </div>
    </div>
</div>

<!-- Add Result Modal -->
<div id="addResultModal" class="result-modal">
    <div class="result-modal-content">
        <div class="result-modal-header">
            <h2 class="result-modal-title">Add Result</h2>
            <button class="result-modal-close" onclick="closeAddResultModal()">&times;</button>
        </div>
        <form id="addResultForm" enctype="multipart/form-data">
            @csrf
            <div class="result-modal-body">
                <!-- BEFORE RESULT Section -->
                <div class="result-form-section">
                    <div class="result-section-header">BEFORE RESULT</div>
                    
                    <div class="result-form-group">
                        <label>Upload Photo/Video</label>
                        <div class="file-upload-area" id="beforeFilesArea">
                            <div class="file-upload-buttons">
                                <input type="file" name="before_files[]" id="beforeFiles" multiple accept="image/*,video/*" style="display: none;">
                                <button type="button" class="btn-upload" onclick="document.getElementById('beforeFiles').click()">
                                    <i data-feather="upload"></i> Choose Photo/Video
                                </button>
                                <button type="button" class="btn-camera" onclick="openCamera('beforeFiles')">
                                    <i data-feather="camera"></i> Open Camera
                                </button>
                            </div>
                            <div class="file-preview" id="beforeFilesPreview"></div>
                        </div>
                    </div>
                    
                    <div class="result-form-group">
                        <label>Notes (Optional)</label>
                        <div id="beforeNotesContainer">
                            <div class="bullet-note-item">
                                <span class="bullet-point">•</span>
                                <input type="text" name="before_notes[]" class="bullet-input" placeholder="Enter note...">
                                <button type="button" class="btn-remove-bullet" onclick="removeBulletItem(this)" style="display: none;">×</button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-bullet" onclick="addBulletItem('beforeNotesContainer')" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background-color: #008080; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Another</button>
                    </div>
                </div>
                
                <!-- AFTER RESULT Section -->
                <div class="result-form-section">
                    <div class="result-section-header">AFTER RESULT</div>
                    
                    <div class="result-form-group">
                        <label>Upload Photo/Video</label>
                        <div class="file-upload-area" id="afterFilesArea">
                            <div class="file-upload-buttons">
                                <input type="file" name="after_files[]" id="afterFiles" multiple accept="image/*,video/*" style="display: none;">
                                <button type="button" class="btn-upload" onclick="document.getElementById('afterFiles').click()">
                                    <i data-feather="upload"></i> Choose Photo/Video
                                </button>
                                <button type="button" class="btn-camera" onclick="openCamera('afterFiles')">
                                    <i data-feather="camera"></i> Open Camera
                                </button>
                            </div>
                            <div class="file-preview" id="afterFilesPreview"></div>
                        </div>
                    </div>
                    
                    <div class="result-form-group">
                        <label>Notes (Optional)</label>
                        <div id="afterNotesContainer">
                            <div class="bullet-note-item">
                                <span class="bullet-point">•</span>
                                <input type="text" name="after_notes[]" class="bullet-input" placeholder="Enter note...">
                                <button type="button" class="btn-remove-bullet" onclick="removeBulletItem(this)" style="display: none;">×</button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-bullet" onclick="addBulletItem('afterNotesContainer')" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background-color: #008080; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Another</button>
                    </div>
                </div>
                
                <!-- MEDICATION / PROCESS Section -->
                <div class="result-form-section">
                    <div class="result-section-header">MEDICATION / PROCESS</div>
                    
                    <div class="result-form-group">
                        <label>Step-by-step Instructions (Bullet Format)</label>
                        <div id="medicationInstructionsContainer">
                            <div class="bullet-note-item">
                                <span class="bullet-point">•</span>
                                <input type="text" name="medication_instructions[]" class="bullet-input" placeholder="Enter instruction step...">
                                <button type="button" class="btn-remove-bullet" onclick="removeBulletItem(this)" style="display: none;">×</button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-bullet" onclick="addBulletItem('medicationInstructionsContainer')" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background-color: #008080; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Another</button>
                    </div>
                    
                    <div class="result-form-group">
                        <label for="medicinesToTake">Medicines to Take</label>
                        <textarea name="medicines_to_take" id="medicinesToTake" rows="4" placeholder="Enter medicines, dosages, and schedules..."></textarea>
                    </div>
                </div>
            </div>
            <div class="result-modal-footer">
                <button type="button" class="result-modal-btn result-modal-btn-cancel" onclick="closeAddResultModal()">Cancel</button>
                <button type="submit" class="result-modal-btn result-modal-btn-submit">Save Result</button>
            </div>
        </form>
    </div>
</div>
@endsection

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

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                searchForm.submit();
            }, 500); // Submit after 500ms of no typing
        });

        // Also submit on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                searchForm.submit();
            }
        });
    }

    // Close modal when clicking outside
    const modal = document.getElementById('patientModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePatientModal();
            }
        });
    }
});

function openPatientModal(appointmentId) {
    const modal = document.getElementById('patientModal');
    modal.classList.add('active');
    
    // Clear previous data
    clearModal();
    
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
            
            populateModal(data);
        })
        .catch(error => {
            console.error('Error fetching patient info:', error);
            const loading = document.getElementById('modal-loading');
            if (loading) {
                loading.innerHTML = '<p style="color: red;">Error loading patient information. Please try again.</p><button onclick="closePatientModal()" class="patient-modal-btn patient-modal-btn-cancel" style="margin-top: 1rem;">Close</button>';
            }
        });
}

function clearModal() {
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

function populateModal(data) {
    const personalInfo = data.personal_information || {};
    const medicalInfo = data.medical_information || {};
    const emergencyContact = data.emergency_contact || {};
    const patient = data.patient || {};
    
    // Personal Information
    document.getElementById('modal-name').value = personalInfo.full_name || 
        `${personalInfo.first_name || ''} ${personalInfo.middle_initial || ''} ${personalInfo.last_name || ''}`.trim() ||
        patient.name || 'N/A';
    
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
    
    document.getElementById('modal-address').value = personalInfo.address || patient.address || '';
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
    
    document.getElementById('modal-preferred-pronoun').value = personalInfo.preferred_pronoun || '';
    
    // Medical Information
    document.getElementById('modal-hypertension').checked = medicalInfo.hypertension || false;
    document.getElementById('modal-diabetes').checked = medicalInfo.diabetes || false;
    document.getElementById('modal-comorbidities-others').value = medicalInfo.comorbidities_others || '';
    
    // Allergies
    const allergies = medicalInfo.allergies || '';
    document.getElementById('modal-allergies-medications').checked = allergies.includes('Medications');
    document.getElementById('modal-allergies-anesthetics').checked = medicalInfo.anesthetics || allergies.includes('Anesthetics');
    
    // Extract "Others" from allergies if it exists
    if (allergies && !allergies.includes('Medications') && !allergies.includes('Anesthetics')) {
        document.getElementById('modal-allergies-others').value = allergies;
    } else {
        document.getElementById('modal-allergies-others').value = medicalInfo.anesthetics_others || '';
    }
    
    document.getElementById('modal-previous-hospitalizations').value = medicalInfo.previous_hospitalizations_surgeries || '';
    
    // Smoker
    if (medicalInfo.smoker) {
        const smokerRadios = document.querySelectorAll('input[name="modal-smoker"]');
        smokerRadios.forEach(radio => {
            if (radio.value.toLowerCase() === medicalInfo.smoker.toLowerCase()) {
                radio.checked = true;
            }
        });
    }
    
    // Alcoholic drinker
    if (medicalInfo.alcoholic_drinker) {
        const alcoholicRadios = document.querySelectorAll('input[name="modal-alcoholic-drinker"]');
        alcoholicRadios.forEach(radio => {
            if (radio.value.toLowerCase() === medicalInfo.alcoholic_drinker.toLowerCase()) {
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
    if (personalInfo.signature) {
        // Check if it's a base64 data URL or a regular URL
        let signatureSrc = personalInfo.signature;
        if (!signatureSrc.startsWith('data:') && !signatureSrc.startsWith('http')) {
            // Assume it's base64, add data URL prefix
            signatureSrc = `data:image/png;base64,${signatureSrc}`;
        }
        signatureDisplay.innerHTML = `<img src="${signatureSrc}" alt="Signature" style="max-width: 100%; max-height: 150px;" />`;
    } else {
        signatureDisplay.innerHTML = '<span style="color: #999;">No signature available</span>';
    }
    
    // Date - use current date formatted as "MM/DD/YYYY"
    const today = new Date();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const year = today.getFullYear();
    document.getElementById('modal-date').value = `${month}/${day}/${year}`;
}

function printPatientInfo() {
    const modalContent = document.querySelector('#patientModal .patient-modal-content');
    if (!modalContent) {
        notifyUser('No patient information to print.', 'error');
        return;
    }
    const printWindow = window.open('', '_blank');
    
    // Clone the content to avoid modifying the original
    const clonedContent = modalContent.cloneNode(true);
    
    // Hide footer buttons and close button in the clone
    const footer = clonedContent.querySelector('.patient-modal-footer');
    if (footer) footer.style.display = 'none';
    const closeBtn = clonedContent.querySelector('.patient-modal-close');
    if (closeBtn) closeBtn.style.display = 'none';
    
    // Convert readonly inputs to display their values as text for printing
    // Get values from original modal to ensure they're captured correctly
    const originalInputs = modalContent.querySelectorAll('input[readonly], textarea[readonly]');
    const clonedInputs = clonedContent.querySelectorAll('input[readonly], textarea[readonly]');
    
    originalInputs.forEach((originalInput, index) => {
        const clonedInput = clonedInputs[index];
        if (clonedInput) {
            const value = originalInput.value || '';
            const wrapper = document.createElement('div');
            wrapper.className = 'print-value-display';
            wrapper.style.cssText = 'width: 100%; padding: 0.3rem 0.4rem; border: 1px solid #ccc; border-radius: 3px; font-size: 0.85rem; background-color: #ffffff; min-height: 1.5rem; display: flex; align-items: center; color: #2c3e50; white-space: pre-wrap;';
            wrapper.textContent = value || ' ';
            if (clonedInput.parentNode) {
                clonedInput.parentNode.replaceChild(wrapper, clonedInput);
            }
        }
    });
    
    // Convert image src to absolute URL
    const images = clonedContent.querySelectorAll('img');
    images.forEach(img => {
        const src = img.getAttribute('src');
        if (src && !src.startsWith('http') && !src.startsWith('data:')) {
            if (src.startsWith('/')) {
                img.src = window.location.origin + src;
            } else {
                img.src = window.location.origin + '/' + src;
            }
        }
    });
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <title>Patient Information Sheet</title>
                <style>
                    @page {
                        size: letter;
                        margin: 0.25in;
                    }
                    
                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        color-adjust: exact !important;
                        box-sizing: border-box;
                    }
                    
                    body {
                        font-family: 'Figtree', Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        color: #2c3e50;
                        background: white;
                        font-size: 11px;
                        line-height: 1.3;
                    }
                    
                    .patient-modal-content {
                        background-color: #ffffff;
                        margin: 0;
                        padding: 0;
                        border: 2px solid #FFD700;
                        width: 100%;
                        max-width: 100%;
                        box-shadow: none;
                    }
                    
                    .patient-modal-header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 0.5rem 0.8rem;
                        border-bottom: 1px solid #e0e0e0;
                        background-color: #ffffff;
                    }
                    
                    .patient-modal-logo {
                        display: flex;
                        align-items: center;
                        gap: 0.3rem;
                    }
                    
                    .patient-modal-logo img {
                        width: 35px;
                        height: 35px;
                    }
                    
                    .patient-modal-logo-text {
                        font-size: 0.9rem;
                        font-weight: bold;
                        color: #197a8c;
                    }
                    
                    .patient-modal-title {
                        font-size: 1rem;
                        font-weight: bold;
                        color: #000000;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        margin: 0;
                    }
                    
                    .patient-modal-close {
                        display: none !important;
                    }
                    
                    .patient-modal-body {
                        padding: 0.6rem 0.8rem;
                    }
                    
                    .patient-form-section {
                        background-color: #E6F3F5 !important;
                        border: 2px solid #FFD700 !important;
                        border-radius: 3px;
                        padding: 0.6rem;
                        margin-bottom: 0.6rem;
                        page-break-inside: avoid;
                    }
                    
                    .patient-section-header {
                        background-color: #008080 !important;
                        color: #ffffff !important;
                        padding: 0.4rem 0.6rem;
                        margin: -0.6rem -0.6rem 0.6rem -0.6rem;
                        font-weight: bold;
                        font-size: 0.75rem;
                        text-transform: uppercase;
                        letter-spacing: 0.3px;
                    }
                    
                    .patient-form-row {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 0.5rem;
                        margin-bottom: 0.5rem;
                    }
                    
                    .patient-form-group {
                        margin-bottom: 0.4rem;
                    }
                    
                    .patient-form-group label {
                        display: block;
                        margin-bottom: 0.2rem;
                        font-weight: 600;
                        color: #333;
                        font-size: 0.7rem;
                    }
                    
                    .patient-form-group input[type="text"],
                    .patient-form-group input[type="email"],
                    .patient-form-group input[type="date"],
                    .patient-form-group textarea,
                    .patient-form-group div {
                        width: 100%;
                        padding: 0.3rem 0.4rem;
                        border: 1px solid #ccc;
                        border-radius: 2px;
                        font-size: 0.85rem;
                        background-color: #ffffff;
                        min-height: 1.5rem;
                        display: block;
                    }
                    
                    .patient-form-group textarea {
                        min-height: 40px;
                        resize: none;
                    }
                    
                    .patient-radio-group {
                        display: flex;
                        gap: 1rem;
                        align-items: center;
                        flex-wrap: wrap;
                    }
                    
                    .patient-radio-group label {
                        display: flex;
                        align-items: center;
                        gap: 0.3rem;
                        font-weight: normal;
                        font-size: 0.8rem;
                        cursor: default;
                    }
                    
                    .patient-checkbox-group {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 0.8rem;
                        margin-bottom: 0.3rem;
                    }
                    
                    .patient-checkbox-group label {
                        display: flex;
                        align-items: center;
                        gap: 0.3rem;
                        font-weight: normal;
                        font-size: 0.8rem;
                        cursor: default;
                    }
                    
                    .patient-certification-section {
                        margin-top: 0.8rem;
                        padding-top: 0.6rem;
                        border-top: 1px solid #e0e0e0;
                    }
                    
                    .patient-certification-text {
                        margin-bottom: 0.6rem;
                        font-size: 0.8rem;
                        color: #333;
                    }
                    
                    .patient-signature-section {
                        display: grid;
                        grid-template-columns: 2fr 1fr;
                        gap: 1rem;
                        margin-top: 0.5rem;
                    }
                    
                    .patient-signature-field {
                        display: flex;
                        flex-direction: column;
                    }
                    
                    .patient-signature-field label {
                        margin-bottom: 0.3rem;
                        font-weight: 600;
                        color: #333;
                        font-size: 0.7rem;
                    }
                    
                    .patient-signature-display {
                        border: 1px solid #ccc;
                        border-radius: 3px;
                        padding: 0.4rem;
                        background: white;
                        min-height: 50px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    
                    .patient-signature-display img {
                        max-width: 100%;
                        max-height: 80px;
                    }
                    
                    .patient-modal-footer {
                        display: none !important;
                    }
                    
                    input[type="radio"],
                    input[type="checkbox"] {
                        width: auto;
                        margin: 0;
                        padding: 0;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                    
                    input[type="radio"]:checked::before,
                    input[type="checkbox"]:checked::before {
                        content: '✓';
                        display: inline-block;
                        width: 12px;
                        height: 12px;
                        background-color: #008080;
                        color: white;
                        text-align: center;
                        line-height: 12px;
                        font-size: 10px;
                        border: 1px solid #008080;
                    }
                    
                    .print-value-display {
                        width: 100%;
                        padding: 0.3rem 0.4rem;
                        border: 1px solid #ccc;
                        border-radius: 3px;
                        font-size: 0.85rem;
                        background-color: #ffffff;
                        min-height: 1.5rem;
                        display: flex;
                        align-items: center;
                        color: #2c3e50;
                    }
                </style>
            </head>
            <body>
                ${clonedContent.innerHTML}
            </body>
        </html>
    `);
    printWindow.document.close();
    
    // Wait for images to load before printing
    printWindow.onload = function() {
        setTimeout(function() {
            printWindow.print();
        }, 250);
    };
}

function downloadPatientInfo() {
    // For now, trigger print which allows saving as PDF
    printPatientInfo();
}

// Add Result Modal Functions
let currentAppointmentId = null;

function openAddResultModal(appointmentId) {
    currentAppointmentId = appointmentId;
    const modal = document.getElementById('addResultModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Reset form
    document.getElementById('addResultForm').reset();
    clearAllPreviews();
    resetBulletNotes();
    
    // Replace feather icons
    if (window.feather && typeof window.feather.replace === 'function') {
        window.feather.replace();
    }
}

function closeAddResultModal() {
    const modal = document.getElementById('addResultModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    currentAppointmentId = null;
    document.getElementById('addResultForm').reset();
    clearAllPreviews();
    resetBulletNotes();
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const resultModal = document.getElementById('addResultModal');
    if (resultModal) {
        resultModal.addEventListener('click', function(e) {
            if (e.target === resultModal) {
                closeAddResultModal();
            }
        });
    }
    
    // Handle file inputs (combined photo/video)
    setupFileInput('beforeFiles', 'beforeFilesPreview');
    setupFileInput('afterFiles', 'afterFilesPreview');
    
    // Reset bullet notes on modal open
    resetBulletNotes();
    
    // Handle form submission
    const addResultForm = document.getElementById('addResultForm');
    if (addResultForm) {
        addResultForm.addEventListener('submit', handleResultSubmit);
    }
});

function setupFileInput(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    
    if (input && preview) {
        input.addEventListener('change', function(e) {
            handleFileSelect(e, previewId, inputId);
        });
    }
}

function handleFileSelect(event, previewId, inputId) {
    const files = Array.from(event.target.files);
    const preview = document.getElementById(previewId);
    
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item';
            
            // Determine if it's an image or video
            if (file.type.startsWith('image/')) {
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <div class="file-name">${file.name}</div>
                    <button type="button" class="remove-file" onclick="removeFile(this, '${inputId}')">&times;</button>
                `;
            } else if (file.type.startsWith('video/')) {
                previewItem.innerHTML = `
                    <video src="${e.target.result}" controls></video>
                    <div class="file-name">${file.name}</div>
                    <button type="button" class="remove-file" onclick="removeFile(this, '${inputId}')">&times;</button>
                `;
            }
            
            preview.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

function removeFile(button, inputId) {
    const previewItem = button.closest('.file-preview-item');
    const previewContainer = previewItem.parentElement;
    previewItem.remove();
    
    // Remove file from input by recreating the file list
    const input = document.getElementById(inputId);
    if (input) {
        const dt = new DataTransfer();
        const remainingItems = previewContainer.querySelectorAll('.file-preview-item');
        
        // Get all files from input
        const allFiles = Array.from(input.files);
        
        // Keep only files that still have preview items
        remainingItems.forEach((item, index) => {
            if (allFiles[index]) {
                dt.items.add(allFiles[index]);
            }
        });
        
        input.files = dt.files;
    }
}

function clearAllPreviews() {
    ['beforeFilesPreview', 'afterFilesPreview'].forEach(id => {
        const preview = document.getElementById(id);
        if (preview) {
            preview.innerHTML = '';
        }
    });
}

function openCamera(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        // Set capture attribute to use camera (environment = back camera, user = front camera)
        // For mobile devices, this will open the camera directly
        input.setAttribute('capture', 'environment');
        // Keep accept attribute to allow both images and videos
        input.setAttribute('accept', 'image/*,video/*');
        input.click();
        // Note: On desktop, this may still show file picker, but on mobile devices with cameras,
        // the capture attribute will trigger the camera directly
    }
}

// Bullet Notes Functions
function addBulletItem(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const bulletItem = document.createElement('div');
    bulletItem.className = 'bullet-note-item';
    bulletItem.innerHTML = `
        <span class="bullet-point">•</span>
        <input type="text" name="${getFieldName(containerId)}[]" class="bullet-input" placeholder="Enter note...">
        <button type="button" class="btn-remove-bullet" onclick="removeBulletItem(this)">×</button>
    `;
    
    container.appendChild(bulletItem);
    
    // Show remove buttons if there's more than one item
    updateRemoveButtons(containerId);
}

function removeBulletItem(button) {
    const container = button.closest('.result-form-group').querySelector('[id$="Container"]');
    const item = button.closest('.bullet-note-item');
    item.remove();
    
    // If only one item left, hide remove button
    if (container) {
        updateRemoveButtons(container.id);
        // If no items left, add one back
        if (container.querySelectorAll('.bullet-note-item').length === 0) {
            addBulletItem(container.id);
        }
    }
}

function updateRemoveButtons(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const items = container.querySelectorAll('.bullet-note-item');
    items.forEach((item, index) => {
        const removeBtn = item.querySelector('.btn-remove-bullet');
        if (removeBtn) {
            removeBtn.style.display = items.length > 1 ? 'flex' : 'none';
        }
    });
}

function getFieldName(containerId) {
    if (containerId === 'beforeNotesContainer') return 'before_notes';
    if (containerId === 'afterNotesContainer') return 'after_notes';
    if (containerId === 'medicationInstructionsContainer') return 'medication_instructions';
    return 'notes';
}

function resetBulletNotes() {
    // Reset all bullet note containers to have one empty item
    ['beforeNotesContainer', 'afterNotesContainer', 'medicationInstructionsContainer'].forEach(containerId => {
        const container = document.getElementById(containerId);
        if (container) {
            const fieldName = getFieldName(containerId);
            container.innerHTML = `
                <div class="bullet-note-item">
                    <span class="bullet-point">•</span>
                    <input type="text" name="${fieldName}[]" class="bullet-input" placeholder="Enter note...">
                    <button type="button" class="btn-remove-bullet" onclick="removeBulletItem(this)" style="display: none;">×</button>
                </div>
            `;
        }
    });
}

function deleteAppointment(appointmentId) {
    if (!confirm('Are you sure you want to delete this appointment? This action cannot be undone.')) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    
    fetch(`{{ url('/admin/appointments') }}/${appointmentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error deleting appointment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the appointment');
    });
}

function handleResultSubmit(e) {
    e.preventDefault();
    
    if (!currentAppointmentId) {
        alert('Error: No appointment selected');
        return;
    }
    
    const form = e.target;
    const formData = new FormData(form);
    
    // Remove array entries first
    formData.delete('before_notes[]');
    formData.delete('after_notes[]');
    formData.delete('medication_instructions[]');
    
    // Combine bullet notes into single strings (only set if not empty)
    const beforeNotesArray = Array.from(form.querySelectorAll('input[name="before_notes[]"]'))
        .map(input => input.value.trim())
        .filter(val => val !== '');
    if (beforeNotesArray.length > 0) {
        formData.set('before_notes', '• ' + beforeNotesArray.join('\n• '));
    }
    
    const afterNotesArray = Array.from(form.querySelectorAll('input[name="after_notes[]"]'))
        .map(input => input.value.trim())
        .filter(val => val !== '');
    if (afterNotesArray.length > 0) {
        formData.set('after_notes', '• ' + afterNotesArray.join('\n• '));
    }
    
    const medicationInstructionsArray = Array.from(form.querySelectorAll('input[name="medication_instructions[]"]'))
        .map(input => input.value.trim())
        .filter(val => val !== '');
    if (medicationInstructionsArray.length > 0) {
        formData.set('medication_instructions', '• ' + medicationInstructionsArray.join('\n• '));
    }
    
    // Show loading state
    const submitBtn = form.querySelector('.result-modal-btn-submit');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';
    
    fetch(`{{ url('/admin/appointments') }}/${currentAppointmentId}/result`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Result added successfully!');
            closeAddResultModal();
            location.reload();
        } else {
            alert(data.message || 'Error saving result');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the result');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}
</script>
@endpush
