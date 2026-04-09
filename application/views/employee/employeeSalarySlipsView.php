<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
    /* ── Salary Slips Page Styles ── */
    .slips-page { padding-bottom: 3rem; }

    .slips-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    .slips-table { width: 100%; border-collapse: collapse; }
    .slips-table thead th {
        background: #f8fafc;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #94a3b8;
        padding: 12px 18px;
        white-space: nowrap;
        border-bottom: 2px solid #e2e8f0;
    }
    .slips-table tbody td {
        padding: 14px 18px;
        font-size: 0.86rem;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }
    .slips-table tbody tr:last-child td { border-bottom: none; }
    .slips-table tbody tr:hover td { background: #fafbff; }

    /* Month badge */
    .month-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(67,56,202,0.07);
        border: 1px solid rgba(67,56,202,0.15);
        color: #4338ca;
        font-weight: 800;
        font-size: 0.82rem;
        padding: 5px 12px;
        border-radius: 8px;
    }

    /* Salary amount cells */
    .amt-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .amt-gross  { font-weight: 700; color: #10b981; font-size: 0.9rem; }
    .amt-deduct { font-weight: 700; color: #ef4444; font-size: 0.9rem; }
    .amt-net    { font-weight: 800; color: #1e293b; font-size: 0.95rem; }

    .amt-masked {
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
        opacity: 0.55;
        font-size: 0.85rem;
    }

    /* Per-row eye toggle */
    .eye-toggle-sm {
        background: rgba(100,116,139,0.08);
        border: 1px solid rgba(100,116,139,0.15);
        color: #94a3b8;
        width: 26px; height: 26px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 0.72rem;
        transition: all .2s;
        flex-shrink: 0;
        outline: none;
    }
    .eye-toggle-sm:hover { background: rgba(67,56,202,0.1); color: #4338ca; border-color: rgba(67,56,202,0.2); }

    /* Global reveal toggle */
    .toggle-all-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(67,56,202,0.06);
        border: 1.5px solid rgba(67,56,202,0.2);
        color: #4338ca;
        font-size: 0.78rem; font-weight: 700;
        padding: 6px 14px;
        border-radius: 50px;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: all .2s;
    }
    .toggle-all-btn:hover { background: rgba(67,56,202,0.12); }

    /* Download button */
    .btn-download-slip {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(59,130,246,0.06);
        border: 1.5px solid rgba(59,130,246,0.2);
        color: #3b82f6;
        font-size: 0.78rem; font-weight: 700;
        padding: 6px 14px;
        border-radius: 50px;
        text-decoration: none;
        transition: all .2s;
        white-space: nowrap;
    }
    .btn-download-slip:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }

    /* Empty state */
    .slips-empty {
        text-align: center;
        padding: 3.5rem 1rem;
        color: #94a3b8;
    }
    .slips-empty-icon {
        width: 64px; height: 64px;
        background: #f1f5f9;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; color: #cbd5e1;
        margin: 0 auto 1rem;
    }

    /* Mobile card layout */
    .slip-mobile-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 8px rgba(15,23,42,0.05);
    }
    .slip-card-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 5px 0;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.82rem;
    }
    .slip-card-row:last-child { border-bottom: none; }
    .slip-card-label { color: #94a3b8; font-weight: 600; }

    @media (max-width: 640px) {
        .slips-desktop { display: none !important; }
        .slips-mobile  { display: block !important; }
    }
    @media (min-width: 641px) {
        .slips-desktop { display: block !important; }
        .slips-mobile  { display: none !important; }
    }
</style>

<div class="container-xl px-3 px-md-4 slips-page">

    <!-- Section Banner -->
    <div class="ent-section-banner">
        <h4><i class="fas fa-file-invoice-dollar me-2"></i>My Salary Slips</h4>
        <small>View and download your monthly payment records</small>
    </div>

    <!-- Salary Slips Card -->
    <div class="ent-card">
            <div class="ent-card-header">
                <div class="card-title-group" style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="card-section-icon bank"><i class="fas fa-receipt "></i></span>
                    <div class="card-section-title">Payment History</div>
                </div>
                <button class="btn btn-outline-primary rounded-pill px-4 btn-sm" id="toggleAllBtn" onclick="toggleAllSalaries()">
                    <i class="fas fa-eye" id="toggleAllIcon"></i>
                    <span id="toggleAllText">Show All Amounts</span>
                </button>
            </div>

        <!-- ── Desktop Table ── -->
        <div class="slips-desktop">
            <div class="slips-table-wrap">
                <table class="slips-table">
                    <thead>
                        <tr>
                            <th>Salary Month</th>
                            <th>Generated On</th>
                            <th>Gross Pay</th>
                            <th>Deductions</th>
                            <th>Net Payable</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($slips)): ?>
                            <?php foreach ($slips as $i => $slip): ?>
                                <tr>
                                    <td>
                                        <span class="month-badge">
                                            <i class="fas fa-calendar-alt" style="font-size:0.7rem;"></i>
                                            <?= date('F Y', strtotime($slip->slip_month)) ?>
                                        </span>
                                    </td>
                                    <td style="color:#64748b;">
                                        <?= date('d M Y, h:i A', strtotime($slip->generated_on)) ?>
                                    </td>
                                    <!-- Gross -->
                                    <td>
                                        <div class="amt-cell">
                                            <span class="amt-gross salary-amount" id="gross_<?= $i ?>"
                                                  data-value="+&nbsp;₹<?= number_format($slip->gross_earnings, 2) ?>"
                                                  data-masked="₹ •••••">
                                                <span class="amt-masked">₹ •••••</span>
                                            </span>
                                            <button class="eye-toggle-sm" onclick="toggleRow(<?= $i ?>)"
                                                    id="eyeBtn_<?= $i ?>" title="Show / hide amounts">
                                                <i class="fas fa-eye" id="eyeIcon_<?= $i ?>"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <!-- Deductions -->
                                    <td>
                                        <span class="amt-deduct salary-amount" id="deduct_<?= $i ?>"
                                              data-value="-&nbsp;₹<?= number_format($slip->total_deductions, 2) ?>"
                                              data-masked="₹ •••">
                                            <span class="amt-masked">₹ •••</span>
                                        </span>
                                    </td>
                                    <!-- Net -->
                                    <td>
                                        <span class="amt-net salary-amount" id="net_<?= $i ?>"
                                              data-value="₹<?= number_format($slip->net_salary, 2) ?>"
                                              data-masked="₹ •••••">
                                            <span class="amt-masked">₹ •••••</span>
                                        </span>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="<?= base_url('Employee/viewMySlip/' . $slip->slip_id) ?>" target="_blank"
                                           class="btn-download-slip">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="slips-empty">
                                        <div class="slips-empty-icon"><i class="fas fa-folder-open"></i></div>
                                        <div style="font-weight:800;color:#475569;font-size:0.95rem;margin-bottom:4px;">No Salary Slips Yet</div>
                                        <div style="font-size:0.78rem;">Your payroll records will appear here once generated by HR.</div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Mobile Cards ── -->
        <div class="slips-mobile" style="padding:1rem;">
            <?php if (!empty($slips)): ?>
                <?php foreach ($slips as $i => $slip): ?>
                <div class="slip-mobile-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                        <span class="month-badge">
                            <?= date('M Y', strtotime($slip->slip_month)) ?>
                        </span>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <button class="eye-toggle-sm" onclick="toggleRow(<?= $i ?>)" title="Show amounts">
                                <i class="fas fa-eye" id="meyeIcon_<?= $i ?>"></i>
                            </button>
                            <a href="<?= base_url('Employee/viewMySlip/' . $slip->slip_id) ?>" target="_blank"
                               class="btn-download-slip" style="font-size:0.72rem;padding:5px 10px;">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                    <div class="slip-card-row">
                        <span class="slip-card-label">Gross Pay</span>
                        <span class="amt-gross salary-amount" id="mgross_<?= $i ?>"
                              data-value="+&nbsp;₹<?= number_format($slip->gross_earnings, 2) ?>"
                              data-masked="₹ •••••">
                            <span class="amt-masked">₹ •••••</span>
                        </span>
                    </div>
                    <div class="slip-card-row">
                        <span class="slip-card-label">Deductions</span>
                        <span class="amt-deduct salary-amount" id="mdeduct_<?= $i ?>"
                              data-value="-&nbsp;₹<?= number_format($slip->total_deductions, 2) ?>"
                              data-masked="₹ •••">
                            <span class="amt-masked">₹ •••</span>
                        </span>
                    </div>
                    <div class="slip-card-row">
                        <span class="slip-card-label">Net Payable</span>
                        <span class="amt-net salary-amount" id="mnet_<?= $i ?>"
                              data-value="₹<?= number_format($slip->net_salary, 2) ?>"
                              data-masked="₹ •••••">
                            <span class="amt-masked">₹ •••••</span>
                        </span>
                    </div>
                    <div class="slip-card-row" style="color:#94a3b8;font-size:0.75rem;">
                        <span class="slip-card-label">Generated</span>
                        <span><?= date('d M Y', strtotime($slip->generated_on)) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="slips-empty">
                    <div class="slips-empty-icon"><i class="fas fa-folder-open"></i></div>
                    <div style="font-weight:800;color:#475569;font-size:0.95rem;margin-bottom:4px;">No Salary Slips Yet</div>
                    <div style="font-size:0.78rem;">Your records will appear once HR generates them.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
/* ── Robust Toggle Logic ── */
// Initialize row states based on the actual number of slips
const totalSlips = <?= !empty($slips) ? count($slips) : 0 ?>;
const rowState = {};

// Initialize all rows to hidden (false)
for(let k=0; k < totalSlips; k++) {
    rowState[k] = false;
}

function toggleRow(i) {
    // Toggle the specific state
    rowState[i] = !rowState[i];
    const isRevealed = rowState[i];

    // Update Desktop Row
    updateCellDisplay('gross_' + i, isRevealed);
    updateCellDisplay('deduct_' + i, isRevealed);
    updateCellDisplay('net_' + i, isRevealed);

    // Update Mobile Row
    updateCellDisplay('mgross_' + i, isRevealed);
    updateCellDisplay('mdeduct_' + i, isRevealed);
    updateCellDisplay('mnet_' + i, isRevealed);

    // Update Icons (Desktop & Mobile)
    const desktopIcon = document.getElementById('eyeIcon_' + i);
    const mobileIcon = document.getElementById('meyeIcon_' + i);
    
    const newIconClass = isRevealed ? 'fas fa-eye-slash' : 'fas fa-eye';
    if (desktopIcon) desktopIcon.className = newIconClass;
    if (mobileIcon) mobileIcon.className = newIconClass;

    syncGlobalToggleBtn();
}

function updateCellDisplay(id, isRevealed) {
    const el = document.getElementById(id);
    if (!el) return;
    
    if (isRevealed) {
        el.innerHTML = el.getAttribute('data-value');
    } else {
        el.innerHTML = '<span class="amt-masked">' + el.getAttribute('data-masked') + '</span>';
    }
}

let allRevealedGlobal = false;

function toggleAllSalaries() {
    allRevealedGlobal = !allRevealedGlobal;
    
    // Update the state for every row
    for (let i = 0; i < totalSlips; i++) {
        rowState[i] = allRevealedGlobal;
        
        // Update Desktop
        updateCellDisplay('gross_' + i, allRevealedGlobal);
        updateCellDisplay('deduct_' + i, allRevealedGlobal);
        updateCellDisplay('net_' + i, allRevealedGlobal);
        
        // Update Mobile
        updateCellDisplay('mgross_' + i, allRevealedGlobal);
        updateCellDisplay('mdeduct_' + i, allRevealedGlobal);
        updateCellDisplay('mnet_' + i, allRevealedGlobal);
        
        // Update Icons
        const dIcon = document.getElementById('eyeIcon_' + i);
        const mIcon = document.getElementById('meyeIcon_' + i);
        const iconCls = allRevealedGlobal ? 'fas fa-eye-slash' : 'fas fa-eye';
        if (dIcon) dIcon.className = iconCls;
        if (mIcon) mIcon.className = iconCls;
    }

    // Update Global Button
    const globalIcon = document.getElementById('toggleAllIcon');
    const globalText = document.getElementById('toggleAllText');
    
    globalIcon.className = allRevealedGlobal ? 'fas fa-eye-slash' : 'fas fa-eye';
    globalText.textContent = allRevealedGlobal ? 'Hide All Amounts' : 'Show All Amounts';
}

function syncGlobalToggleBtn() {
    let revealedCount = 0;
    for (let k = 0; k < totalSlips; k++) {
        if (rowState[k]) revealedCount++;
    }
    
    allRevealedGlobal = (revealedCount === totalSlips && totalSlips > 0);
    
    const globalIcon = document.getElementById('toggleAllIcon');
    const globalText = document.getElementById('toggleAllText');
    
    if (globalIcon) globalIcon.className = allRevealedGlobal ? 'fas fa-eye-slash' : 'fas fa-eye';
    if (globalText) globalText.textContent = allRevealedGlobal ? 'Hide All Amounts' : 'Show All Amounts';
}
</script>