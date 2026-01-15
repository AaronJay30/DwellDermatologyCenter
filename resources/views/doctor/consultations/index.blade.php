@extends('layouts.dashboard')
@section('page-title', 'Consultations')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem;">Consultation Requests</h1>

    @if($consultations->count() > 0)
        <div class="card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date & Time</th>
                            <th>Branch</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultations as $consultation)
                            @php
                                $patientName = trim($consultation->first_name . ' ' . ($consultation->middle_initial ?? '') . ' ' . $consultation->last_name);
                                $initials = collect(explode(' ', trim($patientName)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                                if ($initials === '') { $initials = 'P'; }
                            @endphp
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="profile-icon">{{ $initials }}</div>
                                        <div>
                                            <span class="primary-column-text">{{ $patientName }}</span><br>
                                            <small style="color: #6c757d; font-size: 0.875rem;">Age: {{ $consultation->age }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $consultation->timeSlot->date->format('M d, Y') }}<br>
                                    <small style="color: #6c757d; font-size: 0.875rem;">{{ $consultation->timeSlot->start_time }} - {{ $consultation->timeSlot->end_time }}</small>
                                </td>
                                <td>{{ $consultation->branch->name }}</td>
                                <td>{{ $consultation->consultation_type }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($consultation->status) }}">
                                        {{ ucfirst($consultation->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('doctor.consultations.show', $consultation) }}" 
                                           class="action-btn" title="View">
                                            <i data-feather="eye"></i>
                                        </a>
                                        @if($consultation->status === 'pending')
                                            <button onclick="confirmConsultation({{ $consultation->id }})" 
                                                    class="action-btn" title="Confirm">
                                                <i data-feather="check"></i>
                                            </button>
                                            <button onclick="cancelConsultation({{ $consultation->id }})" 
                                                    class="action-btn" title="Cancel">
                                                <i data-feather="x"></i>
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
    @else
        <div class="card" style="text-align: center; padding: 3rem;">
            <p style="color: #6c757d;">No consultation requests found.</p>
        </div>
    @endif
</div>

<script>
    if (window.feather && typeof window.feather.replace === 'function') {
        window.feather.replace();
    }
</script>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Confirm Consultation</h3>
                <form id="confirmForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="confirmation_notes" class="block text-sm font-medium mb-2">Confirmation Notes (Optional):</label>
                        <textarea name="confirmation_notes" id="confirmation_notes" rows="3" class="w-full border rounded px-3 py-2" placeholder="Any additional notes for the patient..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancellation Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Cancel Consultation</h3>
                <form id="cancelForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="cancellation_reason" class="block text-sm font-medium mb-2">Cancellation Reason:</label>
                        <textarea name="cancellation_reason" id="cancellation_reason" rows="3" class="w-full border rounded px-3 py-2" placeholder="Please provide a reason for cancellation..." required></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeCancelModal()" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Cancel Consultation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmConsultation(consultationId) {
    document.getElementById('confirmForm').action = `/admin/consultations/${consultationId}/confirm`;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function cancelConsultation(consultationId) {
    document.getElementById('cancelForm').action = `/admin/consultations/${consultationId}/cancel`;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
@endsection
