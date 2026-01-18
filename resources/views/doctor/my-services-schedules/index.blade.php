@extends('layouts.dashboard')
@section('page-title', 'My Services Schedules')

@section('navbar-links')
    @include('partials.doctor_nav')
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
        width: 100%;
        box-sizing: border-box;
    }

    .slots-header-left {
        flex: 1;
        min-width: 0;
    }

    .slots-header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 0;
    }

    .management-label {
        font-family: 'Figtree', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        letter-spacing: 0.5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        flex: 0 1 auto;
        min-width: 0;
        max-width: 100%;
    }

    .slot-search-input {
        width: 280px;
        max-width: 100%;
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
        box-sizing: border-box;
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
        box-sizing: border-box;
    }

    .slots-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 1rem;
        flex-wrap: wrap;
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
        box-sizing: border-box;
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
        box-sizing: border-box;
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
        box-sizing: border-box;
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
        flex-shrink: 0;
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
    .form-group input[type="time"],
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        color: #000000;
        background: #ffffff;
        box-sizing: border-box;
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

    /* Responsive Design - Tablets */
    @media (max-width: 992px) {
        .container {
            padding: 0 1rem;
        }

        .modal-content {
            width: min(900px, 92vw);
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 0.75rem;
        }

        .slots-header {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 1.5rem;
        }

        .slots-header-left {
            width: 100%;
        }

        .management-label {
            font-size: 0.9rem;
        }

        .slots-header-right {
            flex-direction: column;
            width: 100%;
            gap: 0.75rem;
        }

        .search-wrapper,
        .slot-search-input {
            width: 100%;
        }

        .slot-search-input {
            padding-left: 40px;
            min-height: 44px;
        }

        .slots-table-card {
            padding: 0.75rem;
        }

        .slots-table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
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
            content: "Patient";
        }
        
        table tbody td:nth-child(2)::before {
            content: "Service";
        }
        
        table tbody td:nth-child(3)::before {
            content: "Branch";
        }
        
        table tbody td:nth-child(4)::before {
            content: "Date";
        }
        
        table tbody td:nth-child(5)::before {
            content: "Status";
        }
        
        table tbody td:nth-child(6)::before {
            content: "Action";
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

        .status-badge {
            align-self: flex-start;
        }

        .patient-info {
            align-items: center;
            width: 100%;
        }

        .patient-name-link {
            text-align: center;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0;
        }

        table tbody td:nth-child(6) > div {
            width: 100%;
            flex-direction: column;
        }

        table tbody td:nth-child(6) button {
            width: 100%;
            margin-bottom: 0.5rem;
            min-height: 44px;
        }

        table tbody td:nth-child(6) button:last-child {
            margin-bottom: 0;
        }

        /* Modal responsive */
        .modal-content {
            width: 95%;
            max-width: 100%;
        }

        .modal-header {
            padding: 1rem;
            flex-wrap: wrap;
        }

        .modal-body {
            padding: 1rem;
        }

        .modal-section {
            padding: 1rem;
        }

        .modal-section-header {
            margin: -1rem -1rem 1rem -1rem;
            font-size: 1rem;
        }

        .bullet-item {
            flex-wrap: wrap;
        }

        .bullet-item input {
            width: 100%;
            min-width: 0;
        }

        .remove-bullet-btn {
            width: 100%;
            margin-top: 0.25rem;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding: 0 0.5rem;
        }

        .slots-header {
            margin-bottom: 1rem;
        }

        .management-label {
            font-size: 0.85rem;
        }

        .slots-table-card {
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

        table tbody td:nth-child(6) button {
            padding: 0.5rem;
            font-size: 0.8rem;
        }

        .modal-content {
            width: 98%;
        }

        .modal-header {
            padding: 0.75rem;
        }

        .modal-header span {
            font-size: 1rem;
        }

        .modal-body {
            padding: 0.75rem;
        }

        .modal-section {
            padding: 0.65rem;
        }

        .modal-section-header {
            font-size: 0.75rem;
            padding: 0.4rem 0.6rem;
            margin: -0.65rem -0.65rem 0.65rem -0.65rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            font-size: 0.9rem;
            padding: 0.6rem;
        }

        .bullet-item input {
            font-size: 0.9rem;
            padding: 0.4rem;
        }

        .add-bullet-btn {
            width: 100%;
            padding: 0.5rem;
        }
    }

    @media (max-width: 400px) {
        table tbody td {
            font-size: 0.8rem;
        }

        table tbody td:nth-child(6) button {
            padding: 0.4rem;
            font-size: 0.75rem;
            min-height: 40px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="slots-header">
        <div class="slots-header-left">
            <span class="management-label">MY SERVICES SCHEDULES</span>
        </div>
        <div class="slots-header-right">
            <form method="GET" action="{{ route('doctor.my-services-schedules') }}" class="search-wrapper" id="searchForm">
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
                        <th>Branch</th>
                        <th>Date</th>
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
                                    <span class="patient-name-link">{{ $patientName }}</span>
                                </div>
                            </td>
                            <td>{{ $appointment->service->name ?? 'N/A' }}</td>
                            <td>{{ $appointment->branch->name ?? 'N/A' }}</td>
                            <td>
                                @if($appointment->scheduled_date)
                                    {{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('M d, Y') }}
                                    @if($appointment->scheduled_time)
                                        <br><small style="color: #6c757d;">{{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}</small>
                                    @endif
                                @else
                                    {{ $appointment->created_at->format('M d, Y') }}
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
                                @endphp
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    @if($isPastPending)
                                        <button type="button" 
                                                class="delete-past-appointment-btn" 
                                                data-appointment-id="{{ $appointment->id }}"
                                                data-patient-name="{{ json_encode($patientName) }}"
                                                data-appointment-date="{{ $appointmentDate->format('M d, Y') }}"
                                                data-appointment-time="{{ $appointment->scheduled_time ? \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') : '' }}"
                                                style="padding: 0.4rem 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                                            Delete
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
<div id="pastPendingModal" class="modal-container">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <span style="color: #000000;">⚠️ Past Pending Appointments</span>
            <button type="button" onclick="closePastPendingModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #000000;">&times;</button>
        </div>
        <div class="modal-body">
            <p style="margin-bottom: 1rem; color: #6c757d;">
                The following service appointments have dates in the past and are still pending. These appointments need to be dropped.
            </p>
            <div id="pastPendingList" style="max-height: 400px; overflow-y: auto;">
                @foreach($pastPendingAppointments ?? [] as $appointment)
                    @php
                        $patientName = $appointment->patient->name ?? trim(($appointment->first_name ?? '') . ' ' . ($appointment->middle_initial ?? '') . ' ' . ($appointment->last_name ?? '')) ?? 'N/A';
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
        <div style="display:flex; justify-content:flex-end; gap:.5rem; padding: 1rem; border-top: 1px solid #e9ecef;">
            <button type="button" onclick="closePastPendingModal()" style="padding: 0.75rem 1.5rem; background-color: #e5e7eb; color: #2c3e50; border: none; border-radius: 4px; cursor: pointer; font-size: 0.95rem; font-weight: 500;">Close</button>
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

function confirmServiceSchedule(appointmentId) {
    const form = document.getElementById('confirmForm');
    form.action = `{{ url('/doctor/my-services-schedules') }}/${appointmentId}/confirm`;
    // Reset form
    document.getElementById('scheduled_time').value = '';
    document.getElementById('scheduled_date').value = '';
    showModal('confirmModal');
}

function cancelServiceSchedule(appointmentId) {
    const form = document.getElementById('cancelForm');
    form.action = `{{ url('/doctor/my-services-schedules') }}/${appointmentId}/cancel`;
    showModal('cancelModal');
}

function addServiceResult(appointmentId) {
    currentServiceAppointmentId = appointmentId;
    const form = document.getElementById('addResultForm');
    form.action = `{{ url('/doctor/my-services-schedules') }}/${appointmentId}/result`;
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
            if (!confirm(`Are you sure you want to delete the appointment for ${patientName} on ${appointmentDate}${timeStr}? The patient will be notified that their appointment was declined because they did not show up.`)) {
                return;
            }
            
            // Delete the appointment via AJAX
            const submitButton = this;
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Deleting...';
            
            // Create a form to submit DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/doctor/my-appointments/${appointmentId}`;
            
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Appointment deleted successfully! The patient has been notified.');
                    
                    // Remove the appointment from the modal list if it exists
                    const appointmentItem = submitButton.closest('div[style*="border: 2px solid #fbbf24"]');
                    if (appointmentItem) {
                        appointmentItem.style.transition = 'opacity 0.3s';
                        appointmentItem.style.opacity = '0';
                        setTimeout(() => {
                            appointmentItem.remove();
                            
                            // Check if modal list is empty
                            const pastPendingList = document.getElementById('pastPendingList');
                            if (pastPendingList && pastPendingList.children.length === 0) {
                                closePastPendingModal();
                            }
                        }, 300);
                    }
                    
                    // Reload page to refresh the table
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    alert(data.message || 'Failed to delete appointment. Please try again.');
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the appointment. Please try again.');
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            })
            .finally(() => {
                // Clean up the form
                if (form.parentNode) {
                    form.parentNode.removeChild(form);
                }
            });
        });
    });

    // Show past pending appointments modal on page load if there are any
    @if(isset($pastPendingAppointments) && $pastPendingAppointments->isNotEmpty())
        openPastPendingModal();
    @endif
});
</script>
@endsection
