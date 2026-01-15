@extends('layouts.dashboard')
@section('page-title', 'Create Slot')

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/form-inputs.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script>
// Global function to handle button click - must be defined early
function handleGenerateSlotsClick(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const modal = document.getElementById('slotSettingsModal');

    if (!startTime || !endTime) {
        alert('Please enter both start and end times');
        return;
    }

    if (!startDate || !endDate) {
        alert('Please select a date range first');
        return;
    }

    modal.classList.add('show');
    modal.style.display = 'flex';
    modal.style.zIndex = '99999';
}

// Make function globally accessible
window.handleGenerateSlotsClick = handleGenerateSlotsClick;
</script>
@endpush

@section('content')
<style>
    .slot-form-container {
        background: rgba(255, 250, 240, 0.75) !important;
        border-radius: 16px;
        padding: 1rem;
        box-shadow: 
            0 4px 12px rgba(0, 0, 0, 0.08),
            0 2px 6px rgba(255, 215, 0, 0.15),
            0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .slot-form-container:hover {
        box-shadow: 
            0 8px 24px rgba(0, 0, 0, 0.12),
            0 4px 12px rgba(255, 215, 0, 0.25),
            0 2px 6px rgba(0, 0, 0, 0.15);
        background: rgba(255, 252, 248, 0.85) !important;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .form-actions {
        margin-top: 0.75rem;
        display: flex;
        gap: 0.75rem;
    }

    .container {
        padding: 0 20px;
    }

    /* Gold date range picker styling */
    .flatpickr-input {
        border: 1px solid #FFD700 !important;
        border-radius: 6px;
        padding: 0.75rem 0.75rem 0.75rem 2.75rem !important;
        font-size: 1rem;
        background: #ffffff;
        color: #2c3e50;
        transition: all 0.3s ease;
    }

    .flatpickr-input:focus {
        outline: none;
        border-color: #FFD700 !important;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
    }

    .flatpickr-calendar {
        border: 1px solid #FFD700;
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.2);
    }

    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background: #FFD700 !important;
        border-color: #FFD700 !important;
        color: #2c3e50 !important;
        font-weight: 600;
    }

    .flatpickr-day.inRange {
        background: rgba(255, 215, 0, 0.2) !important;
        border-color: rgba(255, 215, 0, 0.3) !important;
    }

    .flatpickr-day:hover {
        background: rgba(255, 215, 0, 0.3) !important;
    }

    .flatpickr-months .flatpickr-month {
        background: rgba(255, 215, 0, 0.1);
    }

    .flatpickr-current-month {
        color: #2c3e50;
    }

    .flatpickr-prev-month,
    .flatpickr-next-month {
        color: #FFD700 !important;
    }

    .flatpickr-prev-month:hover,
    .flatpickr-next-month:hover {
        color: #e6c200 !important;
    }

    .time-range-section {
        margin-top: 1rem;
        padding: 1rem;
        background: rgba(255, 215, 0, 0.05);
        border-radius: 8px;
        border: 1px dashed rgba(255, 215, 0, 0.3);
    }

    .generate-slots-btn {
        background: rgba(255, 215, 0, 0.2);
        border: 2px solid #FFD700;
        color: #2c3e50;
        padding: 0.6rem 1.2rem;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 0.5rem;
        position: relative;
        z-index: 10;
        pointer-events: auto !important;
    }

    .generate-slots-btn:hover {
        background: rgba(255, 215, 0, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 215, 0, 0.2);
    }

    .slots-preview {
        margin-top: 1rem;
        padding: 1rem;
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid rgba(255, 215, 0, 0.3);
        max-height: 300px;
        overflow-y: auto;
    }

    .slot-item {
        padding: 0.5rem;
        margin: 0.25rem 0;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 4px;
        border-left: 3px solid #FFD700;
    }

    .remaining-time-warning {
        margin-top: 0.5rem;
        padding: 0.75rem;
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 6px;
        color: #856404;
        font-size: 0.9rem;
    }

    .hidden {
        display: none;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99999;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-content {
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: modalFadeIn 0.3s ease;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(255, 215, 0, 0.3);
    }

    .modal-header h3 {
        margin: 0;
        color: #2c3e50;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .modal-body {
        margin-bottom: 1.5rem;
    }

    .modal-footer {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .modal-input-group {
        margin-bottom: 1.25rem;
    }

    .modal-input-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .modal-input-group input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .modal-input-group input:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
    }

    .modal-input-group .help-text {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .modal-btn {
        padding: 0.6rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .modal-btn-primary {
        background: #FFD700;
        color: #2c3e50;
    }

    .modal-btn-primary:hover {
        background: #e6c200;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 215, 0, 0.3);
    }

    .modal-btn-secondary {
        background: #e9ecef;
        color: #2c3e50;
    }

    .modal-btn-secondary:hover {
        background: #dee2e6;
        transform: translateY(-2px);
    }
</style>

<div class="container">
    <div class="slot-form-container compact-form">
        <form method="POST" action="{{ route('admin.slots.store') }}" id="slotForm">
            @csrf
            
            <!-- Branch Display (Admin can only create for their branch) -->
            <div class="modern-input-wrapper">
                <label>Branch</label>
                <div class="modern-input-container">
                    <i class="fas fa-building input-icon"></i>
                    <input type="text" value="{{ $branch->name }}" readonly style="background-color: #f3f4f6; cursor: not-allowed;">
                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                </div>
            </div>

            <!-- Consultation Fee -->
            <div class="modern-input-wrapper">
                <label for="consultation_fee">Consultation Fee</label>
                <div class="modern-input-container">
                    <i class="fas fa-money-bill input-icon"></i>
                    <input type="number" id="consultation_fee" name="consultation_fee" value="{{ old('consultation_fee', '600') }}" step="0.01" min="0" placeholder="Enter consultation fee" required>
                </div>
                @error('consultation_fee')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Date Range Picker -->
            <div class="modern-input-wrapper">
                <label for="date_range">Date Range</label>
                <div class="modern-input-container">
                    <i class="fas fa-calendar-alt input-icon"></i>
                    <input type="text" id="date_range" name="date_range" class="flatpickr-input" placeholder="Select date range..." readonly required>
                    <input type="hidden" id="start_date" name="start_date">
                    <input type="hidden" id="end_date" name="end_date">
                </div>
                @error('date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Time Range Section -->
            <div class="time-range-section">
                <div class="form-row">
                    <div class="modern-input-wrapper">
                        <label for="start_time">Start Time</label>
                        <div class="modern-input-container">
                            <i class="fas fa-clock input-icon"></i>
                            <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                        </div>
                        @error('start_time')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modern-input-wrapper">
                        <label for="end_time">End Time</label>
                        <div class="modern-input-container">
                            <i class="fas fa-clock input-icon"></i>
                            <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                        </div>
                        @error('end_time')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button type="button" id="generateSlotsBtn" class="generate-slots-btn">
                    <i class="fas fa-magic"></i> Generate Time Slots Automatically
                </button>

                <div id="remainingTimeWarning" class="remaining-time-warning hidden"></div>

                <div id="slotsPreview" class="slots-preview hidden">
                    <strong>Generated Slots:</strong>
                    <div id="slotsList"></div>
                </div>
            </div>

            <!-- Hidden input for multiple slots -->
            <input type="hidden" id="slots_data" name="slots_data">

            @error('time')
                <div style="color: #dc3545; font-size: 0.875rem; margin-bottom: 1rem; padding: 0.75rem; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;">{{ $message }}</div>
            @enderror

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Create Time Slot(s)</button>
                <a href="{{ route('admin.slots') }}" class="btn btn-accent" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Slot Generation Settings -->
<div class="modal-overlay" id="slotSettingsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-cog"></i> Time Slot Settings</h3>
        </div>
        <div class="modal-body">
            <div class="modal-input-group">
                <label for="consultation_duration">
                    Consultation Duration (minutes) <span style="color: #dc3545;">*</span>
                </label>
                <input type="number" id="consultation_duration" min="1" value="20" required>
                <div class="help-text">Enter the duration of each consultation in minutes (e.g., 20, 30, 45)</div>
            </div>
            <div class="modal-input-group">
                <label for="delay_time">
                    Delay/Buffer Time (minutes) <span style="color: #6c757d; font-size: 0.85rem;">(Optional)</span>
                </label>
                <input type="number" id="delay_time" min="0" value="" placeholder="Leave blank for no delay">
                <div class="help-text">Optional buffer time between consultations (e.g., 5 minutes). Leave blank if no delay is needed.</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" id="cancelModalBtn">Cancel</button>
            <button type="button" class="modal-btn modal-btn-primary" id="generateModalBtn">Generate Slots</button>
        </div>
    </div>
</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const generateSlotsBtn = document.getElementById('generateSlotsBtn');
    const slotsPreview = document.getElementById('slotsPreview');
    const slotsList = document.getElementById('slotsList');
    const slotsDataInput = document.getElementById('slots_data');
    const remainingTimeWarning = document.getElementById('remainingTimeWarning');
    const form = document.getElementById('slotForm');
    let generatedSlots = [];

    // Initialize date range picker with gold styling
    const dateRangePicker = flatpickr("#date_range", {
        mode: "range",
        dateFormat: "Y-m-d",
        minDate: "today",
        allowInput: false,
        onChange: function(selectedDates, dateStr, instance) {
            // Helper function to format date in local timezone (YYYY-MM-DD)
            function formatLocalDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
            
            if (selectedDates.length === 2) {
                // Full range selected
                const startDateStr = formatLocalDate(selectedDates[0]);
                const endDateStr = formatLocalDate(selectedDates[1]);
                document.getElementById('start_date').value = startDateStr;
                document.getElementById('end_date').value = endDateStr;
            } else if (selectedDates.length === 1) {
                // Single date selected - use it for both start and end (single day range)
                const dateStr = formatLocalDate(selectedDates[0]);
                document.getElementById('start_date').value = dateStr;
                document.getElementById('end_date').value = dateStr;
            } else {
                // No date selected - clear the fields
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
            }
        },
        onClose: function(selectedDates, dateStr, instance) {
            // Helper function to format date in local timezone (YYYY-MM-DD)
            function formatLocalDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
            
            // When the picker closes, ensure dates are properly set
            if (selectedDates.length === 2) {
                // Full range selected
                const startDateStr = formatLocalDate(selectedDates[0]);
                const endDateStr = formatLocalDate(selectedDates[1]);
                document.getElementById('start_date').value = startDateStr;
                document.getElementById('end_date').value = endDateStr;
            } else if (selectedDates.length === 1) {
                // Single date selected - use it for both start and end (single day range)
                const dateStr = formatLocalDate(selectedDates[0]);
                document.getElementById('start_date').value = dateStr;
                document.getElementById('end_date').value = dateStr;
            }
            
            // Also handle the case where dateStr might contain a single date or range
            if (dateStr && !document.getElementById('start_date').value) {
                if (dateStr.includes(' to ')) {
                    const parts = dateStr.split(' to ').map(s => s.trim());
                    if (parts.length >= 2) {
                        document.getElementById('start_date').value = parts[0];
                        document.getElementById('end_date').value = parts[1];
                    }
                } else {
                    // Single date - parse it in local timezone
                    // flatpickr returns dates in YYYY-MM-DD format, so use it directly
                    if (dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        document.getElementById('start_date').value = dateStr;
                        document.getElementById('end_date').value = dateStr;
                    } else {
                        // Fallback: parse as local date
                        const date = new Date(dateStr + 'T00:00:00');
                        if (!isNaN(date.getTime())) {
                            const formattedDate = formatLocalDate(date);
                            document.getElementById('start_date').value = formattedDate;
                            document.getElementById('end_date').value = formattedDate;
                        }
                    }
                }
            }
        }
    });

    // Auto-calculate end time (20 minutes after start time)
    startTimeInput.addEventListener('change', function() {
        if (this.value) {
            const [hours, minutes] = this.value.split(':').map(Number);
            const startDate = new Date();
            startDate.setHours(hours, minutes, 0, 0);
            
            // Add 20 minutes
            const endDate = new Date(startDate.getTime() + 20 * 60000);
            
            // Format as HH:MM
            const endHours = String(endDate.getHours()).padStart(2, '0');
            const endMinutes = String(endDate.getMinutes()).padStart(2, '0');
            endTimeInput.value = `${endHours}:${endMinutes}`;
        }
    });

    // Modal elements
    const slotSettingsModal = document.getElementById('slotSettingsModal');
    const consultationDurationInput = document.getElementById('consultation_duration');
    const delayTimeInput = document.getElementById('delay_time');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const generateModalBtn = document.getElementById('generateModalBtn');

    // Attach click event listener
    if (generateSlotsBtn) {
        generateSlotsBtn.addEventListener('click', handleGenerateSlotsClick);
    }

    // Close modal when Cancel is clicked
    if (cancelModalBtn && slotSettingsModal) {
        cancelModalBtn.addEventListener('click', function() {
            slotSettingsModal.classList.remove('show');
            slotSettingsModal.style.display = 'none';
        });
    }

    // Close modal when clicking outside
    if (slotSettingsModal) {
        slotSettingsModal.addEventListener('click', function(e) {
            if (e.target === slotSettingsModal) {
                slotSettingsModal.classList.remove('show');
                slotSettingsModal.style.display = 'none';
            }
        });
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && slotSettingsModal && slotSettingsModal.classList.contains('show')) {
            slotSettingsModal.classList.remove('show');
            slotSettingsModal.style.display = 'none';
        }
    });

    // Generate slots when Generate button in modal is clicked
    if (generateModalBtn && slotSettingsModal && consultationDurationInput && delayTimeInput) {
        generateModalBtn.addEventListener('click', function() {
        const consultationDuration = parseInt(consultationDurationInput.value);
        const delayTime = delayTimeInput.value.trim() === '' ? 0 : parseInt(delayTimeInput.value);

        // Validate consultation duration
        if (!consultationDuration || consultationDuration < 1) {
            alert('Please enter a valid consultation duration (minimum 1 minute)');
            consultationDurationInput.focus();
            return;
        }

        // Validate delay time if provided
        if (delayTimeInput.value.trim() !== '' && (isNaN(delayTime) || delayTime < 0)) {
            alert('Please enter a valid delay time (must be 0 or greater)');
            delayTimeInput.focus();
            return;
        }

        // Close modal
        slotSettingsModal.classList.remove('show');
        slotSettingsModal.style.display = 'none';

        // Generate slots with user-defined settings
        console.log('Generating slots with duration:', consultationDuration, 'delay:', delayTime);
        if (typeof generateTimeSlots === 'function') {
            generateTimeSlots(consultationDuration, delayTime);
        } else {
            console.error('generateTimeSlots function not found!');
            alert('Error: Slot generation function not available. Please refresh the page.');
        }
        });
    }

    // Function to generate time slots
    function generateTimeSlots(slotDuration, delayMinutes) {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        generatedSlots = [];

        // Parse times
        const [startHours, startMinutes] = startTime.split(':').map(Number);
        const [endHours, endMinutes] = endTime.split(':').map(Number);

        // Convert to minutes for easier calculation
        const startTotalMinutes = startHours * 60 + startMinutes;
        const endTotalMinutes = endHours * 60 + endMinutes;

        if (endTotalMinutes <= startTotalMinutes) {
            alert('End time must be after start time');
            return;
        }

        // Generate slots for each date in the range
        const start = new Date(startDate);
        const end = new Date(endDate);
        const currentDate = new Date(start);

        // Helper function to format date in local timezone (YYYY-MM-DD)
        function formatLocalDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        // Clear previous warnings
        remainingTimeWarning.classList.add('hidden');
        remainingTimeWarning.textContent = '';
        
        while (currentDate <= end) {
            const dateStr = formatLocalDate(currentDate);
            let currentSlotStart = startTotalMinutes;

            while (currentSlotStart < endTotalMinutes) {
                const slotStartHours = Math.floor(currentSlotStart / 60);
                const slotStartMinutes = currentSlotStart % 60;
                const slotEnd = currentSlotStart + slotDuration;
                const slotEndHours = Math.floor(slotEnd / 60);
                const slotEndMinutes = slotEnd % 60;

                // Check if slot fits
                if (slotEnd <= endTotalMinutes) {
                    const slotStartTime = `${String(slotStartHours).padStart(2, '0')}:${String(slotStartMinutes).padStart(2, '0')}`;
                    const slotEndTime = `${String(slotEndHours).padStart(2, '0')}:${String(slotEndMinutes).padStart(2, '0')}`;

                    generatedSlots.push({
                        date: dateStr,
                        start_time: slotStartTime,
                        end_time: slotEndTime,
                        consultation_fee: document.getElementById('consultation_fee').value || '700'
                    });

                    // Move to next slot: consultation duration + delay (if any)
                    currentSlotStart += slotDuration + delayMinutes;
                } else {
                    // Calculate remaining time
                    const remainingMinutes = endTotalMinutes - currentSlotStart;
                    if (remainingMinutes > 0) {
                        remainingTimeWarning.textContent = `Note: The last slot on ${dateStr} would only be ${remainingMinutes} minute(s) long (less than ${slotDuration} minutes). It will not be created.`;
                        remainingTimeWarning.classList.remove('hidden');
                    }
                    // Break out of the loop since no more slots can fit
                    break;
                }
            }

            currentDate.setDate(currentDate.getDate() + 1);
        }

        // Display generated slots
        console.log('Generated slots count:', generatedSlots.length);
        if (generatedSlots.length > 0) {
            slotsList.innerHTML = '';
            generatedSlots.forEach((slot, index) => {
                const slotItem = document.createElement('div');
                slotItem.className = 'slot-item';
                slotItem.textContent = `${slot.date} - ${slot.start_time} to ${slot.end_time}`;
                slotsList.appendChild(slotItem);
            });
            slotsPreview.classList.remove('hidden');
            slotsDataInput.value = JSON.stringify(generatedSlots);
            console.log('Slots data saved to input:', slotsDataInput.value);
            console.log('Slots preview shown');
        } else {
            slotsPreview.classList.add('hidden');
            slotsDataInput.value = '';
            alert('No time slots could be generated with the current settings. Please adjust the consultation duration or time range.');
        }
    }
    
    // Make function globally accessible
    window.generateTimeSlots = generateTimeSlots;

    // Handle form submission
    form.addEventListener('submit', function(e) {
        let startDate = document.getElementById('start_date').value;
        let endDate = document.getElementById('end_date').value;
        const dateRangeInput = document.getElementById('date_range');
        
        // If slots were generated, use them
        if (generatedSlots.length > 0) {
            slotsDataInput.value = JSON.stringify(generatedSlots);
            console.log('Form submitting with slots_data:', slotsDataInput.value);
            console.log('Number of slots:', generatedSlots.length);
            return true;
        }
        
        // If no slots generated but slots_data exists, ensure it's set
        if (slotsDataInput.value) {
            console.log('Form submitting with existing slots_data:', slotsDataInput.value);
            return true;
        }
        
        // Single slot mode - ensure date range is set
        if (!startDate || !endDate) {
            // Try to extract from date_range input if hidden fields aren't set
            if (dateRangeInput.value) {
                const dateStr = dateRangeInput.value.trim();
                // Check if it's a range or single date
                if (dateStr.includes(' to ')) {
                    const parts = dateStr.split(' to ').map(s => s.trim());
                    if (parts.length >= 2) {
                        startDate = parts[0];
                        endDate = parts[1];
                        document.getElementById('start_date').value = startDate;
                        document.getElementById('end_date').value = endDate;
                    } else if (parts.length === 1) {
                        // Single date in range format
                        startDate = parts[0];
                        endDate = parts[0];
                        document.getElementById('start_date').value = startDate;
                        document.getElementById('end_date').value = endDate;
                    }
                } else {
                    // Single date - check if it's already in YYYY-MM-DD format
                    if (dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        // Already in correct format, use it directly
                        startDate = dateStr;
                        endDate = dateStr;
                        document.getElementById('start_date').value = startDate;
                        document.getElementById('end_date').value = endDate;
                    } else {
                        // Try to parse it in local timezone
                        const date = new Date(dateStr + 'T00:00:00');
                        if (!isNaN(date.getTime())) {
                            // Format in local timezone
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            startDate = `${year}-${month}-${day}`;
                            endDate = startDate;
                            document.getElementById('start_date').value = startDate;
                            document.getElementById('end_date').value = endDate;
                        } else {
                            // Use as-is if parsing fails
                            startDate = dateStr;
                            endDate = dateStr;
                            document.getElementById('start_date').value = startDate;
                            document.getElementById('end_date').value = endDate;
                        }
                    }
                }
            }
            
            // Final check - if still no dates, prevent submission
            if (!document.getElementById('start_date').value || !document.getElementById('end_date').value) {
                e.preventDefault();
                alert('Please select a date range');
                return false;
            }
        }
        
        // Ensure both dates are set (for single-day ranges, they should be the same)
        startDate = document.getElementById('start_date').value;
        endDate = document.getElementById('end_date').value;
        
        if (!endDate && startDate) {
            document.getElementById('end_date').value = startDate;
        } else if (!startDate && endDate) {
            document.getElementById('start_date').value = endDate;
        }
        
        // For single slot, we use the start_date
        // The controller will handle it
        return true;
    });
});
</script>
@endsection
