@extends('layouts.patient')

@push('styles')
<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Arial', sans-serif;
    }

    .patient-info-sheet-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #ffffff;
        border: 4px solid #FFD700;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .sheet-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .logo-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .logo-circle {
        width: 80px;
        height: 80px;
        border: 3px solid #197a8c;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: bold;
        color: #197a8c;
        margin-bottom: 0.5rem;
    }

    .logo-text {
        font-size: 1.2rem;
        font-weight: bold;
        color: #197a8c;
    }

    .sheet-title {
        font-size: 1.8rem;
        font-weight: bold;
        color: #000000;
        text-align: right;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .form-section {
        background-color: #E0F7FA;
        border: 2px solid #FFD700;
        border-radius: 5px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .section-header {
        background-color: #197a8c;
        color: #ffffff;
        padding: 0.75rem 1rem;
        margin: -1.5rem -1.5rem 1rem -1.5rem;
        font-size: 1.1rem;
        font-weight: bold;
        text-transform: uppercase;
        border-radius: 5px 5px 0 0;
    }

    .form-group {
        margin-bottom: 1rem;
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
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 0.95rem;
        background-color: #ffffff;
    }

    .form-textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 0.95rem;
        background-color: #ffffff;
        min-height: 80px;
        resize: vertical;
    }

    .radio-group {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-top: 0.5rem;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .radio-option input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .radio-option label {
        font-weight: normal;
        cursor: pointer;
        margin: 0;
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
        font-weight: normal;
        cursor: pointer;
        margin: 0;
    }

    .others-input {
        margin-top: 0.5rem;
    }

    .signature-section {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e0e0e0;
    }

    .certification-text {
        font-size: 0.95rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        font-style: italic;
    }

    .signature-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1rem;
    }

    .signature-line {
        border-bottom: 2px solid #000;
        padding-bottom: 0.5rem;
        min-height: 60px;
        position: relative;
    }

    .signature-label {
        position: absolute;
        bottom: -20px;
        left: 0;
        font-size: 0.85rem;
        color: #666;
    }

    .date-line {
        border-bottom: 2px solid #000;
        padding-bottom: 0.5rem;
        min-height: 40px;
        max-width: 200px;
        position: relative;
    }

    .date-label {
        position: absolute;
        bottom: -20px;
        left: 0;
        font-size: 0.85rem;
        color: #666;
    }

    .signature-canvas-container {
        border: 2px solid #ddd;
        border-radius: 4px;
        padding: 1rem;
        background-color: #ffffff;
        margin-bottom: 0.5rem;
    }

    #signature-canvas {
        width: 100%;
        height: 150px;
        border: 1px solid #ccc;
        cursor: crosshair;
        touch-action: none;
        background-color: #ffffff;
    }

    .signature-controls {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #197a8c;
        color: white;
    }

    .btn-primary:hover {
        background-color: #1a6b7a;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e0e0e0;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1.5rem;
        border: 1px solid #f5c6cb;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1.5rem;
        border: 1px solid #c3e6cb;
    }

    .two-column {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .patient-info-sheet-container {
            margin: 1rem;
            padding: 1rem;
        }

        .sheet-header {
            flex-direction: column;
            align-items: center;
        }

        .sheet-title {
            text-align: center;
            margin-top: 1rem;
        }

        .two-column {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="patient-info-sheet-container">
    <!-- Header -->
    <div class="sheet-header">
        <div class="logo-section">
            <div class="logo-circle">P</div>
            <div class="logo-text">Pwell</div>
        </div>
        <div class="sheet-title">NEW PATIENT INFORMATION SHEET</div>
    </div>

    @if ($errors->any())
        <div class="error-message">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('patient-information-sheet.store') }}" method="POST" id="patient-info-form">
        @csrf

        <!-- PERSONAL INFORMATION Section -->
        <div class="form-section">
            <div class="section-header">PERSONAL INFORMATION</div>
            
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="birthday" class="form-label">Birthday</label>
                <input type="date" id="birthday" name="birthday" class="form-input" value="{{ old('birthday') }}" required>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-input" value="{{ old('address') }}" required>
            </div>

            <div class="form-group">
                <label for="contact_number" class="form-label">Contact No</label>
                <input type="text" id="contact_number" name="contact_number" class="form-input" value="{{ old('contact_number') }}" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email address</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Civil Status</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="civil_status_single" name="civil_status" value="Single" {{ old('civil_status') == 'Single' ? 'checked' : '' }} required>
                        <label for="civil_status_single">Single</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="civil_status_married" name="civil_status" value="Married" {{ old('civil_status') == 'Married' ? 'checked' : '' }}>
                        <label for="civil_status_married">Married</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Sex</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="sex_male" name="sex" value="Male" {{ old('sex') == 'Male' ? 'checked' : '' }} required>
                        <label for="sex_male">Male</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="sex_female" name="sex" value="Female" {{ old('sex') == 'Female' ? 'checked' : '' }}>
                        <label for="sex_female">Female</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="preferred_pronoun" class="form-label">Preferred pronoun</label>
                <input type="text" id="preferred_pronoun" name="preferred_pronoun" class="form-input" value="{{ old('preferred_pronoun') }}">
            </div>
        </div>

        <!-- PERTINENT MEDICAL INFORMATION Section -->
        <div class="form-section">
            <div class="section-header">PERTINENT MEDICAL INFORMATION</div>
            
            <div class="form-group">
                <label class="form-label">Comorbids</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="hypertension" name="hypertension" value="1" {{ old('hypertension') ? 'checked' : '' }}>
                    <label for="hypertension">Hypertension</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="diabetes" name="diabetes" value="1" {{ old('diabetes') ? 'checked' : '' }}>
                    <label for="diabetes">Diabetes</label>
                </div>
                <div class="others-input">
                    <input type="text" id="comorbidities_others" name="comorbidities_others" class="form-input" placeholder="Others, please specify:" value="{{ old('comorbidities_others') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Allergies</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="allergies_medications" name="allergies_medications" value="1" {{ old('allergies_medications') ? 'checked' : '' }}>
                    <label for="allergies_medications">Medications</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="allergies_anesthetics" name="allergies_anesthetics" value="1" {{ old('allergies_anesthetics') ? 'checked' : '' }}>
                    <label for="allergies_anesthetics">Anesthetics</label>
                </div>
                <div class="others-input">
                    <input type="text" id="allergies_others" name="allergies_others" class="form-input" placeholder="Others, please specify:" value="{{ old('allergies_others') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="previous_hospitalizations_surgeries" class="form-label">Previous hospitalizations / surgeries</label>
                <textarea id="previous_hospitalizations_surgeries" name="previous_hospitalizations_surgeries" class="form-textarea">{{ old('previous_hospitalizations_surgeries') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Alcoholic beverage drinker?</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="alcoholic_drinker_yes" name="alcoholic_drinker" value="Yes" {{ old('alcoholic_drinker') == 'Yes' ? 'checked' : '' }}>
                        <label for="alcoholic_drinker_yes">Yes</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="alcoholic_drinker_no" name="alcoholic_drinker" value="No" {{ old('alcoholic_drinker') == 'No' ? 'checked' : '' }}>
                        <label for="alcoholic_drinker_no">No</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Smoker?</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="smoker_yes" name="smoker" value="Yes" {{ old('smoker') == 'Yes' ? 'checked' : '' }}>
                        <label for="smoker_yes">Yes</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="smoker_no" name="smoker" value="No" {{ old('smoker') == 'No' ? 'checked' : '' }}>
                        <label for="smoker_no">No</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="known_family_illnesses" class="form-label">Known family illnesses</label>
                <textarea id="known_family_illnesses" name="known_family_illnesses" class="form-textarea">{{ old('known_family_illnesses') }}</textarea>
            </div>
        </div>

        <!-- PERSON TO CONTACT IN CASE OF EMERGENCY Section -->
        <div class="form-section">
            <div class="section-header">PERSON TO CONTACT IN CASE OF EMERGENCY</div>
            
            <div class="form-group">
                <label for="emergency_name" class="form-label">Name</label>
                <input type="text" id="emergency_name" name="emergency_name" class="form-input" value="{{ old('emergency_name') }}" required>
            </div>

            <div class="form-group">
                <label for="emergency_relationship" class="form-label">Relationship</label>
                <input type="text" id="emergency_relationship" name="emergency_relationship" class="form-input" value="{{ old('emergency_relationship') }}" required>
            </div>

            <div class="form-group">
                <label for="emergency_address" class="form-label">Address</label>
                <input type="text" id="emergency_address" name="emergency_address" class="form-input" value="{{ old('emergency_address') }}" required>
            </div>

            <div class="form-group">
                <label for="emergency_contact_number" class="form-label">Contact No</label>
                <input type="text" id="emergency_contact_number" name="emergency_contact_number" class="form-input" value="{{ old('emergency_contact_number') }}" required>
            </div>
        </div>

        <!-- Certification and Signature Section -->
        <div class="signature-section">
            <div class="certification-text">
                I certify that all the information I wrote on this form are true and correct.
            </div>

            <div class="signature-container">
                <div class="signature-line">
                    <div class="signature-canvas-container">
                        <canvas id="signature-canvas"></canvas>
                        <div class="signature-controls">
                            <button type="button" class="btn btn-secondary" onclick="clearSignature()">Clear</button>
                        </div>
                    </div>
                    <div class="signature-label">Signature over Printed Name</div>
                </div>

                <div class="date-line">
                    <input type="date" id="date" name="date" class="form-input" value="{{ old('date', date('Y-m-d')) }}" required>
                    <div class="date-label">Date</div>
                </div>
            </div>
        </div>

        <input type="hidden" id="signature" name="signature" required>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('consultations.medical') }}'">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

<script>
let canvas, ctx;
let isDrawing = false;

document.addEventListener('DOMContentLoaded', function() {
    canvas = document.getElementById('signature-canvas');
    if (!canvas) return;
    
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = 150;
    
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
    
    // Set initial date
    const dateInput = document.getElementById('date');
    if (dateInput && !dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
});

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
        document.getElementById('signature').value = '';
    }
}

function saveSignature() {
    if (canvas) {
        const signatureData = canvas.toDataURL('image/png');
        document.getElementById('signature').value = signatureData;
    }
}

// Save signature before form submission
document.getElementById('patient-info-form').addEventListener('submit', function(e) {
    saveSignature();
    const signatureValue = document.getElementById('signature').value;
    if (!signatureValue || signatureValue.trim() === '') {
        e.preventDefault();
        alert('Please provide your signature before submitting.');
        return false;
    }
});
</script>
@endsection

