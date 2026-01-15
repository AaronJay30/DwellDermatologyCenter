@extends('layouts.dashboard')
@section('page-title', 'Create Promotion')

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 2rem;">Add Promotion</h1>
    <div class="card" style="padding:1rem;">
        <form action="{{ route('doctor.promotions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:grid; gap:1rem; grid-template-columns: 1fr 1fr;">
                <div>
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required />
                    @error('name')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label>Type</label>
                    <input type="text" name="type" value="{{ old('type') }}" class="form-control" placeholder="birthday or campaign" required />
                    @error('type')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div style="grid-column: span 2;">
                    <label>Description</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                    @error('description')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div style="grid-column: span 2; display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div>
                        <label>Starts At</label>
                        <input type="date" name="starts_at" value="{{ old('starts_at') }}" class="form-control" />
                        @error('starts_at')<div style="color:red;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label>Ends At</label>
                        <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="form-control" />
                        @error('ends_at')<div style="color:red;">{{ $message }}</div>@enderror
                    </div>
                    <div style="grid-column: span 2; color:#6c757d; font-size:.9rem;">Campaign Period</div>
                </div>
                <div>
                    <label>Discount (%)</label>
                    <input type="number" name="discount_percent" value="{{ old('discount_percent') }}" min="0" max="100" class="form-control" />
                    @error('discount_percent')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="draft" {{ old('status')==='draft'?'selected':'' }}>Draft</option>
                        <option value="active" {{ old('status')==='active'?'selected':'' }}>Active</option>
                        <option value="archived" {{ old('status')==='archived'?'selected':'' }}>Archived</option>
                    </select>
                    @error('status')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div style="display:flex; align-items:center; gap:.5rem;">
                    <input type="checkbox" name="is_active" {{ old('is_active') ? 'checked' : '' }} id="is_active" />
                    <label for="is_active">Active</label>
                </div>
                <div style="grid-column: span 2;">
                    <label>Photos</label>
                    <input type="file" name="images[]" multiple accept="image/*" />
                    @error('images')<div style="color:red;">{{ $message }}</div>@enderror
                    @error('images.*')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
            </div>
            <div style="margin-top:1rem; display:flex; gap:.5rem;">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('doctor.promotions') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection


