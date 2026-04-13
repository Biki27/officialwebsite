<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payroll Management | HR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        /* ══════════════ BASE ══════════════ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            background: #f0f2f9;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #1e293b;
        }

        .main-content {
            margin-left: 260px;
            padding: 32px 36px;
            min-height: 100vh;
        }

        /* ══════════════ METRIC CARDS ══════════════ */
        .metric-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px 22px;
            border: 1px solid #e8edf5;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform .2s, box-shadow .2s;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.1);
        }

        .metric-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .metric-icon.purple {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
        }

        .metric-icon.green {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .metric-icon.amber {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .metric-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .metric-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
        }

        .metric-value.green {
            color: #10b981;
        }

        .metric-value.amber {
            color: #f59e0b;
        }

        /* ══════════════ TABLE CARD ══════════════ */
        .table-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e8edf5;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .table-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .table-card-header h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        /* Search */
        .search-wrap {
            position: relative;
        }

        .search-wrap .fas {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: .8rem;
        }

        .search-wrap input {
            height: 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 14px 0 34px;
            font-family: inherit;
            font-size: .85rem;
            background: #f8fafc;
            color: #1e293b;
            width: 240px;
            transition: border-color .2s, box-shadow .2s;
        }

        .search-wrap input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .1);
            outline: none;
            background: #fff;
        }

        /* Table */
        .table thead th {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            padding: 14px 16px;
            border: none;
            white-space: nowrap;
        }

        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #475569;
        }

        .table tbody tr {
            transition: background .15s;
        }

        .table tbody tr:hover {
            background: #fafbff;
        }

        .table tbody tr.processed {
            background: #f8fafc;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Emp ID badge */
        .emp-id-badge {
            font-family: monospace;
            font-weight: 700;
            background: rgba(79, 70, 229, .08);
            color: #4f46e5;
            border-radius: 6px;
            padding: 3px 8px;
            font-size: .82rem;
        }

        /* Status badges */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-generated {
            background: #dcfce7;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .status-pending {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* Action buttons */
        .btn-process {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 6px 16px;
            font-size: .82rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            white-space: nowrap;
            transition: opacity .2s, transform .2s;
        }

        .btn-process:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        .btn-view {
            background: #dcfce7;
            color: #047857;
            border: 1.5px solid #a7f3d0;
            border-radius: 10px;
            padding: 5px 12px;
            font-size: .82rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
            transition: all .2s;
        }

        .btn-view:hover {
            background: #10b981;
            color: #fff;
            border-color: #10b981;
        }

        .btn-edit-slip {
            background: #fff;
            color: #ef4444;
            border: 1.5px solid #fca5a5;
            border-radius: 10px;
            padding: 5px 12px;
            font-size: .82rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            white-space: nowrap;
            transition: all .2s;
        }

        .btn-edit-slip:hover {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }

        /* ══════════════ STEPPER MODAL ══════════════ */
        .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .18);
        }

        /* Step indicator */
        .step-header {
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            padding: 20px 28px;
        }

        .step-header .emp-info-bar {
            color: #c7d2fe;
            font-size: .85rem;
            margin-bottom: 14px;
        }

        .step-header .emp-info-bar strong {
            color: #fff;
            font-size: 1rem;
        }

        .steps-row {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .78rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .5);
            flex: 1;
        }

        .step-item.active {
            color: #fff;
        }

        .step-item.done {
            color: #a5f3fc;
        }

        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            font-weight: 800;
            flex-shrink: 0;
            background: rgba(255, 255, 255, .15);
            border: 2px solid rgba(255, 255, 255, .2);
            transition: all .3s;
        }

        .step-item.active .step-num {
            background: #4f46e5;
            border-color: #818cf8;
            color: #fff;
        }

        .step-item.done .step-num {
            background: #10b981;
            border-color: #34d399;
            color: #fff;
        }

        .step-connector {
            flex: 0 0 24px;
            height: 2px;
            background: rgba(255, 255, 255, .15);
            margin: 0 4px;
            border-radius: 2px;
            transition: background .3s;
        }

        .step-connector.done {
            background: #34d399;
        }

        /* Step panels */
        .step-panel {
            display: none;
        }

        .step-panel.active {
            display: block;
            animation: fadeIn .25s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* Step panel body */
        .modal-body {
            padding: 24px 28px 12px;
            max-height: 68vh;
            overflow-y: auto;
        }

        .modal-body::-webkit-scrollbar {
            width: 5px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        /* Section label */
        .section-label {
            font-size: .68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        /* Field rows */
        .field-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 12px;
            border-radius: 10px;
            margin-bottom: 4px;
            transition: background .15s;
        }

        .field-row:hover {
            background: #f8fafc;
        }

        .field-row.earning-row:hover {
            background: rgba(16, 185, 129, .04);
        }

        .field-row.deduction-row:hover {
            background: rgba(239, 68, 68, .04);
        }

        .field-label-wrap {
            flex: 1;
            min-width: 0;
        }

        .field-name {
            font-weight: 600;
            color: #1e293b;
            font-size: .88rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-desc {
            font-size: .75rem;
            color: #94a3b8;
            margin-top: 2px;
            line-height: 1.4;
        }

        /* Tooltip icon */
        .tip-icon {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .6rem;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .2s;
        }

        .tip-icon:hover {
            background: #4f46e5;
            color: #fff;
        }

        /* Salary inputs */
        .salary-input-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .currency-symbol {
            font-weight: 700;
            color: #64748b;
            font-size: .9rem;
            flex-shrink: 0;
        }

        .salary-input {
            width: 130px;
            height: 36px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 10px;
            text-align: right;
            font-family: 'Inter', monospace;
            font-size: .88rem;
            font-weight: 600;
            color: #1e293b;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
        }

        .salary-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .1);
        }

        .salary-input.earning-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .1);
        }

        .salary-input.deduction-input:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .1);
        }

        .salary-input.has-value {
            border-color: #10b981;
            background: rgba(16, 185, 129, .03);
        }

        .salary-input.has-value.deduction-input {
            border-color: #f87171;
            background: rgba(239, 68, 68, .03);
        }

        /* Reason note (deductions) */
        .reason-note {
            font-size: .75rem;
            color: #64748b;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 8px;
            margin-top: 4px;
            display: none;
            /* shown when value > 0 */
        }

        .reason-note input {
            border: none;
            background: transparent;
            outline: none;
            font-family: inherit;
            font-size: inherit;
            color: #475569;
            width: 100%;
        }

        /* Live totals bar */
        .totals-bar {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            padding: 16px 28px;
            border-top: 1px solid #f1f5f9;
            background: #fafbff;
            position: sticky;
            bottom: 0;
        }

        .total-box {
            border-radius: 12px;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
        }

        .total-box.gross-box {
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .total-box.deduct-box {
            background: rgba(239, 68, 68, .08);
            border: 1px solid rgba(239, 68, 68, .2);
        }

        .total-box.net-box {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
        }

        .total-label {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            opacity: .75;
        }

        .total-amount {
            font-size: 1.2rem;
            font-weight: 800;
            line-height: 1.2;
            margin-top: 2px;
        }

        .total-box.gross-box .total-amount {
            color: #10b981;
        }

        .total-box.deduct-box .total-amount {
            color: #ef4444;
        }

        .total-box.net-box .total-label {
            color: rgba(255, 255, 255, .8);
        }

        .total-box.net-box .total-amount {
            color: #fff;
        }

        .net-warning {
            font-size: .72rem;
            color: #fbbf24;
            margin-top: 2px;
            display: none;
        }

        /* Deduction % bar */
        .deduction-pct-bar {
            height: 5px;
            border-radius: 3px;
            background: #e2e8f0;
            margin-top: 6px;
            overflow: hidden;
        }

        .deduction-pct-fill {
            height: 100%;
            border-radius: 3px;
            transition: width .4s, background .4s;
            background: linear-gradient(90deg, #10b981, #f59e0b, #ef4444);
        }

        /* Modal footer */
        .modal-footer-custom {
            padding: 14px 28px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #fff;
        }

        .btn-modal {
            height: 40px;
            border-radius: 10px;
            font-family: inherit;
            font-weight: 600;
            font-size: .875rem;
            cursor: pointer;
            border: none;
            padding: 0 22px;
            transition: all .2s;
        }

        .btn-prev {
            background: #f1f5f9;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
        }

        .btn-prev:hover {
            background: #e2e8f0;
        }

        .btn-next {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
        }

        .btn-next:hover {
            opacity: .9;
        }

        .btn-cancel {
            background: transparent;
            color: #94a3b8;
            border: none;
        }

        .btn-cancel:hover {
            color: #ef4444;
        }

        .btn-generate {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
        }

        .btn-generate:hover {
            opacity: .9;
        }

        .btn-generate:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            opacity: 1;
        }

        /* Step 1 — Employee Info Preview */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 16px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e8edf5;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .info-card-label {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #94a3b8;
            margin-bottom: 3px;
        }

        .info-card-value {
            font-size: .9rem;
            font-weight: 600;
            color: #1e293b;
        }

        .bank-missing-badge {
            background: rgba(239, 68, 68, .08);
            border: 1px solid rgba(239, 68, 68, .2);
            color: #ef4444;
            font-size: .75rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
        }

        .bank-ok-badge {
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
            color: #10b981;
            font-size: .75rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
        }

        /* Step 3 — Preview Table */
        .preview-section {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            margin-bottom: 14px;
        }

        .preview-section-head {
            padding: 8px 14px;
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .preview-section-head.earn {
            background: rgba(16, 185, 129, .1);
            color: #059669;
        }

        .preview-section-head.ded {
            background: rgba(239, 68, 68, .1);
            color: #dc2626;
        }

        .preview-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: .85rem;
        }

        .preview-row:last-child {
            border-bottom: none;
        }

        .preview-row .pv-label {
            color: #475569;
        }

        .preview-row .pv-reason {
            font-size: .72rem;
            color: #94a3b8;
            margin-top: 1px;
        }

        .preview-row .pv-amount {
            font-weight: 700;
        }

        .preview-row .pv-amount.earn {
            color: #10b981;
        }

        .preview-row .pv-amount.ded {
            color: #ef4444;
        }

        .preview-row .pv-amount.zero {
            color: #cbd5e1;
        }

        .preview-net {
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            color: #fff;
            padding: 14px;
            text-align: center;
            border-radius: 10px;
            margin-top: 14px;
        }

        .preview-net .label {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .8px;
            opacity: .75;
        }

        .preview-net .amount {
            font-size: 1.8rem;
            font-weight: 800;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 80px 16px 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .salary-input {
                width: 110px;
            }
        }

        @media (max-width: 576px) {
            .totals-bar {
                grid-template-columns: 1fr;
            }
        }
         
        /* ═══════════════════════════════════════════════
           DATATABLES CUSTOM THEME OVERRIDES (Matched to HR Theme)
        ═══════════════════════════════════════════════ */
        .dataTables_wrapper { padding: 20px 24px; }
        .dataTables_wrapper .row:first-child { margin-bottom: 20px; align-items: center; }
        .dataTables_filter input {
            background-color: #f8fafc; border: 1.5px solid #e2e8f0; color: #1e293b;
            font-size: 0.875rem; border-radius: 10px; padding: 8px 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02); transition: all 0.3s ease; width: 250px;
        }
        .dataTables_filter input:focus {
            outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); background: #fff;
        }
        .dataTables_length select {
            background-color: #f8fafc; border: 1.5px solid #e2e8f0; color: #1e293b;
            font-size: 0.875rem; border-radius: 10px; padding: 6px 32px 6px 12px; margin: 0 8px;
        }
        .dataTables_length select:focus { outline: none; border-color: #4f46e5; }
        .dataTables_wrapper .row:last-child { margin-top: 20px; align-items: center; }
        .dataTables_info { font-size: 0.85rem; color: #94a3b8 !important; font-weight: 600; }
        .dataTables_paginate .pagination { margin: 0; gap: 4px; }
        .dataTables_paginate .page-item .page-link {
            border: none; color: #64748b; background-color: transparent; border-radius: 8px;
            font-weight: 600; font-size: 0.85rem; padding: 8px 14px; transition: all 0.2s ease;
        }
        .dataTables_paginate .page-item:not(.active):not(.disabled) .page-link:hover {
            background-color: #f1f5f9; color: #4f46e5;
        }
        .dataTables_paginate .page-item.active .page-link {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important; color: #fff !important;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }
        /* Fix sorting arrows */
        table.dataTable thead .sorting:before, table.dataTable thead .sorting_asc:before, 
        table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_asc_disabled:before, 
        table.dataTable thead .sorting_desc_disabled:before, table.dataTable thead .sorting:after, 
        table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, 
        table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after {
            bottom: 12px !important; opacity: 0.4;
        }
        table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_desc:after {
            opacity: 1; color: #fff;
        }
    </style>
    </style>
</head>

<body>

    <?php
    // ── Flash Messages ──
    if ($this->session->flashdata('error')): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: <?= json_encode($this->session->flashdata('error')) ?>,
                    confirmButtonColor: '#ef4444'
                });
            });
        </script>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success')): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true
                    })
                    .fire({
                        icon: 'success',
                        title: <?= json_encode($this->session->flashdata('success')) ?>
                    });
            });
        </script>
    <?php endif; ?>

    <div class="main-content">

        <!-- ══ PAGE HEADER ══ -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1" style="font-size:1.45rem;letter-spacing:-.3px;">
                    <i class="fas fa-money-check-alt me-2" style="color:#4f46e5;"></i>Payroll Processing
                </h3>
                <p class="text-muted mb-0" style="font-size:.85rem;">Generate and manage salary slips ·
                    <?= date('F Y', strtotime($selected_month)) ?>
                </p>
            </div>

            <form action="<?= base_url('Employee/salaryManagement') ?>" method="GET"
                style="display:flex;align-items:center;gap:10px;background:#fff;padding:10px 18px;border-radius:12px;border:1.5px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                <i class="fas fa-calendar-alt" style="color:#4f46e5;"></i>
                <label class="fw-bold text-muted"
                    style="font-size:.7rem;text-transform:uppercase;letter-spacing:.6px;margin:0;">Payroll Month</label>
                <input type="month" name="month" value="<?= $selected_month ?>"
                    style="border:none;background:transparent;font-family:inherit;font-weight:700;color:#4f46e5;cursor:pointer;font-size:.9rem;"
                    onchange="this.form.submit()">
            </form>
        </div>

        <!-- ══ METRIC CARDS ══ -->
        <!--
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon purple"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="metric-label">Total Employees</div>
                        <div class="metric-value"><?= $total_emps ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon green"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="metric-label">Slips Generated</div>
                        <div class="metric-value green"><?= $processed_count ?></div>
                    </div>
                    
                    <?php if ($total_emps > 0): ?>
                        <div style="margin-left:auto;width:44px;height:44px;position:relative;">
                            <?php $pct = round(($processed_count / $total_emps) * 100); ?>
                            <svg viewBox="0 0 36 36" style="transform:rotate(-90deg);">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3" />
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#10b981" stroke-width="3"
                                    stroke-dasharray="<?= round(($pct / 100) * 100) ?> 100" stroke-dashoffset="0"
                                    stroke-linecap="round" />
                            </svg>
                            <span
                                style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:.6rem;font-weight:800;color:#10b981;"><?= $pct ?>%</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon amber"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <div class="metric-label">Pending</div>
                        <div class="metric-value amber"><?= $pending_count ?></div>
                    </div>
                </div>
            </div>
        </div>
                    -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-icon purple"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="metric-label">Total Employees</div>
                        <div class="metric-value"><?= $total_emps ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="metric-card" style="border-left: 4px solid #4f46e5;">
                    <div class="metric-icon purple"><i class="fas fa-indian-rupee-sign"></i></div>
                    <div>
                        <div class="metric-label">Total Disbursed</div>
                        <div class="metric-value" style="color:#4f46e5;">₹<?= number_format($total_salary_cost, 2) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-icon green"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="metric-label">Slips Generated</div>
                        <div class="metric-value green"><?= $processed_count ?></div>
                    </div>
                    <?php if ($total_emps > 0): ?>
                        <div style="margin-left:auto;width:40px;height:40px;position:relative;">
                            <?php $pct = round(($processed_count / $total_emps) * 100); ?>
                            <svg viewBox="0 0 36 36" style="transform:rotate(-90deg);">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3" />
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#10b981" stroke-width="3"
                                    stroke-dasharray="<?= $pct ?> 100" stroke-dashoffset="0" stroke-linecap="round" />
                            </svg>
                            <span
                                style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:.55rem;font-weight:800;color:#10b981;"><?= $pct ?>%</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-icon amber"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <div class="metric-label">Pending</div>
                        <div class="metric-value amber"><?= $pending_count ?></div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ══ EMPLOYEE TABLE ══ -->
        <div class="table-card">
            <div class="table-card-header" style="justify-content: space-between;">
                <h5><i class="fas fa-list me-2" style="color:#4f46e5;"></i>Employee Roster</h5>

                <a href="<?= base_url('Employee/exportSalaryReportCSV?month=' . $selected_month) ?>" class="btn btn-outline-success d-flex align-items-center gap-2" style="font-weight: 600; padding: 6px 14px; border-radius: 8px; border: 1.5px solid #10b981; color: #10b981; text-decoration: none; background: transparent; transition: all 0.2s;" onmouseover="this.style.background='#10b981'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#10b981';">
                    <i class="fas fa-file-csv"></i> Export Payroll to CSV
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="payrollTable">
                    <thead>
                        <tr>
                            <th class="ps-4">Emp ID</th>
                            <th>Employee</th>
                            <th>Base Salary</th>
                            <th>Bank</th>
                            <th>Status · <?= date('M Y', strtotime($selected_month)) ?></th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $emp):
                            $is_processed = isset($monthly_slips[$emp->seemp_id]);
                            $slip_data = $is_processed ? $monthly_slips[$emp->seemp_id] : null;
                            $has_bank = !empty($emp->sebank_ac_no);
                        ?>
                            <tr class="<?= $is_processed ? 'processed' : '' ?>">
                                <td class="ps-4">
                                    <span class="emp-id-badge"><?= htmlspecialchars($emp->seemp_id) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold" style="color:#1e293b;"><?= htmlspecialchars($emp->seempd_name) ?>
                                    </div>
                                    <div class="text-muted" style="font-size:.78rem;">
                                        <?= htmlspecialchars($emp->seempd_designation) ?> &bull;
                                        <?= htmlspecialchars($emp->seemp_branch) ?>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        style="font-weight:700;color:#1e293b;">₹<?= number_format($emp->seempd_salary, 2) ?></span>
                                </td>
                                <td>
                                    <?php if ($has_bank): ?>
                                        <span class="bank-ok-badge"><i class="fas fa-check me-1"></i>On file</span>
                                    <?php else: ?>
                                        <span class="bank-missing-badge"><i class="fas fa-exclamation me-1"></i>Missing</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($is_processed): ?>
                                        <span class="status-pill status-generated">
                                            <i class="fas fa-check-circle"></i> Generated
                                        </span>
                                        <?php if ($slip_data): ?>
                                            <div style="font-size:.72rem;color:#94a3b8;margin-top:3px;">
                                                Net: <strong
                                                    style="color:#10b981;">₹<?= number_format($slip_data->net_salary, 2) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="status-pill status-pending">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <?php if ($is_processed): ?>
                                            <a href="<?= base_url('Employee/viewMySlip/' . $slip_data->slip_id) ?>"
                                                target="_blank" class="btn-view">
                                                <i class="fas fa-file-pdf me-1"></i>View Slip
                                            </a>
                                            <button class="btn-edit-slip"
                                                onclick="confirmResetSlip('<?= $slip_data->slip_id ?>','<?= htmlspecialchars($emp->seempd_name, ENT_QUOTES) ?>')">
                                                <i class="fas fa-undo me-1"></i>Edit
                                            </button>
                                        <?php else: ?>
                                            <?php if ($has_bank): ?>
                                                <button class="btn-process" onclick="openSlipModal(
                                                        '<?= addslashes($emp->seemp_id) ?>',
                                                        '<?= addslashes($emp->seempd_name) ?>',
                                                        '<?= addslashes($emp->seempd_designation) ?>',
                                                        '<?= addslashes($emp->seemp_branch) ?>',
                                                        '<?= $emp->seempd_salary ?>',
                                                        '<?= addslashes($emp->sebank_ac_no ?? '') ?>',
                                                        '<?= addslashes($emp->sebank_ifsc ?? '') ?>',
                                                        '<?= addslashes($emp->sebank_esi ?? '') ?>',
                                                        '<?= $selected_month ?>'
                                                    )">
                                                    <i class="fas fa-calculator me-1"></i>Process Slip
                                                </button>
                                            <?php else: ?>
                                                <div class="d-flex flex-column align-items-center">
                                                    <button class="btn-process"
                                                        style="background: #cbd5e1; cursor: not-allowed; opacity: 1;" disabled
                                                        title="Bank details missing">
                                                        <i class="fas fa-lock me-1"></i>Locked
                                                    </button>
                                                    <span
                                                        style="font-size: 0.7rem; color: #ef4444; margin-top: 4px; text-align: center; line-height: 1.2;">
                                                        Missing Bank Details<br>

                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- <tr id="noResultsRow" style="display:none;">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-search fa-2x mb-2 d-block opacity-25"></i>No employees match your
                                search.
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /main-content -->


    <!-- ══════════════════════════════════════════════════════
     SALARY SLIP GENERATOR MODAL — 3-STEP WIZARD
══════════════════════════════════════════════════════ -->
    <div class="modal fade" id="salaryModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">

                <!-- ── Step Header ── -->
                <div class="step-header">
                    <div class="emp-info-bar">
                        <strong id="hdr_name">Employee Name</strong>
                        &nbsp;·&nbsp; <span id="hdr_id"></span>
                        &nbsp;·&nbsp; <span id="hdr_desg"></span>
                        &nbsp;·&nbsp; <span id="hdr_branch"></span>
                    </div>
                    <div class="steps-row">
                        <div class="step-item active" id="si_1">
                            <div class="step-num">1</div>
                            <span class="d-none d-sm-inline">Employee Info</span>
                        </div>
                        <div class="step-connector" id="sc_1"></div>
                        <div class="step-item" id="si_2">
                            <div class="step-num">2</div>
                            <span class="d-none d-sm-inline">Earnings</span>
                        </div>
                        <div class="step-connector" id="sc_2"></div>
                        <div class="step-item" id="si_3">
                            <div class="step-num">3</div>
                            <span class="d-none d-sm-inline">Deductions</span>
                        </div>
                        <div class="step-connector" id="sc_3"></div>
                        <div class="step-item" id="si_4">
                            <div class="step-num">4</div>
                            <span class="d-none d-sm-inline">Review & Generate</span>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('Employee/generatePayslip') ?>" method="POST" target="_blank"
                    id="generateForm">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                        value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="seemp_id" id="slip_empid">
                    <input type="hidden" name="emp_name" id="slip_empname">
                    <input type="hidden" name="designation" id="slip_designation">
                    <input type="hidden" name="branch" id="slip_branch">

                    <!-- ════ STEP 1: EMPLOYEE INFO ════ -->
                    <div class="step-panel active" id="step1">
                        <div class="modal-body">
                            <div class="section-label"><i class="fas fa-user me-1"></i> Employee & Payroll Details</div>

                            <div class="info-grid">
                                <div class="info-card">
                                    <div class="info-card-label">Employee ID</div>
                                    <div class="info-card-value" id="info_empid">—</div>
                                </div>
                                <div class="info-card">
                                    <div class="info-card-label">Full Name</div>
                                    <div class="info-card-value" id="info_name">—</div>
                                </div>
                                <div class="info-card">
                                    <div class="info-card-label">Designation</div>
                                    <div class="info-card-value" id="info_desg">—</div>
                                </div>
                                <div class="info-card">
                                    <div class="info-card-label">Branch</div>
                                    <div class="info-card-value" id="info_branch">—</div>
                                </div>
                                <div class="info-card">
                                    <div class="info-card-label">Base Salary</div>
                                    <div class="info-card-value" id="info_salary" style="color:#4f46e5;">—</div>
                                </div>
                                <div class="info-card">
                                    <div class="info-card-label">Bank Account</div>
                                    <div class="info-card-value" id="info_bank">—</div>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-md-3">
                                    <label class="section-label" style="margin-bottom:6px;"><i
                                            class="fas fa-calendar me-1"></i> Salary Month</label>
                                    <input type="month" name="slip_month" id="slip_month" class="form-control fw-bold"
                                        style="color:#4f46e5;" readonly required>
                                </div>
                                <div class="col-md-2">
                                    <label class="section-label" style="margin-bottom:6px;"><i
                                            class="fas fa-calendar-day me-1"></i> Pay Days</label>
                                    <input type="number" name="pay_days" id="slip_paydays" class="form-control"
                                        value="30" min="1" max="31" required>
                                    <div style="font-size:.72rem;color:#94a3b8;margin-top:3px;">Working days this month
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="section-label" style="margin-bottom:6px;"><i
                                            class="fas fa-university me-1"></i> Bank A/C</label>
                                    <input type="text" name="bank_ac" id="slip_bankac" class="form-control"
                                        placeholder="NILL">
                                </div>
                                <div class="col-md-2">
                                    <label class="section-label" style="margin-bottom:6px;">IFSC</label>
                                    <input type="text" name="ifsc_code" id="slip_ifsc"
                                        class="form-control text-uppercase" placeholder="NILL">
                                </div>
                                <div class="col-md-2">
                                    <label class="section-label" style="margin-bottom:6px;">ESI No.</label>
                                    <input type="text" name="esi_no" id="slip_esi" class="form-control"
                                        placeholder="NILL">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ════ STEP 2: EARNINGS ════ -->
                    <div class="step-panel" id="step2">
                        <div class="modal-body">
                            <div class="section-label"><i class="fas fa-plus-circle me-1" style="color:#10b981;"></i>
                                Earnings &amp; Allowances</div>
                            <p style="font-size:.78rem;color:#94a3b8;margin-bottom:14px;">
                                All amounts below <strong>add to</strong> the employee's total pay. Fields left at ₹0
                                will show as <em>NILL</em> on the salary slip.
                            </p>

                            <!-- BASIC SALARY -->
                            <div class="field-row earning-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Basic Salary
                                        <span class="tip-icon"
                                            title="Fixed monthly base pay as per employment contract. This is the foundation for PF/ESI calculations.">?</span>
                                    </div>
                                    <div class="field-desc">Contractual fixed monthly remuneration</div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol">₹</span>
                                    <input type="number" step="0.01" min="0" name="basic" id="calc_basic"
                                        class="salary-input earning-input calc-input" value="0" placeholder="0.00">
                                </div>
                            </div>

                            <!-- TRANSPORT / HRA -->
                            <div class="field-row earning-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Transport &amp; House Rent Allowance
                                        <span class="tip-icon"
                                            title="Allowance paid for commuting and/or housing. Shown as a single combined allowance on the slip.">?</span>
                                    </div>
                                    <div class="field-desc">Travel reimbursement + HRA (if applicable)</div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol">₹</span>
                                    <input type="number" step="0.01" min="0" name="transport"
                                        class="salary-input earning-input calc-input" value="0" placeholder="0.00">
                                </div>
                            </div>

                            <!-- INCENTIVE -->
                            <!-- <div class="field-row earning-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Performance Incentive
                                        <span class="tip-icon" title="Additional pay for meeting or exceeding targets. Not part of the fixed salary structure.">?</span>
                                    </div>
                                    <div class="field-desc">Target-based bonus or ad-hoc incentive for this month</div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol">₹</span>
                                    <input type="number" step="0.01" min="0" name="incentive"
                                        class="salary-input earning-input calc-input" value="0" placeholder="0.00">
                                </div>
                            </div> -->
                            <div class="field-row earning-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">Performance Incentive</div>
                                    <div class="field-desc">Monthly performance-based reward</div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol">₹</span>
                                    <input type="number" name="incentive" class="salary-input earning-input calc-input"
                                        value="0">
                                </div>
                            </div>

                            <div class="field-row earning-row" style="background: rgba(16, 185, 129, 0.04);">
                                <div class="field-label-wrap">
                                    <div class="field-name">Annual Bonus <i class="fas fa-lock ms-2 text-muted"
                                            style="font-size: 10px;"></i></div>
                                    <div class="field-desc">Automatically fetched from Bonus Management system</div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol">₹</span>
                                    <input type="number" name="bonus_amt" id="calc_bonus"
                                        class="salary-input earning-input calc-input" value="0" readonly
                                        style="background: #f1f5f9; border-color: #cbd5e1;">
                                </div>
                            </div>

                            <!-- OVERTIME PAY — was "Overtime/Half Day" (confusing) -->
                            <div class="field-row earning-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Overtime Pay
                                        <span class="tip-icon"
                                            title="Extra pay for hours worked beyond the standard duty schedule. Half-day deductions go in the Deductions tab, NOT here.">?</span>
                                        <span
                                            style="font-size:.68rem;background:rgba(16,185,129,.1);color:#059669;border-radius:4px;padding:1px 6px;font-weight:700;">EARNING</span>
                                    </div>
                                    <div class="field-desc">
                                        Pay for extra/overtime hours worked &nbsp;
                                    </div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol">₹</span>
                                    <input type="number" step="0.01" min="0" name="overtime"
                                        class="salary-input earning-input calc-input" value="0" placeholder="0.00">
                                </div>
                            </div>

                            <!-- ROUND OFF — moved to earnings but shown separately -->
                            <div class="field-row earning-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Round-Off Adjustment
                                        <span class="tip-icon"
                                            title="Small positive/negative amount to round the final net salary to a clean figure. E.g., if net = ₹14,999.25, add ₹0.75 here to make it ₹15,000.">?</span>
                                    </div>
                                    <div class="field-desc">Small adjustment to round net pay to a clean number (e.g.
                                        +₹0.75)</div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol">₹</span>
                                    <input type="number" step="0.01" min="-999" name="round_off"
                                        class="salary-input earning-input calc-input" value="0" placeholder="0.00">
                                </div>
                            </div>

                            <div class="mt-2 p-3 rounded-3"
                                style="background:rgba(16,185,129,.04);border:1px solid rgba(16,185,129,.15);font-size:.78rem;color:#475569;">
                                <i class="fas fa-info-circle me-1" style="color:#10b981;"></i>
                                <strong>Tip:</strong> All fields default to ₹0. Only fill in the amounts that apply for
                                this month.
                                Fields at ₹0 will print as <em>NILL</em> on the salary slip.
                            </div>
                        </div>
                    </div>

                    <!-- ════ STEP 3: DEDUCTIONS ════ -->
                    <div class="step-panel" id="step3">
                        <div class="modal-body">
                            <div class="section-label"><i class="fas fa-minus-circle me-1" style="color:#ef4444;"></i>
                                Deductions</div>
                            <p style="font-size:.78rem;color:#94a3b8;margin-bottom:14px;">
                                All amounts below are <strong>subtracted</strong> from gross earnings. Each deduction
                                prints with its label on the slip so the employee understands exactly what was deducted.
                            </p>

                            <!-- PF -->
                            <div class="field-row deduction-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Provident Fund (PF)
                                        <span class="tip-icon"
                                            title="Employee's share: 12% of Basic Salary. Mandatory under EPF Act for eligible employees. Deposited to EPFO.">?</span>
                                    </div>
                                    <div class="field-desc">Mandatory retirement fund contribution (12% of Basic) ·
                                        Deposited to EPFO</div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol" style="color:#ef4444;">₹</span>
                                    <input type="number" step="0.01" min="0" name="pf"
                                        class="salary-input deduction-input calc-input" value="0" placeholder="0.00">
                                </div>
                            </div>

                            <!-- ESI -->
                            <div class="field-row deduction-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        ESI Contribution
                                        <span class="tip-icon"
                                            title="Employee State Insurance: 0.75% of Gross Salary. Applicable if gross ≤ ₹21,000/month. Provides medical & sickness benefits.">?</span>
                                    </div>
                                    <div class="field-desc">0.75% of Gross · Employee State Insurance (medical cover) ·
                                        Applicable if gross ≤ ₹21,000</div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol" style="color:#ef4444;">₹</span>
                                    <input type="number" step="0.01" min="0" name="esi_deduction"
                                        class="salary-input deduction-input calc-input" value="0" placeholder="0.00">
                                </div>
                            </div>

                            <!-- PROFESSIONAL TAX -->
                            <div class="field-row deduction-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Professional Tax (PT)
                                        <span class="tip-icon"
                                            title="State-mandated tax on employment income. Amount varies by state and salary slab. Max ₹2,500/year.">?</span>
                                    </div>
                                    <div class="field-desc">State government employment tax · Slab-based (max
                                        ₹2,500/year)</div>
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol" style="color:#ef4444;">₹</span>
                                    <input type="number" step="0.01" min="0" name="prof_tax"
                                        class="salary-input deduction-input calc-input" value="0" placeholder="0.00">
                                </div>
                            </div>

                            <!-- LATE FEES / NPL — was "Late Fees / NPL" (confusing abbreviation) -->
                            <div class="field-row deduction-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Late Arrival &amp; No-Pay Leave (NPL)
                                        <span class="tip-icon"
                                            title="NPL = No Pay Leave. Deduction for habitual late arrivals beyond the grace period OR unpaid leave days taken without prior approval.">?</span>
                                        <span
                                            style="font-size:.68rem;background:rgba(239,68,68,.1);color:#dc2626;border-radius:4px;padding:1px 6px;font-weight:700;">DEDUCTION</span>
                                    </div>
                                    <div class="field-desc">Habitual late arrivals + unauthorized No-Pay Leave days
                                    </div>
                                    <!-- Reason input — shown when value > 0 -->
                                    <!-- <div class="reason-note" id="note_late_fees">
                                        <i class="fas fa-comment-alt me-1"></i>
                                        <input type="text" id="reason_late" placeholder="Optional: e.g. 3 late arrivals in Oct · 1 NPL day on 12th"
                                            maxlength="120" style="width:100%">
                                    </div> -->
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol" style="color:#ef4444;">₹</span>
                                    <input type="number" step="0.01" min="0" name="late_fees"
                                        class="salary-input deduction-input calc-input" value="0" placeholder="0.00"
                                        oninput="toggleReason(this,'note_late_fees')">
                                </div>
                            </div>

                            <!-- HALF DAY / LOSS OF PAY — was "Loss of Pay / Other" (ambiguous) -->
                            <div class="field-row deduction-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Half-Day &amp; Loss of Pay (LOP)
                                        <span class="tip-icon"
                                            title="LOP = Loss of Pay. Deducted for half-day absences OR full absent days without approved leave. This is NOT the same as Late Fees — Late fees are for partial lateness, LOP is for full/half day absence.">?</span>
                                        <span
                                            style="font-size:.68rem;background:rgba(239,68,68,.1);color:#dc2626;border-radius:4px;padding:1px 6px;font-weight:700;">DEDUCTION</span>
                                    </div>
                                    <div class="field-desc">Half-day absences OR full-day Loss of Pay (absent without
                                        approved leave)</div>
                                    <!-- <div class="reason-note" id="note_loss_of_pay">
                                        <i class="fas fa-comment-alt me-1"></i>
                                        <input type="text" id="reason_lop" placeholder="Optional: e.g. 2 half-days on 5th & 18th · 1 LOP on 22nd"
                                            maxlength="120">
                                    </div> -->
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol" style="color:#ef4444;">₹</span>
                                    <input type="number" step="0.01" min="0" name="loss_of_pay"
                                        class="salary-input deduction-input calc-input" value="0" placeholder="0.00"
                                        oninput="toggleReason(this,'note_loss_of_pay')">
                                </div>
                            </div>

                            <!-- LOAN -->
                            <div class="field-row deduction-row">
                                <div class="field-label-wrap">
                                    <div class="field-name">
                                        Loan Recovery (EMI)
                                        <span class="tip-icon"
                                            title="Monthly EMI instalment recovery for salary advance or office loan taken by the employee. Should match the approved loan agreement.">?</span>
                                    </div>
                                    <div class="field-desc">Monthly EMI recovery for salary advance or office/bank loan
                                    </div>
                                    <!-- <div class="reason-note" id="note_loan">
                                        <i class="fas fa-comment-alt me-1"></i>
                                        <input type="text" id="reason_loan" placeholder="Optional: e.g. Salary advance EMI #3 of 6"
                                            maxlength="120">
                                    </div> -->
                                </div>
                                <div class="salary-input-wrap">
                                    <span class="currency-symbol" style="color:#ef4444;">₹</span>
                                    <input type="number" step="0.01" min="0" name="loan"
                                        class="salary-input deduction-input calc-input" value="0" placeholder="0.00"
                                        oninput="toggleReason(this,'note_loan')">
                                </div>
                            </div>

                            <!-- Warning if deductions > 40% gross -->
                            <div id="highDeductionWarning" class="mt-2 p-3 rounded-3"
                                style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);font-size:.78rem;color:#dc2626;display:none;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>High Deduction Alert:</strong> Total deductions exceed 40% of gross earnings.
                                Please double-check the values before generating the slip.
                            </div>
                        </div>
                    </div>

                    <!-- ════ STEP 4: REVIEW & CONFIRM ════ -->
                    <div class="step-panel" id="step4">
                        <div class="modal-body">
                            <div class="section-label"><i class="fas fa-eye me-1"></i> Review Before Generating</div>
                            <p style="font-size:.78rem;color:#94a3b8;margin-bottom:14px;">
                                Verify all amounts. Once generated, the slip is saved and sent to the employee's portal.
                                Use <strong>Edit</strong> on the table to make corrections after generation.
                            </p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <!-- Earnings preview -->
                                    <div class="preview-section">
                                        <div class="preview-section-head earn">
                                            <i class="fas fa-plus-circle me-1"></i> Earnings
                                        </div>
                                        <div id="preview_earnings"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- Deductions preview -->
                                    <div class="preview-section">
                                        <div class="preview-section-head ded">
                                            <i class="fas fa-minus-circle me-1"></i> Deductions
                                        </div>
                                        <div id="preview_deductions"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Net payable -->
                            <div class="preview-net" id="preview_net">
                                <div class="label">Net Salary Payable</div>
                                <div class="amount" id="preview_net_amount">₹ 0.00</div>
                                <div style="font-size:.75rem;opacity:.7;margin-top:4px;" id="preview_month_label"></div>
                            </div>

                            <!-- Warnings -->
                            <div id="preview_warnings" class="mt-3" style="display:none;"></div>
                        </div>
                    </div>

                    <!-- ── Live Totals Bar (always visible during steps 2-3) ── -->
                    <div class="totals-bar" id="totalsBar">
                        <div class="total-box gross-box">
                            <span class="total-label">Gross Earnings</span>
                            <span class="total-amount" id="t_gross">₹ 0.00</span>
                        </div>
                        <div class="total-box deduct-box">
                            <span class="total-label">Total Deductions</span>
                            <span class="total-amount" id="t_ded">₹ 0.00</span>
                            <div class="deduction-pct-bar">
                                <div class="deduction-pct-fill" id="ded_bar" style="width:0%"></div>
                            </div>
                        </div>
                        <div class="total-box net-box">
                            <span class="total-label">Net Payable</span>
                            <span class="total-amount" id="t_net">₹ 0.00</span>
                            <div class="net-warning" id="net_warn">⚠ Deductions exceed earnings!</div>
                        </div>
                    </div>

                    <!-- ── Modal Footer ── -->
                    <div class="modal-footer-custom">
                        <button type="button" class="btn-modal btn-cancel" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <div style="display:flex;gap:10px;">
                            <button type="button" class="btn-modal btn-prev" id="prevBtn" onclick="changeStep(-1)"
                                style="display:none;">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </button>
                            <button type="button" class="btn-modal btn-next" id="nextBtn" onclick="changeStep(1)">
                                Next <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                            <button type="submit" class="btn-modal btn-generate" id="generateBtn" style="display:none;"
                                onclick="return confirmGenerate()">
                                <i class="fas fa-file-invoice-dollar me-1"></i>Generate & Open Slip
                            </button>
                        </div>
                    </div>

                </form><!-- /generateForm -->

            </div><!-- /modal-content -->
        </div>
    </div><!-- /modal -->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

     

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#payrollTable').DataTable({
                "pageLength": 25,
                "order": [[ 1, "asc" ]], // Sort by Employee Name alphabetically by default
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search by name, ID..."
                },
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "columnDefs": [
                    { "orderable": false, "targets": [3, 5] } // Disable sorting on Bank info and Action buttons
                ]
            });
        });
    
   
        const EARNINGS = [{
                name: 'basic',
                label: 'Basic Salary',
                desc: ''
            },
            {
                name: 'transport',
                label: 'Transport & HRA',
                desc: ''
            },
            {
                name: 'incentive',
                label: 'Performance Incentive',
                desc: ''
            },
            {
                name: 'bonus_amt',
                label: 'Annual Bonus',
                desc: 'Fetched from Bonus Management system'
            },
            {
                name: 'overtime',
                label: 'Overtime Pay',
                desc: 'Extra hours worked beyond duty schedule'
            },
            {
                name: 'round_off',
                label: 'Round-Off Adjustment',
                desc: ''
            },
        ];
        const DEDUCTIONS = [{
                name: 'pf',
                label: 'Provident Fund (PF)',
                desc: '12% of Basic · EPFO',
                // reasonId: 'reason_pf'
            },
            {
                name: 'esi_deduction',
                label: 'ESI Contribution',
                desc: '0.75% of Gross',
                // reasonId: null
            },
            {
                name: 'prof_tax',
                label: 'Professional Tax (PT)',
                desc: 'State slab-based',
                // reasonId: null
            },
            {
                name: 'late_fees',
                label: 'Late Arrival & No-Pay Leave (NPL)',
                desc: '',
                // reasonId: 'reason_late'
            },
            {
                name: 'loss_of_pay',
                label: 'Half-Day & Loss of Pay (LOP)',
                desc: '',
                // reasonId: 'reason_lop'
            },
            {
                name: 'loan',
                label: 'Loan Recovery (EMI)',
                desc: '',
                // reasonId: 'reason_loan'
            },
        ];

        /* ═══════════════════════════════════════════════
           MODAL OPEN
        ═══════════════════════════════════════════════ */
        const salaryModalEl = document.getElementById('salaryModal');
        const salaryModal = new bootstrap.Modal(salaryModalEl);

        function openSlipModal(id, name, designation, branch, base_salary, bank_ac, ifsc, esi, selected_month) {
            // 1. Populate hidden fields
            document.getElementById('slip_empid').value = id;
            document.getElementById('slip_empname').value = name;
            document.getElementById('slip_designation').value = designation;
            document.getElementById('slip_branch').value = branch;

            // 2. Populate info panel (Step 1)
            document.getElementById('hdr_name').textContent = name;
            document.getElementById('hdr_id').textContent = id;
            document.getElementById('hdr_desg').textContent = designation;
            document.getElementById('hdr_branch').textContent = branch;
            document.getElementById('info_empid').textContent = id;
            document.getElementById('info_name').textContent = name;
            document.getElementById('info_desg').textContent = designation;
            document.getElementById('info_branch').textContent = branch;
            document.getElementById('info_salary').textContent = '₹' + parseFloat(base_salary).toLocaleString('en-IN', {
                minimumFractionDigits: 2
            });

            const bankEl = document.getElementById('info_bank');
            if (bank_ac && bank_ac.trim() !== '') {
                bankEl.innerHTML = `<span class="bank-ok-badge"><i class="fas fa-check me-1"></i>••••${bank_ac.slice(-4)}</span>`;
            } else {
                bankEl.innerHTML = `<span class="bank-missing-badge"><i class="fas fa-exclamation me-1"></i>Not on file — please enter below</span>`;
            }

            // 3. Bank / month fields
            document.getElementById('slip_month').value = selected_month;
            document.getElementById('slip_bankac').value = bank_ac || '';
            document.getElementById('slip_ifsc').value = ifsc || '';
            document.getElementById('slip_esi').value = esi || '';

            // 4. Pre-fill basic with base salary
            document.getElementById('calc_basic').value = parseFloat(base_salary).toFixed(2);

            // 5. Reset all deduction/earning inputs to 0 (Crucial to clear previous modal data)
            [...EARNINGS, ...DEDUCTIONS].forEach(f => {
                const el = document.querySelector(`[name="${f.name}"]`);
                if (el && f.name !== 'basic') el.value = '0';
                if (el) el.classList.remove('has-value');
            });

            // 6. Reset UI states
            ['note_late_fees', 'note_loss_of_pay', 'note_loan'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });

            // 7. HANDLE BONUS (ASYNC FETCH)
            // 1. Reset the bonus field to 0 and set to Read-Only
            const bonusField = document.getElementById('calc_bonus');
            bonusField.value = '0.00';
            bonusField.classList.remove('has-value');

            // 2. Fetch the bonus for this specific employee and this specific month
            fetch('<?= base_url("Employee/getBonusForPayroll/") ?>' + id + '/' + selected_month)
                .then(response => response.json())
                .then(amount => {
                    if (amount > 0) {
                        bonusField.value = parseFloat(amount).toFixed(2);
                        bonusField.classList.add('has-value');
                    } else {
                        bonusField.value = '0.00';
                    }
                    // 3. Trigger recalculation so Gross and Net are updated immediately
                    calculateTotals();
                })
                .catch(error => {
                    console.error('Error fetching bonus:', error);
                    bonusField.value = '0.00';
                    calculateTotals();
                });

            resetSteps();
            calculateTotals(); // Initial calculation
            salaryModal.show();
        }
        /* ═══════════════════════════════════════════════
           STEP MANAGEMENT
        ═══════════════════════════════════════════════ */
        let currentStep = 1;
        const TOTAL_STEPS = 4;

        function resetSteps() {
            currentStep = 1;
            renderStep(1);
        }

        function changeStep(dir) {
            // Validate before moving forward
            if (dir > 0) {
                if (currentStep === 1 && !validateStep1()) return;
                if (currentStep === 3) buildReviewPanel();
            }
            currentStep = Math.min(Math.max(1, currentStep + dir), TOTAL_STEPS);
            renderStep(currentStep);
        }

        function renderStep(step) {
            // Panels
            document.querySelectorAll('.step-panel').forEach((p, i) => {
                p.classList.toggle('active', i + 1 === step);
            });

            // Step indicators
            for (let i = 1; i <= TOTAL_STEPS; i++) {
                const si = document.getElementById('si_' + i);
                si.classList.remove('active', 'done');
                if (i < step) si.classList.add('done');
                else if (i === step) si.classList.add('active');

                // Connectors
                const sc = document.getElementById('sc_' + i);
                if (sc) sc.classList.toggle('done', i < step);
            }

            // Step num icons
            for (let i = 1; i <= TOTAL_STEPS; i++) {
                const sn = document.getElementById('si_' + i).querySelector('.step-num');
                if (i < step) sn.innerHTML = '<i class="fas fa-check" style="font-size:.65rem;"></i>';
                else sn.textContent = i;
            }

            // Buttons
            document.getElementById('prevBtn').style.display = step > 1 ? '' : 'none';
            document.getElementById('nextBtn').style.display = step < TOTAL_STEPS ? '' : 'none';
            document.getElementById('generateBtn').style.display = step === TOTAL_STEPS ? '' : 'none';

            // Totals bar — hide on step 1 & 4
            document.getElementById('totalsBar').style.display = (step === 2 || step === 3) ? '' : 'none';

            // Update generate button state
            updateGenerateBtn();
        }

        /* ═══════════════════════════════════════════════
           VALIDATION
        ═══════════════════════════════════════════════ */
        function validateStep1() {
            const month = document.getElementById('slip_month').value;
            const days = parseInt(document.getElementById('slip_paydays').value);
            const bankac = document.getElementById('slip_bankac').value.trim();
            const ifsc = document.getElementById('slip_ifsc').value.trim();

            // Bank account validation (basic)
            if (!bankac || bankac.length < 9 || bankac.length > 18) {
                showErr('Please enter the employee\'s bank account number (9-18 characters).');
                return false;
            }
            if (!ifsc || ifsc.length !== 11) {
                showErr('Please enter the IFSC code for the employee\'s bank branch (11 characters).');
                return false;
            }
            if (!month) {
                showErr('Please select a salary month.');
                return false;
            }
            if (!days || days < 1 || days > 31) {
                showErr('Pay days must be between 1 and 31.');
                return false;
            }
            return true;
        }

        function showErr(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: msg,
                confirmButtonColor: '#4f46e5'
            });
        }

        /* ═══════════════════════════════════════════════
           LIVE CALCULATION
        ═══════════════════════════════════════════════ */
        function getVal(name) {
            const el = document.querySelector(`[name="${name}"]`);
            const v = parseFloat(el ? el.value : 0) || 0;
            return Math.max(name === 'round_off' ? -999 : 0, v); // round_off can be negative (but UI limits to -999)
        }

        function fmt(n) {
            return '₹ ' + Math.abs(n).toLocaleString('en-IN', {
                minimumFractionDigits: 2
            });
        }

        function calculateTotals() {
            const basic = getVal('basic');
            const trans = getVal('transport');
            const inc = getVal('incentive');
            const bonus = getVal('bonus_amt');
            const ot = getVal('overtime');
            const round = getVal('round_off');
            const gross = basic + trans + inc + ot + round + bonus;

            const pf = getVal('pf');
            const esi = getVal('esi_deduction');
            const pt = getVal('prof_tax');
            const late = getVal('late_fees');
            const lop = getVal('loss_of_pay');
            const loan = getVal('loan');
            const ded = pf + esi + pt + late + lop + loan;
            const net = gross - ded;

            // Totals bar
            document.getElementById('t_gross').textContent = fmt(gross);
            document.getElementById('t_ded').textContent = fmt(ded);
            document.getElementById('t_net').textContent = net >= 0 ? fmt(net) : '⚠ ' + fmt(net);

            // Deduction bar colour
            const pct = gross > 0 ? Math.min(100, (ded / gross) * 100) : 0;
            document.getElementById('ded_bar').style.width = pct + '%';

            // Net warning
            const warnEl = document.getElementById('net_warn');
            warnEl.style.display = net < 0 ? 'block' : 'none';

            // High deduction warning (step 3)
            const hdWarn = document.getElementById('highDeductionWarning');
            if (hdWarn) hdWarn.style.display = (gross > 0 && pct > 40) ? 'block' : 'none';

            // Highlight filled inputs
            document.querySelectorAll('.calc-input').forEach(el => {
                const v = parseFloat(el.value) || 0;
                if (v > 0) el.classList.add('has-value');
                else el.classList.remove('has-value');
            });

            updateGenerateBtn();
            return {
                gross,
                ded,
                net
            };
        }

        function updateGenerateBtn() {
            const btn = document.getElementById('generateBtn');
            if (!btn) return;
            const {
                net
            } = calculateTotals ? {
                net: getVal('basic') + getVal('transport') + getVal('incentive') + getVal('overtime') + getVal('round_off') - getVal('pf') - getVal('esi_deduction') - getVal('prof_tax') - getVal('late_fees') - getVal('loss_of_pay') - getVal('loan')
            } : {
                net: 0
            };
            btn.disabled = net < 0;
            btn.title = net < 0 ? 'Net salary is negative — reduce deductions or increase earnings.' : '';
        }

        // Attach live calc to all inputs
        document.querySelectorAll('.calc-input').forEach(el => {
            el.addEventListener('input', calculateTotals);
        });

        /* ═══════════════════════════════════════════════
           REASON NOTE TOGGLE
        ═══════════════════════════════════════════════ */
        function toggleReason(input, noteId) {
            const note = document.getElementById(noteId);
            if (!note) return;
            note.style.display = parseFloat(input.value) > 0 ? 'block' : 'none';
        }

        /* ═══════════════════════════════════════════════
           REVIEW PANEL (Step 4)
        ═══════════════════════════════════════════════ */
        function buildReviewPanel() {
            const {
                gross,
                ded,
                net
            } = calculateTotals();

            // Earnings preview
            let earnHtml = '';
            EARNINGS.forEach(f => {
                const v = getVal(f.name);
                earnHtml += `<div class="preview-row">
            <div><div class="pv-label">${f.label}</div></div>
            <div class="pv-amount earn ${v === 0 ? 'zero' : ''}">${v === 0 ? 'NILL' : '+ ' + fmt(v)}</div>
        </div>`;
            });
            earnHtml += `<div class="preview-row" style="background:#f8fafc;font-weight:700;">
        <div class="pv-label">Gross Total</div>
        <div class="pv-amount earn">${fmt(gross)}</div>
    </div>`;
            document.getElementById('preview_earnings').innerHTML = earnHtml;

            // Deductions preview
            let dedHtml = '';
            DEDUCTIONS.forEach(f => {
                const v = getVal(f.name);
                const reasonInput = f.reasonId ? document.getElementById(f.reasonId) : null;
                const reason = reasonInput ? reasonInput.value.trim() : '';
                dedHtml += `<div class="preview-row">
            <div>
                <div class="pv-label">${f.label}</div>
                ${reason ? `<div class="pv-reason"><i class="fas fa-comment-alt me-1"></i>${reason}</div>` : ''}
            </div>
            <div class="pv-amount ded ${v === 0 ? 'zero' : ''}">
                ${v === 0 ? 'NILL' : '- ' + fmt(v)}
            </div>
        </div>`;
            });
            dedHtml += `<div class="preview-row" style="background:#f8fafc;font-weight:700;">
        <div class="pv-label">Total Deductions</div>
        <div class="pv-amount ded">${fmt(ded)}</div>
    </div>`;
            document.getElementById('preview_deductions').innerHTML = dedHtml;

            // Net
            const netEl = document.getElementById('preview_net_amount');
            netEl.textContent = fmt(net);
            netEl.style.color = net < 0 ? '#fbbf24' : '#fff';
            document.getElementById('preview_month_label').textContent =
                'For ' + document.getElementById('slip_month').value;

            // Warnings panel
            const warns = [];
            if (net < 0) warns.push('<i class="fas fa-times-circle me-1"></i><strong>Net salary is negative</strong> — deductions exceed gross. Cannot generate slip.');
            if (getVal('basic') === 0) warns.push('<i class="fas fa-exclamation-triangle me-1"></i>Basic Salary is ₹0. This is unusual — please verify.');
            const pct = gross > 0 ? (ded / gross) * 100 : 0;
            if (pct > 40 && net >= 0) warns.push('<i class="fas fa-exclamation-triangle me-1"></i>Deductions are ' + pct.toFixed(1) + '% of gross. Please confirm this is correct.');

            const wBox = document.getElementById('preview_warnings');
            if (warns.length > 0) {
                wBox.innerHTML = warns.map(w =>
                    `<div style="padding:8px 12px;border-radius:8px;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);color:#dc2626;font-size:.8rem;margin-bottom:6px;">${w}</div>`
                ).join('');
                wBox.style.display = 'block';
            } else {
                wBox.innerHTML = `<div style="padding:10px 14px;border-radius:8px;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);color:#059669;font-size:.82rem;">
            <i class="fas fa-check-circle me-1"></i> All checks passed. Ready to generate.
        </div>`;
                wBox.style.display = 'block';
            }

            updateGenerateBtn();
        }

        /* ═══════════════════════════════════════════════
           GENERATE CONFIRM
        ═══════════════════════════════════════════════ */
        function confirmGenerate() {
            const net = (() => {
                const gross = ['basic', 'transport', 'incentive', 'overtime', 'round_off', 'bonus_amt'].reduce((s, n) => s + getVal(n), 0);
                const ded = ['pf', 'esi_deduction', 'prof_tax', 'late_fees', 'loss_of_pay', 'loan'].reduce((s, n) => s + getVal(n), 0);
                return gross - ded;
            })();
            const name = document.getElementById('slip_empname').value;
            const month = document.getElementById('slip_month').value;

            if (net < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cannot Generate',
                    text: 'Net salary is negative. Please fix deductions.',
                    confirmButtonColor: '#ef4444'
                });
                return false;
            }

            // Prevent double submit — handled by returning true (form submits normally, opens new tab)
            Swal.fire({
                title: 'Generate Salary Slip?',
                html: `<div style="text-align:left;font-size:.88rem;">
            <div style="margin-bottom:6px;"><span style="color:#94a3b8;">Employee:</span> <strong>${name}</strong></div>
            <div style="margin-bottom:6px;"><span style="color:#94a3b8;">Month:</span> <strong>${month}</strong></div>
            <div style="margin-bottom:12px;"><span style="color:#94a3b8;">Net Payable:</span> <strong style="color:#10b981;font-size:1.1rem;">${fmt(net)}</strong></div>
            <hr style="margin:8px 0;">
            <small style="color:#94a3b8;">The slip will open in a new tab for printing/downloading. The record will be saved immediately.</small>
        </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-file-invoice-dollar me-1"></i> Yes, Generate',
                cancelButtonText: 'Review Again'
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Generating Slip…',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    document.getElementById('generateForm').submit();
                    // Close modal + reload table after delay
                    setTimeout(() => {
                        salaryModal.hide();
                        location.reload();
                    }, 2500);
                }
            });
            return false; // Prevent default submit — SweetAlert handles it
        }

        /* ═══════════════════════════════════════════════
           LIVE SEARCH
        ═══════════════════════════════════════════════ */
        // document.getElementById('searchInput').addEventListener('input', function() {
        //     const filter = this.value.toLowerCase();
        //     const rows = document.querySelectorAll('#payrollTable tbody tr:not(#noResultsRow)');
        //     let visible = 0;
        //     rows.forEach(row => {
        //         const match = row.innerText.toLowerCase().includes(filter);
        //         row.style.display = match ? '' : 'none';
        //         if (match) visible++;
        //     });
        //     document.getElementById('noResultsRow').style.display = visible === 0 ? '' : 'none';
        // });

        /* ═══════════════════════════════════════════════
           EDIT / RESET SLIP
        ═══════════════════════════════════════════════ */
        function confirmResetSlip(slipId, empName) {
            Swal.fire({
                title: 'Edit Salary Slip?',
                html: `<div style="font-size:.88rem;text-align:left;">
            Removing the slip for <strong>${empName}</strong> will allow you to re-enter correct values.
            <br><br><span style="color:#ef4444;font-size:.8rem;"><i class="fas fa-exclamation-triangle me-1"></i>The saved record will be deleted immediately.</span>
        </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Delete & Re-process',
                cancelButtonText: 'Keep it'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url("Employee/deleteSlipAjax") ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            slip_id: slipId,
                            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Slip Deleted',
                                    text: 'You can now re-process this employee.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: () => Swal.fire('Error', 'Could not connect to server.', 'error')
                    });
                }
            });
        }

        /* ═══════════════════════════════════════════════
           BOOTSTRAP TOOLTIPS
        ═══════════════════════════════════════════════ */
        document.addEventListener('DOMContentLoaded', function() {
            // Simple native title tooltips on .tip-icon via Bootstrap
            const tipEls = [].slice.call(document.querySelectorAll('.tip-icon'));
            tipEls.forEach(el => {
                el.setAttribute('data-bs-toggle', 'tooltip');
                el.setAttribute('data-bs-placement', 'top');
            });
            const tooltipList = tipEls.map(el => new bootstrap.Tooltip(el, {
                trigger: 'hover'
            }));

            // Reset modal state when hidden
            salaryModalEl.addEventListener('hidden.bs.modal', function() {
                resetSteps();
            });
        });
    </script>
</body>

</html>