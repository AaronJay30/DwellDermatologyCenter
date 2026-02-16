@extends('layouts.patient')

@push('styles')
<style>
    .form-container {
        max-width: 600px;
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
        text-decoration: none;
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

    .form-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
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
        font-weight: 500;
        color: #2c3e50;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
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
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: #197a8c;
        color: white;
    }

    .btn-primary:hover {
        background: #1a6b7a;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

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
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <a href="{{ route('emergency-contact.select') }}" class="back-button">
            ←
        </a>
        <h1 class="page-title">Add Emergency Contact</h1>
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

    <div class="form-card">
        <form action="{{ route('emergency-contact.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-input" 
                       value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="relationship" class="form-label">Relationship</label>
                <input type="text" id="relationship" name="relationship" class="form-input" 
                       value="{{ old('relationship') }}" required>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <textarea id="address" name="address" class="form-textarea" required>{{ old('address') }}</textarea>
            </div>

            <div class="form-group">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" class="form-input" 
                       value="{{ old('contact_number') }}" required>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="is_default" name="is_default" value="1" 
                       {{ old('is_default') ? 'checked' : '' }}>
                <label for="is_default">Set as Default Emergency Contact</label>
            </div>

            <div class="form-actions">
                <a href="{{ route('emergency-contact.select') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

