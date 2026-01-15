@extends('layouts.patient')

@push('styles')
<style>
    .form-container {
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

    .checkbox-group input[type="checkbox"],
    .checkbox-group input[type="radio"] {
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

    .radio-group {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <a href="{{ route('medical-information.select') }}" class="back-button">
            ←
        </a>
        <h1 class="page-title">Edit Medical Information</h1>
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
        <form action="{{ route('medical-information.update', $medicalInformation) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Comorbidities</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="hypertension" name="hypertension" value="1" 
                           {{ old('hypertension', $medicalInformation->hypertension) ? 'checked' : '' }}>
                    <label for="hypertension">Hypertension</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="diabetes" name="diabetes" value="1" 
                           {{ old('diabetes', $medicalInformation->diabetes) ? 'checked' : '' }}>
                    <label for="diabetes">Diabetes</label>
                </div>
                <div style="margin-top: 0.5rem;">
                    <input type="text" id="comorbidities_others" name="comorbidities_others" class="form-input" 
                           value="{{ old('comorbidities_others', $medicalInformation->comorbidities_others) }}" placeholder="Others, please specify">
                </div>
            </div>

            <div class="form-group">
                <label for="allergies" class="form-label">Allergies</label>
                <textarea name="allergies" id="allergies" class="form-textarea" placeholder="List any allergies">{{ old('allergies', $medicalInformation->allergies) }}</textarea>
            </div>

            <div class="form-group">
                <label for="medications" class="form-label">Medications</label>
                <textarea name="medications" id="medications" class="form-textarea" placeholder="List current medications">{{ old('medications', $medicalInformation->medications) }}</textarea>
            </div>

            <div class="form-group">
                <label for="anesthetics" class="form-label">Anesthetics</label>
                <textarea name="anesthetics" id="anesthetics" class="form-textarea" placeholder="List any anesthetics">{{ old('anesthetics', $medicalInformation->anesthetics) }}</textarea>
                <input type="text" id="anesthetics_others" name="anesthetics_others" class="form-input" 
                       style="margin-top: 0.5rem;" value="{{ old('anesthetics_others', $medicalInformation->anesthetics_others) }}" placeholder="Others, please specify">
            </div>

            <div class="form-group">
                <label for="previous_hospitalizations_surgeries" class="form-label">Previous hospitalizations / surgeries</label>
                <textarea name="previous_hospitalizations_surgeries" id="previous_hospitalizations_surgeries" class="form-textarea" placeholder="List previous hospitalizations or surgeries">{{ old('previous_hospitalizations_surgeries', $medicalInformation->previous_hospitalizations_surgeries) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Smoker?</label>
                <div class="radio-group">
                    <div class="checkbox-group">
                        <input type="radio" id="smoker_yes" name="smoker" value="yes" 
                               {{ old('smoker', $medicalInformation->smoker) == 'yes' ? 'checked' : '' }}>
                        <label for="smoker_yes">Yes</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="radio" id="smoker_no" name="smoker" value="no" 
                               {{ old('smoker', $medicalInformation->smoker) == 'no' ? 'checked' : '' }}>
                        <label for="smoker_no">No</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alcoholic beverage drinker?</label>
                <div class="radio-group">
                    <div class="checkbox-group">
                        <input type="radio" id="alcoholic_drinker_yes" name="alcoholic_drinker" value="yes" 
                               {{ old('alcoholic_drinker', $medicalInformation->alcoholic_drinker) == 'yes' ? 'checked' : '' }}>
                        <label for="alcoholic_drinker_yes">Yes</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="radio" id="alcoholic_drinker_no" name="alcoholic_drinker" value="no" 
                               {{ old('alcoholic_drinker', $medicalInformation->alcoholic_drinker) == 'no' ? 'checked' : '' }}>
                        <label for="alcoholic_drinker_no">No</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="known_family_illnesses" class="form-label">Known family illnesses</label>
                <textarea name="known_family_illnesses" id="known_family_illnesses" class="form-textarea" placeholder="List known family illnesses">{{ old('known_family_illnesses', $medicalInformation->known_family_illnesses) }}</textarea>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="is_default" name="is_default" value="1" 
                       {{ old('is_default', $medicalInformation->is_default) ? 'checked' : '' }}>
                <label for="is_default">Set as Default Medical Information</label>
            </div>

            <div class="form-actions">
                <a href="{{ route('medical-information.select') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

