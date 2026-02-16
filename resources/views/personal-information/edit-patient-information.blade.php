@extends('layouts.patient')

@push('styles')
<style>
    body {
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
    }

    .form-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #ffffff;
        border: 3px solid #FFD700;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .form-logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-logo img {
        width: 60px;
        height: 60px;
    }

    .form-logo-text {
        font-size: 1.2rem;
        font-weight: bold;
        color: #197a8c;
    }

    .form-title {
        font-size: 1.8rem;
        font-weight: bold;
        color: #000000;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .form-section {
        background-color: #E6F3F5;
        border: 2px solid #FFD700;
        border-radius: 5px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .section-header {
        background-color: #008080;
        color: #ffffff;
        padding: 0.75rem 1rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        font-weight: bold;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="date"],
    .form-group textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 1rem;
        box-sizing: border-box;
    }

    .form-group textarea {
        min-height: 80px;
        resize: vertical;
    }

    .radio-group {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .radio-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: normal;
        cursor: pointer;
    }

    .radio-group input[type="radio"] {
        width: auto;
        margin: 0;
    }

    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .checkbox-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: normal;
        cursor: pointer;
    }

    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .certification-section {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e0e0e0;
    }

    .certification-text {
        margin-bottom: 1.5rem;
        font-size: 1rem;
        color: #333;
    }

    .signature-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-top: 1rem;
    }

    .signature-field {
        display: flex;
        flex-direction: column;
    }

    .signature-field label {
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .signature-line {
        border-bottom: 2px solid #000;
        min-height: 50px;
        margin-bottom: 0.25rem;
    }

    .date-line {
        border-bottom: 2px solid #000;
        min-height: 50px;
        margin-bottom: 0.25rem;
    }

    .signature-canvas-container {
        border: 2px solid #ccc;
        border-radius: 5px;
        padding: 1rem;
        background: white;
        margin-bottom: 0.5rem;
    }

    #signature-canvas {
        width: 100%;
        height: 150px;
        border: 1px solid #ddd;
        cursor: crosshair;
        touch-action: none;
    }

    .clear-signature-btn {
        padding: 0.5rem 1rem;
        background-color: #6c757d;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .clear-signature-btn:hover {
        background-color: #5a6268;
    }

    .submit-btn {
        width: 100%;
        padding: 1rem;
        background-color: #197a8c;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        margin-top: 2rem;
    }

    .submit-btn:hover {
        background-color: #1a6b7a;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .signature-section {
            grid-template-columns: 1fr;
        }

        .form-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <div class="form-logo">
            <img src="{{ asset('images/dwell-logo.png') }}" alt="Logo">
            <span class="form-logo-text">Dwell</span>
        </div>
        <h1 class="form-title">EDIT PATIENT INFORMATION SHEET</h1>
    </div>

    <form action="{{ route('edit-patient-information.update', $personalInformation->id) }}" method="POST" id="patient-info-form">
        @csrf
        @method('PUT')

        <!-- PERSONAL INFORMATION Section -->
        <div class="form-section">
            <div class="section-header">PERSONAL INFORMATION</div>
            
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ $personalInformation->full_name }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input type="date" id="birthday" name="birthday" value="{{ $personalInformation->birthday ? $personalInformation->birthday->format('Y-m-d') : '' }}" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="{{ $personalInformation->address }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact_number">Contact No</label>
                    <input type="text" id="contact_number" name="contact_number" value="{{ $personalInformation->contact_number }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Civil Status</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="civil_status" value="Single">
                            Single
                        </label>
                        <label>
                            <input type="radio" name="civil_status" value="Married">
                            Married
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Sex</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="sex" value="male">
                            Male
                        </label>
                        <label>
                            <input type="radio" name="sex" value="female">
                            Female
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="preferred_pronoun">Preferred pronoun</label>
                <input type="text" id="preferred_pronoun" name="preferred_pronoun">
            </div>
        </div>

        <!-- PERTINENT MEDICAL INFORMATION Section -->
        <div class="form-section">
            <div class="section-header">PERTINENT MEDICAL INFORMATION</div>
            
            <div class="form-group">
                <label>Comorbids</label>
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="hypertension" value="1" {{ $medicalInfo && $medicalInfo->hypertension ? 'checked' : '' }}>
                        Hypertension
                    </label>
                    <label>
                        <input type="checkbox" name="diabetes" value="1" {{ $medicalInfo && $medicalInfo->diabetes ? 'checked' : '' }}>
                        Diabetes
                    </label>
                </div>
                <div style="margin-top: 0.5rem;">
                    <input type="text" name="comorbidities_others" value="{{ $medicalInfo ? $medicalInfo->comorbidities_others : '' }}" placeholder="Others, please specify:" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 3px;">
                </div>
            </div>

            <div class="form-group">
                <label>Allergies</label>
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="allergies_medications" value="1" {{ $medicalInfo && $medicalInfo->allergies && strpos($medicalInfo->allergies, 'Medications') !== false ? 'checked' : '' }}>
                        Medications
                    </label>
                    <label>
                        <input type="checkbox" name="allergies_anesthetics" value="1" {{ $medicalInfo && ($medicalInfo->allergies && strpos($medicalInfo->allergies, 'Anesthetics') !== false || $medicalInfo->anesthetics) ? 'checked' : '' }}>
                        Anesthetics
                    </label>
                </div>
                <div style="margin-top: 0.5rem;">
                    <input type="text" name="allergies_others" value="{{ $medicalInfo ? ($medicalInfo->anesthetics_others ?? '') : '' }}" placeholder="Others, please specify:" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 3px;">
                </div>
            </div>

            <div class="form-group">
                <label for="previous_hospitalizations_surgeries">Previous hospitalizations / surgeries</label>
                <textarea id="previous_hospitalizations_surgeries" name="previous_hospitalizations_surgeries">{{ $medicalInfo ? $medicalInfo->previous_hospitalizations_surgeries : '' }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Alcoholic beverage drinker?</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="alcoholic_drinker" value="Yes" {{ $medicalInfo && $medicalInfo->alcoholic_drinker === 'yes' ? 'checked' : '' }}>
                            Yes
                        </label>
                        <label>
                            <input type="radio" name="alcoholic_drinker" value="No" {{ $medicalInfo && $medicalInfo->alcoholic_drinker === 'no' ? 'checked' : '' }}>
                            No
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Smoker?</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="smoker" value="Yes" {{ $medicalInfo && $medicalInfo->smoker === 'yes' ? 'checked' : '' }}>
                            Yes
                        </label>
                        <label>
                            <input type="radio" name="smoker" value="No" {{ $medicalInfo && $medicalInfo->smoker === 'no' ? 'checked' : '' }}>
                            No
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="known_family_illnesses">Known family illnesses</label>
                <textarea id="known_family_illnesses" name="known_family_illnesses">{{ $medicalInfo ? $medicalInfo->known_family_illnesses : '' }}</textarea>
            </div>
        </div>

        <!-- PERSON TO CONTACT IN CASE OF EMERGENCY Section -->
        <div class="form-section">
            <div class="section-header">PERSON TO CONTACT IN CASE OF EMERGENCY</div>
            
            <div class="form-group">
                <label for="emergency_name">Name</label>
                <input type="text" id="emergency_name" name="emergency_name" value="{{ $emergencyContact ? $emergencyContact->name : '' }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="emergency_relationship">Relationship</label>
                    <input type="text" id="emergency_relationship" name="emergency_relationship" value="{{ $emergencyContact ? $emergencyContact->relationship : '' }}" required>
                </div>
                <div class="form-group">
                    <label for="emergency_address">Address</label>
                    <input type="text" id="emergency_address" name="emergency_address" value="{{ $emergencyContact ? $emergencyContact->address : '' }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="emergency_contact_number">Contact No</label>
                <input type="text" id="emergency_contact_number" name="emergency_contact_number" value="{{ $emergencyContact ? $emergencyContact->contact_number : '' }}" required>
            </div>
        </div>

        <!-- Certification and Signature Section -->
        <div class="certification-section">
            <p class="certification-text">I certify that all the information I wrote on this form are true and correct.</p>
            
            <div class="signature-section">
                <div class="signature-field">
                    <label>Signature over Printed Name</label>
                    <div class="signature-canvas-container">
                        <canvas id="signature-canvas"></canvas>
                        <button type="button" class="clear-signature-btn" onclick="clearSignature()">Clear</button>
                    </div>
                    <input type="hidden" id="signature" name="signature" value="{{ $personalInformation->signature }}" required>
                </div>
                <div class="signature-field">
                    <label>Date</label>
                    <input type="date" id="date" name="date" required value="{{ date('Y-m-d') }}">
                </div>
            </div>
        </div>

        <button type="submit" class="submit-btn">Update</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let canvas, ctx;
    let isDrawing = false;

    document.addEventListener('DOMContentLoaded', function() {
        initializeSignatureCanvas();
        
        // Load existing signature if available
        @if($personalInformation->signature)
            loadSignatureToCanvas('{{ $personalInformation->signature }}');
        @endif
    });

    function initializeSignatureCanvas() {
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
    }

    function loadSignatureToCanvas(signatureData) {
        if (!signatureData) return;
        
        const img = new Image();
        img.onload = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            document.getElementById('signature').value = signatureData;
        };
        img.src = signatureData;
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
            document.getElementById('signature').value = '';
        }
    }

    function saveSignature() {
        if (canvas) {
            const signatureData = canvas.toDataURL('image/png');
            document.getElementById('signature').value = signatureData;
        }
    }

    // Validate signature before submit
    document.getElementById('patient-info-form').addEventListener('submit', function(e) {
        const signature = document.getElementById('signature').value;
        if (!signature) {
            e.preventDefault();
            alert('Please provide your signature before submitting.');
            return false;
        }
    });
</script>
@endpush

