@extends('layouts.dashboard')
@section('page-title', 'Service Schedule Details')

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@push('styles')
<style>
    /* Base container styling for centering */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
        padding: 0 20px;
    }
    
    /* Small laptops and tablets */
    @media (max-width: 1366px) {
        .container {
            max-width: 1100px;
            padding: 0 1.5rem;
        }
    }
    
    @media (max-width: 1024px) {
        .container {
            max-width: 100%;
            padding: 0 1rem;
            margin: 0 auto;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        
        .page-header h1 {
            font-size: 1.5rem;
        }
        
        .page-header .btn {
            width: 100%;
        }
        
        .info-grid {
            grid-template-columns: 1fr !important;
            gap: 1.5rem !important;
        }
        
        .details-grid {
            grid-template-columns: 1fr !important;
            gap: 1.5rem !important;
        }
        
        .action-buttons {
            flex-direction: column;
            width: 100%;
        }
        
        .action-buttons .btn {
            width: 100%;
        }
        
        .modal-actions {
            flex-direction: column;
        }
        
        .modal-actions .btn {
            width: 100%;
        }
    }
    
    @media (max-width: 768px) {
        .container {
            max-width: 100%;
            padding: 0 0.75rem;
            margin: 0 auto;
        }
        
        .card {
            padding: 1.25rem !important;
        }
        
        .page-header h1 {
            font-size: 1.35rem;
        }
        
        .info-section h3 {
            font-size: 1.1rem;
        }
        
        #addResultModal .card {
            width: calc(100vw - 1.5rem) !important;
            max-height: 95vh !important;
        }
    }
    
    @media (max-width: 480px) {
        .container {
            max-width: 100%;
            padding: 0 0.5rem;
            margin: 0 auto;
        }
        
        .card {
            padding: 1rem !important;
        }
        
        .page-header h1 {
            font-size: 1.2rem;
        }
        
        .info-section h3 {
            font-size: 1rem;
        }
        
        .modal {
            padding: 0.5rem !important;
        }
        
        #addResultModal .card {
            width: calc(100vw - 1rem) !important;
            padding: 0.75rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap;">
        <h1 style="color: var(--primary-color);">Service Schedule Details</h1>
        <a href="{{ route('doctor.my-services-schedules') }}" class="btn btn-accent">Back to Services Schedules</a>
    </div>
    
    <div class="card">
        <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Patient Information -->
            <div class="info-section">
                <h3 style="color: var(--primary-color); margin-bottom: 1rem; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">Patient Information</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <strong>Full Name:</strong> {{ $appointment->first_name }} {{ $appointment->middle_initial }} {{ $appointment->last_name }}
                    </div>
                    <div>
                        <strong>Age:</strong> {{ $appointment->age ?? 'N/A' }}
                    </div>
                    <div>
                        <strong>Email:</strong> {{ $appointment->patient->email ?? 'N/A' }}
                    </div>
                    <div>
                        <strong>Phone:</strong> {{ $appointment->patient->phone ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div class="info-section">
                <h3 style="color: var(--primary-color); margin-bottom: 1rem; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">Service Information</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <strong>Service:</strong> {{ $appointment->service->name ?? 'N/A' }}
                    </div>
                    <div>
                        <strong>Branch:</strong> {{ $appointment->branch->name ?? 'N/A' }}
                    </div>
                    <div>
                        <strong>Date:</strong> 
                        @if($appointment->scheduled_date)
                            {{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('M d, Y') }}
                        @else
                            {{ $appointment->created_at->format('M d, Y') }}
                        @endif
                    </div>
                    @if($appointment->scheduled_time)
                    <div>
                        <strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A') }}
                    </div>
                    @endif
                    <div>
                        <strong>Status:</strong> 
                        <span style="
                            padding: 0.25rem 0.5rem; 
                            border-radius: 4px; 
                            font-size: 0.875rem;
                            @if($appointment->status === 'pending')
                                background-color: #fff3cd; color: #856404;
                            @elseif($appointment->status === 'confirmed')
                                background-color: #d4edda; color: #155724;
                            @elseif($appointment->status === 'cancelled')
                                background-color: #f8d7da; color: #721c24;
                            @else
                                background-color: #e2e3e5; color: #383d41;
                            @endif
                        ">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Details -->
        <div style="margin-top: 2rem; border-top: 2px solid #e9ecef; padding-top: 1.5rem;">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Service Details</h3>
            <div class="details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <div style="margin-bottom: 1rem;">
                        <strong>Description:</strong>
                        <div style="margin-top: 0.5rem; padding: 1rem; background-color: #f8f9fa; border-radius: 5px; border-left: 4px solid var(--primary-color);">
                            {{ $appointment->description ?? 'No description provided.' }}
                        </div>
                    </div>
                </div>
                <div>
                    <div style="margin-bottom: 1rem;">
                        <strong>Medical Background:</strong>
                        <div style="margin-top: 0.5rem; padding: 1rem; background-color: #f8f9fa; border-radius: 5px; border-left: 4px solid var(--primary-color);">
                            {{ $appointment->medical_background ?? 'No medical background provided.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($appointment->notes)
        <div style="margin-top: 2rem; border-top: 2px solid #e9ecef; padding-top: 1.5rem;">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Additional Notes</h3>
            <div style="padding: 1rem; background-color: #f8f9fa; border-radius: 5px; border-left: 4px solid var(--primary-color);">
                {{ $appointment->notes }}
            </div>
        </div>
        @endif

        <!-- Cancellation Reason -->
        @if($appointment->status === 'cancelled' && $appointment->cancellation_reason)
        <div style="margin-top: 2rem; border-top: 2px solid #e9ecef; padding-top: 1.5rem;">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Cancellation Reason</h3>
            <div style="padding: 1rem; background-color: #f8d7da; border-radius: 5px; border-left: 4px solid #dc3545; color: #721c24;">
                {{ $appointment->cancellation_reason }}
            </div>
        </div>
        @endif

        <!-- Actions -->
        @if($appointment->status === 'pending')
        <div style="margin-top: 2rem; border-top: 2px solid #e9ecef; padding-top: 1.5rem;">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Actions</h3>
            <div class="action-buttons" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button class="btn btn-primary" onclick="confirmServiceSchedule({{ $appointment->id }})">
                    Confirm Service Schedule
                </button>
                <button class="btn btn-danger" onclick="cancelServiceSchedule({{ $appointment->id }})">
                    Cancel Service Schedule
                </button>
            </div>
        </div>
        @elseif($appointment->status === 'confirmed')
        <div style="margin-top: 2rem; border-top: 2px solid #e9ecef; padding-top: 1.5rem;">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Actions</h3>
            <div class="action-buttons" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button class="btn btn-primary" onclick="addServiceResult({{ $appointment->id }})">
                    Add Result
                </button>
                <button class="btn btn-danger" onclick="cancelServiceSchedule({{ $appointment->id }})">
                    Cancel Service Schedule
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Confirm Service Schedule Modal -->
<div id="confirmModal" class="modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); align-items:center; justify-content:center; z-index:1000; padding: 1rem;">
    <div class="card" style="width:min(520px, calc(100vw - 2rem)); max-width: 100%; border:1px solid #eef1f4;">
        <div style="padding:1rem; border-bottom:1px solid #e9ecef; font-weight:600;">Confirm Service Schedule</div>
        <div style="padding:1rem;">
            <form id="confirmForm" method="POST" style="display:flex; flex-direction:column; gap:1rem;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="doctor_id" value="{{ auth()->id() }}">
                
                <div class="form-group">
                    <label for="scheduled_time">Time <span style="color: red;">*</span></label>
                    <input type="time" name="scheduled_time" id="scheduled_time" class="form-control" required>
                    <small style="color: #6c757d;">Please select the scheduled time for this service</small>
                </div>
                
                <div class="form-group">
                    <label for="scheduled_date">Date (Optional - for rescheduling)</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" class="form-control" min="{{ date('Y-m-d') }}">
                    <small style="color: #6c757d;">Leave empty to use the original booking date</small>
                </div>
                
                <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:.5rem; margin-top: 1rem; flex-wrap: wrap;">
                    <button type="button" class="btn btn-accent" onclick="closeConfirmModal()" style="flex: 1; min-width: 100px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 100px;">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Service Schedule Modal -->
<div id="cancelModal" class="modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); align-items:center; justify-content:center; z-index:1000; padding: 1rem;">
    <div class="card" style="width:min(520px, calc(100vw - 2rem)); max-width: 100%; border:1px solid #eef1f4;">
        <div style="padding:1rem; border-bottom:1px solid #e9ecef; font-weight:600;">Cancel Service Schedule</div>
        <div style="padding:1rem;">
            <form id="cancelForm" method="POST" style="display:flex; flex-direction:column; gap:1rem;">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="cancellation_reason">Reason for cancellation:</label>
                    <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="3" required placeholder="Please provide a reason for cancelling this service schedule..."></textarea>
                </div>
                <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:.5rem; flex-wrap: wrap;">
                    <button type="button" class="btn btn-accent" onclick="closeCancelModal()" style="flex: 1; min-width: 100px;">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="flex: 1; min-width: 100px;">Cancel Service Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Result Modal -->
<div id="addResultModal" class="modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); align-items:center; justify-content:center; z-index:1000; overflow-y: auto; padding: 1rem;">
    <div class="card" style="width:min(800px, calc(100vw - 2rem)); max-width: 100%; border:1px solid #eef1f4; max-height: 90vh; overflow-y: auto;">
        <div style="padding:1rem; border-bottom:1px solid #e9ecef; font-weight:600; display: flex; justify-content: space-between; align-items: center;">
            <span>Add Result</span>
            <button type="button" onclick="closeAddResultModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #aaa;">&times;</button>
        </div>
        <form id="addResultForm" enctype="multipart/form-data" style="padding: 1rem;">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- BEFORE RESULT Section -->
                <div style="background-color: #E6F3F5; border: 2px solid #FFD700; border-radius: 5px; padding: 1.5rem;">
                    <div style="background-color: #008080; color: #ffffff; padding: 0.75rem 1rem; margin: -1.5rem -1.5rem 1.5rem -1.5rem; font-weight: bold; font-size: 1.1rem; text-transform: uppercase;">BEFORE RESULT</div>
                    
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label>Upload Photo/Video</label>
                        <input type="file" name="before_files[]" id="beforeFiles" multiple accept="image/*,video/*" class="form-control">
                        <small style="color: #6c757d;">You can select multiple files</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="before_notes" id="before_notes" class="form-control" rows="3" placeholder="Enter notes about the before condition..."></textarea>
                    </div>
                </div>
                
                <!-- AFTER RESULT Section -->
                <div style="background-color: #E6F3F5; border: 2px solid #FFD700; border-radius: 5px; padding: 1.5rem;">
                    <div style="background-color: #008080; color: #ffffff; padding: 0.75rem 1rem; margin: -1.5rem -1.5rem 1.5rem -1.5rem; font-weight: bold; font-size: 1.1rem; text-transform: uppercase;">AFTER RESULT</div>
                    
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label>Upload Photo/Video</label>
                        <input type="file" name="after_files[]" id="afterFiles" multiple accept="image/*,video/*" class="form-control">
                        <small style="color: #6c757d;">You can select multiple files</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="after_notes" id="after_notes" class="form-control" rows="3" placeholder="Enter notes about the after condition..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:.5rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e9ecef; flex-wrap: wrap;">
                <button type="button" class="btn btn-accent" onclick="closeAddResultModal()" style="flex: 1; min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 100px;">Save Result</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentServiceAppointmentId = null;

function confirmServiceSchedule(appointmentId) {
    const form = document.getElementById('confirmForm');
    form.action = `/doctor/my-services-schedules/${appointmentId}/confirm`;
    document.getElementById('confirmModal').style.display = 'flex';
    // Reset form
    document.getElementById('scheduled_time').value = '';
    document.getElementById('scheduled_date').value = '';
}

function cancelServiceSchedule(appointmentId) {
    const form = document.getElementById('cancelForm');
    form.action = `/doctor/my-services-schedules/${appointmentId}/cancel`;
    document.getElementById('cancelModal').style.display = 'flex';
}

function addServiceResult(appointmentId) {
    currentServiceAppointmentId = appointmentId;
    const form = document.getElementById('addResultForm');
    form.action = `/doctor/my-services-schedules/${appointmentId}/result`;
    document.getElementById('addResultModal').style.display = 'flex';
    // Reset form
    form.reset();
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
    document.getElementById('cancellation_reason').value = '';
}

function closeAddResultModal() {
    document.getElementById('addResultModal').style.display = 'none';
    document.getElementById('addResultForm').reset();
    currentServiceAppointmentId = null;
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
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});
</script>
@endsection

