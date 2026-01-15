@extends('layouts.dashboard')
@section('page-title', 'Slots')

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tables.css') }}">
<style>
    .slots-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        gap: 1rem;
        flex-wrap: wrap;
        width: 100%;
        box-sizing: border-box;
    }

    .slots-header-left {
        flex: 1;
        min-width: 0;
    }

    .slots-header-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: flex-end;
        min-width: 0;
    }

    .management-label {
        font-family: 'Figtree', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        letter-spacing: 0.5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        flex: 0 1 auto;
        min-width: 0;
        max-width: 100%;
    }

    .slot-search-input {
        width: 200px;
        max-width: 100%;
        min-width: 0;
        height: 40px;
        padding: 0 12px 0 40px;
        border: 2px solid #FFD700;
        background: #ffffff;
        font-family: 'Figtree', sans-serif;
        font-size: 0.95rem;
        color: #2c3e50;
        box-shadow: 0 2px 4px rgba(255, 215, 0, 0.2);
        transition: box-shadow 0.3s ease;
        border-radius: 0;
        box-sizing: border-box;
    }

    .slot-search-input:focus {
        outline: none;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    }

    .slot-search-input::placeholder {
        color: #9ca3af;
        font-style: italic;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        width: 18px;
        height: 18px;
        color: #9ca3af;
        pointer-events: none;
    }

    .add-slot-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 40px;
        padding: 0 16px;
        background: #197a8c;
        color: #ffffff;
        text-decoration: none;
        font-family: 'Figtree', sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        border: none;
        border-radius: 0;
        cursor: pointer;
        transition: background-color 0.3s ease;
        white-space: nowrap;
        flex-shrink: 0;
        box-sizing: border-box;
    }

    .add-slot-btn:hover {
        background: #1a6b7a;
    }

    .add-icon {
        font-size: 1.2rem;
        font-weight: 300;
        color: #ffffff;
        line-height: 1;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
        width: 100%;
        box-sizing: border-box;
    }

    .stat-card {
        background: #ffffff;
        border: 3px solid #FFD700;
        border-radius: 8px;
        padding: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.15);
        box-sizing: border-box;
        min-width: 0;
    }

    .stat-icon {
        font-size: 1.85rem;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 215, 0, 0.15);
        border-radius: 8px;
        flex-shrink: 0;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.65rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .slots-filters {
        margin-bottom: 1.5rem;
        padding: 0.65rem;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #e9ecef;
    }

    .filter-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.85rem;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .filter-label {
        font-family: 'Figtree', sans-serif;
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 600;
    }

    .filter-select,
    .filter-input {
        width: 100%;
        height: 32px;
        padding: 0 10px;
        border: 2px solid #e9ecef;
        background: #ffffff;
        font-family: 'Figtree', sans-serif;
        font-size: 0.82rem;
        color: #2c3e50;
        border-radius: 0;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .filter-select:focus,
    .filter-input:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 2px 4px rgba(255, 215, 0, 0.2);
    }

    .filter-select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239ca3af' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px;
    }

    .scope-toggle {
        display: flex;
        gap: 0.5rem;
    }

    .scope-option {
        flex: 1;
        text-align: center;
        padding: 0.4rem 0.6rem;
        border: 2px solid #e9ecef;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .scope-option.active {
        border-color: #197a8c;
        color: #ffffff;
        background: #197a8c;
    }

    .slots-card {
        padding: 1rem;
        border: 1px solid #eef1f4;
    }

    .slots-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .slots-table-header h2 {
        margin: 0;
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 90px;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-available {
        background-color: #10b981;
        color: #fff;
    }

    .status-pending {
        background-color: #fbbf24;
        color: #78350f;
    }

    .status-booked {
        background-color: #3b82f6;
        color: #fff;
    }

    .patient-info {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .patient-name-link {
        color: #197a8c;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
    }

    .patient-name-link:hover {
        color: #145866;
    }

    .appointment-status-badge {
        display: inline-block;
        margin-top: 0.15rem;
        font-size: 0.75rem;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        background: rgba(25, 122, 140, 0.1);
        color: #197a8c;
    }

    .action-buttons {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.9rem;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.25s ease;
        min-width: 92px;
    }

    .action-btn:hover {
        border-color: #197a8c;
        color: #197a8c;
        background: rgba(25, 122, 140, 0.08);
    }

    .action-btn.accept {
        background: #10b981;
        border-color: #10b981;
        color: #fff;
    }

    .action-btn.accept:hover {
        background: #0f9a6a;
        border-color: #0f9a6a;
        color: #fff;
    }

    .action-btn.delete {
        background: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }

    .action-btn.delete:hover {
        background: #d43535;
        border-color: #d43535;
        color: #fff;
    }

    /* Patient Modal Styles */
    .patient-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .patient-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .patient-modal-content {
        background-color: #ffffff;
        margin: auto;
        padding: 0;
        border: 3px solid #FFD700;
        width: 90%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .patient-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem 2rem;
        border-bottom: 2px solid #e0e0e0;
        background-color: #ffffff;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .patient-modal-logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .patient-modal-logo img {
        width: 60px;
        height: 60px;
    }

    .patient-modal-logo-text {
        font-size: 1.2rem;
        font-weight: bold;
        color: #197a8c;
    }

    .patient-modal-title {
        font-size: 1.8rem;
        font-weight: bold;
        color: #000000;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
    }

    .patient-modal-close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .patient-modal-close:hover,
    .patient-modal-close:focus {
        color: #000;
    }

    .patient-modal-body {
        padding: 2rem;
    }

    .patient-form-section {
        background-color: #E6F3F5;
        border: 2px solid #FFD700;
        border-radius: 5px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .patient-section-header {
        background-color: #008080;
        color: #ffffff;
        padding: 0.75rem 1rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        font-weight: bold;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .patient-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .patient-form-group {
        margin-bottom: 1rem;
    }

    .patient-form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .patient-form-group input[type="text"],
    .patient-form-group input[type="email"],
    .patient-form-group input[type="date"],
    .patient-form-group textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 1rem;
        box-sizing: border-box;
        background-color: #ffffff;
    }

    .patient-form-group textarea {
        min-height: 80px;
        resize: vertical;
    }

    .patient-radio-group {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .patient-radio-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: normal;
        cursor: default;
    }

    .patient-checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .patient-checkbox-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: normal;
        cursor: default;
    }

    .patient-certification-section {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e0e0e0;
    }

    .patient-certification-text {
        margin-bottom: 1.5rem;
        font-size: 1rem;
        color: #333;
    }

    .patient-signature-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-top: 1rem;
    }

    .patient-signature-field {
        display: flex;
        flex-direction: column;
    }

    .patient-signature-field label {
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .patient-signature-display {
        border: 2px solid #ccc;
        border-radius: 5px;
        padding: 1rem;
        background: white;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .patient-signature-display img {
        max-width: 100%;
        max-height: 150px;
    }

    .patient-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-top: 2px solid #e0e0e0;
        background-color: #ffffff;
        position: sticky;
        bottom: 0;
    }

    .patient-modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .patient-modal-btn-cancel {
        background-color: #6c757d;
        color: white;
    }

    .patient-modal-btn-cancel:hover {
        background-color: #5a6268;
    }

    .patient-modal-btn-print {
        background-color: #008080;
        color: white;
    }

    .patient-modal-btn-print:hover {
        background-color: #006666;
    }

    .patient-modal-btn-download {
        background-color: #008080;
        color: white;
    }

    .patient-modal-btn-download:hover {
        background-color: #006666;
    }

    .btn {
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .btn-primary {
        background: #197a8c;
        color: #fff;
    }

    .btn-primary:hover {
        background: #145866;
    }

    .btn-success {
        background: #10b981;
        color: #fff;
    }

    .btn-success:hover {
        background: #059669;
    }

    .btn-outline {
        background: transparent;
        color: #197a8c;
        border: 1px solid #197a8c;
    }

    .btn-outline:hover {
        background: rgba(25, 122, 140, 0.08);
    }

    /* Responsive Design - Tablets and below */
    @media (max-width: 992px) {
        .container {
            padding: 0 1rem;
            width: 100%;
            box-sizing: border-box;
        }

        .slots-header {
            flex-direction: column;
            align-items: stretch;
        }

        .slots-header-left {
            width: 100%;
        }

        .slots-header-right {
            width: 100%;
            justify-content: stretch;
            flex-direction: column;
        }

        .search-wrapper {
            width: 100%;
            max-width: 100%;
        }

        .slot-search-input {
            width: 100%;
        }

        .add-slot-btn {
            width: 100%;
        }

        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-form {
            grid-template-columns: 1fr;
        }

        .filter-group {
            min-width: 100%;
            width: 100%;
        }

        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 800px;
        }

        .patient-form-row {
            grid-template-columns: 1fr;
        }

        .patient-signature-section {
            grid-template-columns: 1fr;
        }

        .patient-modal-content {
            width: 95%;
            max-height: 95vh;
        }

        .patient-modal-header {
            flex-wrap: wrap;
        }

        .patient-modal-body {
            padding: 1rem;
        }

        .patient-form-section {
            padding: 1rem;
        }

        .patient-section-header {
            margin: -1rem -1rem 1rem -1rem;
        }

        .patient-modal-footer {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 0.75rem;
        }

        .management-label {
            font-size: 0.9rem;
        }

        .stats-container {
            grid-template-columns: 1fr;
        }

        .stat-card {
            padding: 1rem;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 1.75rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .stat-label {
            font-size: 0.85rem;
        }

        .slots-filters {
            padding: 0.75rem;
        }

        .filter-form {
            gap: 0.75rem;
        }

        .filter-input,
        .filter-select {
            height: 36px;
            min-height: 44px;
        }

        .slots-card {
            padding: 0.75rem;
        }

        .slots-table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        /* Make table responsive - card layout */
        .table-wrapper {
            overflow-x: visible;
            width: 100%;
        }

        table {
            min-width: 100%;
            width: 100%;
            display: block;
        }

        table thead {
            display: none;
        }

        table tbody {
            display: block;
            width: 100%;
        }

        table tbody tr {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(255, 250, 240, 0.75);
            border-radius: 12px;
            box-shadow: 
                0 4px 12px rgba(0, 0, 0, 0.08),
                0 2px 6px rgba(255, 215, 0, 0.15);
            box-sizing: border-box;
        }

        table tbody tr:last-child {
            margin-bottom: 0;
        }

        table tbody td {
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

        table tbody td:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        table tbody td[colspan] {
            display: block;
            text-align: center;
            padding: 2rem 1rem !important;
            border-bottom: none;
        }

        table tbody td[colspan]::before {
            display: none;
        }

        table tbody td:nth-child(1)::before {
            content: "Branch";
        }
        
        table tbody td:nth-child(2)::before {
            content: "Date";
        }
        
        table tbody td:nth-child(3)::before {
            content: "Time";
        }
        
        table tbody td:nth-child(4)::before {
            content: "Status";
        }
        
        table tbody td:nth-child(5)::before {
            content: "Patient";
        }
        
        table tbody td:nth-child(6)::before {
            content: "Actions";
        }

        table tbody td::before {
            font-weight: 600;
            color: #2c3e50;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            flex-shrink: 0;
            text-align: left;
        }

        table tbody td:first-child {
            padding-top: 0;
        }

        .status-badge {
            align-self: flex-start;
        }

        .action-buttons {
            flex-direction: column;
            width: 100%;
            gap: 0.5rem;
            margin-top: 0;
            align-self: stretch;
        }

        .action-btn,
        .action-btn.accept,
        .action-btn.delete {
            width: 100%;
            justify-content: center;
            min-height: 44px;
        }

        .patient-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
            width: 100%;
        }

        .patient-name-link {
            text-align: left;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0.5rem 0;
        }

        .patient-modal-content {
            width: 95%;
        }

        .patient-modal-header {
            padding: 1rem;
            flex-direction: column;
            align-items: flex-start;
        }

        .patient-modal-title {
            font-size: 1.3rem;
        }

        .patient-modal-close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            min-width: 44px;
            min-height: 44px;
        }

        .patient-modal-footer {
            flex-direction: column;
            padding: 1rem;
        }

        .patient-modal-btn {
            width: 100%;
        }

        .delete-slot-form {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding: 0 0.5rem;
        }

        .slots-header {
            margin-bottom: 1rem;
        }

        .management-label {
            font-size: 0.85rem;
        }

        .stats-container {
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat-card {
            padding: 0.75rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 1.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
        }

        .slots-filters {
            padding: 0.5rem;
        }

        .filter-form {
            gap: 0.5rem;
        }

        .slots-card {
            padding: 0.5rem;
        }

        table tbody tr {
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }

        table tbody td {
            padding: 0.5rem 0;
            font-size: 0.875rem;
        }

        table tbody td::before {
            font-size: 0.7rem;
            margin-right: 0.5rem;
        }

        .status-badge {
            padding: 6px 12px;
            font-size: 0.75rem;
        }

        .action-buttons {
            gap: 0.375rem;
        }

        .action-btn,
        .action-btn.accept,
        .action-btn.delete {
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
            min-height: 40px;
        }

        .patient-modal-content {
            width: 98%;
        }

        .patient-modal-header {
            padding: 0.75rem;
        }

        .patient-modal-title {
            font-size: 1rem;
        }

        .patient-modal-body {
            padding: 0.75rem;
        }

        .patient-modal-footer {
            padding: 0.75rem;
        }

        .patient-form-section {
            padding: 0.65rem;
        }

        .patient-section-header {
            font-size: 0.75rem;
            padding: 0.4rem 0.6rem;
            margin: -0.65rem -0.65rem 0.65rem -0.65rem;
        }

        .patient-form-group input,
        .patient-form-group textarea {
            font-size: 0.9rem;
            padding: 0.6rem;
        }
    }

    @media (max-width: 400px) {
        .stat-icon {
            width: 35px;
            height: 35px;
            font-size: 1.25rem;
        }

        .stat-value {
            font-size: 1.1rem;
        }

        table tbody td {
            font-size: 0.8rem;
        }

        .action-btn,
        .action-btn.accept,
        .action-btn.delete {
            padding: 0.35rem 0.6rem;
            font-size: 0.75rem;
            min-height: 36px;
        }
    }
    /* Pagination Responsive Styles */

/* Base pagination styles enhancement */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 1.5rem;
    padding: 1rem 0;
}

.pagination .page-item {
    display: inline-flex;
}

.pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    min-height: 40px;
    padding: 0.5rem 0.75rem;
    border: 2px solid #e9ecef;
    background: #ffffff;
    color: #2c3e50;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.pagination .page-link:hover {
    border-color: #FFD700;
    background: rgba(255, 215, 0, 0.1);
    color: #197a8c;
}

.pagination .page-item.active .page-link {
    background: #197a8c;
    border-color: #197a8c;
    color: #ffffff;
}

.pagination .page-item.disabled .page-link {
    background: #f8f9fa;
    border-color: #e9ecef;
    color: #9ca3af;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Responsive Design - Tablets */
@media (max-width: 992px) {
    .pagination {
        gap: 0.4rem;
        padding: 0.75rem 0;
    }

    .pagination .page-link {
        min-width: 38px;
        min-height: 38px;
        padding: 0.4rem 0.65rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 768px) {
    .pagination {
        gap: 0.35rem;
        padding: 0.75rem 0;
        margin-top: 1rem;
    }

    .pagination .page-link {
        min-width: 44px;
        min-height: 44px;
        padding: 0.5rem;
        font-size: 0.875rem;
    }

    /* Hide page numbers except first, last, and current on mobile */
    .pagination .page-item:not(.active):not(:first-child):not(:last-child):not(.page-item:has(.page-link[rel="prev"])):not(.page-item:has(.page-link[rel="next"])) {
        display: none;
    }

    /* Show dots indicator */
    .pagination .page-item.disabled:not(:first-child):not(:last-child) {
        display: inline-flex;
    }
}

@media (max-width: 576px) {
    .pagination {
        gap: 0.25rem;
        padding: 0.5rem 0;
    }

    .pagination .page-link {
        min-width: 40px;
        min-height: 40px;
        padding: 0.4rem;
        font-size: 0.8rem;
    }

    /* Simplify pagination even more on small screens */
    .pagination .page-item {
        flex-shrink: 0;
    }
}

@media (max-width: 400px) {
    .pagination {
        gap: 0.2rem;
    }

    .pagination .page-link {
        min-width: 36px;
        min-height: 36px;
        padding: 0.3rem;
        font-size: 0.75rem;
    }
}

/* Laravel pagination specific styling */
nav[role="navigation"] {
    width: 100%;
}

nav[role="navigation"] .flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

nav[role="navigation"] .hidden {
    display: none;
}

@media (max-width: 768px) {
    nav[role="navigation"] .flex {
        flex-direction: column;
        text-align: center;
    }

    nav[role="navigation"] p {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="slots-header">
        <div class="slots-header-left">
            <span class="management-label">TIME SLOT MANAGEMENT</span>
        </div>
        <div class="slots-header-right">
            <form method="GET" action="{{ route('doctor.slots') }}" class="search-wrapper">
                <i data-feather="search" class="search-icon"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    class="slot-search-input"
                    placeholder="Search by branch, date, patient..."
                >
                @if($filterDate)<input type="hidden" name="date" value="{{ $filterDate }}">@endif
                @if($filterStatus)<input type="hidden" name="status" value="{{ $filterStatus }}">@endif
                @if($selectedBranchId)<input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">@endif
              
            </form>
            <a href="{{ route('doctor.slots.create') }}" class="add-slot-btn">
                <span class="add-icon">+</span>
                <span>Add Time Slot</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">✓</div>
            <div>
                <div class="stat-label">Available</div>
                <div class="stat-value">{{ $availableCount }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div>
                <div class="stat-label">Pending</div>
                <div class="stat-value">{{ $pendingCount }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div>
                <div class="stat-label">Booked</div>
                <div class="stat-value">{{ $bookedCount }}</div>
            </div>
        </div>
    </div>

    <div class="slots-filters">
        <form method="GET" action="{{ route('doctor.slots') }}" id="filterForm" class="filter-form">
            
            <div class="filter-group">
                <label for="branch_id" class="filter-label">Branch</label>
                <select id="branch_id" name="branch_id" class="filter-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (string)$selectedBranchId === (string)$branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="date" class="filter-label">Date</label>
                <input type="date" id="date" name="date" value="{{ $filterDate }}" class="filter-input">
            </div>
            @if($search)
                <input type="hidden" name="search" value="{{ $search }}">
            @endif
        </form>
    </div>

    <div class="card slots-card">
        <div class="slots-table-header">
            <h2>Time Slots</h2>
            <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                @if($search)
                    <span class="search-summary">Results for "<strong>{{ $search }}</strong>"</span>
                @endif
                @if($filterDate || $filterStatus || $selectedBranchId || ($scope && $scope !== 'all') || $search)
                    <a href="{{ route('doctor.slots') }}" class="btn btn-outline">Reset Filters</a>
                @endif
            </div>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Patient</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slots as $slot)
                        @php
                            // Only get consultation appointments (those with time_slot_id)
                            $consultationAppointments = $slot->appointments->whereNotNull('time_slot_id');
                            $pendingAppointments = $consultationAppointments->where('status', 'pending');
                            $hasPending = $pendingAppointments->isNotEmpty();
                            $hasAppointments = $slot->appointments->isNotEmpty();
                            $status = 'available';
                            $statusClass = 'status-available';

                            if ($slot->is_booked || $consultationAppointments->whereIn('status', ['booked', 'scheduled', 'confirmed', 'completed'])->isNotEmpty()) {
                                $status = 'booked';
                                $statusClass = 'status-booked';
                            } elseif ($hasPending) {
                                $status = 'pending';
                                $statusClass = 'status-pending';
                            }
                        @endphp
                        <tr>
                            <td>{{ optional($slot->branch)->name ?? '—' }}</td>
                            <td>{{ $slot->date->format('M d, Y') }}</td>
                            <td>{{ $slot->start_time }} – {{ $slot->end_time }}</td>
                            <td>
                                <span class="status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                            </td>
                            <td>
                                @if($hasAppointments)
                                    <div class="patient-info">
                                        @foreach($slot->appointments as $appointment)
                                            @php
                                                // Match the modal format: first_name + middle_initial + last_name, or fall back to patient name
                                                $patientName = trim(($appointment->first_name ?? '') . ' ' . ($appointment->middle_initial ? $appointment->middle_initial . '. ' : '') . ($appointment->last_name ?? ''));
                                                if (empty($patientName)) {
                                                    $patientName = $appointment->patient->name ?? 'Unknown Patient';
                                                }
                                            @endphp
                                            <div>
                                                <a href="#" class="patient-name-link view-patient"
                                                   data-fetch-url="{{ route('doctor.patient.details', $appointment->id) }}"
                                                   data-appointment-id="{{ $appointment->id }}">
                                                    {{ $patientName }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span>—</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @if($status === 'available')
                                        {{-- Available: Show Update and Delete --}}
                                        <a href="{{ route('doctor.slots.edit', $slot) }}" class="action-btn" title="Update">
                                            Update
                                        </a>
                                        <form method="POST" action="{{ route('doctor.slots.destroy', $slot) }}"
                                              class="delete-slot-form"
                                              data-slot-id="{{ $slot->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Delete">
                                                Delete
                                            </button>
                                        </form>
                                    @elseif($status === 'pending')
                                        {{-- Pending: Show Accept and Reject/Delete --}}
                                        @php
                                            $pendingAppointment = $pendingAppointments->first();
                                            $isPastDate = $slot->date < now()->startOfDay();
                                        @endphp
                                        @if($pendingAppointment)
                                            @php
                                                $patientName = trim(($pendingAppointment->first_name ?? '') . ' ' . ($pendingAppointment->middle_initial ? $pendingAppointment->middle_initial . '. ' : '') . ($pendingAppointment->last_name ?? ''));
                                                if (empty($patientName)) {
                                                    $patientName = $pendingAppointment->patient->name ?? 'Unknown Patient';
                                                }
                                            @endphp
                                            @if($isPastDate)
                                                {{-- Past date: Only show Delete button --}}
                                                <button type="button" 
                                                        class="action-btn delete-past-appointment-btn delete" 
                                                        title="Delete"
                                                        data-appointment-id="{{ $pendingAppointment->id }}"
                                                        data-patient-name="{{ json_encode($patientName) }}"
                                                        data-slot-date="{{ $slot->date->format('M d, Y') }}"
                                                        data-slot-time="{{ $slot->start_time }}">
                                                    Delete
                                                </button>
                                            @else
                                                {{-- Future date: Show Accept and Reject --}}
                                                <button type="button" 
                                                        class="action-btn accept-appointment-btn accept" 
                                                        title="Accept"
                                                        data-appointment-id="{{ $pendingAppointment->id }}"
                                                        data-patient-name="{{ json_encode($patientName) }}"
                                                        data-slot-date="{{ $slot->date->format('M d, Y') }}"
                                                        data-slot-time="{{ $slot->start_time }}">
                                                    Accept
                                                </button>
                                                <button type="button" 
                                                        class="action-btn reject-appointment-btn delete" 
                                                        title="Delete"
                                                        data-appointment-id="{{ $pendingAppointment->id }}"
                                                        data-slot-date="{{ $slot->date->format('M d, Y') }}"
                                                        data-slot-time="{{ $slot->start_time }}">
                                                    Delete
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:2rem; color:#6b7280;">
                                No time slots found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($slots->hasPages())
            <div style="margin-top:1rem;">
                {{ $slots->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Accept Appointment Modal -->
<div id="acceptModal" class="patient-modal">
    <div class="patient-modal-content" style="max-width: 500px;">
        <div class="patient-modal-header">
            <h1 class="patient-modal-title" style="font-size: 1.2rem;">Accept Appointment</h1>
            <button class="patient-modal-close" onclick="closeAcceptModal()">&times;</button>
        </div>
        <div class="patient-modal-body">
            <form id="acceptAppointmentForm" method="POST">
                @csrf
                <div class="patient-form-group">
                    <label for="doctor_name">Doctor Name <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="doctor_name" name="doctor_name" required 
                          value="Dr. Dianne Paraz"
                          placeholder="Enter doctor name"
                          style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 4px; font-family: 'Figtree', sans-serif; font-size: 0.95rem;">
                    <div id="doctor_name_error" style="color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem; display: none;"></div>
                </div>
                <div class="patient-form-group">
                    <p style="margin-top: 1rem; color: #6c757d;">
                        Accept appointment for <strong id="acceptPatientNameDisplay"></strong>?
                    </p>
                </div>
            </form>
        </div>
        <div class="patient-modal-footer">
            <button class="patient-modal-btn patient-modal-btn-cancel" onclick="closeAcceptModal()">Cancel</button>
            <button type="submit" form="acceptAppointmentForm" class="patient-modal-btn" style="background-color: #10b981; color: white;">
                Accept Appointment
            </button>
        </div>
    </div>
</div>

<!-- Reject Appointment Modal -->
<div id="rejectModal" class="patient-modal">
    <div class="patient-modal-content" style="max-width: 500px;">
        <div class="patient-modal-header">
            <h1 class="patient-modal-title" style="font-size: 1.2rem;">Reject Appointment</h1>
            <button class="patient-modal-close" onclick="closeRejectModal()">&times;</button>
        </div>
        <div class="patient-modal-body">
            <form id="rejectAppointmentForm" method="POST">
                @csrf
                <div class="patient-form-group">
                    <label for="rejection_reason">Reason for Rejection <span style="color: #ef4444;">*</span></label>
                    <textarea id="rejection_reason" name="rejection_reason" required rows="5" 
                              placeholder="Please provide a reason for rejecting this appointment request..."
                              style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 4px; font-family: 'Figtree', sans-serif; font-size: 0.95rem; resize: vertical;"></textarea>
                    <div id="rejection_reason_error" style="color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem; display: none;"></div>
                </div>
            </form>
        </div>
        <div class="patient-modal-footer">
            <button class="patient-modal-btn patient-modal-btn-cancel" onclick="closeRejectModal()">Cancel</button>
            <button type="submit" form="rejectAppointmentForm" class="patient-modal-btn" style="background-color: #ef4444; color: white;">
                Reject Appointment
            </button>
        </div>
    </div>
</div>

<!-- Past Pending Appointments Modal -->
<div id="pastPendingModal" class="patient-modal">
    <div class="patient-modal-content" style="max-width: 600px;">
        <div class="patient-modal-header">
            <h1 class="patient-modal-title" style="font-size: 1.2rem;">⚠️ Past Pending Appointments</h1>
            <button class="patient-modal-close" onclick="closePastPendingModal()">&times;</button>
        </div>
        <div class="patient-modal-body">
            <p style="margin-bottom: 1rem; color: #6c757d;">
                The following appointments have dates in the past and are still pending. These appointments need to be dropped.
            </p>
            <div id="pastPendingList" style="max-height: 400px; overflow-y: auto;">
                @foreach($pastPendingAppointments as $appointment)
                    @php
                        $patientName = trim(($appointment->first_name ?? '') . ' ' . ($appointment->middle_initial ? $appointment->middle_initial . '. ' : '') . ($appointment->last_name ?? ''));
                        if (empty($patientName)) {
                            $patientName = $appointment->patient->name ?? 'Unknown Patient';
                        }
                    @endphp
                    <div style="padding: 1rem; margin-bottom: 0.75rem; border: 2px solid #fbbf24; border-radius: 6px; background: #fffbeb;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                            <div>
                                <strong style="color: #78350f;">{{ $patientName }}</strong>
                                <div style="font-size: 0.9rem; color: #6c757d; margin-top: 0.25rem;">
                                    {{ $appointment->timeSlot->date->format('M d, Y') }} at {{ $appointment->timeSlot->start_time }}
                                    @if($appointment->timeSlot->branch)
                                        - {{ $appointment->timeSlot->branch->name }}
                                    @endif
                                </div>
                            </div>
                            <button type="button" 
                                    class="action-btn delete-past-appointment-btn delete" 
                                    title="Delete"
                                    data-appointment-id="{{ $appointment->id }}"
                                    data-patient-name="{{ json_encode($patientName) }}"
                                    data-slot-date="{{ $appointment->timeSlot->date->format('M d, Y') }}"
                                    data-slot-time="{{ $appointment->timeSlot->start_time }}">
                                Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="patient-modal-footer">
            <button class="patient-modal-btn patient-modal-btn-cancel" onclick="closePastPendingModal()">Close</button>
        </div>
    </div>
</div>

<!-- Patient Information Modal -->
<div id="patientModal" class="patient-modal">
    <div class="patient-modal-content">
        <div class="patient-modal-header">
            <div class="patient-modal-logo">
                <img src="{{ asset('images/dwell-logo.png') }}" alt="Logo">
                <span class="patient-modal-logo-text">D'well</span>
            </div>
            <h1 class="patient-modal-title">NEW PATIENT INFORMATION SHEET</h1>
            <button class="patient-modal-close" onclick="closePatientModal()">&times;</button>
        </div>
        <div class="patient-modal-body" id="patientModalBody">
            <!-- Content will be populated by JavaScript -->
            <div style="text-align:center; padding:2rem;">Select a patient to view details.</div>
        </div>
        <div class="patient-modal-footer">
            <button class="patient-modal-btn patient-modal-btn-cancel" onclick="closePatientModal()">Cancel</button>
            <button class="patient-modal-btn patient-modal-btn-print" onclick="printPatientInfo()">Print</button>
            <button class="patient-modal-btn patient-modal-btn-download" onclick="downloadPatientInfo()">Download</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Unified notification helper: uses dashboard top-right notifier if available
function notifyUser(message, type = 'info') {
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else {
        alert(message);
    }
}

// Accept Modal Functions
function openAcceptModal(appointmentId, patientName, slotDate, slotTime) {
    const modal = document.getElementById('acceptModal');
    const form = document.getElementById('acceptAppointmentForm');
    const patientNameDisplay = document.getElementById('acceptPatientNameDisplay');
    const doctorNameInput = document.getElementById('doctor_name');
    
    if (modal && form) {
        form.action = `/doctor/slots/appointments/${appointmentId}/accept`;
        if (patientNameDisplay) {
            // Ensure patientName is a string (handle JSON-encoded values)
            let displayName = patientName;
            if (typeof patientName === 'string') {
                try {
                    // Try to parse if it's JSON-encoded
                    const parsed = JSON.parse(patientName);
                    if (typeof parsed === 'string') {
                        displayName = parsed;
                    }
                } catch (e) {
                    // Not JSON, use as-is
                    displayName = patientName;
                }
            }
            patientNameDisplay.textContent = displayName;
        }
        if (doctorNameInput) {
            doctorNameInput.value = 'Dr. Dianne Paraz';
        }
        document.getElementById('doctor_name_error').style.display = 'none';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeAcceptModal() {
    const modal = document.getElementById('acceptModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        const form = document.getElementById('acceptAppointmentForm');
        if (form) {
            const doctorNameInput = document.getElementById('doctor_name');
            if (doctorNameInput) {
                doctorNameInput.value = 'Dr. Dianne Paraz';
            }
            document.getElementById('doctor_name_error').style.display = 'none';
        }
    }
}

// Reject Modal Functions
function openRejectModal(appointmentId, slotDate, slotTime) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectAppointmentForm');
    if (modal && form) {
        form.action = `/doctor/slots/appointments/${appointmentId}/reject`;
        form.querySelector('#rejection_reason').value = '';
        document.getElementById('rejection_reason_error').style.display = 'none';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        const form = document.getElementById('rejectAppointmentForm');
        if (form) {
            form.querySelector('#rejection_reason').value = '';
            document.getElementById('rejection_reason_error').style.display = 'none';
        }
    }
}

// Past Pending Modal Functions
function openPastPendingModal() {
    const modal = document.getElementById('pastPendingModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closePastPendingModal() {
    const modal = document.getElementById('pastPendingModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Patient Modal Functions - Global scope for onclick handlers
function openPatientModal() {
    const modal = document.getElementById('patientModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closePatientModal() {
    const modal = document.getElementById('patientModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function printPatientInfo() {
    const modalContent = document.querySelector('.patient-modal-content');
    if (!modalContent) {
        notifyUser('No patient information to print.', 'error');
        return;
    }
    
    const printWindow = window.open('', '_blank');
    
    // Clone the content to avoid modifying the original
    const clonedContent = modalContent.cloneNode(true);
    
    // Hide footer buttons and close button in the clone
    const footer = clonedContent.querySelector('.patient-modal-footer');
    if (footer) footer.style.display = 'none';
    const closeBtn = clonedContent.querySelector('.patient-modal-close');
    if (closeBtn) closeBtn.style.display = 'none';
    
    // Convert readonly inputs to display their values as text for printing
    const originalInputs = modalContent.querySelectorAll('input[readonly], textarea[readonly]');
    const clonedInputs = clonedContent.querySelectorAll('input[readonly], textarea[readonly]');
    
    originalInputs.forEach((originalInput, index) => {
        const clonedInput = clonedInputs[index];
        if (clonedInput) {
            const value = originalInput.value || '';
            const wrapper = document.createElement('div');
            wrapper.className = 'print-value-display';
            wrapper.style.cssText = 'width: 100%; padding: 0.3rem 0.4rem; border: 1px solid #ccc; border-radius: 3px; font-size: 0.85rem; background-color: #ffffff; min-height: 1.5rem; display: flex; align-items: center; color: #2c3e50; white-space: pre-wrap;';
            wrapper.textContent = value || ' ';
            if (clonedInput.parentNode) {
                clonedInput.parentNode.replaceChild(wrapper, clonedInput);
            }
        }
    });
    
    // Convert image src to absolute URL
    const images = clonedContent.querySelectorAll('img');
    images.forEach(img => {
        const src = img.getAttribute('src');
        if (src && !src.startsWith('http') && !src.startsWith('data:')) {
            if (src.startsWith('/')) {
                img.src = window.location.origin + src;
            } else {
                img.src = window.location.origin + '/' + src;
            }
        }
    });
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Patient Information Sheet</title>
            <style>
                @page {
                    size: 8.5in 13in;
                    margin: 0.25in;
                }
                
                * {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Figtree', Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    color: #2c3e50;
                    background: white;
                    font-size: 10px;
                    line-height: 1.2;
                }
                
                .patient-modal-content {
                    background-color: #ffffff;
                    margin: 0;
                    padding: 0;
                    border: 2px solid #FFD700;
                    width: 100%;
                    max-width: 100%;
                    box-shadow: none;
                }
                
                .patient-modal-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 0.4rem 0.6rem;
                    border-bottom: 1px solid #e0e0e0;
                    background-color: #ffffff;
                }
                
                .patient-modal-logo {
                    display: flex;
                    align-items: center;
                    gap: 0.3rem;
                }
                
                .patient-modal-logo img {
                    width: 30px;
                    height: 30px;
                }
                
                .patient-modal-logo-text {
                    font-size: 0.8rem;
                    font-weight: bold;
                    color: #197a8c;
                }
                
                .patient-modal-title {
                    font-size: 0.9rem;
                    font-weight: bold;
                    color: #000000;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin: 0;
                }
                
                .patient-modal-close {
                    display: none !important;
                }
                
                .patient-modal-body {
                    padding: 0.5rem 0.6rem;
                }
                
                .patient-form-section {
                    background-color: #E6F3F5 !important;
                    border: 2px solid #FFD700 !important;
                    border-radius: 3px;
                    padding: 0.5rem;
                    margin-bottom: 0.5rem;
                    page-break-inside: avoid;
                }
                
                .patient-section-header {
                    background-color: #008080 !important;
                    color: #ffffff !important;
                    padding: 0.3rem 0.5rem;
                    margin: -0.5rem -0.5rem 0.5rem -0.5rem;
                    font-weight: bold;
                    font-size: 0.7rem;
                    text-transform: uppercase;
                    letter-spacing: 0.3px;
                }
                
                .patient-form-row {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 0.4rem;
                    margin-bottom: 0.4rem;
                }
                
                .patient-form-group {
                    margin-bottom: 0.3rem;
                }
                
                .patient-form-group label {
                    display: block;
                    margin-bottom: 0.15rem;
                    font-weight: 600;
                    color: #333;
                    font-size: 0.65rem;
                }
                
                .patient-form-group input[type="text"],
                .patient-form-group input[type="email"],
                .patient-form-group input[type="date"],
                .patient-form-group textarea,
                .patient-form-group div {
                    width: 100%;
                    padding: 0.25rem 0.3rem;
                    border: 1px solid #ccc;
                    border-radius: 2px;
                    font-size: 0.75rem;
                    background-color: #ffffff;
                    min-height: 1.2rem;
                    display: block;
                }
                
                .patient-form-group textarea {
                    min-height: 35px;
                    resize: none;
                }
                
                .patient-radio-group {
                    display: flex;
                    gap: 0.8rem;
                    align-items: center;
                    flex-wrap: wrap;
                }
                
                .patient-radio-group label {
                    display: flex;
                    align-items: center;
                    gap: 0.25rem;
                    font-weight: normal;
                    font-size: 0.7rem;
                    cursor: default;
                }
                
                .patient-checkbox-group {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.6rem;
                    margin-bottom: 0.25rem;
                }
                
                .patient-checkbox-group label {
                    display: flex;
                    align-items: center;
                    gap: 0.25rem;
                    font-weight: normal;
                    font-size: 0.7rem;
                    cursor: default;
                }
                
                .patient-certification-section {
                    margin-top: 0.6rem;
                    padding-top: 0.5rem;
                    border-top: 1px solid #e0e0e0;
                }
                
                .patient-certification-text {
                    margin-bottom: 0.5rem;
                    font-size: 0.7rem;
                    color: #333;
                }
                
                .patient-signature-section {
                    display: grid;
                    grid-template-columns: 2fr 1fr;
                    gap: 0.8rem;
                    margin-top: 0.4rem;
                }
                
                .patient-signature-field {
                    display: flex;
                    flex-direction: column;
                }
                
                .patient-signature-field label {
                    margin-bottom: 0.25rem;
                    font-weight: 600;
                    color: #333;
                    font-size: 0.65rem;
                }
                
                .patient-signature-display {
                    border: 1px solid #ccc;
                    border-radius: 3px;
                    padding: 0.3rem;
                    background: white;
                    min-height: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .patient-signature-display img {
                    max-width: 100%;
                    max-height: 60px;
                }
                
                .patient-modal-footer {
                    display: none !important;
                }
                
                input[type="radio"],
                input[type="checkbox"] {
                    width: auto;
                    margin: 0;
                    padding: 0;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                
                .print-value-display {
                    width: 100%;
                    padding: 0.25rem 0.3rem;
                    border: 1px solid #ccc;
                    border-radius: 3px;
                    font-size: 0.75rem;
                    background-color: #ffffff;
                    min-height: 1.2rem;
                    display: flex;
                    align-items: center;
                    color: #2c3e50;
                }
            </style>
        </head>
        <body>
            ${clonedContent.innerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    
    // Wait for images to load before printing
    printWindow.onload = function() {
        setTimeout(function() {
            printWindow.print();
        }, 250);
    };
}

function downloadPatientInfo() {
    // For now, trigger print which allows saving as PDF
    printPatientInfo();
}

document.addEventListener('DOMContentLoaded', function () {
    if (window.feather && typeof window.feather.replace === 'function') {
        window.feather.replace();
    }

    const filterForm = document.getElementById('filterForm');
    const branchSelect = document.getElementById('branch_id');
    const dateInput = document.getElementById('date');
    const statusSelect = document.getElementById('status');
    const scopeButtons = document.querySelectorAll('.scope-option');
    const scopeInput = document.getElementById('scopeInput');

    [branchSelect, dateInput, statusSelect].forEach(function (input) {
        if (!input) return;
        input.addEventListener('change', function () {
            filterForm.submit();
        });
    });

    scopeButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const selectedScope = this.getAttribute('data-scope');
            scopeInput.value = selectedScope;
            filterForm.submit();
        });
    });

    // Close modals when clicking outside
    const patientModal = document.getElementById('patientModal');
    if (patientModal) {
        patientModal.addEventListener('click', function(e) {
            if (e.target === patientModal) {
                closePatientModal();
            }
        });
    }

    const acceptModal = document.getElementById('acceptModal');
    if (acceptModal) {
        acceptModal.addEventListener('click', function(e) {
            if (e.target === acceptModal) {
                closeAcceptModal();
            }
        });
    }

    const rejectModal = document.getElementById('rejectModal');
    if (rejectModal) {
        rejectModal.addEventListener('click', function(e) {
            if (e.target === rejectModal) {
                closeRejectModal();
            }
        });
    }

    const pastPendingModal = document.getElementById('pastPendingModal');
    if (pastPendingModal) {
        pastPendingModal.addEventListener('click', function(e) {
            if (e.target === pastPendingModal) {
                closePastPendingModal();
            }
        });
    }

    // Show past pending appointments modal on page load if there are any
    @if($pastPendingAppointments->isNotEmpty())
        openPastPendingModal();
    @endif

    // Handle accept appointment button clicks
    document.querySelectorAll('.accept-appointment-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            let patientName = this.getAttribute('data-patient-name');
            // Parse JSON if it's encoded, otherwise use as-is
            try {
                patientName = JSON.parse(patientName);
            } catch (e) {
                // If not JSON, use the value as-is
            }
            const slotDate = this.getAttribute('data-slot-date');
            const slotTime = this.getAttribute('data-slot-time');
            openAcceptModal(appointmentId, patientName, slotDate, slotTime);
        });
    });

    // Handle reject appointment button clicks
    document.querySelectorAll('.reject-appointment-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            const slotDate = this.getAttribute('data-slot-date');
            const slotTime = this.getAttribute('data-slot-time');
            openRejectModal(appointmentId, slotDate, slotTime);
        });
    });

    // Handle delete past appointment button clicks
    document.querySelectorAll('.delete-past-appointment-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            let patientName = this.getAttribute('data-patient-name');
            const slotDate = this.getAttribute('data-slot-date');
            const slotTime = this.getAttribute('data-slot-time');
            
            // Parse JSON if it's encoded
            try {
                patientName = JSON.parse(patientName);
            } catch (e) {
                // If not JSON, use as-is
            }
            
            if (!confirm(`Are you sure you want to delete the appointment for ${patientName} on ${slotDate} at ${slotTime}? The patient will be notified that their appointment was declined because they did not show up.`)) {
                return;
            }
            
            // Delete the appointment via AJAX
            const submitButton = this;
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Deleting...';
            
            // Create a form to submit DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/doctor/my-appointments/${appointmentId}`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            form.appendChild(csrfInput);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfInput.value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(new FormData(form))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notifyUser(data.message || 'Appointment deleted successfully! The patient has been notified.', 'success');
                    
                    // Remove the appointment from the modal list if it exists
                    const appointmentItem = submitButton.closest('div[style*="border: 2px solid #fbbf24"]');
                    if (appointmentItem) {
                        appointmentItem.style.transition = 'opacity 0.3s';
                        appointmentItem.style.opacity = '0';
                        setTimeout(() => {
                            appointmentItem.remove();
                            
                            // Check if modal list is empty
                            const pastPendingList = document.getElementById('pastPendingList');
                            if (pastPendingList && pastPendingList.children.length === 0) {
                                closePastPendingModal();
                            }
                        }, 300);
                    }
                    
                    // Reload page to refresh the table
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    notifyUser(data.message || 'Failed to delete appointment. Please try again.', 'error');
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                notifyUser('An error occurred while deleting the appointment. Please try again.', 'error');
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            })
            .finally(() => {
                // Clean up the form
                if (form.parentNode) {
                    form.parentNode.removeChild(form);
                }
            });
        });
    });

    // Handle reject form submission
    const rejectForm = document.getElementById('rejectAppointmentForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const reasonInput = document.getElementById('rejection_reason');
            const errorDiv = document.getElementById('rejection_reason_error');
            const reason = reasonInput.value.trim();
            
            // Clear previous error
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            
            // Validate reason
            if (!reason) {
                errorDiv.textContent = 'Please provide a reason for rejection.';
                errorDiv.style.display = 'block';
                reasonInput.focus();
                return;
            }
            
            if (reason.length > 255) {
                errorDiv.textContent = 'Reason must not exceed 255 characters.';
                errorDiv.style.display = 'block';
                reasonInput.focus();
                return;
            }
            
            // Submit the form
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]') || document.querySelector('button[form="rejectAppointmentForm"][type="submit"]');
            if (!submitButton) {
                errorDiv.textContent = 'Unable to submit right now. Please try again.';
                errorDiv.style.display = 'block';
                return;
            }
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notifyUser(data.message || 'Appointment rejected successfully!', 'success');
                    closeRejectModal();
                    window.location.reload();
                } else {
                    errorDiv.textContent = data.message || 'Failed to reject appointment. Please try again.';
                    errorDiv.style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = 'An error occurred while rejecting the appointment. Please try again.';
                errorDiv.style.display = 'block';
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
    }

    // Handle accept appointment form submission
    const acceptForm = document.getElementById('acceptAppointmentForm');
    if (acceptForm) {
        acceptForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const doctorNameInput = document.getElementById('doctor_name');
            const errorDiv = document.getElementById('doctor_name_error');
            const doctorName = doctorNameInput.value.trim();
            
            // Clear previous error
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            
            // Validate doctor name
            if (!doctorName) {
                errorDiv.textContent = 'Please enter a doctor name.';
                errorDiv.style.display = 'block';
                doctorNameInput.focus();
                return;
            }
            
            // Submit the form
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]') || document.querySelector('button[form="acceptAppointmentForm"][type="submit"]');
            if (!submitButton) {
                errorDiv.textContent = 'Unable to submit right now. Please try again.';
                errorDiv.style.display = 'block';
                return;
            }
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notifyUser(data.message || 'Appointment accepted successfully!', 'success');
                    closeAcceptModal();
                    window.location.reload();
                } else {
                    errorDiv.textContent = data.message || 'Failed to accept appointment. Please try again.';
                    errorDiv.style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = 'An error occurred while accepting the appointment. Please try again.';
                errorDiv.style.display = 'block';
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
    }

    document.querySelectorAll('.view-patient').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            const url = this.getAttribute('data-fetch-url');
            if (!url) return;

            const modalBody = document.getElementById('patientModalBody');
            modalBody.innerHTML = '<div style="text-align:center; padding:2rem;">Loading patient information...</div>';
            openPatientModal();

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Unable to load patient information.');
                    }
                    return response.json();
                })
                .then(data => {
                    populatePatientModal(data);
                    if (window.feather && typeof window.feather.replace === 'function') {
                        window.feather.replace();
                    }
                })
                .catch(error => {
                    modalBody.innerHTML = '<div style="text-align:center; padding:2rem; color:#ef4444;">' + error.message + '</div>';
                });
        });
    });

    function populatePatientModal(data) {
        const personalInfo = data.personal_information || {};
        const medicalInfo = data.medical_information || {};
        const emergencyContact = data.emergency_contact || {};
        const patient = data.patient || {};
        const appointment = data.appointment || {};
        
        // Get name - prioritize appointment name (matches table display), then personal info, then patient name
        let fullName = '';
        if (appointment.first_name || appointment.last_name) {
            // Use appointment name format (matches table display): first_name + middle_initial. + last_name
            const middlePart = appointment.middle_initial ? appointment.middle_initial + '. ' : '';
            fullName = `${appointment.first_name || ''} ${middlePart}${appointment.last_name || ''}`.trim();
        } else if (personalInfo.full_name) {
            fullName = personalInfo.full_name;
        } else if (personalInfo.first_name || personalInfo.last_name) {
            // Use personal info format with period after middle initial
            const middlePart = personalInfo.middle_initial ? personalInfo.middle_initial + '. ' : '';
            fullName = `${personalInfo.first_name || ''} ${middlePart}${personalInfo.last_name || ''}`.trim();
        } else {
            fullName = patient.name || 'N/A';
        }
        
        // Get birthday
        let birthday = '';
        if (personalInfo.birthday) {
            const bday = new Date(personalInfo.birthday);
            birthday = bday.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        } else if (patient.date_of_birth) {
            const bday = new Date(patient.date_of_birth);
            birthday = bday.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        }
        
        const address = personalInfo.address || patient.address || appointment.address || '';
        const contactNumber = personalInfo.contact_number || patient.phone || patient.contact_phone || '';
        const email = patient.email || '';
        
        // Build modal HTML
        const modalHTML = `
            <!-- PERSONAL INFORMATION Section -->
            <div class="patient-form-section">
                <div class="patient-section-header">PERSONAL INFORMATION</div>
                
                <div class="patient-form-group">
                    <label for="modal-name">Name</label>
                    <input type="text" id="modal-name" value="${fullName}" readonly>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label for="modal-birthday">Birthday</label>
                        <input type="text" id="modal-birthday" value="${birthday}" readonly>
                    </div>
                    <div class="patient-form-group">
                        <label for="modal-address">Address</label>
                        <input type="text" id="modal-address" value="${address}" readonly>
                    </div>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label for="modal-contact-number">Contact No</label>
                        <input type="text" id="modal-contact-number" value="${contactNumber}" readonly>
                    </div>
                    <div class="patient-form-group">
                        <label for="modal-email">Email address</label>
                        <input type="email" id="modal-email" value="${email}" readonly>
                    </div>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label>Civil Status</label>
                        <div class="patient-radio-group">
                            <label>
                                <input type="radio" name="modal-civil-status" value="Single" ${personalInfo.civil_status === 'Single' ? 'checked' : ''} disabled>
                                Single
                            </label>
                            <label>
                                <input type="radio" name="modal-civil-status" value="Married" ${personalInfo.civil_status === 'Married' ? 'checked' : ''} disabled>
                                Married
                            </label>
                        </div>
                    </div>
                    <div class="patient-form-group">
                        <label>Sex</label>
                        <div class="patient-radio-group">
                            <label>
                                <input type="radio" name="modal-sex" value="Male" ${patient.gender && patient.gender.toLowerCase() === 'male' ? 'checked' : ''} disabled>
                                Male
                            </label>
                            <label>
                                <input type="radio" name="modal-sex" value="Female" ${patient.gender && patient.gender.toLowerCase() === 'female' ? 'checked' : ''} disabled>
                                Female
                            </label>
                        </div>
                    </div>
                </div>

                <div class="patient-form-group">
                    <label for="modal-preferred-pronoun">Preferred pronoun</label>
                    <input type="text" id="modal-preferred-pronoun" value="${personalInfo.preferred_pronoun || ''}" readonly>
                </div>
            </div>

            <!-- PERTINENT MEDICAL INFORMATION Section -->
            <div class="patient-form-section">
                <div class="patient-section-header">PERTINENT MEDICAL INFORMATION</div>
                
                <div class="patient-form-group">
                    <label>Comorbids</label>
                    <div class="patient-checkbox-group">
                        <label>
                            <input type="checkbox" id="modal-hypertension" ${medicalInfo.hypertension ? 'checked' : ''} disabled>
                            Hypertension
                        </label>
                        <label>
                            <input type="checkbox" id="modal-diabetes" ${medicalInfo.diabetes ? 'checked' : ''} disabled>
                            Diabetes
                        </label>
                    </div>
                    <div style="margin-top: 0.5rem;">
                        <input type="text" id="modal-comorbidities-others" value="${medicalInfo.comorbidities_others || ''}" placeholder="Others, please specify:" readonly style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 3px;">
                    </div>
                </div>

                <div class="patient-form-group">
                    <label>Allergics</label>
                    <div class="patient-checkbox-group">
                        <label>
                            <input type="checkbox" id="modal-allergies-medications" ${medicalInfo.allergies && medicalInfo.allergies.includes('Medications') ? 'checked' : ''} disabled>
                            Medications
                        </label>
                        <label>
                            <input type="checkbox" id="modal-allergies-anesthetics" ${medicalInfo.allergies && medicalInfo.allergies.includes('Anesthetics') ? 'checked' : ''} disabled>
                            Anesthetics
                        </label>
                    </div>
                    <div style="margin-top: 0.5rem;">
                        <input type="text" id="modal-allergies-others" value="${medicalInfo.anesthetics_others || (medicalInfo.allergies && !medicalInfo.allergies.includes('Medications') && !medicalInfo.allergies.includes('Anesthetics') ? medicalInfo.allergies : '')}" placeholder="Others, please specify:" readonly style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 3px;">
                    </div>
                </div>

                <div class="patient-form-group">
                    <label for="modal-previous-hospitalizations">Previous hospitalizations / surgeries</label>
                    <textarea id="modal-previous-hospitalizations" readonly>${medicalInfo.previous_hospitalizations_surgeries || ''}</textarea>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label>Smoker?</label>
                        <div class="patient-radio-group">
                            <label>
                                <input type="radio" name="modal-smoker" value="Yes" ${medicalInfo.smoker && medicalInfo.smoker.toLowerCase() === 'yes' ? 'checked' : ''} disabled>
                                Yes
                            </label>
                            <label>
                                <input type="radio" name="modal-smoker" value="No" ${medicalInfo.smoker && medicalInfo.smoker.toLowerCase() === 'no' ? 'checked' : ''} disabled>
                                No
                            </label>
                        </div>
                    </div>
                    <div class="patient-form-group">
                        <label>Alcoholic beverage drinker?</label>
                        <div class="patient-radio-group">
                            <label>
                                <input type="radio" name="modal-alcoholic-drinker" value="Yes" ${medicalInfo.alcoholic_drinker && medicalInfo.alcoholic_drinker.toLowerCase() === 'yes' ? 'checked' : ''} disabled>
                                Yes
                            </label>
                            <label>
                                <input type="radio" name="modal-alcoholic-drinker" value="No" ${medicalInfo.alcoholic_drinker && medicalInfo.alcoholic_drinker.toLowerCase() === 'no' ? 'checked' : ''} disabled>
                                No
                            </label>
                        </div>
                    </div>
                </div>

                <div class="patient-form-group">
                    <label for="modal-known-family-illnesses">Known family illnesses</label>
                    <textarea id="modal-known-family-illnesses" readonly>${medicalInfo.known_family_illnesses || ''}</textarea>
                </div>
            </div>

            <!-- PERSON TO CONTACT IN CASE OF EMERGENCY Section -->
            <div class="patient-form-section">
                <div class="patient-section-header">PERSON TO CONTACT IN CASE OF EMERGENCY</div>
                
                <div class="patient-form-group">
                    <label for="modal-emergency-name">Name</label>
                    <input type="text" id="modal-emergency-name" value="${emergencyContact.name || ''}" readonly>
                </div>

                <div class="patient-form-row">
                    <div class="patient-form-group">
                        <label for="modal-emergency-relationship">Relationship</label>
                        <input type="text" id="modal-emergency-relationship" value="${emergencyContact.relationship || ''}" readonly>
                    </div>
                    <div class="patient-form-group">
                        <label for="modal-emergency-address">Address</label>
                        <input type="text" id="modal-emergency-address" value="${emergencyContact.address || ''}" readonly>
                    </div>
                </div>

                <div class="patient-form-group">
                    <label for="modal-emergency-contact-number">Contact No</label>
                    <input type="text" id="modal-emergency-contact-number" value="${emergencyContact.contact_number || ''}" readonly>
                </div>
            </div>

            <!-- Certification and Signature Section -->
            <div class="patient-certification-section">
                <p class="patient-certification-text">I certify that all the information I wrote on this form are true and correct.</p>
                
                <div class="patient-signature-section">
                    <div class="patient-signature-field">
                        <label>Signature over Printed Name</label>
                        <div class="patient-signature-display" id="modal-signature-display">
                            ${personalInfo.signature ? 
                                `<img src="${personalInfo.signature.startsWith('data:') ? personalInfo.signature : 'data:image/png;base64,' + personalInfo.signature}" alt="Signature" style="max-width: 100%; max-height: 150px;" />` : 
                                '<span style="color: #999;">No signature available</span>'
                            }
                        </div>
                    </div>
                    <div class="patient-signature-field">
                        <label>Date</label>
                        <input type="text" id="modal-date" value="${new Date().toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' })}" readonly>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('patientModalBody').innerHTML = modalHTML;
    }


    // Handle slot deletion with AJAX
    document.querySelectorAll('.delete-slot-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this time slot?')) {
                return;
            }

            const form = this;
            const formData = new FormData(form);
            const slotId = form.getAttribute('data-slot-id');
            const submitButton = form.querySelector('button[type="submit"]');
            
            // Disable button during request
            submitButton.disabled = true;
            const originalHTML = submitButton.innerHTML;
            submitButton.innerHTML = '<i data-feather="loader"></i>';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success popup
                    notifyUser(data.message || 'Time slot deleted successfully!', 'success');
                    
                    // Remove the row from the table
                    const row = form.closest('tr');
                    if (row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            
                            // Check if table is empty
                            const tbody = document.querySelector('table tbody');
                            if (tbody && tbody.children.length === 0) {
                                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:2rem; color:#6b7280;">No time slots found.</td></tr>';
                            }
                            
                            // Reload page to refresh stats
                            window.location.reload();
                        }, 300);
                    } else {
                        // Fallback: reload page
                        window.location.reload();
                    }
                } else {
                    notifyUser(data.message || 'Failed to delete time slot.', 'error');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalHTML;
                    if (window.feather && typeof window.feather.replace === 'function') {
                        window.feather.replace();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                notifyUser('An error occurred while deleting the time slot. Please try again.', 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = originalHTML;
                if (window.feather && typeof window.feather.replace === 'function') {
                    window.feather.replace();
                }
            });
        });
    });
});
</script>
@endpush