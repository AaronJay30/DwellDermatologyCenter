@extends('layouts.dashboard')

@section('page-title', 'Doctor Dashboard')

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    {{-- Admin Cards Grid --}}
    <div id="admin-containers" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        @forelse($admins as $admin)
            <div class="admin-container card" 
                 data-admin-id="{{ $admin['id'] }}" 
                 style="border: 3px solid #ffd700; cursor: pointer; transition: all 0.3s; position: relative; background: white; padding: 1.5rem; border-radius: 10px;"
                 onclick="loadAdminReports({{ $admin['id'] }})">
                
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <h3 style="color: var(--primary-color); margin: 0; font-weight: bold; font-size: 1.3rem;">
                        {{ $admin['name'] }}
                    </h3>
                    <span style="font-size: 0.9rem; color: #6c757d;">
                        {{ $admin['branch']['name'] ?? 'No Branch' }}
                    </span>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.25rem;">Total Patients</div>
                        <div style="font-size: 1.8rem; font-weight: bold; color: var(--primary-color);">
                            {{ $admin['total_patients'] }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.25rem;">Total Consultations</div>
                        <div style="font-size: 1.8rem; font-weight: bold; color: var(--primary-color);">
                            {{ $admin['total_consultations'] }}
                        </div>
                    </div>
                </div>
                
                <div style="border-top: 1px solid #e9ecef; padding-top: 0.75rem;">
                    <div style="font-size: 0.85rem; color: #6c757d;">Last Updated</div>
                    <div style="font-size: 0.9rem; color: var(--dark-text); font-weight: 500;">
                        {{ $admin['last_activity'] }}
                    </div>
                </div>
                
                <button class="btn btn-accent" 
                        style="margin-top: 1rem; width: 100%; font-weight: bold;"
                        onclick="event.stopPropagation(); loadAdminReports({{ $admin['id'] }})">
                    View Reports
                </button>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                <p style="color: #6c757d; font-size: 1.1rem;">No admins found. Please create branches with admin users.</p>
            </div>
        @endforelse
    </div>

    {{-- Reports Section --}}
    <div id="admin-reports-section" style="display: none; margin-top: 3rem; border-top: 4px solid var(--primary-color); padding-top: 2rem;">
        <div class="card" style="padding: 2rem; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 id="admin-reports-title" style="color: var(--primary-color); margin: 0; font-weight: bold;">Admin Reports</h2>
                <button id="close-reports-btn" class="btn" style="background: #dc3545; color: white;" onclick="closeAdminReports()">
                    Close Reports
                </button>
            </div>

            <div id="report-summary-header" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 5px solid var(--primary-color);">
                <div>
                    <div style="font-size: 0.85rem; color: #6c757d; font-weight: 600; text-transform: uppercase;">Branch Location</div>
                    <div id="summary-branch" style="font-size: 1.1rem; font-weight: bold; color: var(--dark-text);">-</div>
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #6c757d; font-weight: 600; text-transform: uppercase;">Report Generated</div>
                    <div id="summary-generated" style="font-size: 1.1rem; font-weight: bold; color: var(--dark-text);">-</div>
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #6c757d; font-weight: 600; text-transform: uppercase;">Total Records Found</div>
                    <div id="summary-total" style="font-size: 1.1rem; font-weight: bold; color: var(--primary-color);">0</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1.5rem; margin-bottom: 2rem; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Search</label>
                    <input type="text" id="search-input" class="form-control" placeholder="Search name or ID..." onkeyup="debounceSearch()">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Status</label>
                    <select id="status-filter" class="form-control" onchange="applyFilters()">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>

            <div id="reports-table-container" style="min-height: 200px;">
                </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentAdminId = null;
let searchTimeout = null;

function loadAdminReports(adminId) {
    currentAdminId = adminId;
    const section = document.getElementById('admin-reports-section');
    section.style.display = 'block';

    document.getElementById('reports-table-container').innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; padding: 3rem;">
            <div class="spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid var(--primary-color); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite;"></div>
            <p style="margin-top: 1rem; color: #6c757d;">Fetching records, please wait...</p>
        </div>
    `;

    section.scrollIntoView({ behavior: 'smooth', block: 'start' });

    const adminName = document.querySelector(`[data-admin-id="${adminId}"] h3`).textContent;
    document.getElementById('admin-reports-title').textContent = `${adminName.trim()} - Detailed Reports`;

    applyFilters();
}

function closeAdminReports() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    setTimeout(() => {
        document.getElementById('admin-reports-section').style.display = 'none';
        currentAdminId = null;
    }, 500);
}

function applyFilters() {
    if (!currentAdminId) return;

    // Update Summary Header Data
    const adminCard = document.querySelector(`[data-admin-id="${currentAdminId}"]`);
    const branchName = adminCard ? adminCard.querySelector('span').textContent.trim() : 'N/A';
    document.getElementById('summary-branch').textContent = branchName;

    const params = new URLSearchParams({
        search: document.getElementById('search-input').value,
        status: document.getElementById('status-filter').value,
        ajax: '1'
    });

    fetch(`/doctor/admin/${currentAdminId}/reports?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            // Update Stats
            document.getElementById('summary-total').textContent = data.total_records || '0';
            const now = new Date();
            document.getElementById('summary-generated').textContent = now.toLocaleString('en-US', { 
                month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true 
            });

            const paginationHtml = data.pagination 
                ? `<div class="custom-pagination-container">${data.pagination}</div>` 
                : '';
            document.getElementById('reports-table-container').innerHTML = data.html + paginationHtml;
            attachPaginationHandlers();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('reports-table-container').innerHTML = '<p style="color: red; text-align: center;">Error loading reports.</p>';
        });
}

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 500);
}

function attachPaginationHandlers() {
    document.querySelectorAll('.custom-pagination-container a').forEach(link => {
        link.onclick = (e) => {
            e.preventDefault();
            const url = new URL(link.href);
            const page = url.searchParams.get('page');
            if (page) loadPage(page);
        };
    });
}

function loadPage(page) {
    if (!currentAdminId) return;
    document.getElementById('reports-table-container').style.opacity = '0.5';

    const params = new URLSearchParams({
        search: document.getElementById('search-input').value,
        status: document.getElementById('status-filter').value,
        page: page, 
        ajax: '1'
    });

    fetch(`/doctor/admin/${currentAdminId}/reports?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            const paginationHtml = data.pagination 
                ? `<div class="custom-pagination-container">${data.pagination}</div>` 
                : '';
            const container = document.getElementById('reports-table-container');
            container.innerHTML = data.html + paginationHtml;
            container.style.opacity = '1';
            attachPaginationHandlers();
            document.getElementById('admin-reports-title').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => console.error('Error:', error));
}
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<style>
 /* Container ng Pagination */
.custom-pagination-container {
    margin-top: 2.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: center;
}

/* Siguraduhin na ang nav elements ay naka-hilera (Horizontal) */
.custom-pagination-container nav,
.custom-pagination-container ul {
    display: flex !important;
    flex-direction: row !important;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 8px;
    align-items: center;
}

/* Base style para sa bawat numero/arrow button */
.custom-pagination-container a, 
.custom-pagination-container span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 10px;
    text-decoration: none;
    color: #555;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

/* Hover State */
.custom-pagination-container a:hover {
    background-color: #1a7a8c;
    color: #fff !important;
    border-color: #1a7a8c;
    transform: translateY(-2px);
}

/* Active Page */
.custom-pagination-container .active span,
.custom-pagination-container span[aria-current="page"] {
    background-color: #1a7a8c !important;
    color: #ffffff !important;
    border-color: #1a7a8c !important;
    cursor: default;
    box-shadow: 0 4px 10px rgba(26, 122, 140, 0.3);
}

/* Itago ang mga extra labels */
.custom-pagination-container .flex.justify-between.flex-1.sm\:hidden, 
.custom-pagination-container .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between > div:first-child {
    display: none !important;
}

/* Arrow icons adjustment */
.custom-pagination-container svg {
    width: 20px;
    height: 20px;
}

.admin-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

#reports-table-container {
    margin-top: 1rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Large Screens (1200px and down) */
@media (max-width: 1200px) {
    #admin-containers {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1.5rem !important;
    }
}

/* Tablets (992px and down) */
@media (max-width: 992px) {
    #admin-containers {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1.25rem !important;
    }
    
    /* Report Summary Header - 2 columns */
    #report-summary-header {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
    }
    
    /* Search and Status Filters */
    #admin-reports-section > .card > div:nth-of-type(3) {
        grid-template-columns: 1fr 1fr !important;
        gap: 1rem !important;
    }
    
    #reports-table-container table {
        min-width: 800px;
    }
}

/* Small Tablets and Large Phones (768px and down) */
@media (max-width: 768px) {
    .container {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    #admin-containers {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    .admin-container {
        padding: 1rem !important;
    }
    
    .admin-container h3 {
        font-size: 1.1rem !important;
    }
    
    .admin-container span {
        font-size: 0.85rem !important;
    }
    
    .admin-container > div:first-child {
        flex-direction: column !important;
        gap: 0.5rem !important;
        align-items: flex-start !important;
    }
    
    .admin-container > div:nth-child(2) {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
    }
    
    .admin-container .btn {
        font-size: 0.9rem !important;
        padding: 0.6rem 1rem !important;
        min-height: 44px !important;
    }
    
    .card {
        padding: 1rem !important;
    }
    
    /* Reports Section */
    #admin-reports-section {
        padding-top: 1.5rem !important;
    }
    
    /* Reports header */
    #admin-reports-section .card > div:first-child {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 1rem !important;
    }
    
    #admin-reports-title {
        font-size: 1.3rem !important;
    }
    
    #close-reports-btn {
        width: 100% !important;
        min-height: 44px !important;
    }
    
    /* Report Summary Header - Single Column */
    #report-summary-header {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
        padding: 1rem !important;
    }
    
    #report-summary-header > div > div:first-child {
        font-size: 0.75rem !important;
    }
    
    #report-summary-header > div > div:last-child {
        font-size: 1rem !important;
    }
    
    /* Search and Status Filters - Single Column */
    #admin-reports-section > .card > div:nth-of-type(3) {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
    }
    
    #admin-reports-section > .card > div:nth-of-type(3) > div {
        grid-column: 1 !important;
    }
    
    #admin-reports-section > .card > div:nth-of-type(3) label {
        font-size: 0.9rem !important;
    }
    
    .form-control {
        font-size: 0.9rem !important;
        width: 100% !important;
        min-height: 44px !important;
        font-size: 16px !important; /* Prevents zoom on iOS */
    }
    
    /* Table container */
    #reports-table-container {
        margin-left: -1rem !important;
        margin-right: -1rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    /* Make reports table responsive with card layout */
    #reports-table-container table {
        min-width: 100%;
        width: 100%;
        display: block;
    }
    
    #reports-table-container table thead {
        display: none;
    }
    
    #reports-table-container table tbody {
        display: block;
        width: 100%;
    }
    
    #reports-table-container table tbody tr {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
        padding: 1rem;
        background: rgba(255, 250, 240, 0.75);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 2px 6px rgba(255, 215, 0, 0.15);
        box-sizing: border-box;
    }
    
    #reports-table-container table tbody tr:last-child {
        margin-bottom: 0;
    }
    
    #reports-table-container table tbody td{
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        text-align: left;
        min-height: auto;
        position: relative;
        width: 100%;
        box-sizing: border-box;
    }
    
    #reports-table-container table tbody td:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    #reports-table-container table tbody td[colspan] {
        display: block;
        text-align: center;
        padding: 2rem 1rem !important;
        border-bottom: none;
    }
    
    #reports-table-container table tbody td[colspan]::before {
        display: none;
    }
    
    /* Add labels before each cell - ADJUST THESE BASED ON YOUR ACTUAL TABLE COLUMNS */
    #reports-table-container table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #2c3e50;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        flex-shrink: 0;
        text-align: left;
    }
    
    /* Default labels - Update these to match your table columns */
    #reports-table-container table tbody td:nth-child(1)::before {
        content: "Patient Name";
    }
    
    #reports-table-container table tbody td:nth-child(2)::before {
        content: "Report ID";
    }
    
    #reports-table-container table tbody td:nth-child(3)::before {
        content: "Date";
    }
    
    #reports-table-container table tbody td:nth-child(4)::before {
        content: "Status";
    }
    
    #reports-table-container table tbody td:nth-child(5)::before {
        content: "Actions";
    }
    
    #reports-table-container table tbody td:first-child {
        padding-top: 0;
    }
    
    /* Make action buttons stack vertically */
    #reports-table-container .action-buttons,
    #reports-table-container td > div,
    #reports-table-container td > form,
    #reports-table-container td > a {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    #reports-table-container .btn,
    #reports-table-container button,
    #reports-table-container a.btn {
        width: 100% !important;
        justify-content: center !important;
        min-height: 44px !important;
    }
    
    /* Pagination adjustments */
    .custom-pagination-container {
        gap: 6px !important;
        padding-top: 1rem !important;
        margin-top: 1.5rem !important;
        margin-left: -1rem !important;
        margin-right: -1rem !important;
    }
    
    .custom-pagination-container a,
    .custom-pagination-container span {
        min-width: 38px !important;
        height: 38px !important;
        padding: 0 8px !important;
        font-size: 0.85rem !important;
    }
    
    .custom-pagination-container svg {
        width: 18px !important;
        height: 18px !important;
    }
}

/* Mobile Phones (480px and down) */
@media (max-width: 480px) {
    .container {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    #admin-containers {
        gap: 0.75rem !important;
    }
    
    .admin-container {
        padding: 0.75rem !important;
    }
    
    .admin-container h3 {
        font-size: 1rem !important;
    }
    
    .admin-container > div:first-child span {
        align-self: flex-start !important;
    }
    
    .admin-container .btn {
        font-size: 0.9rem !important;
        padding: 0.6rem 1rem !important;
    }
    
    .card {
        padding: 0.75rem !important;
    }
    
    #admin-reports-title {
        font-size: 1.1rem !important;
    }
    
    /* Report Summary Header */
    #report-summary-header {
        padding: 0.75rem !important;
        gap: 0.5rem !important;
    }
    
    #report-summary-header > div > div:first-child {
        font-size: 0.7rem !important;
    }
    
    #report-summary-header > div > div:last-child {
        font-size: 0.95rem !important;
    }
    
    /* Search and Filters */
    #admin-reports-section > .card > div:nth-of-type(3) {
        gap: 0.5rem !important;
    }
    
    #admin-reports-section > .card > div:nth-of-type(3) label {
        font-size: 0.85rem !important;
        margin-bottom: 0.3rem !important;
    }
    
    .form-control {
        font-size: 0.85rem !important;
        padding: 0.5rem !important;
    }
    
    .btn {
        font-size: 0.85rem !important;
        padding: 0.6rem 1rem !important;
    }
    
    /* Reports Table Cards */
    #reports-table-container table tbody tr {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    #reports-table-container table tbody td {
        padding: 0.5rem 0;
        font-size: 0.875rem;
    }
    
    #reports-table-container table tbody td::before {
        font-size: 0.7rem;
        margin-bottom: 0.4rem;
    }
    
    /* Pagination */
    .custom-pagination-container {
        gap: 5px !important;
        padding-top: 0.75rem !important;
        margin-top: 1rem !important;
    }
    
    .custom-pagination-container a,
    .custom-pagination-container span {
        min-width: 36px !important;
        height: 36px !important;
        padding: 0 6px !important;
        font-size: 0.8rem !important;
        border-radius: 6px !important;
    }
    
    .custom-pagination-container svg {
        width: 16px !important;
        height: 16px !important;
    }
}

/* Extra Small Phones (360px and down) */
@media (max-width: 360px) {
    .admin-container h3 {
        font-size: 0.95rem !important;
    }
    
    #admin-reports-title {
        font-size: 1rem !important;
    }
    
    #admin-reports-section > .card > div:nth-of-type(3) label {
        font-size: 0.8rem !important;
    }
    
    .form-control,
    .btn {
        font-size: 0.8rem !important;
    }
    
    #reports-table-container table tbody tr {
        padding: 0.5rem;
    }
    
    #reports-table-container table tbody td {
        font-size: 0.8rem;
    }
    
    .custom-pagination-container a,
    .custom-pagination-container span {
        min-width: 32px !important;
        height: 32px !important;
        padding: 0 5px !important;
        font-size: 0.75rem !important;
    }
    
    .custom-pagination-container svg {
        width: 14px !important;
        height: 14px !important;
    }
}
.profile-icon, .admin-container img {
    width: 45px !important;    /* Fixed size */
    height: 45px !important;   /* Fixed size */
    min-width: 45px !important;
    min-height: 45px !important;
    border-radius: 50% !important;
    object-fit: cover;
    flex-shrink: 0 !important; /* This stops the "stretch" or "squish" */
    display: flex !important;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
/* Touch-friendly improvements */
@media (max-width: 768px) {
#reports-table-container table tbody td:first-child,
    .table tbody td:first-child {
        flex-direction: row !important; /* Keep icon and name side-by-side */
        align-items: center !important;
        gap: 12px;
    }

    /* Prevent the label "Name" from appearing above the icon */
    #reports-table-container table tbody td:first-child::before,
    .table tbody td:first-child::before {
        display: none !important; 
    }    
.admin-container {
        cursor: pointer;
        -webkit-tap-highlight-color: rgba(255, 215, 0, 0.2);
    }
    
    .btn,
    button,
    a.btn {
        min-height: 44px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    input[type="text"],
    input[type="search"],
    input[type="date"],
    select.form-control,
    select {
        min-height: 44px !important;
        font-size: 16px !important; /* Prevents zoom on iOS */
    }
}

/* Landscape orientation adjustments */
@media (max-width: 992px) and (orientation: landscape) {
    #admin-containers {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    .admin-container {
        padding: 1rem !important;
    }
    
    #report-summary-header {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

/* Print styles */
@media print {
    .admin-container .btn,
    #close-reports-btn,
    .custom-pagination-container {
        display: none !important;
    }
    
    #admin-containers {
        grid-template-columns: 1fr !important;
    }
    
    .card {
        page-break-inside: avoid;
    }
    
    #reports-table-container table {
        display: table !important;
    }
    
    #reports-table-container table thead {
        display: table-header-group !important;
    }
    
    #reports-table-container table tbody {
        display: table-row-group !important;
    }
    
    #reports-table-container table tbody tr {
        display: table-row !important;
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        background: white !important;
    }
    
    #reports-table-container table tbody td {
        display: table-cell !important;
        border: 1px solid #ddd !important;
    }
    
    #reports-table-container table tbody td::before {
        display: none !important;
    }
}
</style>
@endpush
@endsection