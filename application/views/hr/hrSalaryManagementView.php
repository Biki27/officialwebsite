<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payroll Management | HR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #f4f7fe; font-family: 'Inter', sans-serif; }
        .main-content { margin-left: 260px; padding: 30px; min-height: 100vh; }
        
        .metric-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        
        .table-card { background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden; border: 1px solid rgba(0,0,0,0.05); }
        .table th { background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; padding: 15px; border-bottom: 2px solid #e2e8f0; }
        .table td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        
        .status-badge { padding: 6px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #b45309; border: 1px solid #fde68a; }
        .status-generated { background: #dcfce7; color: #047857; border: 1px solid #bbf7d0; }
        
        .calc-box { background: #f8fafc; border-radius: 8px; padding: 15px; border: 1px solid #e2e8f0; }
        .net-pay-box { background: #4f46e5; color: white; border-radius: 8px; padding: 15px; text-align: center; }

        @media (max-width: 992px) { .main-content { margin-left: 0; padding: 80px 15px 20px; } }
    </style>
</head>

<body>
    
    <?php if ($this->session->flashdata('error')): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= $this->session->flashdata('error') ?>', confirmButtonColor: '#ef4444' });
            });
        </script>
    <?php endif; ?>

    <div class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="fas fa-money-check-alt me-2 text-primary"></i> Payroll Processing</h3>
                <p class="text-muted mb-0">Manage and generate salary slips for all employees.</p>
            </div>
            
            <form action="<?= base_url('Employee/salaryManagement') ?>" method="GET" class="d-flex align-items-center gap-2 bg-white p-2 rounded-pill shadow-sm border">
                <label class="fw-bold text-muted ms-2 small text-uppercase">Payroll Month:</label>
                <input type="month" name="month" value="<?= $selected_month ?>" class="form-control form-control-sm border-0 fw-bold text-primary" style="width: auto; background: transparent; cursor: pointer;" onchange="this.form.submit()">
            </form>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="metric-card d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="fas fa-users"></i></div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold text-uppercase">Total Employees</h6>
                        <h3 class="fw-bold mb-0"><?= $total_emps ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card d-flex align-items-center gap-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold text-uppercase">Slips Generated</h6>
                        <h3 class="fw-bold mb-0 text-success"><?= $processed_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card d-flex align-items-center gap-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold text-uppercase">Pending Processing</h6>
                        <h3 class="fw-bold mb-0 text-warning"><?= $pending_count ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center p-4 border-bottom bg-white">
                <h5 class="fw-bold mb-0">Employee Roster - <?= date('F Y', strtotime($selected_month)) ?></h5>
                <input type="text" id="searchInput" class="form-control form-control-sm rounded-pill px-3" placeholder="Search employee..." style="width: 250px;">
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="payrollTable">
                    <thead>
                        <tr>
                            <th class="ps-4">Emp ID</th>
                            <th>Employee Info</th>
                            <th>Base Salary</th>
                            <th>Status (<?= date('M y', strtotime($selected_month)) ?>)</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $emp): 
                            // Check if a slip exists for this employee in the selected month
                            $is_processed = isset($monthly_slips[$emp->seemp_id]);
                            $slip_data = $is_processed ? $monthly_slips[$emp->seemp_id] : null;
                        ?>
                        <tr style="<?= $is_processed ? 'background-color: #f8fafc;' : '' ?>">
                            <td class="ps-4 fw-bold text-muted"><?= $emp->seemp_id ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= $emp->seempd_name ?></div>
                                <div class="text-muted small"><?= $emp->seempd_designation ?> • <?= $emp->seemp_branch ?></div>
                            </td>
                            <td class="fw-medium">₹ <?= number_format($emp->seempd_salary, 2) ?></td>
                            <td>
                                <?php if($is_processed): ?>
                                    <span class="status-badge status-generated"><i class="fas fa-check me-1"></i> Generated</span>
                                <?php else: ?>
                                    <span class="status-badge status-pending"><i class="fas fa-clock me-1"></i> Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <?php if($is_processed): ?>
                                        <a href="<?= base_url('Employee/viewMySlip/' . $slip_data->slip_id) ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-file-pdf me-1"></i> View Slip
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm"
                                            onclick="openSlipModal('<?= $emp->seemp_id ?>', '<?= htmlspecialchars($emp->seempd_name, ENT_QUOTES) ?>', '<?= $emp->seempd_designation ?>', '<?= $emp->seemp_branch ?>', '<?= $emp->seempd_salary ?>', '<?= $emp->sebank_ac_no ?? '' ?>', '<?= $emp->sebank_ifsc ?? '' ?>', '<?= $emp->sebank_esi ?? '' ?>', '<?= $selected_month ?>')">
                                            <i class="fas fa-calculator me-1"></i> Process Slip
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="salaryModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h4 class="modal-title fw-bold text-primary"><i class="fas fa-calculator me-2"></i> Generate Salary Slip</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="<?= base_url('Employee/generatePayslip') ?>" method="POST" target="_blank" id="generateForm">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="seemp_id" id="slip_empid">
                    <input type="hidden" name="emp_name" id="slip_empname">
                    <input type="hidden" name="designation" id="slip_designation">
                    <input type="hidden" name="branch" id="slip_branch">

                    <div class="modal-body p-4">
                        <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                            <div class="col-md-3">
                                <label class="small text-muted fw-bold">Salary Month</label>
                                <input type="month" name="slip_month" id="slip_month" class="form-control form-control-sm fw-bold text-primary bg-white" required readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="small text-muted fw-bold">Pay Days</label>
                                <input type="number" name="pay_days" class="form-control form-control-sm" value="30" required>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-bold">Bank A/C No</label>
                                <input type="text" name="bank_ac" class="form-control form-control-sm" placeholder="NILL">
                            </div>
                            <div class="col-md-2">
                                <label class="small text-muted fw-bold">IFSC</label>
                                <input type="text" name="ifsc_code" class="form-control form-control-sm" placeholder="NILL">
                            </div>
                            <div class="col-md-2">
                                <label class="small text-muted fw-bold">ESI No.</label>
                                <input type="text" name="esi_no" class="form-control form-control-sm" placeholder="NILL">
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-success border-bottom pb-2"><i class="fas fa-plus-circle me-1"></i> EARNINGS</h6>
                                <div class="calc-box">
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="small fw-semibold">Basic Salary</span>
                                        <input type="number" step="0.01" name="basic" id="calc_basic" class="form-control form-control-sm text-end calc-input" style="width: 150px;">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="small fw-semibold">Transport/House Allowance</span>
                                        <input type="number" step="0.01" name="transport" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="small fw-semibold">Incentive</span>
                                        <input type="number" step="0.01" name="incentive" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="small fw-semibold">Overtime/Half Day</span>
                                        <input type="number" step="0.01" name="overtime" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small fw-semibold">Round Off</span>
                                        <input type="number" step="0.01" name="round_off" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-danger border-bottom pb-2"><i class="fas fa-minus-circle me-1"></i> DEDUCTIONS</h6>
                                <div class="calc-box">
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="small fw-semibold">Provident Fund</span>
                                        <input type="number" step="0.01" name="pf" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="small fw-semibold">ESI</span>
                                        <input type="number" step="0.01" name="esi_deduction" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="small fw-semibold">Profession Tax</span>
                                        <input type="number" step="0.01" name="prof_tax" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="small fw-semibold">Late Fees / NPL</span>
                                        <input type="number" step="0.01" name="late_fees" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="small fw-semibold">Loss of Pay / Other</span>
                                        <input type="number" step="0.01" name="loss_of_pay" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small fw-semibold">Loan (Office/Bank)</span>
                                        <input type="number" step="0.01" name="loan" class="form-control form-control-sm text-end calc-input" style="width: 150px;" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mt-1">
                            <div class="col-md-4">
                                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-center border border-success border-opacity-25">
                                    <span class="small fw-bold text-success d-block text-uppercase">Total Gross Addition</span>
                                    <h4 class="fw-bold text-success mb-0" id="display_gross">₹ 0.00</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-center border border-danger border-opacity-25">
                                    <span class="small fw-bold text-danger d-block text-uppercase">Total Deductions</span>
                                    <h4 class="fw-bold text-danger mb-0" id="display_deduction">₹ 0.00</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="net-pay-box shadow-sm">
                                    <span class="small fw-bold d-block text-uppercase opacity-75">Net Salary Payable</span>
                                    <h3 class="fw-bold mb-0" id="display_net">₹ 0.00</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-top-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="setTimeout(()=>{ location.reload(); }, 1500);">
                            <i class="fas fa-print me-2"></i> Generate & Print Slip
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 1. Live Search Filtering
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#payrollTable tbody tr');
            
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // 2. Open Modal and Auto-Fill
        const salaryModal = new bootstrap.Modal(document.getElementById('salaryModal'));

        function openSlipModal(id, name, designation, branch, base_salary, bank_ac, ifsc, esi, selected_month) {
            document.getElementById('slip_empid').value = id;
            document.getElementById('slip_empname').value = name;
            document.getElementById('slip_designation').value = designation;
            document.getElementById('slip_branch').value = branch;
            
            // Auto-lock the month to the one HR selected in the master view
            document.getElementById('slip_month').value = selected_month;
            
            // Auto fill bank details
            document.getElementsByName('bank_ac')[0].value = bank_ac;
            document.getElementsByName('ifsc_code')[0].value = ifsc;
            document.getElementsByName('esi_no')[0].value = esi;
            
            // Set base salary to the basic input
            document.getElementById('calc_basic').value = parseFloat(base_salary).toFixed(2);
            calculateTotals();
            salaryModal.show();
        }

        // 3. Live Auto-Calculation Logic
        const inputs = document.querySelectorAll('.calc-input');
        inputs.forEach(input => { input.addEventListener('input', calculateTotals); });

        function calculateTotals() {
            let basic = parseFloat(document.getElementsByName('basic')[0].value) || 0;
            let trans = parseFloat(document.getElementsByName('transport')[0].value) || 0;
            let inc = parseFloat(document.getElementsByName('incentive')[0].value) || 0;
            let over = parseFloat(document.getElementsByName('overtime')[0].value) || 0;
            let round = parseFloat(document.getElementsByName('round_off')[0].value) || 0;
            let gross = basic + trans + inc + over + round;

            let pf = parseFloat(document.getElementsByName('pf')[0].value) || 0;
            let esi = parseFloat(document.getElementsByName('esi_deduction')[0].value) || 0;
            let prof = parseFloat(document.getElementsByName('prof_tax')[0].value) || 0;
            let late = parseFloat(document.getElementsByName('late_fees')[0].value) || 0;
            let lop = parseFloat(document.getElementsByName('loss_of_pay')[0].value) || 0;
            let loan = parseFloat(document.getElementsByName('loan')[0].value) || 0;
            let ded = pf + esi + prof + late + lop + loan;

            let net = gross - ded;

            document.getElementById('display_gross').innerText = "₹ " + gross.toFixed(2);
            document.getElementById('display_deduction').innerText = "₹ " + ded.toFixed(2);
            document.getElementById('display_net').innerText = "₹ " + net.toFixed(2);
        }
    </script>
</body>
</html>