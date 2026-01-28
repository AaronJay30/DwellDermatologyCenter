@extends('layouts.patient')

@push('styles')
<style>
    /* Medical Consultation Form Styling */
    .consultation-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    .consultation-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .consultation-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: #000000;
        margin-bottom: 0.5rem;
        font-family: 'Figtree', sans-serif;
    }

    .consultation-subtitle {
        font-size: 1.1rem;
        color: #6c757d;
        font-weight: 400;
    }

    /* Personal Information Section - Unified Component */
    .personal-info-section {
        background: #ffffff;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }

    .add-profile-btn {
        width: 100%;
        padding: 1.5rem;
        border: 2px dashed #197a8c;
        border-radius: 10px;
        background: #ffffff;
        color: #197a8c;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Figtree', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .add-profile-btn:hover {
        background: #e5f7fa;
        border-color: #1a6b7a;
        color: #1a6b7a;
    }

    .default-profile-display {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .default-profile-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .profile-fields-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        font-size: 0.95rem;
        color: #2c3e50;
    }

    .profile-field-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .profile-field-label {
        font-weight: 600;
        color: #2c3e50;
        min-width: fit-content;
    }

    .profile-field-value {
        color: #6c757d;
        flex: 1;
    }

    .default-profile-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .default-profile-address {
        font-size: 0.95rem;
        color: #6c757d;
        line-height: 1.5;
    }

    .change-profile-button {
        width: 100%;
        padding: 0.75rem 1.5rem;
        border: 2px solid #197a8c;
        border-radius: 10px;
        background: #ffffff;
        color: #197a8c;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Figtree', sans-serif;
        text-decoration: none;
        text-align: center;
    }

    .change-profile-button:hover {
        background: #e5f7fa;
        border-color: #1a6b7a;
        color: #1a6b7a;
    }

    /* Compact Personal Info Row - One Line */
    .personal-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        width: 100%;
    }

    .personal-info-row:hover {
        background: #f8f9fa;
        border-color: #197a8c;
    }

    .personal-info-row-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
    }

    .personal-info-label {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1rem;
    }

    .personal-info-name {
        color: #6c757d;
        font-size: 1rem;
    }

    .personal-info-chevron {
        color: #197a8c;
        font-size: 1.3rem;
        font-weight: bold;
    }

    /* Profile List Section */
    .profiles-section {
        background: #ffffff;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }

    .profiles-section h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #197a8c;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5f7fa;
    }

    .profiles-list {
        display: grid;
        gap: 1rem;
    }

    .profile-card {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .profile-card:hover {
        border-color: #197a8c;
        box-shadow: 0 4px 12px rgba(25, 122, 140, 0.15);
    }

    .profile-card.selected {
        border-color: #197a8c;
        background: #e5f7fa;
        box-shadow: 0 4px 12px rgba(25, 122, 140, 0.25);
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .profile-info {
        flex: 1;
    }

    .profile-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .profile-label {
        display: inline-block;
        background: #197a8c;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 5px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .default-badge {
        display: inline-block;
        background: #28a745;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 5px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-left: 0.5rem;
    }

    .profile-actions {
        display: flex;
        gap: 0.5rem;
    }

    .profile-action-btn {
        background: transparent;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .profile-action-btn:hover {
        background: #197a8c;
        color: white;
        border-color: #197a8c;
    }

    .profile-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .profile-detail-item {
        display: flex;
        flex-direction: column;
    }

    .profile-detail-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .profile-detail-value {
        color: #6c757d;
    }

    .no-profiles {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    .consultation-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: start;
    }

    .left-section {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .personal-info-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: start;
    }

    /* Three containers stacked vertically - one per line */
    .info-sections-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-sections-row .personal-info-section {
        margin-bottom: 0;
    }

    .add-buttons-row {
        display: flex;
        gap: 1rem;
        align-items: stretch;
    }

    .add-buttons-row .add-profile-btn {
        flex: 1;
    }

    /* Left Card - Personal Information */
    .personal-info-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #197a8c;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5f7fa;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .required-indicator {
        color: #dc3545;
        font-weight: 700;
        margin-left: 0.35rem;
        font-size: 0.9rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 0;
        border: none;
        border-bottom: 2px solid #e9ecef;
        background: transparent;
        font-size: 1rem;
        color: #2c3e50;
        transition: all 0.3s ease;
        font-family: 'Figtree', sans-serif;
    }

    .form-input:focus {
        outline: none;
        border-bottom-color: #197a8c;
        background-color: #f8f9fa;
        padding-left: 0.5rem;
        border-radius: 5px 5px 0 0;
    }

    .form-input::placeholder {
        color: #6c757d;
        font-style: italic;
    }

    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        background: #ffffff;
        font-size: 1rem;
        color: #2c3e50;
        transition: all 0.3s ease;
        font-family: 'Figtree', sans-serif;
    }

    .form-select:focus {
        outline: none;
        border-color: #197a8c;
        box-shadow: 0 0 0 3px rgba(25, 122, 140, 0.1);
    }

    .form-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        background: #ffffff;
        font-size: 1rem;
        color: #2c3e50;
        transition: all 0.3s ease;
        font-family: 'Figtree', sans-serif;
        resize: vertical;
        min-height: 100px;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #197a8c;
        box-shadow: 0 0 0 3px rgba(25, 122, 140, 0.1);
    }

    /* Right Card - Summary */
    .summary-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        position: sticky;
        top: 2rem;
    }

    .consultation-list {
        margin-bottom: 2rem;
    }

    .consultation-item {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 1rem;
        border-left: 4px solid #197a8c;
    }

    .consultation-item:last-child {
        margin-bottom: 0;
    }

    .consultation-detail {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .consultation-detail:last-child {
        margin-bottom: 0;
    }

    .detail-label {
        font-weight: 600;
        color: #2c3e50;
    }

    .detail-value {
        color: #6c757d;
        text-align: right;
    }

    .consultation-fee {
        background: #e5f7fa;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .fee-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .fee-amount {
        font-size: 1.5rem;
        font-weight: bold;
        color: #197a8c;
    }

    .book-button {
        width: 100%;
        padding: 1rem 2rem;
        background: linear-gradient(135deg, #197a8c 0%, #1a6b7a 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Figtree', sans-serif;
        box-shadow: 0 4px 15px rgba(25, 122, 140, 0.3);
    }

    .book-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(25, 122, 140, 0.4);
    }

    .book-button:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: #ffffff;
        margin: auto;
        padding: 2rem;
        border-radius: 15px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #197a8c;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6c757d;
        transition: color 0.3s ease;
    }

    .close-modal:hover {
        color: #197a8c;
    }

    .modal-body {
        padding: 1rem 0;
        color: #2c3e50;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 2px solid #e9ecef;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #197a8c;
        color: white;
    }

    .btn-primary:hover {
        background: #1a6b7a;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-group label {
        font-weight: 500;
        color: #2c3e50;
        cursor: pointer;
    }

    /* Error Messages */
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        border: 1px solid #f5c6cb;
    }

    .error-message ul {
        margin: 0;
        padding-left: 1rem;
    }

    /* Success Message */
    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        border: 1px solid #c3e6cb;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .consultation-layout {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .left-section {
            gap: 1.5rem;
        }

        .personal-info-wrapper {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .info-sections-row {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .add-buttons-row {
            flex-direction: column;
        }

        .consultation-container {
            padding: 1rem;
        }

        .consultation-title {
            font-size: 2rem;
        }

        .personal-info-card,
        .personal-info-section,
        .summary-card {
            padding: 1.5rem;
        }

        .summary-card {
            position: static;
        }

        .profile-details {
            grid-template-columns: 1fr;
        }
    }

    /* Loading States */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="consultation-container">
    <!-- Header -->
    <div class="consultation-header">
        <h1 class="consultation-title">Medical Consultation</h1>
        <p class="consultation-subtitle">Schedule your consultation with our medical professionals</p>
    </div>

    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="consultation-layout">
        <!-- Left Section -->
        <div class="left-section">
            <!-- Personal Information Section - One Line -->
            <a href="{{ route('personal-information.select', ['return' => route('consultations.medical')]) }}" class="personal-info-row">
                <div class="personal-info-row-content">
                    <span class="personal-info-label">Personal Info</span>
                    <span>—</span>
                    <span class="personal-info-name" id="displayed-profile-name">{{ $defaultProfile ? $defaultProfile->full_name : 'Add Info' }}</span>
                </div>
                <i class="fas fa-user-plus personal-info-chevron"></i>
            </a>

            <!-- Consultation Details Section -->
            <div class="personal-info-card">
                <h2 class="card-title">Consultation Details</h2>
                <form id="consultation-form" action="{{ route('consultations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="selected_profile_id" name="personal_information_id" value="{{ $defaultProfile ? $defaultProfile->id : '' }}">
                    
                    <!-- Hidden fields for personal information -->
                    <input type="hidden" id="first_name" name="first_name" value="{{ $defaultProfile ? $defaultProfile->first_name : '' }}">
                    <input type="hidden" id="middle_initial" name="middle_initial" value="{{ $defaultProfile ? $defaultProfile->middle_initial : '' }}">
                    <input type="hidden" id="last_name" name="last_name" value="{{ $defaultProfile ? $defaultProfile->last_name : '' }}">
                    <input type="hidden" id="address" name="address" value="{{ $defaultProfile ? $defaultProfile->address : '' }}">
                    <input type="hidden" id="date_of_birth" name="date_of_birth" value="{{ $defaultProfile ? $defaultProfile->birthday->format('Y-m-d') : '' }}">
                    <input type="hidden" id="contact_number" name="contact_number" value="{{ $defaultProfile ? $defaultProfile->contact_number : '' }}">

                    <div class="form-group">
                        <label for="branch_id" class="form-label">Select Branch <span class="required-indicator">*required</span></label>
                        <select name="branch_id" id="branch_id" class="form-select" required>
                            <option value="">Choose a branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }} - {{ $branch->address }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date" class="form-label">Select Date <span class="required-indicator">*required</span></label>
                        <input type="date" name="date" id="date" class="form-input" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="time_slot_id" class="form-label">Available Time Slots <span class="required-indicator">*required</span></label>
                        <select name="time_slot_id" id="time_slot_id" class="form-select" required>
                            <option value="">Select a branch and date first</option>
                        </select>
                        <small style="color: #6c757d; font-size: 0.85rem;">Time slots will appear after selecting a branch and date.</small>
                    </div>

                    <div class="form-group">
                        <label for="consultation_type" class="form-label">Type of Consultation <span class="required-indicator">*required</span></label>
                        <select name="consultation_type" id="consultation_type" class="form-select" required>
                            <option value="">Select consultation type</option>
                            <option value="General Consultation">General Consultation</option>
                            <option value="Follow-up">Follow-up</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Specialist Referral">Specialist Referral</option>
                            <option value="Preventive Care">Preventive Care</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description of Symptoms/Condition</label>
                        <textarea name="description" id="description" class="form-textarea" 
                                  placeholder="Please describe your symptoms or the reason for consultation..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="medical_background" class="form-label">Medical Background</label>
                        <textarea name="medical_background" id="medical_background" class="form-textarea" 
                                  placeholder="Do you have any previous medical conditions, allergies, or ongoing treatments?"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="referral_source" class="form-label">How did you hear about Dwell?</label>
                        <select name="referral_source" id="referral_source" class="form-select">
                            <option value="">Select referral source</option>
                            <option value="Google Search">Google Search</option>
                            <option value="Social Media">Social Media</option>
                            <option value="Friend/Family">Friend/Family</option>
                            <option value="Advertisement">Advertisement</option>
                            <option value="Walk-in">Walk-in</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Section - Summary -->
        <div class="summary-card">
            <h2 class="card-title">Consultation Summary</h2>
            
            <div class="consultation-list" id="consultation-summary">
                <div class="consultation-item">
                    <div class="consultation-detail">
                        <span class="detail-label">Full Name:</span>
                        <span class="detail-value" id="summary-name">-</span>
                    </div>
                    <div class="consultation-detail">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value" id="summary-address">-</span>
                    </div>
                    <div class="consultation-detail">
                        <span class="detail-label">Branch:</span>
                        <span class="detail-value" id="summary-branch">-</span>
                    </div>
                    <div class="consultation-detail">
                        <span class="detail-label">Date & Time:</span>
                        <span class="detail-value" id="summary-datetime">-</span>
                    </div>
                    <div class="consultation-detail">
                        <span class="detail-label">Consultation Type:</span>
                        <span class="detail-value" id="summary-type">-</span>
                    </div>
                </div>
            </div>

            <div class="consultation-fee">
                <div class="fee-label">Consultation Fee</div>
                <div class="fee-amount" id="consultation-fee">₱700</div>
            </div>

            <button type="button" id="book-consultation-btn" class="book-button" disabled>
                BOOK NOW
            </button>
        </div>
    </div>
</div>

<!-- Combined Form Modal (Personal + Medical + Emergency) -->
<div id="combinedFormModal" class="modal">
    <div class="modal-content" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2 class="modal-title">Add Complete Information</h2>
            <button type="button" class="close-modal" onclick="closeCombinedFormModal()">&times;</button>
        </div>
        <form id="combined-form" method="POST">
            @csrf
            
            <!-- Personal Information Section -->
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.2rem; font-weight: 600; color: #197a8c; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5f7fa;">Personal Information</h3>
                
                <div class="form-group">
                    <label for="combined_first_name" class="form-label">First Name</label>
                    <input type="text" id="combined_first_name" name="first_name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="combined_middle_initial" class="form-label">Middle Initial</label>
                    <input type="text" id="combined_middle_initial" name="middle_initial" class="form-input" maxlength="1">
                </div>

                <div class="form-group">
                    <label for="combined_last_name" class="form-label">Last Name</label>
                    <input type="text" id="combined_last_name" name="last_name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="combined_address" class="form-label">Address</label>
                    <input type="text" id="combined_address" name="address" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="combined_birthday" class="form-label">Birthday</label>
                    <input type="date" id="combined_birthday" name="birthday" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="combined_contact_number" class="form-label">Contact Number</label>
                    <input type="text" id="combined_contact_number" name="contact_number" class="form-input" required>
                </div>
            </div>

            <!-- Medical Information Section -->
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.2rem; font-weight: 600; color: #197a8c; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5f7fa;">Medical Information</h3>
                
                <div class="form-group">
                    <label class="form-label">Comorbidities</label>
                    <div class="checkbox-group">
                        <input type="checkbox" id="combined_hypertension" name="hypertension" value="1">
                        <label for="combined_hypertension">Hypertension</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="combined_diabetes" name="diabetes" value="1">
                        <label for="combined_diabetes">Diabetes</label>
                    </div>
                    <div style="margin-top: 0.5rem;">
                        <input type="text" id="combined_comorbidities_others" name="comorbidities_others" class="form-input" placeholder="Others, please specify">
                    </div>
                </div>

                <div class="form-group">
                    <label for="combined_allergies" class="form-label">Allergies</label>
                    <textarea name="allergies" id="combined_allergies" class="form-textarea" placeholder="List any allergies"></textarea>
                </div>

                <div class="form-group">
                    <label for="combined_medications" class="form-label">Medications</label>
                    <textarea name="medications" id="combined_medications" class="form-textarea" placeholder="List current medications"></textarea>
                </div>

                <div class="form-group">
                    <label for="combined_anesthetics" class="form-label">Anesthetics</label>
                    <textarea name="anesthetics" id="combined_anesthetics" class="form-textarea" placeholder="List any anesthetics"></textarea>
                    <input type="text" id="combined_anesthetics_others" name="anesthetics_others" class="form-input" style="margin-top: 0.5rem;" placeholder="Others, please specify">
                </div>

                <div class="form-group">
                    <label for="combined_previous_hospitalizations_surgeries" class="form-label">Previous hospitalizations / surgeries</label>
                    <textarea name="previous_hospitalizations_surgeries" id="combined_previous_hospitalizations_surgeries" class="form-textarea" placeholder="List previous hospitalizations or surgeries"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Smoker?</label>
                    <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                        <div class="checkbox-group">
                            <input type="radio" id="combined_smoker_yes" name="smoker" value="yes">
                            <label for="combined_smoker_yes">Yes</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="radio" id="combined_smoker_no" name="smoker" value="no">
                            <label for="combined_smoker_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alcoholic beverage drinker?</label>
                    <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                        <div class="checkbox-group">
                            <input type="radio" id="combined_alcoholic_drinker_yes" name="alcoholic_drinker" value="yes">
                            <label for="combined_alcoholic_drinker_yes">Yes</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="radio" id="combined_alcoholic_drinker_no" name="alcoholic_drinker" value="no">
                            <label for="combined_alcoholic_drinker_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="combined_known_family_illnesses" class="form-label">Known family illnesses</label>
                    <textarea name="known_family_illnesses" id="combined_known_family_illnesses" class="form-textarea" placeholder="List known family illnesses"></textarea>
                </div>
            </div>

            <!-- Emergency Contact Section -->
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.2rem; font-weight: 600; color: #197a8c; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5f7fa;">Emergency Contact</h3>
                
                <div class="form-group">
                    <label for="combined_emergency_name" class="form-label">Name</label>
                    <input type="text" name="emergency_name" id="combined_emergency_name" class="form-input" placeholder="Enter name" required>
                </div>

                <div class="form-group">
                    <label for="combined_emergency_relationship" class="form-label">Relationship</label>
                    <input type="text" name="emergency_relationship" id="combined_emergency_relationship" class="form-input" placeholder="Enter relationship" required>
                </div>

                <div class="form-group">
                    <label for="combined_emergency_address" class="form-label">Address</label>
                    <textarea name="emergency_address" id="combined_emergency_address" class="form-textarea" placeholder="Enter address" required></textarea>
                </div>

                <div class="form-group">
                    <label for="combined_emergency_contact_number" class="form-label">Contact Number</label>
                    <input type="text" name="emergency_contact_number" id="combined_emergency_contact_number" class="form-input" placeholder="Enter contact number" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCombinedFormModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save All Information</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Medical Information Modal -->
<div id="addMedicalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add Medical Information</h2>
            <button type="button" class="close-modal" onclick="closeAddMedicalModal()">&times;</button>
        </div>
        <form id="add-medical-form" action="{{ route('medical-information.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Comorbidities</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="hypertension" name="hypertension" value="1">
                    <label for="hypertension">Hypertension</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="diabetes" name="diabetes" value="1">
                    <label for="diabetes">Diabetes</label>
                </div>
                <div style="margin-top: 0.5rem;">
                    <input type="text" id="comorbidities_others" name="comorbidities_others" class="form-input" placeholder="Others, please specify">
                </div>
            </div>

            <div class="form-group">
                <label for="allergies" class="form-label">Allergies</label>
                <textarea name="allergies" id="allergies" class="form-textarea" placeholder="List any allergies"></textarea>
            </div>

            <div class="form-group">
                <label for="medications" class="form-label">Medications</label>
                <textarea name="medications" id="medications" class="form-textarea" placeholder="List current medications"></textarea>
            </div>

            <div class="form-group">
                <label for="anesthetics" class="form-label">Anesthetics</label>
                <textarea name="anesthetics" id="anesthetics" class="form-textarea" placeholder="List any anesthetics"></textarea>
                <input type="text" id="anesthetics_others" name="anesthetics_others" class="form-input" style="margin-top: 0.5rem;" placeholder="Others, please specify">
            </div>

            <div class="form-group">
                <label for="previous_hospitalizations_surgeries" class="form-label">Previous hospitalizations / surgeries</label>
                <textarea name="previous_hospitalizations_surgeries" id="previous_hospitalizations_surgeries" class="form-textarea" placeholder="List previous hospitalizations or surgeries"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Smoker?</label>
                <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                    <div class="checkbox-group">
                        <input type="radio" id="smoker_yes" name="smoker" value="yes">
                        <label for="smoker_yes">Yes</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="radio" id="smoker_no" name="smoker" value="no">
                        <label for="smoker_no">No</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alcoholic beverage drinker?</label>
                <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                    <div class="checkbox-group">
                        <input type="radio" id="alcoholic_drinker_yes" name="alcoholic_drinker" value="yes">
                        <label for="alcoholic_drinker_yes">Yes</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="radio" id="alcoholic_drinker_no" name="alcoholic_drinker" value="no">
                        <label for="alcoholic_drinker_no">No</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="known_family_illnesses" class="form-label">Known family illnesses</label>
                <textarea name="known_family_illnesses" id="known_family_illnesses" class="form-textarea" placeholder="List known family illnesses"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddMedicalModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>


<!-- Delete Medical Information Confirmation Modal -->
<div id="deleteMedicalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Delete Medical Information</h2>
            <button type="button" class="close-modal" onclick="closeDeleteMedicalModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this medical information?</p>
            <p style="color: #dc3545; margin-top: 1rem;">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteMedicalModal()">Cancel</button>
            <form id="delete-medical-form" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- Add Emergency Contact Modal -->
<div id="addEmergencyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add Emergency Contact</h2>
            <button type="button" class="close-modal" onclick="closeAddEmergencyModal()">&times;</button>
        </div>
        <form id="add-emergency-form" action="{{ route('emergency-contact.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="emergency_name" class="form-label">Name</label>
                <input type="text" name="name" id="emergency_name" class="form-input" placeholder="Enter name" required>
            </div>

            <div class="form-group">
                <label for="emergency_relationship" class="form-label">Relationship</label>
                <input type="text" name="relationship" id="emergency_relationship" class="form-input" placeholder="Enter relationship" required>
            </div>

            <div class="form-group">
                <label for="emergency_address" class="form-label">Address</label>
                <textarea name="address" id="emergency_address" class="form-textarea" placeholder="Enter address" required></textarea>
            </div>

            <div class="form-group">
                <label for="emergency_contact_number" class="form-label">Contact Number</label>
                <input type="text" name="contact_number" id="emergency_contact_number" class="form-input" placeholder="Enter contact number" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddEmergencyModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>


<!-- Delete Emergency Contact Confirmation Modal -->
<div id="deleteEmergencyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Delete Emergency Contact</h2>
            <button type="button" class="close-modal" onclick="closeDeleteEmergencyModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this emergency contact?</p>
            <p style="color: #dc3545; margin-top: 1rem;">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteEmergencyModal()">Cancel</button>
            <form id="delete-emergency-form" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedProfile = null;
let isDrawing = false;
let canvas, ctx;

// Initialize signature canvas
document.addEventListener('DOMContentLoaded', function() {
    loadSelectedProfile();
    initializeConsultationForm();
    initializeSignatureCanvas();
    
    // Check if there's a selected profile in sessionStorage first
    const selectedIdFromStorage = sessionStorage.getItem('selectedPersonalInfoId');
    if (selectedIdFromStorage) {
        // Don't override with default if user has selected a specific profile
        // The loadSelectedProfile() function will handle loading it
    } else {
        // Initialize selectedProfile if default profile exists (only if no selection was made)
        const defaultProfileId = document.getElementById('selected_profile_id');
        if (defaultProfileId && defaultProfileId.value) {
            selectedProfile = defaultProfileId.value;
            // Don't set sessionStorage here - let the user's selection take precedence
        }
    }
});

// Signature Canvas Functions
function initializeSignatureCanvas() {
    canvas = document.getElementById('signature-canvas');
    if (!canvas) return;
    
    // Set canvas size based on display size
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = 200;
    
    ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    
    // Mouse events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    
    // Touch events for mobile
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);
    
    // Handle window resize - reinitialize canvas size
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            const rect = canvas.getBoundingClientRect();
            const signatureData = canvas.toDataURL('image/png');
            canvas.width = rect.width;
            canvas.height = 200;
            const img = new Image();
            img.onload = function() {
                ctx.drawImage(img, 0, 0);
            };
            img.src = signatureData;
        }, 250);
    });
}

function startDrawing(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    const x = (e.clientX || e.touches[0].clientX) - rect.left;
    const y = (e.clientY || e.touches[0].clientY) - rect.top;
    ctx.beginPath();
    ctx.moveTo(x, y);
}

function draw(e) {
    if (!isDrawing) return;
    e.preventDefault();
    const rect = canvas.getBoundingClientRect();
    const x = (e.clientX || e.touches[0].clientX) - rect.left;
    const y = (e.clientY || e.touches[0].clientY) - rect.top;
    ctx.lineTo(x, y);
    ctx.stroke();
}

function stopDrawing() {
    if (isDrawing) {
        isDrawing = false;
        saveSignature();
    }
}

function handleTouch(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                      e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function clearSignature() {
    if (canvas && ctx) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('form_signature').value = '';
    }
}

function saveSignature() {
    if (canvas) {
        const signatureData = canvas.toDataURL('image/png');
        document.getElementById('form_signature').value = signatureData;
    }
}

function loadSignatureToCanvas(signatureData) {
    if (!canvas || !ctx || !signatureData) return;
    const img = new Image();
    img.onload = function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0);
    };
    img.src = signatureData;
}

// Personal Info CRUD Functions

function cancelPersonalInfoForm() {
    document.getElementById('personal-info-form-container').style.display = 'none';
    resetPersonalInfoForm();
}

function resetPersonalInfoForm() {
    document.getElementById('personal-info-form').reset();
    document.getElementById('form-profile-id').value = '';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('form-submit-btn').textContent = 'Save';
    clearSignature();
    document.getElementById('form_is_default').checked = false;
}


function deleteProfile(profileId) {
    if (!confirm('Are you sure you want to delete this personal information? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('_method', 'DELETE');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch(`{{ route('personal-information.destroy', ':id') }}`.replace(':id', profileId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to delete profile. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting. Please try again.');
    });
}

function setDefaultProfile(profileId) {
    fetch(`{{ route('personal-information.set-default', ':id') }}`.replace(':id', profileId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to set default profile. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function selectProfileForConsultation(profileId) {
    selectedProfile = profileId;
    sessionStorage.setItem('selectedPersonalInfoId', profileId);
    
    // Update consultation form hidden fields
    fetch(`{{ route('personal-information.index') }}`)
        .then(response => response.json())
        .then(profiles => {
            const profile = profiles.find(p => p.id == profileId);
            if (profile) {
                if (document.getElementById('selected_profile_id')) {
                    document.getElementById('selected_profile_id').value = profile.id;
                }
                if (document.getElementById('first_name')) {
                    document.getElementById('first_name').value = profile.first_name || '';
                }
                if (document.getElementById('middle_initial')) {
                    document.getElementById('middle_initial').value = profile.middle_initial || '';
                }
                if (document.getElementById('last_name')) {
                    document.getElementById('last_name').value = profile.last_name || '';
                }
                if (document.getElementById('address')) {
                    document.getElementById('address').value = profile.address || '';
                }
                if (document.getElementById('date_of_birth')) {
                    document.getElementById('date_of_birth').value = profile.birthday || '';
                }
                if (document.getElementById('contact_number')) {
                    document.getElementById('contact_number').value = profile.contact_number || '';
                }
                
                // Update summary if function exists
                if (window.updateSummary) {
                    updateSummary();
                }
                
                // Highlight selected profile
                document.querySelectorAll('.profile-card').forEach(card => {
                    card.classList.remove('selected');
                });
                const selectedCard = document.getElementById(`profile-${profileId}`);
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                }
            }
        })
        .catch(error => {
            console.error('Error loading profile:', error);
        });
}

// Handle personal info form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('personal-info-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Save signature before submit
            saveSignature();
            
            const formData = new FormData(form);
            const profileId = document.getElementById('form-profile-id').value;
            const method = document.getElementById('form-method').value;
            
            let url = '{{ route("personal-information.store") }}';
            if (method === 'PUT' && profileId) {
                url = `{{ route('personal-information.update', ':id') }}`.replace(':id', profileId);
                formData.append('_method', 'PUT');
            }
            
            const submitBtn = document.getElementById('form-submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to save personal information. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = method === 'PUT' ? 'Update' : 'Save';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = method === 'PUT' ? 'Update' : 'Save';
            });
        });
    }
});

// Load selected profile from sessionStorage or API
function loadSelectedProfile() {
    const selectedId = sessionStorage.getItem('selectedPersonalInfoId');
    
    // Only load from sessionStorage if there's a selected ID
    if (selectedId) {
        // Load profile from API
        fetch('{{ route("personal-information.index") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(profiles => {
                const profile = profiles.find(p => p.id == selectedId);
                if (profile) {
                    // Update the displayed profile
                    selectProfile(profile);
                } else {
                    // If selected profile not found, clear sessionStorage
                    sessionStorage.removeItem('selectedPersonalInfoId');
                }
            })
            .catch(error => {
                console.error('Error loading profiles:', error);
            });
    }
    // If no selectedId in sessionStorage, the server-side rendered content is already correct
}

// Select a profile (called when returning from select page)
function selectProfile(profile) {
    selectedProfile = profile.id;
    
    // Populate hidden form fields
    if (document.getElementById('first_name')) {
        document.getElementById('first_name').value = profile.first_name || '';
    }
    if (document.getElementById('middle_initial')) {
        document.getElementById('middle_initial').value = profile.middle_initial || '';
    }
    if (document.getElementById('last_name')) {
        document.getElementById('last_name').value = profile.last_name || '';
    }
    if (document.getElementById('address')) {
        document.getElementById('address').value = profile.address || '';
    }
    if (document.getElementById('date_of_birth')) {
        // Handle birthday format - it might be a date string or object
        let birthday = profile.birthday || '';
        if (birthday) {
            // If it's a date string, format it to Y-m-d
            if (typeof birthday === 'string') {
                const date = new Date(birthday);
                if (!isNaN(date.getTime())) {
                    birthday = date.toISOString().split('T')[0];
                }
            }
        }
        document.getElementById('date_of_birth').value = birthday;
    }
    if (document.getElementById('contact_number')) {
        document.getElementById('contact_number').value = profile.contact_number || '';
    }
    if (document.getElementById('selected_profile_id')) {
        document.getElementById('selected_profile_id').value = profile.id || '';
    }
    
    // Update displayed profile name
    const nameElement = document.getElementById('displayed-profile-name');
    if (nameElement) {
        // Build full_name from profile data if not available
        let fullName = profile.full_name;
        if (!fullName && profile.first_name) {
            fullName = profile.first_name;
            if (profile.middle_initial) {
                fullName += ' ' + profile.middle_initial + '.';
            }
            if (profile.last_name) {
                fullName += ' ' + profile.last_name;
            }
        }
        if (fullName) {
            nameElement.textContent = fullName;
        }
    }
    
    if (addressElement && profile.address) {
        addressElement.textContent = profile.address;
    }
    
    // Update summary
    if (window.updateSummary) {
        updateSummary();
    }
}

    // Initialize consultation form
function initializeConsultationForm() {
    const form = document.getElementById('consultation-form');
    const branchSelect = document.getElementById('branch_id');
    const dateInput = document.getElementById('date');
    const slotSelect = document.getElementById('time_slot_id');
    const bookButton = document.getElementById('book-consultation-btn');
    
    // Form fields for summary
    const firstNameInput = document.getElementById('first_name');
    const middleInitialInput = document.getElementById('middle_initial');
    const lastNameInput = document.getElementById('last_name');
    const addressInput = document.getElementById('address');
    const consultationTypeSelect = document.getElementById('consultation_type');
    
    // Summary elements
    const summaryName = document.getElementById('summary-name');
    const summaryAddress = document.getElementById('summary-address');
    const summaryBranch = document.getElementById('summary-branch');
    const summaryDatetime = document.getElementById('summary-datetime');
    const summaryType = document.getElementById('summary-type');
    const consultationFeeElement = document.getElementById('consultation-fee');
    
    // Store loaded slots data
    let loadedSlots = [];

    // Update summary when form fields change
    function updateSummary() {
        // Update name
        const firstName = firstNameInput.value.trim();
        const middleInitial = middleInitialInput.value.trim();
        const lastName = lastNameInput.value.trim();
        
        let fullName = firstName;
        if (middleInitial) fullName += ` ${middleInitial}.`;
        if (lastName) fullName += ` ${lastName}`;
        
        summaryName.textContent = fullName || '-';
        
        // Update address
        summaryAddress.textContent = addressInput.value.trim() || '-';
        
        // Update branch
        const selectedBranch = branchSelect.options[branchSelect.selectedIndex];
        summaryBranch.textContent = selectedBranch.value ? selectedBranch.text : '-';
        
        // Update date and time
        const selectedDate = dateInput.value;
        const selectedSlot = slotSelect.options[slotSelect.selectedIndex];
        
        if (selectedDate && selectedSlot.value) {
            const dateObj = new Date(selectedDate);
            const formattedDate = dateObj.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            summaryDatetime.textContent = `${formattedDate} - ${selectedSlot.text}`;
        } else {
            summaryDatetime.textContent = '-';
        }
        
        // Update consultation type
        const selectedType = consultationTypeSelect.options[consultationTypeSelect.selectedIndex];
        summaryType.textContent = selectedType.value ? selectedType.text : '-';
        
        // Update consultation fee from selected slot
        const selectedSlotOption = slotSelect.options[slotSelect.selectedIndex];
        if (selectedSlotOption && selectedSlotOption.value) {
            const consultationFee = selectedSlotOption.getAttribute('data-consultation-fee') || '700';
            consultationFeeElement.textContent = `₱${parseFloat(consultationFee).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        } else {
            consultationFeeElement.textContent = '₱700';
        }
        
        // Enable/disable book button
        const isFormValid = firstName && lastName && addressInput.value.trim() && 
                           branchSelect.value && dateInput.value && slotSelect.value && 
                           consultationTypeSelect.value;
        
        bookButton.disabled = !isFormValid;
    }

    // Load available time slots
    function loadAvailableTimeSlots() {
        const branchId = branchSelect.value;
        const date = dateInput.value;

        if (!branchId || !date) {
            slotSelect.innerHTML = '<option value="">Select a branch and date first</option>';
            updateSummary();
            return;
        }

        // Show loading state
        slotSelect.innerHTML = '<option value="">Loading available slots...</option>';
        slotSelect.disabled = true;

        fetch(`{{ route('consultations.available-slots') }}?branch_id=${branchId}&date=${date}`)
            .then(response => response.json())
            .then(slots => {
                // Store slots data for later use
                loadedSlots = slots;
                
                slotSelect.innerHTML = '<option value="">Select a time slot</option>';
                
                if (slots.length === 0) {
                    slotSelect.innerHTML = '<option value="">No available slots for this date</option>';
                } else {
                    slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.id;
                        option.textContent = `${slot.start_time} - ${slot.end_time}`;
                        option.setAttribute('data-consultation-fee', slot.consultation_fee || '700');
                        slotSelect.appendChild(option);
                    });
                }
                
                slotSelect.disabled = false;
                updateSummary();
            })
            .catch(error => {
                console.error('Error loading slots:', error);
                slotSelect.innerHTML = '<option value="">Error loading slots</option>';
                slotSelect.disabled = false;
                updateSummary();
            });
    }

    // Event listeners
    branchSelect.addEventListener('change', loadAvailableTimeSlots);
    dateInput.addEventListener('change', loadAvailableTimeSlots);
    slotSelect.addEventListener('change', updateSummary);
    consultationTypeSelect.addEventListener('change', updateSummary);

    // Book consultation
    bookButton.addEventListener('click', function() {
        if (!bookButton.disabled) {
            // Validate that personal information is selected
            const profileIdField = document.getElementById('selected_profile_id');
            const profileId = profileIdField ? profileIdField.value : null;
            
            // Check both the JavaScript variable and the hidden field
            if ((!selectedProfile || selectedProfile === 'null' || selectedProfile === '') && (!profileId || profileId === '')) {
                alert('Please select personal information first.');
                return;
            }
            
            // Ensure selectedProfile is set from the field if it exists
            if (profileId && (!selectedProfile || selectedProfile === 'null' || selectedProfile === '')) {
                selectedProfile = profileId;
            }
            
            // Show loading state
            bookButton.textContent = 'BOOKING...';
            bookButton.disabled = true;
            
            // Submit the form
            form.submit();
        }
    });

    // Initial summary update
    updateSummary();
    
    // Make updateSummary available globally
    window.updateSummary = updateSummary;
}

// Combined Form Modal Functions
function openCombinedFormModal() {
    const modal = document.getElementById('combinedFormModal');
    modal.classList.add('show');
    // Reset form
    document.getElementById('combined-form').reset();
}

function closeCombinedFormModal() {
    const modal = document.getElementById('combinedFormModal');
    modal.classList.remove('show');
}

// Handle combined form submission
document.getElementById('combined-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Disable submit button and show loading
    submitButton.disabled = true;
    submitButton.textContent = 'Saving...';
    
    // Extract data for each form
    const personalData = new FormData();
    personalData.append('_token', formData.get('_token'));
    personalData.append('first_name', formData.get('first_name'));
    personalData.append('middle_initial', formData.get('middle_initial'));
    personalData.append('last_name', formData.get('last_name'));
    personalData.append('address', formData.get('address'));
    personalData.append('birthday', formData.get('birthday'));
    personalData.append('contact_number', formData.get('contact_number'));
    personalData.append('is_default', '1');
    
    const medicalData = new FormData();
    medicalData.append('_token', formData.get('_token'));
    medicalData.append('hypertension', formData.get('hypertension') || '0');
    medicalData.append('diabetes', formData.get('diabetes') || '0');
    medicalData.append('comorbidities_others', formData.get('comorbidities_others') || '');
    medicalData.append('allergies', formData.get('allergies') || '');
    medicalData.append('medications', formData.get('medications') || '');
    medicalData.append('anesthetics', formData.get('anesthetics') || '');
    medicalData.append('anesthetics_others', formData.get('anesthetics_others') || '');
    medicalData.append('previous_hospitalizations_surgeries', formData.get('previous_hospitalizations_surgeries') || '');
    medicalData.append('smoker', formData.get('smoker') || '');
    medicalData.append('alcoholic_drinker', formData.get('alcoholic_drinker') || '');
    medicalData.append('known_family_illnesses', formData.get('known_family_illnesses') || '');
    medicalData.append('is_default', '1');
    
    const emergencyData = new FormData();
    emergencyData.append('_token', formData.get('_token'));
    emergencyData.append('name', formData.get('emergency_name'));
    emergencyData.append('relationship', formData.get('emergency_relationship'));
    emergencyData.append('address', formData.get('emergency_address'));
    emergencyData.append('contact_number', formData.get('emergency_contact_number'));
    emergencyData.append('is_default', '1');
    
    // Submit all three forms sequentially
    Promise.all([
        fetch('{{ route("personal-information.store") }}', {
            method: 'POST',
            body: personalData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token')
            }
        }),
        fetch('{{ route("medical-information.store") }}', {
            method: 'POST',
            body: medicalData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token')
            }
        }),
        fetch('{{ route("emergency-contact.store") }}', {
            method: 'POST',
            body: emergencyData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token')
            }
        })
    ])
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(results => {
        const allSuccess = results.every(r => r.success);
        if (allSuccess) {
            closeCombinedFormModal();
            window.location.reload();
        } else {
            const errorMessages = results.filter(r => !r.success).map(r => r.message || 'An error occurred').join('\n');
            alert('Some information could not be saved:\n' + errorMessages);
            submitButton.disabled = false;
            submitButton.textContent = 'Save All Information';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving. Please try again.');
        submitButton.disabled = false;
        submitButton.textContent = 'Save All Information';
    });
});

// Medical Information Modal Functions
function openAddMedicalModal() {
    const modal = document.getElementById('addMedicalModal');
    modal.classList.add('show');
    // Reset form
    document.getElementById('add-medical-form').reset();
}

function closeAddMedicalModal() {
    const modal = document.getElementById('addMedicalModal');
    modal.classList.remove('show');
}


function openDeleteMedicalModal(medicalInfoId) {
    const modal = document.getElementById('deleteMedicalModal');
    const form = document.getElementById('delete-medical-form');
    form.action = `{{ route('medical-information.destroy', ':id') }}`.replace(':id', medicalInfoId);
    modal.classList.add('show');
}

function closeDeleteMedicalModal() {
    const modal = document.getElementById('deleteMedicalModal');
    modal.classList.remove('show');
}

// Close modals when clicking outside - handled below

// Handle add medical form submission
document.getElementById('add-medical-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAddMedicalModal();
            window.location.reload();
        } else {
            alert(data.message || 'Failed to save medical information. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving. Please try again.');
    });
});


// Handle delete medical form submission
document.getElementById('delete-medical-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    formData.append('_method', 'DELETE');
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeDeleteMedicalModal();
            window.location.reload();
        } else {
            alert(data.message || 'Failed to delete medical information. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting. Please try again.');
    });
});

// Emergency Contact Modal Functions
function openAddEmergencyModal() {
    const modal = document.getElementById('addEmergencyModal');
    modal.classList.add('show');
    // Reset form
    document.getElementById('add-emergency-form').reset();
}

function closeAddEmergencyModal() {
    const modal = document.getElementById('addEmergencyModal');
    modal.classList.remove('show');
}


function openDeleteEmergencyModal(emergencyContactId) {
    const modal = document.getElementById('deleteEmergencyModal');
    const form = document.getElementById('delete-emergency-form');
    form.action = `{{ route('emergency-contact.destroy', ':id') }}`.replace(':id', emergencyContactId);
    modal.classList.add('show');
}

function closeDeleteEmergencyModal() {
    const modal = document.getElementById('deleteEmergencyModal');
    modal.classList.remove('show');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const combinedModal = document.getElementById('combinedFormModal');
    const addModal = document.getElementById('addMedicalModal');
    const deleteModal = document.getElementById('deleteMedicalModal');
    const addEmergencyModal = document.getElementById('addEmergencyModal');
    const deleteEmergencyModal = document.getElementById('deleteEmergencyModal');
    
    if (event.target == combinedModal) {
        closeCombinedFormModal();
    }
    if (event.target == addModal) {
        closeAddMedicalModal();
    }
    if (event.target == deleteModal) {
        closeDeleteMedicalModal();
    }
    if (event.target == addEmergencyModal) {
        closeAddEmergencyModal();
    }
    if (event.target == deleteEmergencyModal) {
        closeDeleteEmergencyModal();
    }
}

// Handle add emergency form submission
document.getElementById('add-emergency-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAddEmergencyModal();
            window.location.reload();
        } else {
            alert(data.message || 'Failed to save emergency contact. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving. Please try again.');
    });
});


// Handle delete emergency form submission
document.getElementById('delete-emergency-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    formData.append('_method', 'DELETE');
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeDeleteEmergencyModal();
            window.location.reload();
        } else {
            alert(data.message || 'Failed to delete emergency contact. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting. Please try again.');
    });
});
</script>
@endpush
