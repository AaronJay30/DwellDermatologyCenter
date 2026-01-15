@extends('layouts.dashboard')
@section('page-title', 'Edit Slot')

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/form-inputs.css') }}">
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
</style>

<div class="container">
    <div class="slot-form-container compact-form">
        <form method="POST" action="{{ route('admin.slots.update', $slot) }}" id="slotForm">
            @csrf
            @method('PUT')
            
            <!-- Branch Display (Admin can only edit for their branch) -->
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
                    <input type="number" id="consultation_fee" name="consultation_fee" value="{{ old('consultation_fee', $slot->consultation_fee ?? '700') }}" step="0.01" min="0" placeholder="Enter consultation fee" required>
                </div>
                @error('consultation_fee')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Date -->
            <div class="modern-input-wrapper">
                <label for="date">Date</label>
                <div class="modern-input-container">
                    <i class="fas fa-calendar input-icon"></i>
                    <input type="date" id="date" name="date" value="{{ old('date', optional($slot->date)->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                </div>
                @error('date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Start Time and End Time on one line -->
            <div class="form-row">
                <div class="modern-input-wrapper">
                    <label for="start_time">Start Time</label>
                    <div class="modern-input-container">
                        <i class="fas fa-clock input-icon"></i>
                        <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('H:i') : '') }}" required>
                    </div>
                    @error('start_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modern-input-wrapper">
                    <label for="end_time">End Time</label>
                    <div class="modern-input-container">
                        <i class="fas fa-clock input-icon"></i>
                        <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('H:i') : '') }}" required>
                    </div>
                    @error('end_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @error('time')
                <div style="color: #dc3545; font-size: 0.875rem; margin-bottom: 1rem; padding: 0.75rem; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;">{{ $message }}</div>
            @enderror

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Update Time Slot</button>
                <a href="{{ route('admin.slots') }}" class="btn btn-accent" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
