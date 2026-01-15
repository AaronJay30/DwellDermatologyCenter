@extends('layouts.dashboard')
@section('page-title', 'Update Patient History')

@php
    use Illuminate\Support\Str;
    
    // Parse consultation_result JSON if it exists
    $consultationData = null;
    if ($selectedHistory && $selectedHistory->consultation_result) {
        $decoded = json_decode($selectedHistory->consultation_result, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $consultationData = $decoded;
        }
    }
    
    // Extract data from JSON structure
    $beforePhotos = $consultationData['before']['photos'] ?? [];
    $afterPhotos = $consultationData['after']['photos'] ?? [];
    $medicationInstructions = $consultationData['medication']['instructions'] ?? '';
    $medicationMedicines = $consultationData['medication']['medicines'] ?? '';
@endphp

@section('navbar-links')
    @if(isset($routePrefix) && $routePrefix === 'doctor.history.patient')
        @include('partials.doctor_nav')
    @else
        @include('admin.partials.sidebar-links')
    @endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/consultation-form.css') }}">
<style>
    .update-page-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .page-header {
        background: linear-gradient(135deg, #197a8c 0%, #1a6b7a 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 8px 24px rgba(25, 122, 140, 0.2);
    }

    .page-header h2 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }

    .history-list-container {
        background: #ffffff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .history-item-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        background: #ffffff;
    }

    .history-item-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .history-item-card.editable {
        cursor: pointer;
        border-color: #197a8c;
    }

    .history-item-card.editable:hover {
        background: #e8f5e9;
        border-color: #27ae60;
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.2);
    }

    .history-item-card.view-only {
        opacity: 0.7;
        background: #f8f9fa;
    }

    .update-form-wrapper {
        background: #ffffff;
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .result-form-section {
        background: linear-gradient(135deg, #E6F3F5 0%, #f0f9fa 100%);
        border: 2px solid #FFD700;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .result-section-header {
        background: linear-gradient(135deg, #008080 0%, #006666 100%);
        color: #ffffff;
        padding: 1rem 1.5rem;
        margin: -2rem -2rem 1.5rem -2rem;
        font-weight: 700;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .result-form-group {
        margin-bottom: 1.5rem;
    }

    .result-form-group:last-child {
        margin-bottom: 0;
    }

    .result-form-group label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .file-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .file-preview-item {
        position: relative;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        background: #f5f5f5;
        transition: all 0.3s ease;
    }

    .file-preview-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .file-preview-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .file-preview-item .remove-photo {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.2s;
    }

    .file-preview-item .remove-photo:hover {
        background: #dc3545;
        transform: scale(1.1);
    }

    .file-upload-area {
        border: 2px dashed #197a8c;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .file-upload-area:hover {
        border-color: #1a6b7a;
        background: #e8f5e9;
    }

    .file-upload-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .btn-upload, .btn-camera {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-upload {
        background: linear-gradient(135deg, #197a8c 0%, #1a6b7a 100%);
        color: white;
    }

    .btn-upload:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 122, 140, 0.3);
    }

    .btn-camera {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .btn-camera:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .bullet-note-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
        padding: 0.75rem;
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .bullet-note-item:hover {
        border-color: #197a8c;
        box-shadow: 0 2px 8px rgba(25, 122, 140, 0.1);
    }

    .bullet-point {
        color: #008080;
        font-size: 1.5rem;
        font-weight: bold;
        flex-shrink: 0;
        line-height: 1;
        margin-top: 0.1rem;
    }

    .bullet-text {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        font-size: 0.95rem;
        background-color: #ffffff;
        min-height: 40px;
    }

    .bullet-text:focus {
        outline: none;
        border-color: #197a8c;
        box-shadow: 0 0 0 3px rgba(25, 122, 140, 0.1);
    }

    .add-bullet-btn {
        background: #197a8c;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        margin-top: 0.5rem;
        transition: all 0.2s;
    }

    .add-bullet-btn:hover {
        background: #1a6b7a;
        transform: translateY(-1px);
    }

    .remove-bullet-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        margin-left: 0.5rem;
        transition: all 0.2s;
    }

    .remove-bullet-btn:hover {
        background: #c82333;
    }

    .form-control-modern {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #ffffff;
    }

    .form-control-modern:focus {
        outline: none;
        border-color: #197a8c;
        box-shadow: 0 0 0 3px rgba(25, 122, 140, 0.1);
        transform: translateY(-1px);
    }

    .form-label-modern {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #197a8c 0%, #1a6b7a 100%);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(25, 122, 140, 0.3);
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(25, 122, 140, 0.4);
    }

    .btn-secondary-modern {
        background: #6c757d;
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary-modern:hover {
        background: #5a6268;
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    .patient-info-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #dee2e6;
    }

    .patient-info-card h5 {
        color: #197a8c;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .update-page-container {
            padding: 1rem;
        }
        
        .file-upload-buttons {
            flex-direction: column;
        }
        
        .file-preview {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }
    }
</style>
@endpush

@section('content')
<div class="update-page-container">
    <div class="page-header">
        <h2>Update Patient History</h2>
        <p class="mb-0" style="opacity: 0.9;">{{ $patient->name }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!$selectedHistory)
        {{-- Show list of history items --}}
        <div class="history-list-container">
            <h4 class="mb-4" style="color: #2c3e50;">Select a Consultation or Service to Update</h4>
            
            @if($historyItemsWithAccess->isEmpty())
                <div class="alert alert-info">
                    No patient history available to update.
                </div>
            @else
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i> 
                    You can only update history items from your branch. Items from other branches are view-only.
                </div>

                @foreach($historyItemsWithAccess as $history)
                    @php
                        $serviceName = $history->appointment && $history->appointment->service 
                            ? $history->appointment->service->name 
                            : 'Consultation';
                        $branchName = $history->appointment && $history->appointment->branch 
                            ? $history->appointment->branch->name 
                            : 'N/A';
                        $doctorName = $history->doctor ? $history->doctor->name : 'N/A';
                        $treatmentDate = $history->treatment_date 
                            ? $history->treatment_date->format('M d, Y') 
                            : $history->created_at->format('M d, Y');
                    @endphp

                    <div class="history-item-card {{ $history->canEdit ? 'editable' : 'view-only' }}" 
                         @if($history->canEdit) 
                         onclick="window.location.href='{{ route(($routePrefix ?? 'admin.patients.history') . '.update', ['patient' => $patient->id, 'history_id' => $history->id]) }}'"
                         @endif>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0" style="color: #2c3e50;">{{ $serviceName }}</h5>
                            <span class="badge {{ $history->canEdit ? 'bg-success' : 'bg-secondary' }}">
                                {{ $history->canEdit ? 'Editable' : 'View Only' }}
                            </span>
                        </div>
                        <div class="text-muted">
                            <div><strong>Branch:</strong> {{ $branchName }}</div>
                            <div><strong>Doctor:</strong> {{ $doctorName }}</div>
                            <div><strong>Date:</strong> {{ $treatmentDate }}</div>
                        </div>
                        @if(!$history->canEdit)
                            <div class="mt-2 text-muted small">
                                <i class="fas fa-lock"></i> This item belongs to a different branch and cannot be edited.
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    @else
        {{-- Show update form for selected history item --}}
        <div class="update-form-wrapper">
            <a href="{{ route(($routePrefix ?? 'admin.patients.history') . '.update', $patient->id) }}" class="btn-secondary-modern mb-3">
                ← Back to History List
            </a>

            <div class="patient-info-card">
                <h5>Patient Information</h5>
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Name:</strong> {{ $patient->name }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Email:</strong> {{ $patient->email }}</p>
                    </div>
                    @if($patient->phone)
                    <div class="col-md-4">
                        <p class="mb-0"><strong>Phone:</strong> {{ $patient->phone }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route(($routePrefix ?? 'admin.patients.history') . '.store', $patient->id) }}" enctype="multipart/form-data" id="historyUpdateForm">
                @csrf
                <input type="hidden" name="history_id" value="{{ $selectedHistory->id }}">
                <input type="hidden" name="consultation_result_json" id="consultation_result_json">
                
                <!-- BEFORE PHOTOS Section -->
                <div class="result-form-section">
                    <div class="result-section-header">
                        📸 BEFORE PHOTOS
                    </div>
                    
                    <div class="result-form-group">
                        <label class="form-label-modern">Upload Before Photos (Multiple)</label>
                        <div class="file-upload-area">
                            <div class="file-upload-buttons">
                                <input type="file" name="before_photos[]" id="beforePhotos" multiple accept="image/*" style="display: none;" onchange="handleBeforePhotos(this)">
                                <button type="button" class="btn-upload" onclick="document.getElementById('beforePhotos').click()">
                                    <i class="fas fa-upload"></i> Choose Photos
                                </button>
                            </div>
                            <div class="file-preview" id="beforePhotosPreview">
                                @if(!empty($beforePhotos))
                                    @foreach($beforePhotos as $photo)
                                        <div class="file-preview-item">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Before Photo">
                                            <input type="hidden" name="existing_before_photos[]" value="{{ $photo }}">
                                            <button type="button" class="remove-photo" onclick="removeBeforePhoto(this)">×</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AFTER PHOTOS Section -->
                <div class="result-form-section">
                    <div class="result-section-header">
                        📸 AFTER PHOTOS
                    </div>
                    
                    <div class="result-form-group">
                        <label class="form-label-modern">Upload After Photos (Multiple)</label>
                        <div class="file-upload-area">
                            <div class="file-upload-buttons">
                                <input type="file" name="after_photos[]" id="afterPhotos" multiple accept="image/*" style="display: none;" onchange="handleAfterPhotos(this)">
                                <button type="button" class="btn-upload" onclick="document.getElementById('afterPhotos').click()">
                                    <i class="fas fa-upload"></i> Choose Photos
                                </button>
                            </div>
                            <div class="file-preview" id="afterPhotosPreview">
                                @if(!empty($afterPhotos))
                                    @foreach($afterPhotos as $photo)
                                        <div class="file-preview-item">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="After Photo">
                                            <input type="hidden" name="existing_after_photos[]" value="{{ $photo }}">
                                            <button type="button" class="remove-photo" onclick="removeAfterPhoto(this)">×</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MEDICATION INSTRUCTIONS Section -->
                <div class="result-form-section">
                    <div class="result-section-header">
                        💊 MEDICATION INSTRUCTIONS
                    </div>
                    
                    <div class="result-form-group">
                        <label class="form-label-modern">Instructions (Multiple Bullet Points)</label>
                        <div id="medicationInstructionsContainer">
                            @if(!empty($medicationInstructions))
                                @php
                                    $instructions = explode("\n", $medicationInstructions);
                                    $instructions = array_filter(array_map('trim', $instructions), function($item) {
                                        return !empty($item) && $item !== '•';
                                    });
                                @endphp
                                @foreach($instructions as $instruction)
                                    @php
                                        $cleanInstruction = preg_replace('/^[•\-\*]\s*/', '', $instruction);
                                    @endphp
                                    @if(!empty($cleanInstruction))
                                        <div class="bullet-note-item">
                                            <span class="bullet-point">•</span>
                                            <textarea class="bullet-text" name="medication_instructions[]" rows="2">{{ $cleanInstruction }}</textarea>
                                            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)">Remove</button>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="add-bullet-btn" onclick="addMedicationInstruction()">+ Add Instruction</button>
                    </div>
                </div>

                <!-- MEDICATIONS TO TAKE Section -->
                <div class="result-form-section">
                    <div class="result-section-header">
                        💧 MEDICATIONS TO TAKE
                    </div>
                    
                    <div class="result-form-group">
                        <label class="form-label-modern">Medicines</label>
                        <textarea name="medication_medicines" id="medicationMedicines" class="form-control-modern" rows="3" placeholder="Enter medicines separated by commas or new lines">{{ $medicationMedicines }}</textarea>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="result-form-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-color: #197a8c;">
                    <div class="result-section-header" style="background: linear-gradient(135deg, #197a8c 0%, #1a6b7a 100%);">
                        📋 BASIC INFORMATION
                    </div>
                    
                    <div class="result-form-group">
                        <label for="treatment_date" class="form-label-modern">Treatment Date <span class="text-danger">*</span></label>
                        <input type="date" name="treatment_date" id="treatment_date" class="form-control-modern" 
                               value="{{ $selectedHistory->treatment_date ? $selectedHistory->treatment_date->format('Y-m-d') : date('Y-m-d') }}" required>
                    </div>

                    <div class="result-form-group">
                        <label for="diagnosis" class="form-label-modern">Diagnosis</label>
                        <textarea name="diagnosis" id="diagnosis" class="form-control-modern" rows="3">{{ $selectedHistory->diagnosis ?? '' }}</textarea>
                    </div>

                    <div class="result-form-group">
                        <label for="treatment_notes" class="form-label-modern">Treatment Notes <span class="text-danger">*</span></label>
                        <textarea name="treatment_notes" id="treatment_notes" class="form-control-modern" rows="4" required>{{ $selectedHistory->treatment_notes ?? '' }}</textarea>
                    </div>

                    <div class="result-form-group">
                        <label for="prescription" class="form-label-modern">Prescription</label>
                        <textarea name="prescription" id="prescription" class="form-control-modern" rows="3">{{ $selectedHistory->prescription ?? '' }}</textarea>
                    </div>

                    <div class="result-form-group">
                        <label for="outcome" class="form-label-modern">Outcome</label>
                        <textarea name="outcome" id="outcome" class="form-control-modern" rows="3">{{ $selectedHistory->outcome ?? '' }}</textarea>
                    </div>

                    <div class="result-form-group">
                        <div class="form-check">
                            <input type="checkbox" name="follow_up_required" id="follow_up_required" class="form-check-input" 
                                   value="1" {{ $selectedHistory->follow_up_required ? 'checked' : '' }}>
                            <label for="follow_up_required" class="form-check-label">Follow-up Required</label>
                        </div>
                    </div>

                    <div class="result-form-group" id="follow_up_date_wrapper" style="display: {{ $selectedHistory->follow_up_required ? 'block' : 'none' }};">
                        <label for="follow_up_date" class="form-label-modern">Follow-up Date</label>
                        <input type="date" name="follow_up_date" id="follow_up_date" class="form-control-modern" 
                               value="{{ $selectedHistory->follow_up_date ? $selectedHistory->follow_up_date->format('Y-m-d') : '' }}">
                    </div>

                    <div class="result-form-group">
                        <label for="notes" class="form-label-modern">Additional Notes</label>
                        <textarea name="notes" id="notes" class="form-control-modern" rows="3">{{ $selectedHistory->notes ?? '' }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-primary-modern">Update History</button>
                    <a href="{{ route(($routePrefix ?? 'admin.patients.history') . '.update', $patient->id) }}" class="btn-secondary-modern">Cancel</a>
                </div>
            </form>
        </div>
    @endif
</div>

@push('scripts')
<script>
    let beforePhotosData = [];
    let afterPhotosData = [];
    let existingBeforePhotos = [];
    let existingAfterPhotos = [];

    // Initialize existing photos
    document.querySelectorAll('input[name="existing_before_photos[]"]').forEach(input => {
        existingBeforePhotos.push(input.value);
    });
    document.querySelectorAll('input[name="existing_after_photos[]"]').forEach(input => {
        existingAfterPhotos.push(input.value);
    });

    function handleBeforePhotos(input) {
        const files = Array.from(input.files);
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('beforePhotosPreview');
                const item = document.createElement('div');
                item.className = 'file-preview-item';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="Before Photo">
                    <button type="button" class="remove-photo" onclick="removeBeforePhoto(this)">×</button>
                `;
                item.dataset.fileName = file.name;
                preview.appendChild(item);
                beforePhotosData.push({file: file, preview: item});
            };
            reader.readAsDataURL(file);
        });
    }

    function handleAfterPhotos(input) {
        const files = Array.from(input.files);
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('afterPhotosPreview');
                const item = document.createElement('div');
                item.className = 'file-preview-item';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="After Photo">
                    <button type="button" class="remove-photo" onclick="removeAfterPhoto(this)">×</button>
                `;
                item.dataset.fileName = file.name;
                preview.appendChild(item);
                afterPhotosData.push({file: file, preview: item});
            };
            reader.readAsDataURL(file);
        });
    }

    function removeBeforePhoto(btn) {
        const item = btn.closest('.file-preview-item');
        const hiddenInput = item.querySelector('input[name="existing_before_photos[]"]');
        if (hiddenInput) {
            existingBeforePhotos = existingBeforePhotos.filter(p => p !== hiddenInput.value);
        }
        beforePhotosData = beforePhotosData.filter(p => p.preview !== item);
        item.remove();
    }

    function removeAfterPhoto(btn) {
        const item = btn.closest('.file-preview-item');
        const hiddenInput = item.querySelector('input[name="existing_after_photos[]"]');
        if (hiddenInput) {
            existingAfterPhotos = existingAfterPhotos.filter(p => p !== hiddenInput.value);
        }
        afterPhotosData = afterPhotosData.filter(p => p.preview !== item);
        item.remove();
    }

    function addMedicationInstruction() {
        const container = document.getElementById('medicationInstructionsContainer');
        const item = document.createElement('div');
        item.className = 'bullet-note-item';
        item.innerHTML = `
            <span class="bullet-point">•</span>
            <textarea class="bullet-text" name="medication_instructions[]" rows="2" placeholder="Enter instruction"></textarea>
            <button type="button" class="remove-bullet-btn" onclick="removeBulletItem(this)">Remove</button>
        `;
        container.appendChild(item);
    }

    function removeBulletItem(btn) {
        btn.closest('.bullet-note-item').remove();
    }

    // Build JSON before form submission
    document.getElementById('historyUpdateForm').addEventListener('submit', function(e) {
        // Collect remaining existing photos (those not removed)
        const remainingBeforePhotos = [];
        const remainingAfterPhotos = [];
        
        document.querySelectorAll('#beforePhotosPreview .file-preview-item').forEach(item => {
            const hiddenInput = item.querySelector('input[name="existing_before_photos[]"]');
            if (hiddenInput) {
                remainingBeforePhotos.push(hiddenInput.value);
            }
        });
        
        document.querySelectorAll('#afterPhotosPreview .file-preview-item').forEach(item => {
            const hiddenInput = item.querySelector('input[name="existing_after_photos[]"]');
            if (hiddenInput) {
                remainingAfterPhotos.push(hiddenInput.value);
            }
        });

        // Collect medication instructions
        const instructions = Array.from(document.querySelectorAll('textarea[name="medication_instructions[]"]'))
            .map(textarea => textarea.value.trim())
            .filter(val => val)
            .map(val => '• ' + val);

        // Build consultation result JSON
        const consultationResult = {
            before: {
                photos: remainingBeforePhotos,
                videos: [],
                notes: null
            },
            after: {
                photos: remainingAfterPhotos,
                videos: [],
                notes: null
            },
            medication: {
                instructions: instructions.join('\n'),
                medicines: document.getElementById('medicationMedicines').value.trim()
            }
        };

        document.getElementById('consultation_result_json').value = JSON.stringify(consultationResult);
    });

    // Follow-up date toggle
    document.addEventListener('DOMContentLoaded', function() {
        const followUpCheckbox = document.getElementById('follow_up_required');
        const followUpDateWrapper = document.getElementById('follow_up_date_wrapper');

        if (followUpCheckbox && followUpDateWrapper) {
            followUpCheckbox.addEventListener('change', function() {
                followUpDateWrapper.style.display = this.checked ? 'block' : 'none';
            });
        }
    });
</script>
@endpush
@endsection
