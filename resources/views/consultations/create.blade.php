@extends('layouts.patient')

@push('styles')
<style>
    /* Service Booking Form Styling */
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
        margin-bottom: 2rem;
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
        color: #6c757d;
        font-size: 1.2rem;
        font-weight: bold;
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

    .branch-display {
        background: #e5f7fa;
        padding: 1rem;
        border-radius: 10px;
        border-left: 4px solid #197a8c;
        margin-bottom: 1.5rem;
    }

    .branch-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #197a8c;
        margin-bottom: 0.25rem;
    }

    .branch-address {
        font-size: 0.95rem;
        color: #6c757d;
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

    .service-list {
        margin-bottom: 2rem;
    }

    .service-item {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 1rem;
        border-left: 4px solid #197a8c;
    }

    .service-item:last-child {
        margin-bottom: 0;
    }

    .service-detail {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .service-detail:last-child {
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

    .service-fee {
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

        .consultation-container {
            padding: 1rem;
        }

        .consultation-title {
            font-size: 2rem;
        }

        .personal-info-card,
        .summary-card {
            padding: 1.5rem;
        }

        .summary-card {
            position: static;
        }
    }
</style>
@endpush

@section('content')
<div class="consultation-container">
    <!-- Header -->
    <div class="consultation-header">
        <h1 class="consultation-title">BOOK SERVICES</h1>
        <p class="consultation-subtitle">Complete your service booking</p>
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
            <a href="{{ route('personal-information.select', ['return' => request()->fullUrl()]) }}" class="personal-info-row">
                <div class="personal-info-row-content">
                    <span class="personal-info-label">Personal Info</span>
                    <span>—</span>
                    <span class="personal-info-name">{{ $defaultProfile ? $defaultProfile->full_name : 'Add Info' }}</span>
                </div>
                <span class="personal-info-chevron">></span>
            </a>

            <!-- Branch Display -->
            <div class="branch-display">
                <div class="branch-name">{{ $branch->name }}</div>
                @if($branch->address)
                    <div class="branch-address">{{ $branch->address }}</div>
                @endif
            </div>

            <!-- Booking Details Section -->
            <div class="personal-info-card">
                <h2 class="card-title">Booking Details</h2>
                <form id="booking-form" action="{{ route('consultations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                    <input type="hidden" id="selected_profile_id" name="personal_information_id" value="{{ $defaultProfile ? $defaultProfile->id : '' }}">
                    
                    <!-- Hidden fields for personal information -->
                    <input type="hidden" id="first_name" name="first_name" value="{{ $defaultProfile ? $defaultProfile->first_name : '' }}">
                    <input type="hidden" id="middle_initial" name="middle_initial" value="{{ $defaultProfile ? $defaultProfile->middle_initial : '' }}">
                    <input type="hidden" id="last_name" name="last_name" value="{{ $defaultProfile ? $defaultProfile->last_name : '' }}">
                    <input type="hidden" id="address" name="address" value="{{ $defaultProfile ? $defaultProfile->address : '' }}">
                    <input type="hidden" id="date_of_birth" name="date_of_birth" value="{{ $defaultProfile ? $defaultProfile->birthday->format('Y-m-d') : '' }}">
                    <input type="hidden" id="contact_number" name="contact_number" value="{{ $defaultProfile ? $defaultProfile->contact_number : '' }}">

                    <!-- Hidden fields for cart items or service IDs -->
                    @if(isset($isDirectBooking) && $isDirectBooking)
                        @foreach($cartItems as $cartItem)
                            <input type="hidden" name="service_ids[]" value="{{ $cartItem->service_id }}">
                        @endforeach
                    @else
                        @foreach($cartItems as $cartItem)
                            <input type="hidden" name="cart_items[]" value="{{ $cartItem->id }}">
                        @endforeach
                    @endif

                    <div class="form-group">
                        <label for="date" class="form-label">Select Date</label>
                        <input type="date" name="date" id="date" class="form-input" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Additional Notes (Optional)</label>
                        <textarea name="description" id="description" class="form-textarea" 
                                  placeholder="Any special requests or notes..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="medical_background" class="form-label">Medical Background (Optional)</label>
                        <textarea name="medical_background" id="medical_background" class="form-textarea" 
                                  placeholder="Do you have any previous medical conditions, allergies, or ongoing treatments?"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="referral_source" class="form-label">How did you hear about Dwell? (Optional)</label>
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
            <h2 class="card-title">Booking Summary</h2>
            
            <div class="service-list" id="service-summary">
                @foreach($cartItems as $cartItem)
                    <div class="service-item">
                        <div class="service-detail">
                            <span class="detail-label">Service:</span>
                            <span class="detail-value">{{ $cartItem->service->name }}</span>
                        </div>
                        <div class="service-detail">
                            <span class="detail-label">Quantity:</span>
                            <span class="detail-value">{{ $cartItem->quantity }}</span>
                        </div>
                        <div class="service-detail">
                            <span class="detail-label">Price:</span>
                            <span class="detail-value" style="display: flex; flex-direction: column; gap: 0.25rem;">
                                @include('components.service-price', ['pricing' => $cartItem->service->pricing, 'layout' => 'compact'])
                                <small style="color: var(--light-text);">
                                    x{{ $cartItem->quantity }} = ₱{{ number_format($cartItem->service->pricing['display_price'] * $cartItem->quantity, 2) }}
                                </small>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="service-fee">
                <div class="fee-label">Total Amount</div>
                <div class="fee-amount" id="total-amount">₱{{ number_format($totalPrice, 2) }}</div>
            </div>

            <div class="service-item" style="margin-bottom: 1.5rem;">
                <div class="service-detail">
                    <span class="detail-label">Branch:</span>
                    <span class="detail-value" id="summary-branch">{{ $branch->name }}</span>
                </div>
                <div class="service-detail">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value" id="summary-date">-</span>
                </div>
                <div class="service-detail">
                    <span class="detail-label">Patient:</span>
                    <span class="detail-value" id="summary-name">{{ $defaultProfile ? $defaultProfile->full_name : '-' }}</span>
                </div>
            </div>

            <button type="button" id="book-service-btn" class="book-button" disabled>
                BOOK SERVICES
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedProfile = null;

document.addEventListener('DOMContentLoaded', function() {
    loadSelectedProfile();
    initializeBookingForm();
    
    // Initialize selectedProfile if default profile exists
    const defaultProfileId = document.getElementById('selected_profile_id');
    if (defaultProfileId && defaultProfileId.value) {
        selectedProfile = defaultProfileId.value;
        if (selectedProfile) {
            sessionStorage.setItem('selectedPersonalInfoId', selectedProfile);
        }
    }
});

// Load selected profile from sessionStorage or API
function loadSelectedProfile() {
    const selectedId = sessionStorage.getItem('selectedPersonalInfoId');
    
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
                    selectProfile(profile);
                } else {
                    sessionStorage.removeItem('selectedPersonalInfoId');
                }
            })
            .catch(error => {
                console.error('Error loading profiles:', error);
            });
    }
}

// Select a profile (called when returning from select page)
function selectProfile(profile) {
    selectedProfile = profile;
    
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
        document.getElementById('date_of_birth').value = profile.birthday || '';
    }
    if (document.getElementById('contact_number')) {
        document.getElementById('contact_number').value = profile.contact_number || '';
    }
    if (document.getElementById('selected_profile_id')) {
        document.getElementById('selected_profile_id').value = profile.id || '';
    }
    
    // Update displayed profile info
    const nameElement = document.querySelector('.personal-info-name');
    if (nameElement) {
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
    
    // Update summary
    if (window.updateSummary) {
        updateSummary();
    }
}

// Initialize booking form
function initializeBookingForm() {
    const form = document.getElementById('booking-form');
    const dateInput = document.getElementById('date');
    const bookButton = document.getElementById('book-service-btn');
    
    // Summary elements
    const summaryName = document.getElementById('summary-name');
    const summaryDate = document.getElementById('summary-date');
    const firstNameInput = document.getElementById('first_name');
    const middleInitialInput = document.getElementById('middle_initial');
    const lastNameInput = document.getElementById('last_name');

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
        
        // Update date
        const selectedDate = dateInput.value;
        if (selectedDate) {
            const dateObj = new Date(selectedDate);
            const formattedDate = dateObj.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            summaryDate.textContent = formattedDate;
        } else {
            summaryDate.textContent = '-';
        }
        
        // Enable/disable book button
        const isFormValid = firstName && lastName && dateInput.value;
        
        bookButton.disabled = !isFormValid;
    }

    // Event listeners
    dateInput.addEventListener('change', updateSummary);

    // Book service
    bookButton.addEventListener('click', function() {
        if (!bookButton.disabled) {
            // Validate that personal information is selected
            const profileIdField = document.getElementById('selected_profile_id');
            const profileId = profileIdField ? profileIdField.value : null;
            
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
</script>
@endpush
