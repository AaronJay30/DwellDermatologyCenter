@extends('layouts.patient')

@push('styles')
<style>
    .select-address-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    .page-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .back-button {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #197a8c;
        margin-right: 1rem;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .back-button:hover {
        color: #1a6b7a;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        font-family: 'Figtree', sans-serif;
    }

    .add-address-button {
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
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
        text-decoration: none;
    }

    .add-address-button:hover {
        background: #e5f7fa;
        border-color: #1a6b7a;
        color: #1a6b7a;
    }

    .add-icon {
        font-size: 1.5rem;
    }

    .address-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .address-item {
        background: #ffffff;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .address-item:hover {
        border-color: #197a8c;
        box-shadow: 0 4px 12px rgba(25, 122, 140, 0.15);
    }

    .address-item.selected {
        border-color: #197a8c;
        background: #e5f7fa;
        box-shadow: 0 4px 12px rgba(25, 122, 140, 0.25);
    }

    .location-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #197a8c;
        font-size: 1.5rem;
    }

    .address-content {
        flex: 1;
    }

    .address-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .address-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .address-phone {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .address-text {
        font-size: 0.95rem;
        color: #2c3e50;
        margin-bottom: 0.75rem;
        line-height: 1.5;
    }

    .address-labels {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .address-label {
        padding: 0.25rem 0.75rem;
        border-radius: 5px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .label-default {
        background: #fff;
        color: #dc3545;
        border: 1px solid #dc3545;
    }

    .address-actions {
        display: flex;
        gap: 0.5rem;
    }

    .edit-button {
        background: none;
        border: none;
        color: #197a8c;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        text-decoration: none;
    }

    .edit-button:hover {
        color: #1a6b7a;
        text-decoration: underline;
    }

    .no-addresses {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        border: 1px solid #c3e6cb;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        border: 1px solid #f5c6cb;
    }

    .delete-button {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        text-decoration: none;
    }

    .delete-button:hover {
        color: #c82333;
        text-decoration: underline;
    }

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
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
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
        margin-bottom: 1.5rem;
        color: #2c3e50;
        line-height: 1.6;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
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

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }
</style>
@endpush

@section('content')
<div class="select-address-container">
    <div class="page-header">
        <a href="{{ url()->previous() }}" class="back-button">
            ←
        </a>
        <h1 class="page-title">Select Emergency Contact</h1>
    </div>

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

    <a href="{{ route('emergency-contact.create') }}" class="add-address-button">
        <span class="add-icon">+</span>
        <span>Add Emergency Contact</span>
    </a>

    <div class="address-list">
        @forelse($emergencyContacts as $contact)
            <div class="address-item" onclick="selectContact({{ $contact->id }})">
                <div class="location-icon">
                    📞
                </div>
                <div class="address-content">
                    <div class="address-header">
                        <div>
                            <div class="address-name">{{ $contact->name }}</div>
                            <div class="address-phone">{{ $contact->contact_number }}</div>
                        </div>
                        <div class="address-actions" onclick="event.stopPropagation()">
                            <a href="{{ route('emergency-contact.edit', $contact) }}" class="edit-button">Edit</a>
                            <button type="button" class="delete-button" onclick="event.stopPropagation(); openDeleteModal({{ $contact->id }}, '{{ $contact->name }}')">Delete</button>
                        </div>
                    </div>
                    <div class="address-text">
                        <strong>Relationship:</strong> {{ $contact->relationship }}<br>
                        <strong>Address:</strong> {{ $contact->address }}
                    </div>
                    <div class="address-labels">
                        @if($contact->is_default)
                            <span class="address-label label-default">Default emergency contact</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="no-addresses">
                <p>No emergency contacts saved yet. Click "Add Emergency Contact" to create one.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Delete Emergency Contact</h2>
            <button type="button" class="close-modal" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this emergency contact?</p>
            <p><strong id="delete-contact-name"></strong></p>
            <p style="color: #dc3545; margin-top: 1rem;">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <form id="delete-form" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
    function selectContact(contactId) {
        // Store selected contact ID in sessionStorage
        sessionStorage.setItem('selectedEmergencyContactId', contactId);
        
        // Redirect back to consultation page
        const returnUrl = sessionStorage.getItem('returnUrl') || '{{ route("consultations.medical") }}';
        window.location.href = returnUrl;
    }

    // Store return URL when page loads
    @if(request()->has('return'))
        sessionStorage.setItem('returnUrl', '{{ request()->get("return") }}');
    @else
        sessionStorage.setItem('returnUrl', '{{ route("consultations.medical") }}');
    @endif

    // Delete Modal Functions
    function openDeleteModal(contactId, contactName) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('delete-form');
        const nameElement = document.getElementById('delete-contact-name');
        
        nameElement.textContent = contactName;
        form.action = '{{ route("emergency-contact.destroy", ":id") }}'.replace(':id', contactId);
        modal.classList.add('show');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('show');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeDeleteModal();
        }
    }

    // Handle delete form submission
    document.getElementById('delete-form').addEventListener('submit', function(e) {
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
                closeDeleteModal();
                window.location.reload();
            } else {
                alert('Failed to delete emergency contact. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting. Please try again.');
        });
    });
</script>
@endsection

