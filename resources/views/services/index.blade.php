@extends('layouts.patient')

@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 2rem;">Our Services</h1>

    <!-- Categories Section styled like containers/cards with image -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">Service Categories</h2>
        @if(!empty($selectedCategoryId))
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 1rem;">
                <div style="color:#495057;">
                    Showing services for category:
                    <strong>
                        {{ optional($categories->firstWhere('id', (int)$selectedCategoryId))->name ?? 'All' }}
                    </strong>
                </div>
                <a href="{{ route('services.index') }}" class="btn btn-accent">Clear filter</a>
            </div>
        @endif
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
            @foreach($categories as $category)
                <a href="{{ route('services.index', ['category_id' => $category->id]) }}" style="text-decoration:none; color:inherit;">
                <div style="border: 1px solid #e9ecef; border-radius: 12px; background: white; overflow: hidden; display:flex; flex-direction:column; {{ (string)$selectedCategoryId === (string)$category->id ? 'box-shadow: 0 0 0 3px var(--primary-color) inset;' : '' }}">
                    <div style="height: 140px; background:#f1f3f5;">
                        @if($category->image_path)
                            <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                        @else
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#94a3b8;">No image</div>
                        @endif
                    </div>
                    <div style="padding: 1rem; display:flex; flex-direction:column; gap:.5rem;">
                        <h3 style="color: var(--primary-color); margin: 0;">{{ $category->name }}</h3>
                        <p style="color: var(--light-text); margin: 0;">{{ Str::limit($category->description ?? 'No description available', 90) }}</p>
                        <p style="font-size: 0.85rem; color: var(--light-text); margin: 0;">
                            {{ $category->services_count ?? $category->services->count() }} service(s) available
                        </p>
                    </div>
                </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- All Services Section -->
    <div class="card">
        <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">{{ empty($selectedCategoryId) ? 'All Services' : 'Services' }}</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            @foreach($services as $service)
                <div style="border: 1px solid #e9ecef; border-radius: 12px; background: white; overflow: hidden; display:flex; flex-direction:column;">
                    @php($firstImage = $service->images->first())
                    <div style="height: 160px; background:#f1f3f5;">
                        @if($firstImage)
                            <img src="{{ asset('storage/' . $firstImage->image_path) }}" alt="{{ $service->name }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                        @else
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#94a3b8;">No image</div>
                        @endif
                    </div>
                    <div style="padding: 1rem; display:flex; flex-direction:column; gap:.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                        <h3 style="color: var(--primary-color); margin: 0;">{{ $service->name }}</h3>
                        <span style="background: var(--primary-color); color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.8rem;">
                            {{ $service->category->name ?? 'Uncategorized' }}
                        </span>
                    </div>
                        <p style="color: var(--light-text); margin: 0;">{{ Str::limit($service->description ?? 'No description available', 120) }}</p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>
                                @include('components.service-price', ['pricing' => $service->pricing, 'layout' => 'compact'])
                            </span>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('services.show', $service) }}" class="btn btn-accent" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View Details</a>
                                @if($service->is_active)
                                    <form method="POST" action="{{ route('cart.add') }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                                        <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Add to Cart</button>
                                    </form>
                                    <a href="{{ route('consultations.create') }}?service={{ $service->id }}" class="btn btn-accent" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Book Consultation</a>
                                @else
                                    <button disabled style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #ccc; color: #666; border: none; border-radius: 4px; cursor: not-allowed;">Unavailable</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
