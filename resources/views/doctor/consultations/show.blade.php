@extends('layouts.dashboard')
@section('page-title', 'Consultation Details')

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container mx-auto p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Consultation Details</h1>
        <a href="{{ route('doctor.consultations.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to Consultations
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Patient Information -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h2 class="text-xl font-semibold mb-4">Patient Information</h2>
            <div class="space-y-3">
                <div>
                    <label class="font-medium">Name:</label>
                    <p>{{ $consultation->first_name }} {{ $consultation->middle_initial }} {{ $consultation->last_name }}</p>
                </div>
                <div>
                    <label class="font-medium">Age:</label>
                    <p>{{ $consultation->age }}</p>
                </div>
                <div>
                    <label class="font-medium">Email:</label>
                    <p>{{ $consultation->patient->email }}</p>
                </div>
            </div>
        </div>

        <!-- Appointment Details -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h2 class="text-xl font-semibold mb-4">Appointment Details</h2>
            <div class="space-y-3">
                <div>
                    <label class="font-medium">Date:</label>
                    <p>{{ $consultation->timeSlot->date->format('M d, Y') }}</p>
                </div>
                <div>
                    <label class="font-medium">Time:</label>
                    <p>{{ $consultation->timeSlot->start_time }} - {{ $consultation->timeSlot->end_time }}</p>
                </div>
                <div>
                    <label class="font-medium">Branch:</label>
                    <p>{{ $consultation->branch->name }}</p>
                </div>
                <div>
                    <label class="font-medium">Status:</label>
                    <p>
                        @switch($consultation->status)
                            @case('pending')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">Pending</span>
                                @break
                            @case('confirmed')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Confirmed</span>
                                @break
                            @case('cancelled')
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Cancelled</span>
                                @break
                            @default
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">{{ ucfirst($consultation->status) }}</span>
                        @endswitch
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Consultation Information -->
    <div class="mt-6 bg-gray-50 p-6 rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Consultation Information</h2>
        <div class="space-y-4">
            <div>
                <label class="font-medium">Consultation Type:</label>
                <p>{{ $consultation->consultation_type }}</p>
            </div>
            <div>
                <label class="font-medium">Description:</label>
                <p class="mt-1 p-3 bg-white rounded border">{{ $consultation->description }}</p>
            </div>
            @if($consultation->medical_background)
                <div>
                    <label class="font-medium">Medical Background:</label>
                    <p class="mt-1 p-3 bg-white rounded border">{{ $consultation->medical_background }}</p>
                </div>
            @endif
            @if($consultation->referral_source)
                <div>
                    <label class="font-medium">Referral Source:</label>
                    <p>{{ $consultation->referral_source }}</p>
                </div>
            @endif
            @if($consultation->cancellation_reason)
                <div>
                    <label class="font-medium">Cancellation Reason:</label>
                    <p class="mt-1 p-3 bg-red-50 border border-red-200 rounded text-red-800">{{ $consultation->cancellation_reason }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    @if($consultation->status === 'pending')
        <div class="mt-6 flex space-x-4">
            <button onclick="confirmConsultation({{ $consultation->id }})" 
                    class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                Confirm Consultation
            </button>
            <button onclick="cancelConsultation({{ $consultation->id }})" 
                    class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">
                Cancel Consultation
            </button>
        </div>
    @endif
</div>

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
