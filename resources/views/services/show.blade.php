@extends('layouts.patient')

@section('content')
<div class="container">
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('services.index') }}" style="color: var(--primary-color); text-decoration: none;">← Back to Services</a>
    </div>

    <div class="card">
        @php($images = $service->images)
        @if($images && $images->count())
            <div style="margin-bottom: 1.5rem;">
                <div id="service-slider" style="position: relative; overflow: hidden; border-radius: 8px;">
                    <div id="service-slides" style="display: flex; transition: transform .4s ease;">
                        @foreach($images as $img)
                            <div style="min-width: 100%;">
                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="Service image" style="width: 100%; height: 320px; object-fit: cover; display:block;">
                            </div>
                        @endforeach
                    </div>
                    <button id="prevSlide" class="btn btn-accent" style="position: absolute; top: 50%; left: .5rem; transform: translateY(-50%);">‹</button>
                    <button id="nextSlide" class="btn btn-accent" style="position: absolute; top: 50%; right: .5rem; transform: translateY(-50%);">›</button>
                </div>
                <div style="display:flex; gap:.5rem; justify-content:center; margin-top:.5rem;">
                    @foreach($images as $idx => $img)
                        <span class="dot" data-idx="{{ $idx }}" style="width:8px; height:8px; border-radius:999px; background:#cfd4da; display:inline-block;"></span>
                    @endforeach
                </div>
            </div>
            <script>
                (function(){
                    const slides = document.getElementById('service-slides');
                    const total = slides ? slides.children.length : 0;
                    if (!slides || total === 0) return;
                    let current = 0;
                    const update = () => {
                        slides.style.transform = 'translateX(' + (-current * 100) + '%)';
                        const dots = document.querySelectorAll('.dot');
                        dots.forEach((d,i)=>{ d.style.background = i===current ? 'var(--primary-color)' : '#cfd4da'; });
                    };
                    document.getElementById('prevSlide').addEventListener('click', ()=>{ current = (current - 1 + total) % total; update(); });
                    document.getElementById('nextSlide').addEventListener('click', ()=>{ current = (current + 1) % total; update(); });
                    document.querySelectorAll('.dot').forEach(el=>{
                        el.addEventListener('click', ()=>{ current = parseInt(el.getAttribute('data-idx')); update(); });
                    });
                    update();
                })();
            </script>
        @endif
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
            <h1 style="color: var(--primary-color); margin: 0;">{{ $service->name }}</h1>
            <span style="background: var(--primary-color); color: white; padding: 0.5rem 1rem; border-radius: 4px;">
                {{ $service->category->name ?? 'Uncategorized' }}
            </span>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Service Description</h3>
                <p style="color: var(--light-text); line-height: 1.6;">
                    {{ $service->description ?? 'No detailed description available for this service.' }}
                </p>
            </div>
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Service Details</h3>
                <div style="margin-bottom: 1rem;">
                    <strong>Price:</strong>
                    <div style="margin-top: 0.25rem;">
                        @include('components.service-price', ['pricing' => $service->pricing])
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>Category:</strong>
                    <span>{{ $service->category->name ?? 'Uncategorized' }}</span>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <strong>Status:</strong>
                    <span style="color: {{ $service->is_active ? 'green' : 'red' }};">
                        {{ $service->is_active ? 'Available' : 'Currently Unavailable' }}
                    </span>
                </div>
                @if($service->is_active)
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <form method="POST" action="{{ route('cart.add') }}">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem;">
                                Add to Cart
                            </button>
                        </form>
                        <a href="{{ route('consultations.create') }}?cart_items[]={{ $service->id }}" class="btn btn-accent" style="width: 100%; padding: 0.75rem; font-size: 1rem; text-align: center;">
                            Book Services Now
                        </a>
                    </div>
                @else
                    <button disabled style="width: 100%; padding: 0.75rem; font-size: 1rem; background: #ccc; color: #666; border: none; border-radius: 4px; cursor: not-allowed;">
                        Service Unavailable
                    </button>
                @endif
            </div>
        </div>

        <div style="border-top: 1px solid #e9ecef; padding-top: 1.5rem;">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Related Services</h3>
            @if($service->category && $service->category->services->count() > 1)
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    @foreach($service->category->services->where('id', '!=', $service->id)->take(3) as $relatedService)
                        <div style="border: 1px solid #e9ecef; padding: 1rem; border-radius: 8px; background: white;">
                            <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">{{ $relatedService->name }}</h4>
                            <p style="color: var(--light-text); font-size: 0.9rem; margin-bottom: 1rem;">{{ Str::limit($relatedService->description ?? 'No description', 100) }}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span>
                                    @include('components.service-price', ['pricing' => $relatedService->pricing, 'layout' => 'compact'])
                                </span>
                                <a href="{{ route('services.show', $relatedService) }}" style="color: var(--primary-color); text-decoration: none; font-size: 0.9rem;">View Details →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: var(--light-text);">No related services available.</p>
            @endif
        </div>
    </div>
</div>
@endsection
