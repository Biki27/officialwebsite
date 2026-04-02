<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!--
<style>
    /* Unified Card Container */
    .unified-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    /* Seamless Purple Header */
    .card-header-purple {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        padding: 1.8rem 2rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-purple h4 {
        font-weight: 700;
        margin-bottom: 0.2rem;
        font-size: 1.5rem;
        letter-spacing: -0.5px;
    }
    
    .card-header-purple p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }
    
    /* Premium Table Styling */
    .table { margin-bottom: 0; }
    
    .table th {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.8px;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff; 
    }
    
    .table td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
    }
    
    .table tbody tr { transition: all 0.2s ease; }
    .table tbody tr:hover { background-color: #f8fafc; }
    
    /* Custom UI Elements */
    .month-text {
        color: #3b82f6;
        font-weight: 700;
    }
    
    .btn-download {
        background-color: transparent;
        color: #3b82f6;
        border: 1px solid #3b82f6;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 50rem;
        padding: 0.4rem 1.2rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-download:hover {
        background-color: #3b82f6;
        color: #ffffff;
    }
    
    /* Financial Typography */
    .amt-gross { color: #10b981; font-weight: 600; }
    .amt-deduct { color: #ef4444; font-weight: 600; }
    .amt-net { font-weight: 800; font-size: 1.15rem; color: #1e293b; }
    .text-muted-dark { color: #475569; font-weight: 500; font-size: 0.9rem;}
</style>

<div class="container py-4">
    
    <div class="unified-card">
        
        <div class="card-header-purple">
            <div>
                <h4><i class="fas fa-file-invoice-dollar me-2"></i> My Salary Slips</h4>
                <p>View and download your monthly payment records.</p>
            </div>
            <i class="fas fa-wallet fa-3x opacity-50"></i>
        </div>
        

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">SALARY MONTH</th>
                        <th>GENERATED ON</th>
                        <th>GROSS PAY</th>
                        <th>DEDUCTIONS</th>
                        <th>NET PAYABLE</th>
                        <th class="text-center pe-4">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($slips)): ?>
                        <?php foreach ($slips as $slip): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="month-text">
                                    <?= date('F Y', strtotime($slip->slip_month)) ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-muted-dark">
                                    <?= date('d M Y, h:i A', strtotime($slip->generated_on)) ?>
                                </span>
                            </td>
                            <td class="amt-gross">+ ₹<?= number_format($slip->gross_earnings, 2) ?></td>
                            <td class="amt-deduct">- ₹<?= number_format($slip->total_deductions, 2) ?></td>
                            <td class="amt-net">₹<?= number_format($slip->net_salary, 2) ?></td>
                            <td class="text-center pe-4">
                                <a href="<?= base_url('Employee/viewMySlip/' . $slip->slip_id) ?>" target="_blank" class="btn-download">
                                    <i class="fas fa-download me-1"></i> Download PDF
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25 text-primary"></i>
                                    <h5 class="fw-bold text-dark mb-1">No salary slips generated yet</h5>
                                    <p class="mb-0 small">When HR processes your salary, your slips will automatically appear here.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>
-->
<style>
    /* Global Stability */
    html,
    body {
        overflow-x: hidden;
        background: #f4f7fe;
        font-family: 'Inter', sans-serif;
    }

    /* Card Styling */
    .card {
        background: #ffffff;
        border: none;
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        transform: none !important;
        /* Prevents jumping */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06) !important;
    }

    /* Table Fixes */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .table th {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 1rem 1.5rem;
        white-space: nowrap;
    }

    .table td {
        padding: 1.25rem 1.5rem;
        white-space: nowrap;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8fafc !important;
        transform: none !important;
    }

    /* Text & Colors */
    .month-text {
        color: #3b82f6;
        font-weight: 700;
    }

    .amt-gross {
        color: #10b981;
        font-weight: 600;
    }

    .amt-deduct {
        color: #ef4444;
        font-weight: 600;
    }

    .amt-net {
        font-weight: 800;
        font-size: 1.1rem;
        color: #1e293b;
    }

    .btn-download {
        background-color: transparent;
        color: #3b82f6;
        border: 1px solid #3b82f6;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 50rem;
        padding: 0.4rem 1.2rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-download:hover {
        background-color: #3b82f6;
        color: white;
    }
</style>

<div class="container py-4">

    <div class="mb-4 p-3 rounded-4 shadow-sm text-white" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
        <h4 class="fw-bold mb-1">
            <i class="fas fa-file-invoice-dollar me-2"></i>My Salary Slips
        </h4>
        <small class="opacity-75">View and download your monthly payment records</small>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="p-4 border-bottom bg-white">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-list me-2 text-primary"></i>Payment History
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Salary Month</th>
                        <th>Generated On</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Payable</th>
                        <th class="text-center pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($slips)): ?>
                        <?php foreach ($slips as $slip): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="month-text">
                                        <?= date('F Y', strtotime($slip->slip_month)) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted-dark">
                                        <?= date('d M Y, h:i A', strtotime($slip->generated_on)) ?>
                                    </span>
                                </td>
                                <td class="amt-gross">+ ₹<?= number_format($slip->gross_earnings, 2) ?></td>
                                <td class="amt-deduct">- ₹<?= number_format($slip->total_deductions, 2) ?></td>
                                <td class="amt-net">₹<?= number_format($slip->net_salary, 2) ?></td>
                                <td class="text-center pe-4">
                                    <a href="<?= base_url('Employee/viewMySlip/' . $slip->slip_id) ?>" target="_blank"
                                        class="btn-download">
                                        <i class="fas fa-download me-1"></i> Download PDF
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25 text-primary"></i>
                                <h5 class="fw-bold text-dark mb-1">No salary slips generated yet</h5>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>