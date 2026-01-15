@extends('layouts.dashboard')
@section('page-title', 'Edit Promotion')

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h1 style="color: var(--primary-color);">Edit Promotion</h1>
        <a href="{{ route('doctor.promotions') }}" class="btn btn-accent">Back to Promotions</a>
    </div>
    <div class="card" style="padding:1rem;">
        <form action="{{ route('doctor.promotions.update', $promotion) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="display:grid; gap:1rem; grid-template-columns: 1fr 1fr;">
                <div>
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $promotion->name) }}" class="form-control" required />
                    @error('name')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label>Type</label>
                    <input type="text" name="type" value="{{ old('type', $promotion->type) }}" class="form-control" placeholder="birthday or campaign" required />
                    @error('type')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div style="grid-column: span 2;">
                    <label>Description</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description', $promotion->description) }}</textarea>
                    @error('description')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label>Discount (%)</label>
                    <input type="number" name="discount_percent" value="{{ old('discount_percent', $promotion->discount_percent) }}" min="0" max="100" class="form-control" />
                    @error('discount_percent')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        @php($statusOld = old('status', $promotion->status))
                        <option value="draft" {{ $statusOld==='draft'?'selected':'' }}>Draft</option>
                        <option value="active" {{ $statusOld==='active'?'selected':'' }}>Active</option>
                        <option value="archived" {{ $statusOld==='archived'?'selected':'' }}>Archived</option>
                    </select>
                    @error('status')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
                <div style="grid-column: span 2; display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div>
                        <label>Starts At</label>
                        <input type="date" name="starts_at" value="{{ old('starts_at', optional($promotion->starts_at)->format('Y-m-d')) }}" class="form-control" />
                        @error('starts_at')<div style="color:red;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label>Ends At</label>
                        <input type="date" name="ends_at" value="{{ old('ends_at', optional($promotion->ends_at)->format('Y-m-d')) }}" class="form-control" />
                        @error('ends_at')<div style="color:red;">{{ $message }}</div>@enderror
                    </div>
                    <div style="grid-column: span 2; color:#6c757d; font-size:.9rem;">Campaign Period</div>
                </div>
                <div style="display:flex; align-items:center; gap:.5rem;">
                    <input type="checkbox" name="is_active" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }} id="is_active" />
                    <label for="is_active">Active</label>
                </div>
                <div style="grid-column: span 2;">
                    <label>Add Photos</label>
                    <input type="file" name="images[]" multiple accept="image/*" />
                    @error('images')<div style="color:red;">{{ $message }}</div>@enderror
                    @error('images.*')<div style="color:red;">{{ $message }}</div>@enderror
                </div>
            </div>

            @if($promotion->images->count())
                <div style="margin-top:1rem;">
                    <h3>Existing Photos</h3>
                    <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                        @foreach($promotion->images as $img)
                            <div style="border:1px solid #e9ecef; padding:.5rem; border-radius:4px;">
                                <img src="{{ asset('storage/'.$img->image_path) }}" alt="Promotion Image" style="height:100px; width:auto; display:block;" />
                                <label style="display:flex; align-items:center; gap:.25rem; margin-top:.25rem;">
                                    <input type="checkbox" name="remove_image_ids[]" value="{{ $img->id }}" /> Remove
                                </label>
                            </div>
                        @endforeach
                    </div>

                    @if($promotion->images->count() > 1)
                        <div style="margin-top:1rem;">
                            <div id="promo-slideshow" style="position:relative; width:100%; max-width:640px; height:360px; overflow:hidden; border-radius:8px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); border: 1px solid #eef1f4;">
                                @foreach($promotion->images as $index => $img)
                                    <img src="{{ asset('storage/'.$img->image_path) }}" alt="Slide {{ $index+1 }}" style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; transition: opacity .6s ease; opacity: {{ $index === 0 ? '1' : '0' }};">
                                @endforeach
                            </div>
                        </div>
                        @push('scripts')
                        <script>
                            (function(){
                                const container = document.getElementById('promo-slideshow');
                                if(!container) return;
                                const slides = Array.from(container.querySelectorAll('img'));
                                if (slides.length <= 1) return;
                                let idx = 0;
                                setInterval(() => {
                                    slides[idx].style.opacity = '0';
                                    idx = (idx + 1) % slides.length;
                                    slides[idx].style.opacity = '1';
                                }, 3000);
                            })();
                        </script>
                        @endpush
                    @endif
                </div>
            @endif

            <div style="margin-top:1rem; display:flex; gap:.5rem;">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('doctor.promotions') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection


