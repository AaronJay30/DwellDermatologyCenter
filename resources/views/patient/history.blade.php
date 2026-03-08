@extends('layouts.patient')
@php
    use Illuminate\Support\Str;
@endphp

@push('styles')
<style>
    .history-hero {
        background: linear-gradient(135deg, #197a8c, #1f9bb5);
        color: #fff;
        padding: 1.75rem 2rem;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .history-hero h2 { margin: 0; font-weight: 700; }
    .history-hero .stat {
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.18);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        text-align: center;
        min-width: 130px;
    }
    .history-hero .stat .label { font-size: 0.9rem; opacity: 0.85; }
    .history-hero .stat .value { font-size: 1.4rem; font-weight: 700; }

    .filters-card {
        background: #ffffff;
        border: 1px solid #e7ecf0;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        margin-bottom: 1.25rem;
    }
    .filters-card label { font-weight: 600; color: #344357; }

    .filters-card select {
        border: 1px solid #e7ecf0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        background: #fff;
        color: #344357;
        transition: all 0.2s ease;
        width: 100%;
    }
    .filters-card select:focus {
        border-color: #197a8c;
        box-shadow: 0 0 0 3px rgba(25, 122, 140, 0.1);
        outline: none;
    }

    .profile-section { margin-top: 1.5rem; }
    .profile-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 700;
        color: #197a8c;
        margin-bottom: 0.75rem;
    }
    .empty-state {
        border: 1px dashed #c7d3de;
        background: #f7fafc;
        border-radius: 14px;
        padding: 1.25rem;
        text-align: center;
        color: #526070;
    }
    .table-history {
        background: #fff;
        border: 1px solid #dfe6ed;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 12px 26px rgba(0,0,0,0.07);
    }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        display: block;
        position: relative;
    }
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #197a8c;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #1a6b7a;
    }
    .table-history table { 
        margin-bottom: 0; 
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 900px;
    }
    .table-history th,
    .table-history td {
        min-width: 100px;
        white-space: nowrap;
    }
    .table-history th:first-child,
    .table-history td:first-child {
        min-width: 140px;
    }
    .table-history th:last-child,
    .table-history td:last-child {
        min-width: 120px;
    }
    .table-history th {
        background: #f5f9fb;
        color: #2f3b4a;
        font-weight: 700;
        border-bottom: 2px solid #dfe6ed;
        text-transform: uppercase;
        font-size: 0.86rem;
        letter-spacing: 0.02em;
        padding: 1.25rem 1.5rem;
        white-space: nowrap;
    }
    .table-history td { 
        vertical-align: middle; 
        border-color: #edf2f7; 
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f0f4f8;
    }
    .table-history tbody tr {
        transition: all 0.2s ease;
    }
    .table-history tbody tr:nth-child(odd) { background: #f9fbfc; }
    .table-history tbody tr:nth-child(even) { background: #ffffff; }
    .table-history tbody tr:hover { 
        background: #e8f5e9; 
        box-shadow: inset 4px 0 0 #27ae60;
        transform: translateX(2px);
    }
    .table-history tbody tr:last-child td {
        border-bottom: none;
    }
    .table-history tbody tr td:first-child { 
        border-left: 4px solid transparent; 
        padding-left: 1.75rem;
    }
    .table-history tbody tr:hover td:first-child { 
        border-left-color: #27ae60; 
    }
    .table-history tbody tr td:last-child {
        padding-right: 1.75rem;
    }
    .badge-pill { 
        border-radius: 999px; 
        padding: 0.5rem 0.85rem; 
        font-weight: 600; 
        font-size: 0.85rem;
        display: inline-block;
        white-space: nowrap;
    }
    .badge-branch { 
        background: #eef2f6; 
        color: #465160; 
        border: 1px solid #d1d9e0;
    }
    .badge-service { 
        background: #eaf6ff; 
        color: #0f5c8c; 
        border: 1px solid #b8d9f0;
    }
    .badge-followup { 
        background: #fff4e5; 
        color: #b77007; 
        border: 1px solid #f5d896;
    }
    .badge-result { 
        background: #f0f7ff; 
        color: #175e91; 
        border: 1px solid #a8cfe8;
    }
    .badge-prescription { 
        background: #ecf9f1; 
        color: #2a7a4b; 
        border: 1px solid #b8e0c8;
    }
    .badge-light {
        background: #eef2f6;
        color: #465160;
        border-radius: 999px;
        padding: 0.3rem 0.7rem;
        font-weight: 600;
        font-size: 0.8rem;
        border: 1px solid #d1d9e0;
    }
    .table-history td .text-muted {
        margin-top: 0.25rem;
        display: block;
        font-size: 0.85rem;
    }
    .modal-media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.75rem;
    }
    .modal-media-card {
        border: 1px solid #e7ecf0;
        border-radius: 10px;
        overflow: hidden;
        background: #f9fbfd;
        box-shadow: 0 4px 10px rgba(0,0,0,0.04);
    }
    .modal-media-card img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        display: block;
    }

    /* Modal Styles */
    .modal.fallback {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.55);
        z-index: 2000;
        padding: 2rem 1rem;
        overflow-y: auto;
    }

    .modal.fallback.show {
        display: flex;
    }

    .modal-dialog {
        margin: auto;
        max-width: 1100px;
        width: 100%;
    }

    .modal-content {
        border: 3px solid #FFD700;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .modal-header {
        background-color: #008080;
        color: #ffffff;
        padding: 1.5rem 2rem;
        border-bottom: 2px solid #FFD700;
        flex-shrink: 0;
    }

    .modal-title {
        font-weight: bold;
        font-size: 1.3rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .modal-body {
        background: #f5f5f5;
        padding: 2rem;
        overflow-y: auto;
        flex: 1 1 auto;
    }

    .modal-footer {
        border-top: 2px solid #e0e0e0;
        padding: 1.5rem 2rem;
        flex-shrink: 0;
        background: #ffffff;
    }

    .btn-close {
        background: white;
        border: none;
        color: #ffffff;
        font-size: 1.5rem;
        cursor: pointer;
        opacity: 0.8;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .btn-secondary {
        padding: 0.75rem 1.5rem;
        background-color: #6c757d;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    /* Form Section Styles for Modal */
    .form-section {
        background-color: #E6F3F5;
        border: 2px solid #FFD700;
        border-radius: 5px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .form-section-header {
        background-color: #008080;
        color: #ffffff;
        padding: 0.75rem 1rem;
        font-weight: bold;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-icon {
        font-size: 1.25rem;
    }

    .form-section-content {
        padding: 1.5rem;
        background: white;
    }

    .form-field {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }

    .form-value {
        background: #f9f9f9;
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 0.75rem 1rem;
        color: #333;
        font-size: 0.95rem;
        line-height: 1.6;
        min-height: 44px;
    }

    .form-section-content ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .form-section-content ul li {
        background: #f9f9f9;
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.75rem;
        color: #333;
        position: relative;
        padding-left: 2.5rem;
    }

    .form-section-content ul li::before {
        content: '•';
        position: absolute;
        left: 1rem;
        color: #197a8c;
        font-weight: bold;
        font-size: 1.5rem;
    }

    .modal-media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .modal-media-card {
        border: 2px solid #ccc;
        border-radius: 5px;
        overflow: hidden;
        background: white;
    }

    .modal-media-card img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        display: block;
        cursor: pointer;
    }

    /* Report Card Sections */
    .report-card {
        border: 1px solid #c8c8c8;
        border-radius: 6px;
        margin-bottom: 1rem;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .report-card-header {
        background: #e0e0e0;
        border-bottom: 1px solid #c8c8c8;
        padding: 0.55rem 1rem;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #222;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .report-card-body {
        padding: 0.9rem 1.1rem;
        background: #fff;
    }
    .report-dotfield {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }
    .report-dotfield label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 3px;
    }
    .report-dotfield .report-dotline {
        border-bottom: 1px dotted #999;
        padding-bottom: 2px;
        font-size: 0.88rem;
        min-height: 1.25rem;
        word-break: break-word;
    }
    .report-field-row {
        display: flex;
        gap: 1.25rem;
        margin-bottom: 0.7rem;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .history-container {
            padding: 1rem;
            margin: 1rem;
        }

        .history-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .history-title {
            font-size: 1.4rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .table-history th,
        .table-history td {
            padding: 0.75rem;
            font-size: 0.875rem;
        }

        .modal-body {
            padding: 1rem;
        }

        .form-section-content {
            padding: 1rem;
        }

        .modal-media-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }
    }
</style>

@endpush

@section('content')
<div class="container">
    <div class="history-hero" style="display: flex; flex-direction: column;">
        <div style="display: flex; flex-direction: column;">
            <p class="mb-1" style="opacity:0.85;">Your consultation journey</p>
            <h2>Patient History</h2>
            <p class="mb-0" style="opacity:0.85;">Review past visits, results, and follow-ups.</p>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <div class="stat">
                <div class="label">Total Records</div>
                <div class="value" id="stat-total">{{ $history->count() }}</div>
            </div>
            <div class="stat">
                <div class="label">Profiles</div>
                <div class="value" id="stat-profiles">{{ $historyByProfile->count() }}</div>
            </div>
            <div class="stat">
                <div class="label">Follow-ups</div>
                <div class="value" id="stat-followups">{{ $history->where('follow_up_required', true)->count() }}</div>
            </div>
        </div>
    </div>

    <div class="filters-card">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="personal_information_id" class="form-label">Profile</label>
                <select name="personal_information_id" id="personal_information_id" class="form-control filter-control">
                    <option value="">All Profiles</option>
                    @foreach($profiles as $profile)
                        <option value="{{ $profile->id }}" {{ request('personal_information_id') == $profile->id ? 'selected' : '' }}>
                            {{ $profile->full_name ?? ($profile->first_name . ' ' . $profile->last_name) }} {{ $profile->is_default ? '(Default)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="branch_id" class="form-label">Branch</label>
                <select name="branch_id" id="branch_id" class="form-control filter-control">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="type" class="form-label">Type</label>
                <select name="type" id="type" class="form-control filter-control">
                    <option value="">All</option>
                    <option value="consult" {{ request('type') == 'consult' ? 'selected' : '' }}>Consult</option>
                    <option value="services" {{ request('type') == 'services' ? 'selected' : '' }}>Services</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="month" class="form-label">Month</label>
                <select name="month" id="month" class="form-control filter-control">
                    <option value="">All</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label for="year" class="form-label">Year</label>
                <select name="year" id="year" class="form-control filter-control">
                    <option value="">All</option>
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <div id="history-content">
        @include('patient.partials.history-table', ['history' => $history, 'historyByProfile' => $historyByProfile, 'profiles' => $profiles])
                        </div>
                            </div>

<!-- Result Modal (Bootstrap or custom) -->
<div class="modal fade fallback" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="margin: 2rem auto; max-height: 90vh; display: flex; flex-direction: column;">
        <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Consultation Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto; flex: 1;">
                <div class="mb-2 text-muted small" id="resultMeta"></div>
                <div id="resultContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="downloadPdfBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="vertical-align:-2px"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
                    Download PDF
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('resultModal');
    const resultModal = (window.bootstrap && window.bootstrap.Modal)
        ? new bootstrap.Modal(modalEl)
        : null;
    const fallbackBackdropId = 'result-backdrop-fallback';
    const resultContent = document.getElementById('resultContent');
    const resultMeta = document.getElementById('resultMeta');
    const historyContainer = document.getElementById('history-content');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    function renderBulletSection(title, items) {
        if (!items || !items.length) return '';
        const safeItems = Array.isArray(items) ? items : [items];
        const bullets = safeItems
            .filter(i => i && String(i).trim().length)
            .map(i => `<li>${String(i)}</li>`)
            .join('');
        if (!bullets) return '';
        return `
            <div class="form-field">
                ${title ? `<label class="form-label">${title}</label>` : ''}
                <ul class="mb-0">${bullets}</ul>
            </div>
        `;
    }

    function renderMedia(label, items) {
        if (!items || !items.length) return '';
        const cards = items.map(src => `
            <div class="modal-media-card">
                <img src="${src}" alt="${label} photo" onclick="window.open('${src}', '_blank')" style="cursor: pointer;" />
            </div>
        `).join('');
        return `
            <div class="form-field">
                ${label ? `<label class="form-label">${label}</label>` : ''}
                <div class="modal-media-grid">${cards}</div>
            </div>
        `;
    }

    function renderResult(data) {
        const c = data.consultation_data;

        const complaints    = c?.before?.skin_condition ?? c?.before_condition ?? [];
        const beforePhotos  = c?.before?.photos ?? [];
        const afterPhotos   = c?.after?.photos ?? [];
        const procedures    = c?.procedures ?? [];
        const results       = c?.results ?? c?.result ?? [];
        let prescriptionItems = c?.prescription ?? [];
        if (!prescriptionItems.length && data.prescription) prescriptionItems = [data.prescription];
        const medication    = c?.medication ?? null;
        const followUpDate  = data.follow_up_date || c?.follow_up?.date || '';
        const followUpReq   = data.follow_up_required || c?.follow_up?.required || false;
        const notesText     = data.notes || c?.before?.notes || c?.after?.notes || '';
        const patientName   = data.patient?.name ?? '—';
        const patientDob    = data.patient?.birthday ?? '—';
        const patientSex    = data.patient?.sex ?? '—';
        const patientAge    = data.patient?.age ?? '—';

        const card = (icon, title, body) => `
            <div class="report-card">
                <div class="report-card-header">${icon}&nbsp; ${title}</div>
                <div class="report-card-body">${body}</div>
            </div>`;

        const dotField = (label, value, flex='1') => `
            <div class="report-dotfield" style="flex:${flex}">
                <label>${label}</label>
                <div class="report-dotline">${value || ''}</div>
            </div>`;

        const row = (...fields) => `<div class="report-field-row">${fields.join('')}</div>`;

        const bulletList = (items) => items.length
            ? `<ul style="margin:0;padding-left:1.4rem;">${items.map(i=>`<li style="font-size:0.88rem;margin-bottom:2px;">${i}</li>`).join('')}</ul>`
            : '';

        let html = '';

        // ── SECTION 1: CONSULTATION OVERVIEW ─────────────────────────────
        let s1 = row(dotField('Patient Name (Full)', patientName, '2'), dotField('Date of Birth', patientDob));
        s1    += row(dotField('Service', data.service), dotField('Date of Visit', data.date), dotField('Branch', data.branch), dotField('Doctor', data.doctor));
        s1    += complaints.length
            ? `<div style="margin-top:0.5rem;"><label class="report-dotfield"><label>Chief Complaint(s)</label></label>${bulletList(complaints)}</div>`
            : row(dotField('Chief Complaint(s)', ''));
        html  += card('🗒️', 'SECTION 1: CONSULTATION OVERVIEW', s1);

        // ── SECTION 2: NOTES ─────────────────────────────────────────────
        if (notesText) {
            html += card('✏️', 'SECTION 2: NOTES',
                `<div style="font-size:0.88rem;white-space:pre-wrap;">${notesText}</div>`);
        }

        // ── SECTION 3: PHOTO DOCUMENTATION ───────────────────────────────
        if (beforePhotos.length || afterPhotos.length || results.length) {
            let s3 = '';
            if (beforePhotos.length || afterPhotos.length) {
                s3 += `<div style="display:flex;gap:1rem;margin-bottom:0.75rem;flex-wrap:wrap;">`;
                const photoCol = (label, photos) => `
                    <div style="flex:1;min-width:120px;">
                        <div style="font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;margin-bottom:6px;">${label}</div>
                        <div class="modal-media-grid">${photos.map(src=>`<div class="modal-media-card"><img src="${src}" style="cursor:pointer;" onclick="window.open('${src}','_blank')"/></div>`).join('')}</div>
                    </div>`;
                if (beforePhotos.length) s3 += photoCol('Before Photos', beforePhotos);
                if (afterPhotos.length)  s3 += photoCol('After Photos',  afterPhotos);
                s3 += `</div>`;
            }
            if (results.length) {
                s3 += `<div style="font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;margin-bottom:4px;margin-top:0.5rem;">Consultation Results</div>${bulletList(results)}`;
            }
            html += card('📷', 'SECTION 3: PHOTO DOCUMENTATION', s3);
        }

        // ── SECTION 4: TREATMENT PLAN & PRESCRIPTION ─────────────────────
        let s4 = '';
        if (prescriptionItems.length) {
            s4 += `<div style="font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;margin-bottom:4px;">Prescription</div>${bulletList(prescriptionItems)}`;
        }
        if (procedures.length) {
            s4 += `<div style="font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;margin-bottom:4px;${prescriptionItems.length?'margin-top:0.75rem;':''}">Procedures</div>${bulletList(procedures)}`;
        }
        if (s4) html += card('💊', 'SECTION 4: TREATMENT PLAN & PRESCRIPTION', s4);

        // ── SECTION 5: MEDICATIONS TO TAKE ───────────────────────────────
        let s5 = '';
        if (medication) {
            let medItems = Array.isArray(medication) ? medication : (medication.medicines ?? []);
            let instructions = Array.isArray(medication) ? '' : (medication.instructions ?? '');
            if (medItems.length) s5 += bulletList(medItems);
            if (instructions) s5 += `<div style="margin-top:0.5rem;font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;margin-bottom:2px;">Instructions</div><div style="font-size:0.88rem;">${instructions}</div>`;
        }
        if (s5) html += card('💉', 'SECTION 5: MEDICATIONS TO TAKE', s5);

        // ── SECTION 6: FOLLOW-UP ──────────────────────────────────────────
        if (followUpReq || followUpDate) {
            let s6 = row(dotField('Follow-up Date', followUpDate || 'Required (date TBD)', '2'), dotField('Time', ''));
            s6   += row(dotField('Purpose', ''));
            html += card('📅', 'SECTION 6: FOLLOW-UP', s6);
        }

        return `<div class="consultation-form-container">${html || '<p style="color:#777;text-align:center;padding:1rem;">No detailed result available.</p>'}</div>`;
    }

    function buildPdfDocument(data) {
        const c = data.consultation_data;

        const complaints    = c?.before?.skin_condition ?? c?.before_condition ?? [];
        const beforePhotos  = c?.before?.photos ?? [];
        const afterPhotos   = c?.after?.photos ?? [];
        const procedures    = c?.procedures ?? [];
        const results       = c?.results ?? c?.result ?? [];
        let prescriptionItems = c?.prescription ?? [];
        if (!prescriptionItems.length && data.prescription) prescriptionItems = [data.prescription];
        const medication    = c?.medication ?? null;
        const followUpDate  = data.follow_up_date || c?.follow_up?.date || '';
        const followUpReq   = data.follow_up_required || c?.follow_up?.required || false;
        const notesText     = data.notes || c?.before?.notes || c?.after?.notes || '';
        const patientName   = data.patient?.name ?? '';
        const patientDob    = data.patient?.birthday ?? '';
        const patientSex    = data.patient?.sex ?? '';
        const patientAge    = data.patient?.age ?? '';

        const sec = (num, title, body) => `
            <div style="border:1px solid #bbb;border-radius:4px;margin-bottom:12px;page-break-inside:avoid;">
                <div style="background:#d5d5d5;padding:6px 11px;">
                    <span style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#222;">SECTION ${num}: ${title}</span>
                </div>
                <div style="background:#fff;padding:10px 13px;">${body}</div>
            </div>`;

        const f = (label, value, w) => `
            <span style="display:inline-block;vertical-align:top;width:${w||'160px'};margin-right:10px;margin-bottom:8px;">
                <span style="display:block;font-size:7.5px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:3px;">${label}</span>
                <span style="display:block;border-bottom:1px dotted #888;padding-bottom:2px;font-size:10px;min-height:14px;word-break:break-word;">${value || '&nbsp;'}</span>
            </span>`;
        const bullets = (items) => items.length
            ? `<ul style="margin:0;padding-left:16px;">${items.map(i=>`<li style="font-size:10px;margin-bottom:2px;">${i}</li>`).join('')}</ul>`
            : '';
        const subLabel = (text, mt) => `<div style="font-size:7.5px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:4px;${mt?'margin-top:'+mt+';':''}">${text}</div>`;

        // S1
        let s1  = `<div style="margin-bottom:8px;">${f('Patient Name (Full)', patientName, '300px')}${f('Date of Birth', patientDob, '140px')}</div>`;
        s1     += `<div style="margin-bottom:8px;">${f('Sex', patientSex, '90px')}${f('Age', patientAge, '60px')}${f('Date of Visit', data.date ?? '', '140px')}${f('Branch', data.branch ?? '', '160px')}</div>`;
        s1     += complaints.length
            ? `<div style="margin-top:4px;">${subLabel('Chief Complaint(s)')}${bullets(complaints)}</div>`
            : `<div>${f('Chief Complaint(s)', '', '480px')}</div>`;

        // S3
        let s3 = '';
        if (beforePhotos.length || afterPhotos.length) {
            const photoBox = (label, src) => src
                ? `<span style="display:inline-block;vertical-align:top;width:210px;text-align:center;margin-right:14px;"><div style="font-size:7.5px;font-weight:700;color:#555;text-transform:uppercase;margin-bottom:5px;">${label}</div><div style="border:2px dashed #888;border-radius:4px;height:110px;text-align:center;"><img src="${src}" style="max-width:100%;max-height:110px;object-fit:contain;" crossorigin="anonymous"></div></span>`
                : `<span style="display:inline-block;vertical-align:top;width:210px;margin-right:14px;"><div style="font-size:7.5px;font-weight:700;color:#555;text-transform:uppercase;margin-bottom:5px;">${label}</div><div style="border:2px dashed #bbb;border-radius:4px;height:110px;padding-top:44px;text-align:center;"><span style="font-size:9px;color:#aaa;">${label}</span></div></span>`;
            s3 += `<div style="margin-bottom:10px;">${photoBox('BEFORE PHOTO', beforePhotos[0]??null)}${photoBox('AFTER PHOTO', afterPhotos[0]??null)}</div>`;
            s3 += `<div>${f('Before Picture Notes','','210px')}${f('After Picture Notes','','210px')}</div>`;
        }
        if (results.length) s3 += `<div style="margin-top:8px;">${subLabel('Consultation Results')}${bullets(results)}</div>`;

        // S4
        let s4 = '';
        if (prescriptionItems.length) s4 += `${subLabel('Prescription')}${bullets(prescriptionItems)}`;
        if (procedures.length) s4 += `${subLabel('Procedures', prescriptionItems.length?'8px':null)}${bullets(procedures)}`;
        if (!s4) s4 = `<div style="border-bottom:1px dotted #aaa;margin:6px 0;"></div><div style="border-bottom:1px dotted #aaa;margin:6px 0;"></div>`;

        // S5
        let s5 = '';
        if (medication) {
            let medItems = Array.isArray(medication) ? medication : (medication.medicines ?? []);
            let instr = Array.isArray(medication) ? '' : (medication.instructions ?? '');
            if (medItems.length) s5 += bullets(medItems);
            if (instr) s5 += `${subLabel('Instructions','6px')}<div style="font-size:10px;">${instr}</div>`;
        }
        if (!s5) s5 = `<div style="font-size:10px;margin-bottom:5px;">&#9633; &nbsp;Use as directed. Continue existing: <span style="display:inline-block;border-bottom:1px dotted #888;width:160px;">&nbsp;</span></div><div style="font-size:10px;">&#9633; &nbsp;New medications: <span style="display:inline-block;border-bottom:1px dotted #888;width:195px;">&nbsp;</span></div>`;

        // S6
        let s6  = `<div style="margin-bottom:8px;">${f('Follow-up Date', followUpDate||(followUpReq?'Required (date TBD)':''), '260px')}${f('Time','','160px')}</div>`;
        s6     += `<div>${f('Purpose','','480px')}</div>`;

        return `
            <div style="font-family:Arial,Helvetica,sans-serif;color:#111;font-size:11px;line-height:1.5;width:100%;">
                <div style="background:#1a1a1a;color:#fff;text-align:center;padding:13px;margin-bottom:14px;border-radius:3px;">
                    <div style="font-size:15px;font-weight:900;letter-spacing:2px;text-transform:uppercase;">PATIENT CONSULTATION REPORT</div>
                </div>
                <div style="margin-bottom:13px;">
                    <div style="font-size:15px;font-weight:800;margin-bottom:3px;">${data.service ?? 'Consultation'}</div>
                    <div style="font-size:9px;color:#555;">${data.date ?? 'Date TBD'} &bull; ${data.branch ?? 'N/A'} &bull; ${data.doctor ?? 'N/A'}</div>
                    <div style="font-size:9px;color:#555;">Recorded: ${data.created_at ?? 'N/A'}</div>
                </div>
                ${sec('1','CONSULTATION OVERVIEW', s1)}
                ${notesText ? sec('2','NOTES',`<div style="font-size:10px;white-space:pre-wrap;">${notesText}</div>`) : ''}
                ${s3 ? sec('3','PHOTO DOCUMENTATION', s3) : ''}
                ${sec('4','TREATMENT PLAN &amp; PRESCRIPTION', s4)}
                ${sec('5','MEDICATIONS TO TAKE', s5)}
                ${sec('6','FOLLOW-UP', s6)}
                <div style="margin-top:18px;padding-top:10px;border-top:1px solid #bbb;">
                    <span style="display:inline-block;vertical-align:bottom;width:320px;margin-right:30px;">
                        <span style="font-size:9px;font-weight:700;">Patient Signature:</span>
                        <div style="border-bottom:2px solid #333;height:22px;"></div>
                    </span>
                    <span style="display:inline-block;vertical-align:bottom;width:160px;">
                        <span style="font-size:9px;font-weight:700;">Date:</span>
                        <div style="border-bottom:2px solid #333;height:22px;"></div>
                    </span>
                </div>
            </div>`;
    }

    function bindShowResultButtons(scope = document) {
        scope.querySelectorAll('.show-result-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const data = btn.dataset.history ? JSON.parse(btn.dataset.history) : {};
                resultMeta.innerHTML = `
                    <strong>${data.service ?? 'Consultation'}</strong><br>
                    <span class="text-muted">${data.date ?? ''} &middot; ${data.branch ?? ''} &middot; ${data.doctor ?? ''}</span><br>
                    <span class="text-muted">Recorded: ${data.created_at ?? ''}</span>
                `;
                resultContent.innerHTML = renderResult(data);
                resultContent.dataset.lastHistory = btn.dataset.history;
                if (resultModal) {
                    resultModal.show();
                } else {
                    // fallback overlay
                    let backdrop = document.getElementById(fallbackBackdropId);
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.id = fallbackBackdropId;
                        backdrop.className = 'modal-backdrop fade show';
                        backdrop.style.zIndex = '1999';
                        document.body.appendChild(backdrop);
                    }
                    modalEl.classList.add('show');
                    modalEl.removeAttribute('aria-hidden');
                    modalEl.style.display = 'flex';
                    document.body.classList.add('modal-open');
                }
            });
        });
    }

    async function fetchHistory() {
        const personalId = document.getElementById('personal_information_id').value;
        const branchId = document.getElementById('branch_id').value;
        const type = document.getElementById('type').value;
        const month = document.getElementById('month')?.value;
        const year = document.getElementById('year')?.value;
        
        // Build query string
        const params = new URLSearchParams();
        if (personalId) params.append('personal_information_id', personalId);
        if (branchId) params.append('branch_id', branchId);
        if (type) params.append('type', type);
        if (month) params.append('month', month);
        if (year) params.append('year', year);
        
        const url = `{{ route('patient.history.filter') }}?${params.toString()}`;
        console.log('Request URL:', url);
        
        try {
            const res = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            console.log('Response status:', res.status);
            if (!res.ok) throw new Error('Failed to load history');
            const data = await res.json();
            console.log('Response data:', data);
            historyContainer.innerHTML = data.html;
            document.getElementById('stat-total').textContent = data.stats.total;
            document.getElementById('stat-profiles').textContent = data.stats.profiles;
            document.getElementById('stat-followups').textContent = data.stats.followups;
            bindShowResultButtons(historyContainer);
        } catch (e) {
            console.error('Filter error:', e);
            alert('Unable to filter right now. Please try again.');
        }
    }

    // Live filtering
    document.querySelectorAll('.filter-control').forEach(select => {
        select.addEventListener('change', function() {
            console.log('Filter changed:', this.id, this.value);
            fetchHistory();
        });
    });

    // Initial bindings
    bindShowResultButtons();

    // Close modal when not using Bootstrap
    modalEl.addEventListener('click', (e) => {
        if (!resultModal && e.target === modalEl) {
            modalEl.classList.remove('show');
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            const backdrop = document.getElementById(fallbackBackdropId);
            if (backdrop) backdrop.remove();
        }
    });
    modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!resultModal) {
                modalEl.classList.remove('show');
                modalEl.setAttribute('aria-hidden', 'true');
                modalEl.style.display = 'none';
                document.body.classList.remove('modal-open');
                const backdrop = document.getElementById(fallbackBackdropId);
                if (backdrop) backdrop.remove();
            }
        });
    });

    document.getElementById('downloadPdfBtn').addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Opening...';

        const currentData = JSON.parse(document.getElementById('resultContent').dataset?.lastHistory || '{}');
        const pdfHtml = buildPdfDocument(currentData);

        const fullHtml = `<!DOCTYPE html><html lang="en"><head>
<meta charset="utf-8">
<title>Consultation Report</title>
<style>
  @page { size: A4; margin: 10mm 12mm; }
  body  { margin: 0; padding: 20px; background: #fff; font-family: Arial, Helvetica, sans-serif; color: #111; }
  * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
  @media print { body { padding: 0; } }
</style></head>
<body>${pdfHtml}</body>
</html>`;

        const win = window.open('', '_blank');
        if (!win) {
            alert('Pop-ups are blocked. Please allow pop-ups for this page, then try again.');
            btn.disabled = false;
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="vertical-align:-2px"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg> Download PDF`;
            return;
        }

        win.document.open();
        win.document.write(fullHtml);
        win.document.close();

        win.addEventListener('load', function () {
            setTimeout(function () {
                win.focus();
                win.print();
            }, 350);
        });

        btn.disabled = false;
        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="vertical-align:-2px"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg> Download PDF`;
    });
});
</script>
@endpush