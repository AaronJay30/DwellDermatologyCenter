@extends('layouts.patient')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/appointment.css') }}">
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
<style>
    /* Enhanced mobile styles for consultations */
    @media (max-width: 640px) {
        .appointment-wrapper {
            padding: 10px !important;
        }
        
        .appointment-header {
            margin-bottom: 15px !important;
            padding: 0 15px;
        }
        
        .appointment-title {
            font-size: 1.5rem !important;
            margin-bottom: 5px !important;
        }
        
        .appointment-subtitle {
            font-size: 0.875rem !important;
        }
        
        .appointment-card {
            padding: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        
        /* Mobile card styling */
        .table-wrapper table tbody tr {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .table-wrapper table tbody tr td {
            padding: 6px 0 !important;
            border: none !important;
        }
        
        .table-wrapper table tbody tr td:before {
            font-weight: 600;
            color: #197a8c;
            font-size: 0.8rem;
            min-width: 85px;
        }
        
        /* Profile icon in mobile */
        .profile-icon {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.9rem !important;
            margin-right: 8px !important;
        }
        
        .primary-column-text {
            font-size: 0.9rem !important;
        }
        
        .primary-column-text small {
            font-size: 0.8rem !important;
        }
        
        /* Status badges */
        .status-badge {
            padding: 4px 10px !important;
            font-size: 0.75rem !important;
        }
        
        /* Action buttons */
        .action-btn {
            padding: 6px 10px !important;
            font-size: 0.85rem !important;
        }
        
        /* Empty state */
        .empty-state {
            padding: 30px 15px !important;
        }
        
        .empty-state-icon {
            font-size: 2.5rem !important;
        }
        
        .empty-state h3 {
            font-size: 1.1rem !important;
        }
        
        .empty-state p {
            font-size: 0.85rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container appointment-wrapper">
    <!-- Header -->
    <div class="appointment-header">
        <h1 class="appointment-title">My Consultations</h1>
        <p class="appointment-subtitle">Track and review your scheduled consultations</p>
    </div>

    @if($consultations->count() > 0)
        <div class="appointment-card">
            <div class="table-wrapper">
                <table class="table appointment-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Branch</th>
                            <th>Doctor</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultations as $consultation)
                            @php
                                $branchName = $consultation->branch->name ?? 'N/A';
                                $initials = $branchName !== 'N/A' 
                                    ? mb_substr($branchName, 0, 1)
                                    : 'B';
                                
                                // Check if it's a service booking or consultation
                                $isServiceBooking = $consultation->service_id && !$consultation->time_slot_id;
                                
                                // Get date and time
                                if ($consultation->timeSlot) {
                                    $date = $consultation->timeSlot->date->format('M d, Y');
                                    $time = $consultation->timeSlot->start_time . ' - ' . $consultation->timeSlot->end_time;
                                } elseif ($consultation->scheduled_date) {
                                    $date = \Carbon\Carbon::parse($consultation->scheduled_date)->format('M d, Y');
                                    $time = $consultation->scheduled_time ?? 'To be scheduled';
                                } else {
                                    // Extract date from notes if available
                                    $date = 'To be scheduled';
                                    $time = 'Pending';
                                    if ($consultation->notes) {
                                        if (preg_match('/Preferred Date:\s*([^\n]+)/i', $consultation->notes, $matches)) {
                                            try {
                                                $date = \Carbon\Carbon::parse(trim($matches[1]))->format('M d, Y');
                                            } catch (\Exception $e) {
                                                // Keep default
                                            }
                                        }
                                    }
                                }
                                
                                // Get type/name
                                if ($isServiceBooking) {
                                    $typeName = $consultation->service->name ?? 'Service Booking';
                                } else {
                                    $typeName = ucfirst($consultation->consultation_type ?? 'Consultation');
                                }
                            @endphp
                            <tr>
                                <td data-label="Date & Time">
                                    <div class="profile-icon">{{ $initials }}</div>
                                    <span class="primary-column-text">
                                        {{ $date }}<br>
                                        <small style="font-size: 0.85rem; color: #6c757d; font-weight: 400;">
                                            {{ $time }}
                                        </small>
                                    </span>
                                </td>
                                <td data-label="Branch">{{ $branchName }}</td>
                                <td data-label="Doctor">{{ $consultation->doctor->name ?? 'N/A' }}</td>
                                <td data-label="Type">{{ $typeName }}</td>
                                <td data-label="Status">
                                    <span class="status-badge status-{{ strtolower($consultation->status ?? 'pending') }}">
                                        {{ ucfirst($consultation->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button onclick="showConsultationDetails({{ $consultation->id }})" 
                                                class="action-btn" 
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(in_array($consultation->status, ['pending', 'confirmed']))
                                        <button onclick="openCancelModal({{ $consultation->id }})" 
                                                class="action-btn" 
                                                style="color: #dc3545;"
                                                title="Cancel Consultation">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($consultations->hasPages())
        <div class="pagination-wrapper">
            {{ $consultations->links() }}
        </div>
        @endif
    @else
        <div class="text-center py-8 mx-auto" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <p class="no-appointments">You haven't booked any consultations yet.</p>
            <a href="{{ route('consultations.create') }}" class="btn btn-primary mx-auto" style="margin-top: 1rem;">
                Book Your First Consultation
            </a>
        </div>
    @endif
</div>

<!-- Consultation Details Modal -->
<div id="consultationModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; width: 100%;">
        <div style="background: var(--modal-bg, #ffffff); border-radius: 16px; max-width: 42rem; width: auto; margin: auto; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px var(--shadow-color, rgba(0,0,0,0.3)); color: var(--dark-text, #2c3e50);">
            <div style="padding: 1.5rem; width: 500px" class="view-modal">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color, #e9ecef); padding-bottom: 1rem;">
                    <h2 id="consultationModalTitle" style="font-size: 1.5rem; font-weight: bold; color: var(--primary-color); margin: 0;">Consultation Details</h2>
                    <button onclick="closeConsultationModal()" style="background: none; border: none; cursor: pointer; font-size: 1.5rem; padding: 0.25rem 0.5rem; color: var(--light-text, #6c757d); transition: color 0.2s;" onmouseover="this.style.color=window.getComputedStyle(document.documentElement).getPropertyValue('--dark-text').trim()" onmouseout="this.style.color=window.getComputedStyle(document.documentElement).getPropertyValue('--light-text').trim()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="consultationDetails" style="padding-top: 1rem;">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Consultation Modal -->
<div id="cancelModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1001; align-items: center; justify-content: center;">
    <div style="background: var(--modal-bg, #ffffff); border-radius: 16px; max-width: 32rem; width: 100%; margin: 1rem; box-shadow: 0 20px 60px var(--shadow-color, rgba(0,0,0,0.3)); color: var(--dark-text, #2c3e50);">
        <div style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color, #e9ecef); padding-bottom: 1rem;">
                    <h2 style="font-size: 1.5rem; font-weight: bold; color: #dc3545; margin: 0;">Cancel Consultation</h2>
                    <button onclick="closeCancelModal()" style="background: none; border: none; cursor: pointer; font-size: 1.5rem; padding: 0.25rem 0.5rem; color: var(--light-text, #6c757d); transition: color 0.2s;" onmouseover="this.style.color=window.getComputedStyle(document.documentElement).getPropertyValue('--dark-text').trim()" onmouseout="this.style.color=window.getComputedStyle(document.documentElement).getPropertyValue('--light-text').trim()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="cancelForm" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    <div>
                        <label for="cancellation_reason" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark-text, #2c3e50);">
                            Reason for Cancellation <span style="color: #dc3545;">*</span>
                        </label>
                        <textarea 
                            id="cancellation_reason" 
                            name="cancellation_reason" 
                            rows="4" 
                            required
                            placeholder="Please provide a reason for cancelling this consultation..."
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--input-border, #ced4da); border-radius: 8px; font-size: 0.95rem; font-family: inherit; resize: vertical; background: var(--input-bg, #ffffff); color: var(--dark-text, #2c3e50);"
                        ></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                        <button 
                            type="button" 
                            onclick="closeCancelModal()" 
                            style="padding: 0.75rem 1.5rem; border: 1px solid var(--input-border, #ced4da); background: var(--card-bg, #ffffff); border-radius: 8px; cursor: pointer; font-weight: 500; color: var(--light-text, #6c757d); transition: all 0.2s;"
                            onmouseover="this.style.background=window.getComputedStyle(document.documentElement).getPropertyValue('--gray-light').trim()"
                            onmouseout="this.style.background=window.getComputedStyle(document.documentElement).getPropertyValue('--card-bg').trim()"
                        >
                            Close
                        </button>
                        <button 
                            type="submit" 
                            style="padding: 0.75rem 1.5rem; border: none; background: #dc3545; border-radius: 8px; cursor: pointer; font-weight: 500; color: white; transition: all 0.2s;"
                            onmouseover="this.style.background='#c82333'"
                            onmouseout="this.style.background='#dc3545'"
                        >
                            Cancel Consultation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentConsultationId = null;

function showConsultationDetails(consultationId) {
    const modal = document.getElementById('consultationModal');
    const modalBody = document.getElementById('consultationDetails');
    
    // Show loading state
    modalBody.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="margin-top: 1rem; color: #6c757d;">Loading consultation details...</p>
        </div>
        <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    `;
    modal.style.display = 'flex';
    
    // Fetch consultation data
    fetch(`/consultations/${consultationId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch consultation details');
            }
            return response.json();
        })
        .then(data => {
            const consultation = data.consultation;
            
            // Update modal title
            const modalTitle = document.getElementById('consultationModalTitle');
            if (modalTitle) {
                modalTitle.textContent = consultation.is_service_booking ? 'Service Booking Details' : 'Consultation Details';
            }
            
            // Get CSS variables for dark mode support
            const root = document.documentElement;
            const grayLight = getComputedStyle(root).getPropertyValue('--gray-light').trim();
            const darkText = getComputedStyle(root).getPropertyValue('--dark-text').trim();
            const primaryColor = getComputedStyle(root).getPropertyValue('--primary-color').trim();
            
            // Build the HTML with actual data
            let html = `
                <div style="display: grid; gap: 1.5rem;">
                    <div style="background: ${grayLight}; padding: 1.25rem; border-radius: 12px; border-left: 4px solid ${primaryColor};">
                        <h3 style="font-size: 1.1rem; font-weight: 600; color: ${primaryColor}; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-user" style="font-size: 1rem;"></i> Patient Information
                        </h3>
                        <div style="display: grid; gap: 0.5rem; color: ${darkText};">
                            <p><strong>Name:</strong> ${consultation.patient_name}</p>
                            <p><strong>Age:</strong> ${consultation.age}</p>
                        </div>
                    </div>
                    <div style="background: ${grayLight}; padding: 1.25rem; border-radius: 12px; border-left: 4px solid ${primaryColor};">
                        <h3 style="font-size: 1.1rem; font-weight: 600; color: ${primaryColor}; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-calendar-check" style="font-size: 1rem;"></i> ${consultation.is_service_booking ? 'Service Booking Details' : 'Consultation Details'}
                        </h3>
                        <div style="display: grid; gap: 0.5rem; color: ${darkText};">
                            ${consultation.is_service_booking ? `<p><strong>Service:</strong> ${consultation.service_name || consultation.consultation_type}</p>` : `<p><strong>Type:</strong> ${consultation.consultation_type}</p>`}
                            <p><strong>Description:</strong> ${consultation.description}</p>
                            ${consultation.medical_background && consultation.medical_background !== 'N/A' ? `<p><strong>Medical Background:</strong> ${consultation.medical_background}</p>` : ''}
                            <p><strong>Branch:</strong> ${consultation.branch_name}</p>
                            <p><strong>Date:</strong> ${consultation.date}</p>
                            <p><strong>Time:</strong> ${consultation.time}</p>
                            ${!consultation.is_service_booking ? `<p><strong>Doctor:</strong> ${consultation.doctor_name}</p>` : ''}
                        </div>
                    </div>
                    <div style="background: ${grayLight}; padding: 1.25rem; border-radius: 12px; border-left: 4px solid ${primaryColor};">
                        <h3 style="font-size: 1.1rem; font-weight: 600; color: ${primaryColor}; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-info-circle" style="font-size: 1rem;"></i> Status
                        </h3>
                        <div style="display: grid; gap: 0.5rem; color: ${darkText};">
                            <p><strong>Current Status:</strong> <span style="text-transform: capitalize;">${consultation.status}</span></p>
            `;
            
            // Show cancellation reason if cancelled
            if (consultation.status === 'cancelled' && consultation.cancellation_reason) {
                html += `
                            <div style="margin-top: 0.75rem; padding: 0.75rem; background: #f8d7da; border-radius: 8px; border-left: 4px solid #dc3545;">
                                <p style="margin: 0;"><strong style="color: #721c24;">Cancellation Reason:</strong></p>
                                <p style="margin: 0.5rem 0 0 0; color: #721c24;">${consultation.cancellation_reason}</p>
                            </div>
                `;
            }
            
            html += `
                        </div>
                    </div>
                </div>
            `;
            
            modalBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching consultation data:', error);
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #dc3545;">
                    <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>Error loading consultation information. Please try again.</p>
                    <button onclick="showConsultationDetails(${consultationId})" style="margin-top: 1rem; padding: 0.5rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">
                        Retry
                    </button>
                </div>
            `;
        });
}

function closeConsultationModal() {
    document.getElementById('consultationModal').style.display = 'none';
}

function openCancelModal(consultationId) {
    currentConsultationId = consultationId;
    const modal = document.getElementById('cancelModal');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    document.getElementById('cancellation_reason').value = '';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
    currentConsultationId = null;
    document.getElementById('cancelForm').reset();
}

// Handle cancel form submission
document.addEventListener('DOMContentLoaded', function() {
    const consultationModal = document.getElementById('consultationModal');
    const cancelModal = document.getElementById('cancelModal');
    const cancelForm = document.getElementById('cancelForm');
    
    // Close consultation modal when clicking outside
    if (consultationModal) {
        consultationModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeConsultationModal();
            }
        });
    }
    
    // Close cancel modal when clicking outside
    if (cancelModal) {
        cancelModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });
    }
    
    // Handle cancel form submission
    if (cancelForm) {
        cancelForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentConsultationId) {
                alert('Error: Consultation ID not found');
                return;
            }
            
            const reason = document.getElementById('cancellation_reason').value.trim();
            if (!reason) {
                alert('Please provide a cancellation reason');
                return;
            }
            
            // Disable submit button
            const submitBtn = cancelForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Cancelling...';
            
            // Send cancellation request
            fetch(`/consultations/${currentConsultationId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                   document.querySelector('input[name="_token"]')?.value
                },
                body: JSON.stringify({
                    cancellation_reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Consultation cancelled successfully!');
                    closeCancelModal();
                    // Reload the page to update the table
                    window.location.reload();
                } else {
                    alert(data.error || 'Failed to cancel consultation. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error cancelling consultation:', error);
                alert('An error occurred while cancelling the consultation. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }
});
</script>
@endsection
