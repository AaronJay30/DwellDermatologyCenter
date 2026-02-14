@extends('layouts.patient')

@section('content')
@php
    // Ensure activePromos is always set, even if empty
    $activePromos = $activePromos ?? collect();
    $totalSlides = $activePromos->sum(function($p) { return $p->images->count(); });
@endphp


<!-- Hero Section with Slideshow -->
<section class="hero-section">
    <div class="hero-content">
        <div class="hero-image-container">
            @if($activePromos->count() > 0 && $totalSlides > 0)
                <!-- Promotion Slideshow -->
                <div class="promo-carousel" style="position: relative; border-radius: 15px; overflow: hidden; height: 300px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div class="carousel-wrapper" id="promoCarousel" style="display: flex; height: 100%; transition: transform 0.5s ease; will-change: transform;">
                        @php $slideIndex = 0; @endphp
                        @foreach($activePromos as $promo)
                            @foreach($promo->images as $image)
                                <div class="carousel-slide" data-slide-index="{{ $slideIndex }}" style="min-width: 100%; height: 100%; flex-shrink: 0;">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $promo->display_title }}" 
                                         style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                </div>
                                @php $slideIndex++; @endphp
                            @endforeach
                        @endforeach
                    </div>
                    <!-- Carousel Controls -->
                    @if($totalSlides > 1)
                        <button class="carousel-btn prev" onclick="changePromoSlide(-1)" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; font-size: 1.2rem; color: #197a8c; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: all 0.3s; z-index: 10;">‹</button>
                        <button class="carousel-btn next" onclick="changePromoSlide(1)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; font-size: 1.2rem; color: #197a8c; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: all 0.3s; z-index: 10;">›</button>
                        <!-- Indicators -->
                        <div class="carousel-indicators" style="position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10;">
                            @for($i = 0; $i < $totalSlides; $i++)
                                <span class="indicator {{ $i === 0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})" 
                                      style="width: 10px; height: 10px; border-radius: 50%; background: {{ $i === 0 ? '#fff' : 'rgba(255,255,255,0.5)' }}; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"></span>
                            @endfor
                        </div>
                    @endif
                </div>
            @else
                <!-- Default Image -->
                <img src="{{ asset('banner.svg') }}" alt="Dwell Dermatology Banner" class="hero-image">
            @endif
        </div>
        <div class="hero-text">
            <h1>Glow Naturally with Dwell Derma</h1>
            <p>We combine advanced dermatology with personalized care to help your skin look and feel its best</p>
            <div class="hero-buttons">
                <a href="{{ route('consultations.medical') }}" class="btn btn-primary">Book Consultation</a>
                <a href="{{ route('about') }}" class="btn btn-outline">About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Shop by Category Section -->
<section class="shop-categories">
    <div class="container">
        <h2 class="section-title">Choose Branch</h2>
        <div class="categories-grid">
            @forelse($branches as $branch)
                <div class="category-item" onclick="filterByBranch('{{ $branch->id }}', this)">
                    <div class="category-circle" id="branch-{{ $branch->id }}">
                        @if($branch->image_path)
                            <img src="{{ asset('storage/' . $branch->image_path) }}" alt="{{ $branch->name }}" class="category-image">
                        @else
                            <i class="fas fa-building" style="font-size: 40px; color: var(--primary-color);"></i>
                        @endif
                    </div>
                    <div class="category-name">{{ $branch->name }}</div>
                </div>
            @empty
                <div class="category-item">
                    <div class="category-circle">
                        <i class="fas fa-building" style="font-size: 40px; color: var(--primary-color);"></i>
                    </div>
                    <div class="category-name">Main Branch</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <h2 class="services-title">Choose Your Categories</h2>
        
        <!-- Category Tabs -->
        <div class="category-tabs" id="category-tabs">
            <button class="tab-btn active" onclick="loadAllServices(this)">All Categories</button>
            <!-- Categories will be loaded dynamically here -->
        </div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" class="loading-indicator" style="display: none;">
            <div class="spinner"></div>
            <p>Loading services...</p>
        </div>

        <!-- Services Grid -->
        <div class="services-grid" id="services-grid">
            @forelse($services as $service)
                <div class="service-card" data-category="{{ $service->category_id }}" data-branch="{{ $service->category->branch_id }}">
                    @if($service->images->count() > 0)
                        <img src="{{ asset('storage/' . $service->images->first()->image_path) }}" alt="{{ $service->name }}" class="service-image">
                    @else
                        <div class="service-image" style="background: linear-gradient(135deg, var(--teal-light), var(--teal-medium)); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-spa" style="font-size: 60px; color: var(--primary-color);"></i>
                        </div>
                    @endif
                    
                    <div class="service-content">
                        <h3 class="service-name">{{ $service->name }}</h3>
                        <p class="service-description">{{ Str::limit($service->description, 80) }}</p>
                        
                        <div class="service-price">
                            @include('components.service-price', ['pricing' => $service->pricing, 'layout' => 'compact'])
                        </div>
                        
                        <div class="service-actions">
                            <a href="{{ route('services.show', $service->id) }}" class="btn btn-primary">View Details</a>
                            @if($service->is_active)
                                <form method="POST" action="{{ route('cart.add') }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                    <button type="submit" class="btn btn-secondary">Add to Cart</button>
                                </form>
                            @else
                                <button disabled class="btn btn-secondary" style="background: #ccc; color: #666; cursor: not-allowed;">Unavailable</button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-services">
                    <p>No services available at the moment.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($services->hasPages())
        <div class="pagination-wrapper" id="pagination-wrapper">
            {{ $services->links() }}
        </div>
        @endif
    </div>
</section>

<script>
let selectedBranchId = null;
let selectedCategoryId = null;

function formatCurrency(value) {
    const numeric = parseFloat(value ?? 0);
    return numeric.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function buildPriceMarkup(pricing) {
    const hasPromo = pricing && pricing.has_promo;
    const original = pricing && typeof pricing.original_price !== 'undefined'
        ? pricing.original_price
        : null;
    const promo = pricing && typeof pricing.promo_price !== 'undefined'
        ? pricing.promo_price
        : null;
    const discount = pricing && typeof pricing.discount_percent !== 'undefined'
        ? pricing.discount_percent
        : null;
    const fallback = pricing && typeof pricing.display_price !== 'undefined'
        ? pricing.display_price
        : original;

    if (hasPromo && promo !== null) {
        return `
            <div class="price-display price-compact">
                <span class="price-original">₱${formatCurrency(original)}</span>
                <span class="price-promo">₱${formatCurrency(promo)}</span>
                ${discount ? `<span class="price-discount">-${Math.round(discount)}%</span>` : ''}
            </div>
        `;
    }

    return `
        <div class="price-display price-compact">
            <span class="price-regular">₱${formatCurrency(original ?? fallback ?? 0)}</span>
        </div>
    `;
}

// Enhanced branch filtering with AJAX
function filterByBranch(branchId, element) {
    // Remove highlight from all branch circles
    document.querySelectorAll('.category-circle').forEach(circle => {
        circle.style.border = 'none';
        circle.style.boxShadow = 'none';
    });
    
    // Highlight selected branch circle
    const selectedCircle = document.getElementById('branch-' + branchId);
    if (selectedCircle) {
        selectedCircle.style.border = '3px solid var(--primary-color)';
        selectedCircle.style.boxShadow = '0 0 15px rgba(0, 123, 255, 0.5)';
    }
    
    // Store selected branch ID
    selectedBranchId = branchId;
    
    // Show loading indicator
    showLoading();
    
    // Load categories for this branch
    loadCategoriesForBranch(branchId);
    
    // Load all services for this branch
    loadServicesForBranch(branchId);
}

// Load categories for a specific branch
async function loadCategoriesForBranch(branchId) {
    try {
        const response = await fetch(`/api/branches/${branchId}/categories`);
        const data = await response.json();
        
        if (data.success) {
            updateCategoryTabs(data.categories);
        } else {
            showError('Failed to load categories');
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        showError('Error loading categories');
    }
}

// Update category tabs
function updateCategoryTabs(categories) {
    const categoryTabs = document.getElementById('category-tabs');
    
    // Clear existing category tabs (keep "All Categories" button)
    const allCategoriesBtn = categoryTabs.querySelector('.tab-btn');
    categoryTabs.innerHTML = '';
    categoryTabs.appendChild(allCategoriesBtn);
    
    // Add category tabs
    if (categories && categories.length > 0) {
        categories.forEach(category => {
            const tabBtn = document.createElement('button');
            tabBtn.className = 'tab-btn';
            tabBtn.textContent = category.name;
            tabBtn.onclick = (e) => loadServicesForCategory(category.id, e.currentTarget);
            categoryTabs.appendChild(tabBtn);
        });
    } else {
        // Show message if no categories available
        const noCategoriesMsg = document.createElement('div');
        noCategoriesMsg.className = 'no-categories';
        noCategoriesMsg.innerHTML = '<p style="color: #666; font-style: italic;">No categories available for this branch</p>';
        categoryTabs.appendChild(noCategoriesMsg);
    }
}

// Load services for a specific category
async function loadServicesForCategory(categoryId, clickedEl) {
    selectedCategoryId = categoryId;
    showLoading();
    
    try {
        const response = await fetch(`/api/categories/${categoryId}/services`);
        const data = await response.json();
        
        if (data.success) {
            updateServicesGrid(data.services);
            updateActiveTab(clickedEl);
        } else {
            showError('Failed to load services');
        }
    } catch (error) {
        console.error('Error loading services:', error);
        showError('Error loading services');
    }
    
    hideLoading();
}

// Load all services for a branch
async function loadServicesForBranch(branchId) {
    try {
        const response = await fetch(`/api/branches/${branchId}/services`);
        const data = await response.json();
        
        if (data.success) {
            updateServicesGrid(data.services);
            updateActiveTab(document.querySelector('.tab-btn'));
        } else {
            showError('Failed to load services');
        }
    } catch (error) {
        console.error('Error loading services:', error);
        showError('Error loading services');
    }
    
    hideLoading();
}

// Load all services (reset view)
async function loadAllServices(clickedEl) {
    selectedCategoryId = null;
    showLoading();
    
    try {
        // If no branch is selected, reload the page to show server-side pagination
        if (!selectedBranchId) {
            window.location.href = '{{ route('dashboard') }}';
            return;
        }
        
        let url = '/api/branches/0/services'; // Load all services
        if (selectedBranchId) {
            url = '/api/branches/' + selectedBranchId + '/services';
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            updateServicesGrid(data.services);
            updateActiveTab(clickedEl);
        } else {
            showError('Failed to load services');
        }
    } catch (error) {
        console.error('Error loading services:', error);
        showError('Error loading services');
    }
    
    hideLoading();
}

// Update services grid with new data
function updateServicesGrid(services) {
    const servicesGrid = document.getElementById('services-grid');
    const paginationWrapper = document.getElementById('pagination-wrapper');
    
    // Hide pagination when filtering
    if (paginationWrapper) {
        paginationWrapper.style.display = 'none';
    }
    
    if (services.length === 0) {
        servicesGrid.innerHTML = '<div class="no-services"><p>No services available for this selection.</p></div>';
        return;
    }
    
    let html = '';
    services.forEach(service => {
        const imageUrl = service.images && service.images.length > 0 
            ? `/storage/${service.images[0].image_path}` 
            : null;
        
        html += `
            <div class="service-card" data-category="${service.category_id}" data-branch="${service.category.branch_id}">
                ${imageUrl ? 
                    `<img src="${imageUrl}" alt="${service.name}" class="service-image">` :
                    `<div class="service-image" style="background: linear-gradient(135deg, var(--teal-light), var(--teal-medium)); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-spa" style="font-size: 60px; color: var(--primary-color);"></i>
                    </div>`
                }
                
                <div class="service-content">
                    <h3 class="service-name">${service.name}</h3>
                    <p class="service-description">${service.description ? (service.description.length > 80 ? service.description.substring(0, 80) + '...' : service.description) : ''}</p>
                    
                    <div class="service-price">
                        ${buildPriceMarkup(service.pricing)}
                    </div>
                    
                    <div class="service-actions">
                        <a href="/services/${service.id}" class="btn btn-primary">View Details</a>
                        ${service.is_active ? 
                            `<form method="POST" action="/cart/add" style="display: inline;">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                <input type="hidden" name="service_id" value="${service.id}">
                                <button type="submit" class="btn btn-secondary">Add to Cart</button>
                            </form>` :
                            `<button disabled class="btn btn-secondary" style="background: #ccc; color: #666; cursor: not-allowed;">Unavailable</button>`
                        }
                    </div>
                </div>
            </div>
        `;
    });
    
    servicesGrid.innerHTML = html;

    // Re-bind AJAX cart handlers for newly rendered services
    attachCartAjaxHandler(servicesGrid);
}

// Update active tab
function updateActiveTab(activeElement) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    if (activeElement) {
        activeElement.classList.add('active');
    }
}

// Show loading indicator
function showLoading() {
    document.getElementById('loading-indicator').style.display = 'flex';
}

// Hide loading indicator
function hideLoading() {
    document.getElementById('loading-indicator').style.display = 'none';
}

// Show error message
function showError(message) {
    const servicesGrid = document.getElementById('services-grid');
    servicesGrid.innerHTML = `<div class="no-services"><p style="color: #e74c3c;">${message}</p></div>`;
}

function attachCartAjaxHandler(scope = document) {
    const forms = scope.querySelectorAll('form[action*="/cart/add"]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    forms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const serviceId = form.querySelector('input[name="service_id"]')?.value;
            const quantity = form.querySelector('input[name="quantity"]')?.value || 1;

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Adding...';
            }

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ service_id: serviceId, quantity })
                });

                if (!res.ok) throw new Error('Failed to add to cart');
                const data = await res.json();

                if (typeof updateCartBadge === 'function' && data.count !== undefined) {
                    updateCartBadge(data.count);
                }

                if (submitBtn) {
                    submitBtn.textContent = 'Add to Cart';
                    submitBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                if (submitBtn) {
                    submitBtn.textContent = 'Try Again';
                    submitBtn.disabled = false;
                }
                alert('Unable to add to cart right now. Please try again.');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    attachCartAjaxHandler(document);
});

// Legacy function for backward compatibility
function filterServices(categoryId) {
    if (categoryId === 'all') {
        loadAllServices();
    } else {
        loadServicesForCategory(categoryId);
    }
}

// Initialize - show all categories and services on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show all category tabs initially
    document.querySelectorAll('.tab-btn[data-branch]').forEach(tab => {
        tab.style.display = 'inline-block';
    });
    
    // Initialize promo carousel if it exists
    @if($activePromos->count() > 0 && $totalSlides > 0)
    setTimeout(function() {
        initPromoCarousel();
    }, 100);
    @endif
});

@if($activePromos->count() > 0 && $totalSlides > 0)
// Promo Carousel Functionality
@php
    $promoDataArray = $activePromos->map(function($p) {
        return [
            'title' => $p->display_title,
            'description' => $p->description ?? 'Special promotion available now!',
            'starts_at' => $p->starts_at ? $p->starts_at->format('M d, Y') : null,
            'ends_at' => $p->ends_at ? $p->ends_at->format('M d, Y') : null,
            'promo_code' => $p->promo_code,
            'image_count' => $p->images->count(),
            'services' => $p->promoServices->map(function($ps) {
                return [
                    'name' => $ps->service->name,
                    'original_price' => $ps->original_price,
                    'promo_price' => $ps->promo_price,
                    'discount_percent' => $ps->discount_percent,
                ];
            })->toArray()
        ];
    })->toArray();
@endphp
let currentPromoSlide = 0;
let promoCarouselInterval = null;
let promoSlides = null;
let totalPromoSlides = 0;
let promoCarousel = null;
let promoIndicators = null;
const promoData = @json($promoDataArray);

function initPromoCarousel() {
    promoSlides = document.querySelectorAll('.carousel-slide');
    totalPromoSlides = promoSlides.length;
    promoCarousel = document.getElementById('promoCarousel');
    promoIndicators = document.querySelectorAll('.carousel-indicators .indicator');
    
    if (!promoCarousel || totalPromoSlides === 0) {
        console.log('Promo carousel not found or no slides');
        return;
    }
    
    // Set initial position
    updatePromoCarousel();
    
    // Clear any existing interval
    if (promoCarouselInterval) {
        clearInterval(promoCarouselInterval);
    }
    
    // Auto-rotate every 5 seconds
    promoCarouselInterval = setInterval(() => {
        changePromoSlide(1);
    }, 5000);
}

function changePromoSlide(direction) {
    if (totalPromoSlides === 0) return;
    
    currentPromoSlide += direction;
    
    if (currentPromoSlide >= totalPromoSlides) {
        currentPromoSlide = 0;
    } else if (currentPromoSlide < 0) {
        currentPromoSlide = totalPromoSlides - 1;
    }
    
    updatePromoCarousel();
}

function goToSlide(index) {
    if (totalPromoSlides === 0 || index < 0 || index >= totalPromoSlides) return;
    
    currentPromoSlide = index;
    updatePromoCarousel();
    
    // Reset auto-rotate timer
    if (promoCarouselInterval) {
        clearInterval(promoCarouselInterval);
        promoCarouselInterval = setInterval(() => {
            changePromoSlide(1);
        }, 5000);
    }
}

function updatePromoCarousel() {
    if (promoCarousel && totalPromoSlides > 0) {
        promoCarousel.style.transform = `translateX(-${currentPromoSlide * 100}%)`;
    }
    
    // Update indicators
    if (promoIndicators && promoIndicators.length > 0) {
        promoIndicators.forEach((indicator, index) => {
            if (index === currentPromoSlide) {
                indicator.classList.add('active');
                indicator.style.background = '#fff';
                indicator.style.width = '12px';
                indicator.style.height = '12px';
            } else {
                indicator.classList.remove('active');
                indicator.style.background = 'rgba(255,255,255,0.5)';
                indicator.style.width = '12px';
                indicator.style.height = '12px';
            }
        });
    }
    
    // Update promo info (find which promo this slide belongs to)
    let imageIndex = 0;
    let promoIndex = 0;
    for (let i = 0; i < promoData.length; i++) {
        const imageCount = promoData[i].image_count;
        if (currentPromoSlide >= imageIndex && currentPromoSlide < imageIndex + imageCount) {
            promoIndex = i;
            break;
        }
        imageIndex += imageCount;
    }
    
    updatePromoInfo(promoIndex);
}

function updatePromoInfo(promoIndex) {
    const promo = promoData[promoIndex];
    const promoInfoDiv = document.querySelector('.promo-info');
    
    if (promo && promoInfoDiv) {
        const servicesHtml = promo.services && promo.services.length > 0 ? `
            <div class="promo-services" style="margin-top: 1.5rem;">
                <h3 style="color: #197a8c; margin-bottom: 1rem; font-size: 1.3rem; font-weight: 600;">Services on Promo:</h3>
                ${promo.services.slice(0, 3).map(service => `
                    <div style="background: rgba(255,255,255,0.9); padding: 1rem; border-radius: 10px; margin-bottom: 0.75rem; border-left: 4px solid #197a8c; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="color: #333; font-size: 1rem;">${service.name}</strong>
                            </div>
                            <div style="text-align: right;">
                                <div style="color: #999; text-decoration: line-through; font-size: 0.85rem;">
                                    ₱${parseFloat(service.original_price).toFixed(2)}
                                </div>
                                <div style="color: #e74c3c; font-weight: bold; font-size: 1.1rem;">
                                    ₱${parseFloat(service.promo_price).toFixed(2)}
                                </div>
                                <div style="color: #27ae60; font-size: 0.8rem; font-weight: 600;">
                                    ${parseFloat(service.discount_percent).toFixed(0)}% OFF
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        ` : '';
        
        const datesHtml = (promo.starts_at || promo.ends_at) ? `
            <div style="display: flex; gap: 1.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                ${promo.starts_at ? `<span style="color: #666; font-size: 0.95rem;"><strong>Valid from:</strong> ${promo.starts_at}</span>` : ''}
                ${promo.ends_at ? `<span style="color: #666; font-size: 0.95rem;"><strong>Until:</strong> ${promo.ends_at}</span>` : ''}
            </div>
        ` : '';
        
        promoInfoDiv.innerHTML = `
            <div class="promo-content" style="background: rgba(255,255,255,0.7); padding: 2rem; border-radius: 15px; backdrop-filter: blur(10px);">
                <h2 style="color: #197a8c; font-size: 2.5rem; margin-bottom: 1rem; font-weight: 700;">${promo.title}</h2>
                <p style="color: #555; margin-bottom: 1.5rem; line-height: 1.8; font-size: 1.1rem;">${promo.description}</p>
                
                <div style="margin-bottom: 1.5rem;">
                    ${datesHtml}
                    ${promo.promo_code ? `
                        <div style="background: #f0f8fa; padding: 0.75rem 1.25rem; border-radius: 10px; display: inline-block; margin-top: 0.5rem; border: 2px solid #197a8c;">
                            <strong style="color: #197a8c;">Promo Code:</strong> <code style="background: white; padding: 0.4rem 0.8rem; border-radius: 6px; font-weight: bold; color: #197a8c; font-size: 1.1rem; margin-left: 0.5rem;">${promo.promo_code}</code>
                        </div>
                    ` : ''}
                </div>

                ${servicesHtml}
                
                <div style="margin-top: 2rem;">
                    <a href="/services" class="btn-promo" style="display: inline-block; background: #197a8c; color: white; padding: 1rem 2.5rem; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.3s; box-shadow: 0 4px 15px rgba(25, 122, 140, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(25, 122, 140, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(25, 122, 140, 0.3)'">Book Now</a>
                </div>
            </div>
        `;
    }
}
@endif
</script>

<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
<style>
@media (max-width: 600px) {
    .promo-carousel img {
        object-fit: contain !important;
        max-height: 220px;
        width: 100%;
        height: auto !important;
        background: #fff;
    }
    .promo-carousel {
        height: auto !important;
        min-height: 180px;
        max-height: 240px;
    }
    .carousel-slide {
        height: auto !important;
        min-height: 180px;
        max-height: 240px;
    }
}
/* Enhanced Service Card Styles */
.service-card {
    background: var(--card-bg, white);
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.service-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 15px 15px 0 0;
}

.service-content {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.service-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 10px;
    line-height: 1.3;
}

.service-description {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 15px;
    flex-grow: 1;
}

@media (max-width: 768px) {
    .service-description {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

.service-price {
    margin-bottom: 15px;
}

.price-single {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.service-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
}

.service-actions .btn {
    flex: 1;
    padding: 10px 15px;
    text-align: center;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-secondary {
    background: #f8f9fa;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-secondary:hover {
    background: var(--primary-color);
    color: white;
}

/* Loading Indicator Styles */
.loading-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-indicator p {
    color: #666;
    font-size: 1rem;
    margin: 0;
}

/* Enhanced Category Tabs */
.category-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
    justify-content: center;
}

.tab-btn {
    background: var(--card-bg, white);
    border: 2px solid #e9ecef;
    color: #666;
    padding: 12px 24px;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 0.9rem;
}

.tab-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-2px);
}

.tab-btn.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

/* Services Grid Enhancement */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

/* No Services Message */
.no-services {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-services p {
    font-size: 1.1rem;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .promo-container {
        grid-template-columns: 1fr !important;
        padding: 2rem 1.5rem !important;
    }
    
    .promo-carousel {
        height: 300px !important;
        margin-bottom: 2rem;
    }
    
    .promo-info {
        padding: 1.5rem !important;
    }
    
    .promo-info h2 {
        font-size: 1.8rem !important;
    }
    
    .promo-info p {
        font-size: 1rem !important;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .service-card {
        padding: 12px;
    }
    
    .service-image {
        height: 120px;
    }
    
    .service-name {
        font-size: 0.95rem;
        margin-bottom: 6px;
    }
    
    .service-description {
        font-size: 0.75rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 10px;
        color: #666;
    }
    
    .service-price {
        margin-bottom: 10px;
    }
    
    .service-actions {
        flex-direction: row;
        gap: 8px;
    }
    
    .service-actions .btn {
        padding: 6px 12px;
        font-size: 0.8rem;
        flex: 1;
    }
    
    .category-tabs {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        justify-content: flex-start;
        padding-bottom: 10px;
        gap: 10px;
    }
    
    .category-tabs::-webkit-scrollbar {
        height: 4px;
    }
    
    .category-tabs::-webkit-scrollbar-track {
        background: #f1f3f4;
        border-radius: 10px;
    }
    
    .category-tabs::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }
    
    .tab-btn {
        white-space: nowrap;
        flex-shrink: 0;
        padding: 8px 16px;
        font-size: 0.85rem;
    }
}

/* Branch Selection Enhancement */
.category-item {
    cursor: pointer;
    transition: all 0.3s ease;
}

.category-item:hover {
    transform: translateY(-3px);
}

.category-circle {
    transition: all 0.3s ease;
}

/* Error Message Styling */
.no-services p[style*="color: #e74c3c"] {
    color: #e74c3c !important;
    font-weight: 500;
}
</style>
@endsection
