@extends('layouts.dashboard')
@section('page-title', 'View Patient History')

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<style>
    .patient-history-container {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 2rem;
        margin-top: 1.5rem;
    }

    .patient-info-panel {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1.5rem;
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    .patient-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 1.5rem;
        display: block;
        border: 3px solid #197a8c;
    }

    .patient-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2c3e50;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .patient-detail-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .patient-detail-item:last-child {
        border-bottom: none;
    }

    .patient-detail-label {
        font-weight: 500;
        color: #666;
        font-size: 0.9rem;
    }

    .patient-detail-value {
        color: #2c3e50;
        font-weight: 600;
        text-align: right;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 1.5rem 0 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .section-title:first-child {
        margin-top: 0;
    }

    .view-all-link {
        font-size: 0.85rem;
        color: #197a8c;
        text-decoration: none;
    }

    .view-all-link:hover {
        text-decoration: underline;
    }

    .allergy-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .allergy-item:last-child {
        border-bottom: none;
    }

    .allergy-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .allergy-reaction {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 0.5rem;
    }

    .severity-indicator {
        display: flex;
        gap: 4px;
    }

    .severity-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #e0e0e0;
    }

    .severity-dot.filled {
        background: #dc3545;
    }

    .history-panel {
        background: #ffffff;
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .profile-filter-form {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .profile-history-section {
        margin-bottom: 2.5rem;
    }
    
    .profile-section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .history-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2c3e50;
    }

    .timeline-visualization {
        height: 60px;
        background: linear-gradient(to right, #e0e0e0 0%, #197a8c 30%, #197a8c 70%, #e0e0e0 100%);
        border-radius: 4px;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .timeline-label {
        position: absolute;
        bottom: 5px;
        left: 10px;
        font-size: 0.75rem;
        color: #666;
    }

    .date-filters {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .date-filter-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        background: #ffffff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s;
    }

    .date-filter-btn:hover,
    .date-filter-btn.active {
        background: #197a8c;
        color: #ffffff;
        border-color: #197a8c;
    }

    .date-range-inputs {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        margin-left: auto;
    }

    .date-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .date-input-wrapper input {
        padding: 0.5rem 2rem 0.5rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.85rem;
    }

    .calendar-icon {
        position: absolute;
        right: 8px;
        width: 16px;
        height: 16px;
        color: #666;
        pointer-events: none;
    }

    .history-timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline-line {
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .year-group {
        margin-bottom: 2.5rem;
    }

    .year-header {
        font-size: 1.25rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 1rem;
        padding-left: 1rem;
    }

    .annual-report-bar {
        background: #8b5cf6;
        color: #ffffff;
        padding: 0.75rem 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s;
    }

    .annual-report-bar:hover {
        background: #7c3aed;
    }

    .history-item {
        background: #f8f9fa;
        border-left: 3px solid #197a8c;
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 4px;
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .history-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .history-item::before {
        content: '';
        position: absolute;
        left: -10px;
        top: 1rem;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #197a8c;
        border: 3px solid #ffffff;
    }

    .history-item-title {
        font-size: 1.1rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .history-item-details {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 0.25rem;
    }

    .history-item-date {
        font-size: 0.85rem;
        color: #999;
        margin-top: 0.5rem;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #6c757d;
        color: #ffffff;
        text-decoration: none;
        border-radius: 4px;
        font-size: 0.9rem;
        transition: background 0.3s;
        margin-bottom: 1rem;
    }

    .back-button:hover {
        background: #5a6268;
        color: #ffffff;
    }

    /* Annual Report Modal - Full Screen Overlay */
    .annual-report-modal {
        display: none;
        position: fixed;
        z-index: 99999;
        left: 0 !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
        box-sizing: border-box;
        margin: 0 !important;
        padding: 0 !important;
    }

    .annual-report-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px !important;
    }

    .annual-report-modal-content {
        background-color: #ffffff;
        margin: auto;
        padding: 0;
        border: 3px solid #FFD700;
        width: 90%;
        max-width: 850px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        overflow-x: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        position: relative;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .annual-report-modal-header {
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

    .annual-report-modal-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #197a8c;
        margin: 0;
    }

    .annual-report-modal-close {
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

    .annual-report-modal-close:hover {
        color: #000;
    }

    .annual-report-modal-body {
        padding: 1.5rem;
        box-sizing: border-box;
        width: 100%;
        overflow-x: hidden;
    }

    .report-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid #e0e0e0;
        box-sizing: border-box;
    }

    .report-section:last-child {
        border-bottom: none;
    }

    .result-form-section {
        background-color: #E6F3F5;
        border: 2px solid #FFD700;
        border-radius: 5px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-sizing: border-box;
        width: 100%;
        overflow: hidden;
    }

    .result-section-header {
        background-color: #008080;
        color: #ffffff;
        padding: 0.75rem 1rem;
        margin: -1.25rem -1.25rem 1.25rem -1.25rem;
        font-weight: bold;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 5px 5px 0 0;
    }

    .result-form-group {
        margin-bottom: 1.5rem;
        box-sizing: border-box;
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

    .result-form-group input[type="date"] {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 1rem;
        box-sizing: border-box;
        background-color: #ffffff;
        font-family: inherit;
    }

    .result-form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 1rem;
        box-sizing: border-box;
        background-color: #ffffff;
        font-family: inherit;
    }

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

    .bullet-text {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 1rem;
        background-color: #ffffff;
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

    .report-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }

    .report-item-title {
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .report-item-details {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 0.5rem;
    }

    .report-item-date {
        font-size: 0.85rem;
        color: #999;
    }

    .report-photos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .report-photo-item {
        position: relative;
        border-radius: 4px;
        overflow: hidden;
    }

    .report-photo-item img,
    .report-photo-item video {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .report-photo-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.5rem;
        font-size: 0.75rem;
        text-align: center;
    }

    .report-medication-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .report-medication-list li {
        padding: 0.5rem 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .report-medication-list li:last-child {
        border-bottom: none;
    }

    .report-notes {
        white-space: pre-wrap;
        line-height: 1.6;
    }

    /* For screens with sidebar visible (1024px to 1320px) - offset modal to account for sidebar */
    @media (min-width: 1025px) and (max-width: 1320px) {
        .annual-report-modal.active {
            padding-left: 250px; /* Account for sidebar width (230px) + some margin */
        }
        
        .annual-report-modal-content {
            width: calc(100% - 280px);
            max-width: 750px;
            margin-left: auto;
            margin-right: auto;
        }
    }

    @media (max-width: 1024px) {
        .patient-history-container {
            grid-template-columns: 1fr;
        }

        .patient-info-panel {
            position: static;
        }
        
        .annual-report-modal-content {
            width: calc(100% - 20px);
            max-width: 800px;
        }
    }
    
    @media (max-width: 768px) {
        .annual-report-modal.active {
            padding: 10px;
        }
        
        .annual-report-modal-content {
            width: calc(100% - 20px);
            max-width: none;
            margin: 0 auto;
            max-height: calc(100vh - 20px);
        }
        
        .annual-report-modal-header {
            padding: 1rem;
        }
        
        .annual-report-modal-title {
            font-size: 1.1rem;
        }
        
        .annual-report-modal-body {
            padding: 1rem;
        }
        
        .result-form-section {
            padding: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .result-section-header {
            font-size: 0.9rem;
            padding: 0.6rem 0.875rem;
            margin: -0.875rem -0.875rem 1rem -0.875rem;
        }
        
        .file-preview {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.75rem;
        }
        
        .file-preview-item img,
        .file-preview-item video {
            height: 100px;
        }
        
        .bullet-note-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .bullet-text {
            width: 100%;
        }
    }
    
    
    @media (max-width: 480px) {
        .annual-report-modal.active {
            padding: 0;
        }
        
        .annual-report-modal-content {
            width: 100%;
            max-height: 100vh;
            border-radius: 0;
            border: none;
        }
        
        .annual-report-modal-header {
            padding: 0.75rem 1rem;
        }
        
        .annual-report-modal-title {
            font-size: 1rem;
        }
        
        .annual-report-modal-close {
            font-size: 24px;
            width: 28px;
            height: 28px;
        }
        
        .annual-report-modal-body {
            padding: 0.75rem;
        }
        
        .result-form-section {
            padding: 0.75rem;
        }
        
        .result-section-header {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
            margin: -0.75rem -0.75rem 0.75rem -0.75rem;
        }
        
        .file-preview {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.patients') }}" class="back-button">
            ← Back to Patient History
        </a>
        @if($canEdit)
            <div class="d-flex gap-2">
                <a href="{{ route('admin.patients.history.update', $patient->id) }}" class="btn btn-primary">
                    Update History
                </a>
            </div>
        @else
            <div class="alert alert-info mb-0" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> View-only mode: This patient belongs to a different branch. You can view but cannot edit or delete.
            </div>
        @endif
    </div>

    <div class="patient-history-container">
        <!-- Left Panel - Patient Information -->
        <div class="patient-info-panel">
            @php
                $initials = collect(explode(' ', trim($patient->name)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                if ($initials === '') { $initials = 'P'; }
                $age = null;
                if ($patient->date_of_birth) {
                    try {
                        $birthday = \Carbon\Carbon::parse($patient->date_of_birth);
                        $now = \Carbon\Carbon::now();
                        
                        // Check if birthday is in the future (invalid)
                        if ($birthday->isFuture()) {
                            $age = null;
                        } else {
                            // Calculate age properly - always positive
                            $diff = $now->diff($birthday);
                            $age = (int) $diff->y;
                            
                            // Additional check: if still negative or invalid, set to null
                            if ($age < 0 || $age > 150) {
                                $age = null;
                            }
                        }
                    } catch (\Exception $e) {
                        $age = null;
                    }
                }
            @endphp

            @if($patient->profile_photo)
                <img src="{{ asset('storage/' . $patient->profile_photo) }}?t={{ time() }}" alt="{{ $patient->name }}" class="patient-photo">
            @else
                <div class="patient-photo" style="background: #197a8c; color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold;">
                    {{ $initials }}
                </div>
            @endif

            <h2 class="patient-name">
                @if($personalInfo)
                    {{ $personalInfo->full_name ?? ($personalInfo->first_name . ' ' . $personalInfo->last_name) }}
                @else
                    {{ $patient->name }}
                @endif
            </h2>
            
            @if($profiles->count() > 1)
                <div style="text-align: center; margin-bottom: 1rem; padding: 0.5rem; background: #f5f9fb; border-radius: 4px;">
                    <small style="color: #666;">Viewing profile: 
                        <strong>{{ $personalInfo ? ($personalInfo->full_name ?? ($personalInfo->first_name . ' ' . $personalInfo->last_name)) : 'Default' }}</strong>
                        @if($personalInfo && $personalInfo->is_default)
                            <span class="badge bg-success" style="font-size: 0.7rem; margin-left: 0.25rem;">Default</span>
                        @endif
                    </small>
                </div>
            @endif

            <div class="patient-detail-item">
                <span class="patient-detail-label">Age</span>
                <span class="patient-detail-value">
                    @if($personalInfo && $personalInfo->birthday)
                        @php
                            try {
                                $birthday = \Carbon\Carbon::parse($personalInfo->birthday);
                                if ($birthday->isFuture()) {
                                    $calculatedAge = null;
                                } else {
                                    $diff = now()->diff($birthday);
                                    $calculatedAge = (int) $diff->y;
                                    // Validate age is reasonable
                                    if ($calculatedAge < 0 || $calculatedAge > 150) {
                                        $calculatedAge = null;
                                    }
                                }
                            } catch (\Exception $e) {
                                $calculatedAge = null;
                            }
                        @endphp
                        {{ $calculatedAge ?? 'N/A' }}
                    @else
                        {{ $age ?? 'N/A' }}
                    @endif
                </span>
            </div>

            <div class="patient-detail-item">
                <span class="patient-detail-label">Birthday</span>
                <span class="patient-detail-value">
                    @if($personalInfo && $personalInfo->birthday)
                        {{ $personalInfo->birthday->format('m/d/Y') }}
                    @elseif($patient->date_of_birth)
                        {{ $patient->date_of_birth->format('m/d/Y') }}
                    @else
                        N/A
                    @endif
                </span>
            </div>

            <div class="patient-detail-item">
                <span class="patient-detail-label">Gender</span>
                <span class="patient-detail-value">{{ ucfirst($patient->gender ?? 'N/A') }}</span>
            </div>
            
            @if($personalInfo && $personalInfo->contact_number)
                <div class="patient-detail-item">
                    <span class="patient-detail-label">Contact</span>
                    <span class="patient-detail-value">{{ $personalInfo->contact_number }}</span>
                </div>
            @endif
            
            @if($personalInfo && $personalInfo->address)
                <div class="patient-detail-item">
                    <span class="patient-detail-label">Address</span>
                    <span class="patient-detail-value" style="font-size: 0.85rem;">{{ $personalInfo->address }}</span>
                </div>
            @endif

            @if($emergencyContact)
                <div class="patient-detail-item">
                    <span class="patient-detail-label">Guardian</span>
                    <span class="patient-detail-value">{{ $emergencyContact->name }}</span>
                </div>
            @endif

            <!-- Allergies Section -->
            @if($medicalInfo && $medicalInfo->allergies)
                <div class="section-title">
                    <span>Allergies</span>
                    <a href="#" class="view-all-link">View all</a>
                </div>
                @php
                    $allergies = explode(',', $medicalInfo->allergies);
                @endphp
                @foreach(array_slice($allergies, 0, 3) as $allergy)
                    <div class="allergy-item">
                        <div class="allergy-name">{{ trim($allergy) }}</div>
                        <div class="allergy-reaction">Moderate to severe</div>
                        <div class="severity-indicator">
                            <span class="severity-dot filled"></span>
                            <span class="severity-dot filled"></span>
                            <span class="severity-dot filled"></span>
                            <span class="severity-dot"></span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Right Panel - History Timeline -->
        <div class="history-panel">
            <div class="history-header">
                <h2 class="history-title">History</h2>
                @if($profiles->count() > 1)
                    <form method="GET" action="{{ route('admin.patients.history', $patient->id) }}" class="profile-filter-form">
                        <label for="personal_information_id" style="margin-right: 0.5rem; font-weight: 500; color: #666;">Filter by Profile:</label>
                        <select name="personal_information_id" id="personal_information_id" class="form-control" style="display: inline-block; width: auto; min-width: 200px;" onchange="this.form.submit()">
                            <option value="">All Profiles</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}" {{ $selectedProfileId == $profile->id ? 'selected' : '' }}>
                                    {{ $profile->full_name ?? ($profile->first_name . ' ' . $profile->last_name) }} {{ $profile->is_default ? '(Default)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>

            <!-- Timeline Visualization -->
            <div class="timeline-visualization">
                <span class="timeline-label">January</span>
            </div>

            <!-- Date Filters -->
            <div class="date-filters">
                <button class="date-filter-btn" data-range="1w">1w</button>
                <button class="date-filter-btn" data-range="3M">3M</button>
                <button class="date-filter-btn" data-range="6M">6M</button>
                <button class="date-filter-btn active" data-range="1Y">1Y</button>
                <div class="date-range-inputs">
                    <div class="date-input-wrapper">
                        <input type="date" id="startDate" value="{{ $history->min('treatment_date') ? $history->min('treatment_date')->format('Y-m-d') : date('Y-m-d', strtotime('-1 year')) }}">
                        <i data-feather="calendar" class="calendar-icon"></i>
                    </div>
                    <span>to</span>
                    <div class="date-input-wrapper">
                        <input type="date" id="endDate" value="{{ date('Y-m-d') }}">
                        <i data-feather="calendar" class="calendar-icon"></i>
                    </div>
                </div>
            </div>

            @if($history->isEmpty())
                <div class="alert alert-info">No history available for this patient{{ $selectedProfileId ? ' with the selected profile' : '' }}.</div>
            @else
                @if($profiles->count() > 1 && !$selectedProfileId)
                    {{-- Show history grouped by profile when viewing all profiles --}}
                    @foreach($historyByProfile as $profileId => $profileHistory)
                        @php
                            $profile = $profiles->firstWhere('id', $profileId);
                            $profileName = 'Unknown Profile';
                            
                            if ($profile) {
                                // Try full_name accessor first
                                $profileName = $profile->full_name ?? '';
                                
                                // If empty, build from parts
                                if (empty($profileName)) {
                                    $nameParts = array_filter([
                                        $profile->first_name ?? '',
                                        $profile->middle_initial ? ($profile->middle_initial . '.') : '',
                                        $profile->last_name ?? ''
                                    ]);
                                    $profileName = trim(implode(' ', $nameParts));
                                }
                                
                                // If still empty, use patient name as fallback
                                if (empty($profileName)) {
                                    $profileName = $patient->name ?? 'Unknown Profile';
                                }
                            } else {
                                // If no profile found, use patient name
                                $profileName = $patient->name ?? 'Unknown Profile';
                            }
                        @endphp
                        <div class="profile-history-section" style="margin-bottom: 2.5rem;">
                            <div class="profile-section-header" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e0e0e0;">
                                <span style="font-size: 1.25rem;">👤</span>
                                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #197a8c;">{{ $profileName }}</h3>
                                @if($profile && $profile->is_default)
                                    <span class="badge bg-success" style="font-size: 0.75rem;">Default</span>
                                @endif
                            </div>
                            
                            <div class="history-timeline">
                                <div class="timeline-line"></div>
                                
                                @php
                                    $groupedHistory = $profileHistory->groupBy(function($item) {
                                        return $item->treatment_date ? $item->treatment_date->format('Y') : $item->created_at->format('Y');
                                    });
                                @endphp

                                @foreach($groupedHistory as $year => $yearHistory)
                                    <div class="year-group" data-year="{{ $year }}">
                                        <div class="year-header">{{ $year }}</div>
                                        
                                        <!-- Annual Progress Report -->
                                        <div class="annual-report-bar" onclick="openAnnualReport({{ $year }}, {{ $profileId }})" data-year="{{ $year }}" data-profile="{{ $profileId }}">
                                            View annual progress report
                                        </div>

                                        @foreach($yearHistory as $item)
                                            @php
                                                $eventDate = $item->treatment_date ?? $item->created_at;
                                                $monthName = $eventDate->format('F');
                                                $dayName = $eventDate->format('l');
                                                $day = $eventDate->format('j');
                                                $year = $eventDate->format('Y');
                                                
                                                $eventTitle = $item->diagnosis ?? $item->treatment_notes ?? 'Consultation';
                                                if ($item->appointment && $item->appointment->service) {
                                                    $eventTitle = $item->appointment->service->name;
                                                }
                                                
                                                $doctorName = $item->doctor->name ?? 'N/A';
                                                $location = $item->appointment && $item->appointment->branch ? $item->appointment->branch->name : 'N/A';
                                                
                                                // Prepare consultation data
                                                $consultationData = null;
                                                if ($item->consultation_result) {
                                                    $decoded = json_decode($item->consultation_result, true);
                                                    if (json_last_error() === JSON_ERROR_NONE) {
                                                        $consultationData = $decoded;
                                                    }
                                                }
                                                
                                                // Prepare history item data for JSON
                                                $historyItemData = [
                                                    'id' => $item->id,
                                                    'title' => $eventTitle,
                                                    'doctor' => $doctorName,
                                                    'branch' => $location,
                                                    'date' => $monthName . ' ' . $day . ', ' . $year . ' (' . $dayName . ')',
                                                    'diagnosis' => $item->diagnosis,
                                                    'treatment_notes' => $item->treatment_notes,
                                                    'consultation_data' => $consultationData,
                                                    'prescription' => $item->prescription,
                                                    'notes' => $item->notes,
                                                    'follow_up_date' => $item->follow_up_date ? $item->follow_up_date->format('M d, Y') : null,
                                                ];
                                            @endphp

                                            <div class="history-item" 
                                                 data-history='@json($historyItemData)'>
                                                <div class="history-item-title">{{ $eventTitle }}</div>
                                                <div class="history-item-details">with {{ $doctorName }} at {{ $location }}</div>
                                                <div class="history-item-date">{{ $monthName }} {{ $day }}, {{ $year }} ({{ $dayName }})</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Show history normally when a specific profile is selected or only one profile exists --}}
                    <div class="history-timeline">
                        <div class="timeline-line"></div>
                        
                        @php
                            $groupedHistory = $history->groupBy(function($item) {
                                return $item->treatment_date ? $item->treatment_date->format('Y') : $item->created_at->format('Y');
                            });
                        @endphp

                        @foreach($groupedHistory as $year => $yearHistory)
                            <div class="year-group" data-year="{{ $year }}">
                                <div class="year-header">{{ $year }}</div>
                                
                                <!-- Annual Progress Report -->
                                <div class="annual-report-bar" onclick="openAnnualReport({{ $year }})" data-year="{{ $year }}">
                                    View annual progress report
                                </div>

                                @foreach($yearHistory as $item)
                                    @php
                                        $eventDate = $item->treatment_date ?? $item->created_at;
                                        $monthName = $eventDate->format('F');
                                        $dayName = $eventDate->format('l');
                                        $day = $eventDate->format('j');
                                        $year = $eventDate->format('Y');
                                        
                                        $eventTitle = $item->diagnosis ?? $item->treatment_notes ?? 'Consultation';
                                        if ($item->appointment && $item->appointment->service) {
                                            $eventTitle = $item->appointment->service->name;
                                        }
                                        
                                        $doctorName = $item->doctor->name ?? 'N/A';
                                        $location = $item->appointment && $item->appointment->branch ? $item->appointment->branch->name : 'N/A';
                                        
                                        // Prepare consultation data
                                        $consultationData = null;
                                        if ($item->consultation_result) {
                                            $decoded = json_decode($item->consultation_result, true);
                                            if (json_last_error() === JSON_ERROR_NONE) {
                                                $consultationData = $decoded;
                                            }
                                        }
                                        
                                        // Prepare history item data for JSON
                                        $historyItemData = [
                                            'id' => $item->id,
                                            'title' => $eventTitle,
                                            'doctor' => $doctorName,
                                            'branch' => $location,
                                            'date' => $monthName . ' ' . $day . ', ' . $year . ' (' . $dayName . ')',
                                            'diagnosis' => $item->diagnosis,
                                            'treatment_notes' => $item->treatment_notes,
                                            'consultation_data' => $consultationData,
                                            'prescription' => $item->prescription,
                                            'notes' => $item->notes,
                                            'follow_up_date' => $item->follow_up_date ? $item->follow_up_date->format('M d, Y') : null,
                                        ];
                                    @endphp

                                    <div class="history-item" 
                                         data-history='@json($historyItemData)'>
                                        <div class="history-item-title">{{ $eventTitle }}</div>
                                        <div class="history-item-details">with {{ $doctorName }} at {{ $location }}</div>
                                        <div class="history-item-date">{{ $monthName }} {{ $day }}, {{ $year }} ({{ $dayName }})</div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Annual Report Modal -->
<div id="annualReportModal" class="annual-report-modal">
    <div class="annual-report-modal-content">
        <div class="annual-report-modal-header">
            <h2 class="annual-report-modal-title" id="annualReportTitle">Annual Progress Report</h2>
            <button class="annual-report-modal-close" onclick="closeAnnualReport()">&times;</button>
        </div>
        <div class="annual-report-modal-body" id="annualReportBody">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- History Item Detail Modal -->
<div id="historyItemModal" class="annual-report-modal">
    <div class="annual-report-modal-content">
        <div class="annual-report-modal-header">
            <h2 class="annual-report-modal-title" id="historyItemTitle">Consultation Details</h2>
            <button class="annual-report-modal-close" onclick="closeHistoryItemModal()">&times;</button>
        </div>
        <div class="annual-report-modal-body" id="historyItemBody">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

@php
    // Prepare history data for JavaScript - grouped by year and profile
    $historyData = [];
    foreach($history as $item) {
        $year = $item->treatment_date ? $item->treatment_date->format('Y') : $item->created_at->format('Y');
        $profileId = $item->personal_information_id ?? 'default';
        
        if (!isset($historyData[$year])) {
            $historyData[$year] = [];
        }
        if (!isset($historyData[$year][$profileId])) {
            $historyData[$year][$profileId] = [];
        }
        
        $consultationData = null;
        if ($item->consultation_result) {
            $decoded = json_decode($item->consultation_result, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $consultationData = $decoded;
                // Add direct fields if they exist in the JSON
                if (isset($decoded['before_findings'])) {
                    $consultationData['before_findings'] = $decoded['before_findings'];
                }
                if (isset($decoded['after_results'])) {
                    $consultationData['after_results'] = $decoded['after_results'];
                }
                if (isset($decoded['prescription'])) {
                    $consultationData['prescription'] = $decoded['prescription'];
                }
                if (isset($decoded['medications_to_take'])) {
                    $consultationData['medications_to_take'] = $decoded['medications_to_take'];
                }
                if (isset($decoded['follow_up_date'])) {
                    $consultationData['follow_up_date'] = $decoded['follow_up_date'];
                }
            }
        }
        
        $historyData[$year][$profileId][] = [
            'id' => $item->id,
            'title' => $item->diagnosis ?? $item->treatment_notes ?? 'Consultation',
            'service' => $item->appointment && $item->appointment->service ? $item->appointment->service->name : null,
            'doctor' => $item->doctor ? $item->doctor->name : 'N/A',
            'branch' => $item->appointment && $item->appointment->branch ? $item->appointment->branch->name : 'N/A',
            'treatment_date' => $item->treatment_date ? $item->treatment_date->format('F d, Y (l)') : $item->created_at->format('F d, Y (l)'),
            'diagnosis' => $item->diagnosis,
            'treatment_notes' => $item->treatment_notes,
            'consultation_result' => $item->consultation_result,
            'prescription' => $item->prescription,
            'medicines_to_take' => $item->prescription,
            'outcome' => $item->outcome,
            'notes' => $item->notes,
            'consultation_data' => $consultationData,
        ];
    }
@endphp

<script>
    const historyData = @json($historyData);
</script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }

        // Date filter functionality
        const dateFilterBtns = document.querySelectorAll('.date-filter-btn');
        dateFilterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                dateFilterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const range = this.dataset.range;
                const endDate = new Date();
                let startDate = new Date();
                
                switch(range) {
                    case '1w':
                        startDate.setDate(endDate.getDate() - 7);
                        break;
                    case '3M':
                        startDate.setMonth(endDate.getMonth() - 3);
                        break;
                    case '6M':
                        startDate.setMonth(endDate.getMonth() - 6);
                        break;
                    case '1Y':
                        startDate.setFullYear(endDate.getFullYear() - 1);
                        break;
                }
                
                document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
                document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
                
                filterHistoryByDate(startDate, endDate);
            });
        });

        // Date range input functionality
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        
        [startDateInput, endDateInput].forEach(input => {
            input.addEventListener('change', function() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                filterHistoryByDate(startDate, endDate);
            });
        });

        function filterHistoryByDate(startDate, endDate) {
            const yearGroups = document.querySelectorAll('.year-group');
            yearGroups.forEach(group => {
                const items = group.querySelectorAll('.history-item');
                let hasVisibleItems = false;
                
                items.forEach(item => {
                    const dateText = item.querySelector('.history-item-date').textContent;
                    // Parse date from text like "March 1, 2014 (Tuesday)"
                    const dateMatch = dateText.match(/(\w+)\s+(\d+),\s+(\d+)/);
                    if (dateMatch) {
                        const itemDate = new Date(dateMatch[1] + ' ' + dateMatch[2] + ', ' + dateMatch[3]);
                        if (itemDate >= startDate && itemDate <= endDate) {
                            item.style.display = 'block';
                            hasVisibleItems = true;
                        } else {
                            item.style.display = 'none';
                        }
                    }
                });
                
                // Hide year group if no visible items
                group.style.display = hasVisibleItems ? 'block' : 'none';
            });
        }

        // Close modal when clicking outside
        const annualReportModal = document.getElementById('annualReportModal');
        if (annualReportModal) {
            annualReportModal.addEventListener('click', function(e) {
                if (e.target === annualReportModal) {
                    closeAnnualReport();
                }
            });
        }
        
        // Close history item modal when clicking outside or on close button
        const historyItemModal = document.getElementById('historyItemModal');
        if (historyItemModal) {
            // Close when clicking on the backdrop
            historyItemModal.addEventListener('click', function(e) {
                if (e.target === historyItemModal) {
                    closeHistoryItemModal();
                }
            });
            
            // Close when clicking on the close button (X)
            const closeBtn = historyItemModal.querySelector('.annual-report-modal-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeHistoryItemModal();
                });
            }
        }
        
        // Add click handlers to history items using event delegation
        // This works for both existing and dynamically added items
        document.body.addEventListener('click', function(e) {
            // Check if clicked element or its parent is a history item
            let historyItem = e.target;
            while (historyItem && !historyItem.classList.contains('history-item')) {
                historyItem = historyItem.parentElement;
            }
            
            if (historyItem && historyItem.hasAttribute('data-history')) {
                e.preventDefault();
                e.stopPropagation();
                
                try {
                    const historyDataStr = historyItem.getAttribute('data-history');
                    console.log('History data string:', historyDataStr);
                    console.log('History data string length:', historyDataStr ? historyDataStr.length : 0);
                    
                    if (historyDataStr && historyDataStr.trim() !== '') {
                        // Try to parse the JSON
                        let historyData;
                        try {
                            historyData = JSON.parse(historyDataStr);
                        } catch (parseError) {
                            console.error('JSON Parse Error:', parseError);
                            console.error('First 100 chars of data:', historyDataStr.substring(0, 100));
                            // Try to decode HTML entities if that's the issue
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = historyDataStr;
                            const decodedStr = tempDiv.textContent || tempDiv.innerText;
                            console.log('Decoded string:', decodedStr);
                            historyData = JSON.parse(decodedStr);
                        }
                        
                        console.log('Parsed history data:', historyData);
                        openHistoryItemModal(historyData);
                    } else {
                        console.error('No data-history attribute found or empty');
                        alert('No consultation data available.');
                    }
                } catch (error) {
                    console.error('Error parsing history data:', error);
                    console.error('Error stack:', error.stack);
                    console.error('Data string (first 200 chars):', historyItem.getAttribute('data-history')?.substring(0, 200));
                    alert('Error loading consultation details: ' + error.message + '\n\nPlease check the browser console for more details.');
                }
            }
        });
    });

    function openAnnualReport(year, profileId = null) {
        const modal = document.getElementById('annualReportModal');
        const title = document.getElementById('annualReportTitle');
        const body = document.getElementById('annualReportBody');
        
        // Filter by profile if provided, otherwise show all profiles
        let itemsToShow = [];
        if (profileId && historyData[year] && historyData[year][profileId]) {
            itemsToShow = historyData[year][profileId];
        } else if (historyData[year]) {
            // If no profile specified, show all items from all profiles
            Object.values(historyData[year]).forEach(profileItems => {
                itemsToShow = itemsToShow.concat(profileItems);
            });
        }
        
        if (itemsToShow.length === 0) {
            body.innerHTML = '<p>No history available for this year.</p>';
            modal.classList.add('active');
            return;
        }

        title.textContent = `Annual Progress Report - ${year}`;
        
        let html = '';
        
        itemsToShow.forEach((item, index) => {
            // Add separator between items
            if (index > 0) {
                html += `<div style="margin: 2rem 0; border-top: 3px solid #e0e0e0;"></div>`;
            }
            
            // Consultation Data (from JSON)
            if (item.consultation_data) {
                const data = item.consultation_data;
                
                // BEFORE PHOTOS Section
                if (data.before && data.before.photos && data.before.photos.length > 0) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">📸 BEFORE PHOTOS</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Upload Before Photos (Multiple)</label>`;
                    html += `<div class="file-preview">`;
                    data.before.photos.forEach(photo => {
                        html += `<div class="file-preview-item">`;
                        html += `<img src="{{ asset('storage/') }}/` + escapeHtml(photo) + `" alt="Before Photo" onerror="this.style.display='none'">`;
                        html += `</div>`;
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // BEFORE CONSULTATION FINDINGS Section
                let beforeFindings = [];
                if (data.before_findings) {
                    // Handle string format with bullet points
                    beforeFindings = data.before_findings.split('\n').map(f => f.replace(/^•\s*/, '').trim()).filter(f => f);
                } else if (data.before && data.before.findings) {
                    beforeFindings = Array.isArray(data.before.findings) ? data.before.findings : [data.before.findings];
                }
                
                if (beforeFindings.length > 0) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">🩺 BEFORE CONSULTATION FINDINGS</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Findings (Multiple Bullet Points)</label>`;
                    html += `<div>`;
                    beforeFindings.forEach(finding => {
                        if (finding && finding.trim()) {
                            html += `<div class="bullet-note-item">`;
                            html += `<span class="bullet-point">•</span>`;
                            html += `<div class="bullet-text">${escapeHtml(finding.trim())}</div>`;
                            html += `</div>`;
                        }
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // AFTER PHOTOS Section
                if (data.after && data.after.photos && data.after.photos.length > 0) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">📸 AFTER PHOTOS</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Upload After Photos (Multiple)</label>`;
                    html += `<div class="file-preview">`;
                    data.after.photos.forEach(photo => {
                        html += `<div class="file-preview-item">`;
                        html += `<img src="{{ asset('storage/') }}/` + escapeHtml(photo) + `" alt="After Photo" onerror="this.style.display='none'">`;
                        html += `</div>`;
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // AFTER CONSULTATION RESULTS Section
                let afterResults = [];
                if (data.after_results) {
                    // Handle string format with bullet points
                    afterResults = data.after_results.split('\n').map(r => r.replace(/^•\s*/, '').trim()).filter(r => r);
                } else if (data.after && data.after.results) {
                    afterResults = Array.isArray(data.after.results) ? data.after.results : [data.after.results];
                }
                
                if (afterResults.length > 0) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">🧪 AFTER CONSULTATION RESULTS</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Results (Multiple Bullet Points)</label>`;
                    html += `<div>`;
                    afterResults.forEach(result => {
                        if (result && result.trim()) {
                            html += `<div class="bullet-note-item">`;
                            html += `<span class="bullet-point">•</span>`;
                            html += `<div class="bullet-text">${escapeHtml(result.trim())}</div>`;
                            html += `</div>`;
                        }
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // PRESCRIPTION Section
                let prescriptions = [];
                if (data.prescription) {
                    // Handle string format with bullet points
                    prescriptions = data.prescription.split('\n').map(p => p.replace(/^•\s*/, '').trim()).filter(p => p);
                } else if (data.medication && data.medication.instructions) {
                    prescriptions = [data.medication.instructions];
                }
                
                if (prescriptions.length > 0) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">💊 PRESCRIPTION</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Prescription Items (Multiple Bullet Points)</label>`;
                    html += `<div>`;
                    prescriptions.forEach(pres => {
                        if (pres && pres.trim()) {
                            html += `<div class="bullet-note-item">`;
                            html += `<span class="bullet-point">•</span>`;
                            html += `<div class="bullet-text">${escapeHtml(pres.trim())}</div>`;
                            html += `</div>`;
                        }
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // MEDICATIONS TO TAKE Section
                let medications = [];
                if (data.medications_to_take) {
                    // Handle string format with bullet points
                    medications = data.medications_to_take.split('\n').map(m => m.replace(/^•\s*/, '').trim()).filter(m => m);
                } else if (data.medication && data.medication.medicines) {
                    medications = typeof data.medication.medicines === 'string' 
                        ? data.medication.medicines.split('\n').map(m => m.replace(/^•\s*/, '').trim()).filter(m => m)
                        : (Array.isArray(data.medication.medicines) ? data.medication.medicines : [data.medication.medicines]);
                }
                
                if (medications.length > 0) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">💧 MEDICATIONS TO TAKE</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Oral Medications (Multiple Bullet Points)</label>`;
                    html += `<div>`;
                    medications.forEach(med => {
                        if (med && med.trim()) {
                            html += `<div class="bullet-note-item">`;
                            html += `<span class="bullet-point">•</span>`;
                            html += `<div class="bullet-text">${escapeHtml(med.trim())}</div>`;
                            html += `</div>`;
                        }
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // NOTES Section
                const notes = data.notes || (data.after && data.after.notes) || null;
                if (notes && notes.trim()) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">📝 NOTES</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Notes (Optional)</label>`;
                    html += `<textarea readonly style="min-height: 100px;">${escapeHtml(notes)}</textarea>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // FOLLOW-UP DATE Section
                if (data.follow_up_date) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">📅 FOLLOW-UP DATE</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Follow-up Date (Optional)</label>`;
                    html += `<input type="text" class="result-form-input" value="${escapeHtml(data.follow_up_date)}" readonly>`;
                    html += `</div>`;
                    html += `</div>`;
                }
            } else {
                // Fallback: Display legacy data format
                // BEFORE PHOTOS (if any in legacy format)
                // BEFORE CONSULTATION FINDINGS
                if (item.diagnosis) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">🩺 BEFORE CONSULTATION FINDINGS</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Findings (Multiple Bullet Points)</label>`;
                    html += `<div>`;
                    const findings = item.diagnosis.split('\n').filter(f => f.trim());
                    findings.forEach(finding => {
                        html += `<div class="bullet-note-item">`;
                        html += `<span class="bullet-point">•</span>`;
                        html += `<div class="bullet-text">${escapeHtml(finding.trim())}</div>`;
                        html += `</div>`;
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // AFTER CONSULTATION RESULTS
                if (item.treatment_notes) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">🧪 AFTER CONSULTATION RESULTS</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Results (Multiple Bullet Points)</label>`;
                    html += `<div>`;
                    const results = item.treatment_notes.split('\n').filter(r => r.trim());
                    results.forEach(result => {
                        html += `<div class="bullet-note-item">`;
                        html += `<span class="bullet-point">•</span>`;
                        html += `<div class="bullet-text">${escapeHtml(result.trim())}</div>`;
                        html += `</div>`;
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // PRESCRIPTION
                if (item.prescription) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">💊 PRESCRIPTION</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Prescription Items (Multiple Bullet Points)</label>`;
                    html += `<div>`;
                    const prescriptions = item.prescription.split('\n').filter(p => p.trim());
                    prescriptions.forEach(pres => {
                        html += `<div class="bullet-note-item">`;
                        html += `<span class="bullet-point">•</span>`;
                        html += `<div class="bullet-text">${escapeHtml(pres.trim())}</div>`;
                        html += `</div>`;
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // MEDICATIONS TO TAKE
                if (item.medicines_to_take) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">💧 MEDICATIONS TO TAKE</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Oral Medications (Multiple Bullet Points)</label>`;
                    html += `<div>`;
                    const medications = item.medicines_to_take.split('\n').filter(m => m.trim());
                    medications.forEach(med => {
                        html += `<div class="bullet-note-item">`;
                        html += `<span class="bullet-point">•</span>`;
                        html += `<div class="bullet-text">${escapeHtml(med.trim())}</div>`;
                        html += `</div>`;
                    });
                    html += `</div>`;
                    html += `</div>`;
                    html += `</div>`;
                }
                
                // NOTES
                if (item.notes) {
                    html += `<div class="result-form-section">`;
                    html += `<div class="result-section-header">📝 NOTES</div>`;
                    html += `<div class="result-form-group">`;
                    html += `<label>Notes (Optional)</label>`;
                    html += `<textarea readonly style="min-height: 100px;">${escapeHtml(item.notes)}</textarea>`;
                    html += `</div>`;
                    html += `</div>`;
                }
            }
        });
        
        body.innerHTML = html;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeAnnualReport() {
        const modal = document.getElementById('annualReportModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    function openHistoryItemModal(item) {
        console.log('Opening history item modal:', item);
        const modal = document.getElementById('historyItemModal');
        const title = document.getElementById('historyItemTitle');
        const body = document.getElementById('historyItemBody');
        
        if (!modal || !title || !body) {
            console.error('Modal elements not found');
            alert('Error: Modal elements not found. Please refresh the page.');
            return;
        }
        
        if (!item) {
            console.error('No item data provided');
            return;
        }
        
        title.textContent = item.title || 'Consultation Details';
        
        let html = '';
        
        // Header information
        html += `<div style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 2px solid #e0e0e0;">`;
        html += `<h3 style="margin: 0 0 0.5rem 0; color: #197a8c; font-size: 1.3rem;">${escapeHtml(item.title || 'Consultation')}</h3>`;
        html += `<div style="color: #666; font-size: 0.95rem; margin-bottom: 0.25rem;">With ${escapeHtml(item.doctor)}</div>`;
        html += `<div style="color: #666; font-size: 0.95rem; margin-bottom: 0.25rem;">At ${escapeHtml(item.branch)}</div>`;
        html += `<div style="color: #999; font-size: 0.85rem;">${escapeHtml(item.date)}</div>`;
        html += `</div>`;
        
        // Consultation Data (from JSON)
        if (item.consultation_data) {
            const data = item.consultation_data;
            
            // BEFORE PHOTOS Section
            if (data.before && data.before.photos && data.before.photos.length > 0) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">📸 BEFORE PHOTOS</div>`;
                html += `<div class="result-form-group">`;
                html += `<div class="file-preview">`;
                data.before.photos.forEach(photo => {
                    const photoPath = photo.startsWith('http') ? photo : `{{ asset('storage/') }}/${escapeHtml(photo)}`;
                    html += `<div class="file-preview-item">`;
                    html += `<img src="${photoPath}" alt="Before Photo" onerror="this.style.display='none'">`;
                    html += `</div>`;
                });
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // BEFORE CONSULTATION FINDINGS Section
            let beforeFindings = [];
            if (data.before_findings) {
                beforeFindings = data.before_findings.split('\n').map(f => f.replace(/^•\s*/, '').trim()).filter(f => f);
            } else if (data.before && data.before.findings) {
                beforeFindings = Array.isArray(data.before.findings) ? data.before.findings : [data.before.findings];
            }
            
            if (beforeFindings.length > 0) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">🩺 BEFORE CONSULTATION FINDINGS</div>`;
                html += `<div class="result-form-group">`;
                html += `<div>`;
                beforeFindings.forEach(finding => {
                    if (finding && finding.trim()) {
                        html += `<div class="bullet-note-item">`;
                        html += `<span class="bullet-point">•</span>`;
                        html += `<div class="bullet-text">${escapeHtml(finding.trim())}</div>`;
                        html += `</div>`;
                    }
                });
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // AFTER PHOTOS Section
            if (data.after && data.after.photos && data.after.photos.length > 0) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">📸 AFTER PHOTOS</div>`;
                html += `<div class="result-form-group">`;
                html += `<div class="file-preview">`;
                data.after.photos.forEach(photo => {
                    const photoPath = photo.startsWith('http') ? photo : `{{ asset('storage/') }}/${escapeHtml(photo)}`;
                    html += `<div class="file-preview-item">`;
                    html += `<img src="${photoPath}" alt="After Photo" onerror="this.style.display='none'">`;
                    html += `</div>`;
                });
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // AFTER CONSULTATION RESULTS Section
            let afterResults = [];
            if (data.after_results) {
                afterResults = data.after_results.split('\n').map(r => r.replace(/^•\s*/, '').trim()).filter(r => r);
            } else if (data.after && data.after.results) {
                afterResults = Array.isArray(data.after.results) ? data.after.results : [data.after.results];
            }
            
            if (afterResults.length > 0) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">🧪 AFTER CONSULTATION RESULTS</div>`;
                html += `<div class="result-form-group">`;
                html += `<div>`;
                afterResults.forEach(result => {
                    if (result && result.trim()) {
                        html += `<div class="bullet-note-item">`;
                        html += `<span class="bullet-point">•</span>`;
                        html += `<div class="bullet-text">${escapeHtml(result.trim())}</div>`;
                        html += `</div>`;
                    }
                });
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // PRESCRIPTION Section
            let prescriptions = [];
            if (data.prescription) {
                prescriptions = data.prescription.split('\n').map(p => p.replace(/^•\s*/, '').trim()).filter(p => p);
            } else if (data.medication && data.medication.instructions) {
                prescriptions = [data.medication.instructions];
            }
            
            if (prescriptions.length > 0) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">💊 PRESCRIPTION</div>`;
                html += `<div class="result-form-group">`;
                html += `<div>`;
                prescriptions.forEach(pres => {
                    if (pres && pres.trim()) {
                        html += `<div class="bullet-note-item">`;
                        html += `<span class="bullet-point">•</span>`;
                        html += `<div class="bullet-text">${escapeHtml(pres.trim())}</div>`;
                        html += `</div>`;
                    }
                });
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // MEDICATIONS TO TAKE Section
            let medications = [];
            if (data.medications_to_take) {
                medications = data.medications_to_take.split('\n').map(m => m.replace(/^•\s*/, '').trim()).filter(m => m);
            } else if (data.medication && data.medication.medicines) {
                medications = typeof data.medication.medicines === 'string' 
                    ? data.medication.medicines.split('\n').map(m => m.replace(/^•\s*/, '').trim()).filter(m => m)
                    : (Array.isArray(data.medication.medicines) ? data.medication.medicines : [data.medication.medicines]);
            }
            
            if (medications.length > 0) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">💧 MEDICATIONS TO TAKE</div>`;
                html += `<div class="result-form-group">`;
                html += `<div>`;
                medications.forEach(med => {
                    if (med && med.trim()) {
                        html += `<div class="bullet-note-item">`;
                        html += `<span class="bullet-point">•</span>`;
                        html += `<div class="bullet-text">${escapeHtml(med.trim())}</div>`;
                        html += `</div>`;
                    }
                });
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // NOTES Section
            const notes = data.notes || (data.after && data.after.notes) || null;
            if (notes && notes.trim()) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">📝 NOTES</div>`;
                html += `<div class="result-form-group">`;
                html += `<textarea readonly style="min-height: 100px;">${escapeHtml(notes)}</textarea>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // FOLLOW-UP DATE Section
            if (data.follow_up_date) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">📅 FOLLOW-UP DATE</div>`;
                html += `<div class="result-form-group">`;
                html += `<input type="text" class="result-form-input" value="${escapeHtml(data.follow_up_date)}" readonly>`;
                html += `</div>`;
                html += `</div>`;
            }
        } else {
            // Fallback: Display legacy data format
            // BEFORE CONSULTATION FINDINGS
            if (item.diagnosis) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">🩺 BEFORE CONSULTATION FINDINGS</div>`;
                html += `<div class="result-form-group">`;
                html += `<div>`;
                const findings = item.diagnosis.split('\n').filter(f => f.trim());
                findings.forEach(finding => {
                    html += `<div class="bullet-note-item">`;
                    html += `<span class="bullet-point">•</span>`;
                    html += `<div class="bullet-text">${escapeHtml(finding.trim())}</div>`;
                    html += `</div>`;
                });
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // AFTER CONSULTATION RESULTS
            if (item.treatment_notes) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">🧪 AFTER CONSULTATION RESULTS</div>`;
                html += `<div class="result-form-group">`;
                html += `<div>`;
                const results = item.treatment_notes.split('\n').filter(r => r.trim());
                results.forEach(result => {
                    html += `<div class="bullet-note-item">`;
                    html += `<span class="bullet-point">•</span>`;
                    html += `<div class="bullet-text">${escapeHtml(result.trim())}</div>`;
                    html += `</div>`;
                });
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // PRESCRIPTION
            if (item.prescription) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">💊 PRESCRIPTION</div>`;
                html += `<div class="result-form-group">`;
                html += `<div>`;
                const prescriptions = item.prescription.split('\n').filter(p => p.trim());
                prescriptions.forEach(pres => {
                    html += `<div class="bullet-note-item">`;
                    html += `<span class="bullet-point">•</span>`;
                    html += `<div class="bullet-text">${escapeHtml(pres.trim())}</div>`;
                    html += `</div>`;
                });
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // NOTES
            if (item.notes) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">📝 NOTES</div>`;
                html += `<div class="result-form-group">`;
                html += `<textarea readonly style="min-height: 100px;">${escapeHtml(item.notes)}</textarea>`;
                html += `</div>`;
                html += `</div>`;
            }
            
            // FOLLOW-UP DATE
            if (item.follow_up_date) {
                html += `<div class="result-form-section">`;
                html += `<div class="result-section-header">📅 FOLLOW-UP DATE</div>`;
                html += `<div class="result-form-group">`;
                html += `<input type="text" class="result-form-input" value="${escapeHtml(item.follow_up_date)}" readonly>`;
                html += `</div>`;
                html += `</div>`;
            }
        }
        
        // If no data available
        if (html === '') {
            html = '<p style="color: #666; text-align: center; padding: 2rem;">No detailed information available for this consultation.</p>';
        }
        
        body.innerHTML = html;
        
        // Ensure modal is visible
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        console.log('Modal displayed, classList:', modal.classList.toString());
    }

    function closeHistoryItemModal() {
        const modal = document.getElementById('historyItemModal');
        if (modal) {
            modal.classList.remove('active');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

</script>
@endpush
