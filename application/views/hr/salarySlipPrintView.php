<?php defined('BASEPATH') or exit('No direct script access allowed');

$company_prefix = "SE";
$branch_code = !empty($branch) ? strtoupper(substr($branch, 0, 3)) : "HQ";
$month_code = date('Ym', strtotime($slip_month));
$display_id = isset($slip_id) ? $slip_id : 0;
$unique_slip_no = "{$company_prefix}/{$branch_code}/{$month_code}/" . str_pad($display_id, 4, '0', STR_PAD_LEFT);

function numberToWords($num)
{
    $ones = [0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine", 10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen"];
    $tens = [0 => "Zero", 1 => "Ten", 2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty", 6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"];
    $hundreds = ["Hundred", "Thousand", "Million", "Billion", "Trillion"];

    if ($num == 0)
        return "Zero Rupees Only";

    // Format number to 2 decimal places
    $num = number_format($num, 2, ".", ",");
    $num_arr = explode(".", $num);
    $wholenum = str_replace(",", "", $num_arr[0]); // Remove commas for calculation
    $decnum = $num_arr[1];

    // Helper to process chunks of numbers
    $processChunk = function ($n) use ($ones, $tens, $hundreds) {
        $txt = "";
        $n = (int) $n;
        if ($n < 20) {
            $txt .= $ones[$n];
        } elseif ($n < 100) {
            $txt .= $tens[substr($n, 0, 1)] . " " . $ones[substr($n, 1, 1)];
        } else {
            $txt .= $ones[substr($n, 0, 1)] . " " . $hundreds[0] . " " . $tens[substr($n, 1, 1)] . " " . $ones[substr($n, 2, 1)];
        }
        return str_replace(" Zero", "", $txt);
    };

    // Process Rupees (Whole number)
    $whole_arr = array_reverse(explode(",", number_format($wholenum)));
    krsort($whole_arr);
    $rupeeTxt = "";
    foreach ($whole_arr as $key => $i) {
        if ((int) $i == 0)
            continue;
        $rupeeTxt .= $processChunk($i);
        if ($key > 0)
            $rupeeTxt .= " " . $hundreds[$key] . " ";
    }

    $final_string = trim($rupeeTxt) . " Rupees";

    // Process Paise (Decimals)
    if ((int) $decnum > 0) {
        $paiseTxt = $processChunk($decnum);
        $final_string .= " and " . trim($paiseTxt) . " Paise";
    }

    return $final_string . " Only";
}

function fmtVal($val)
{
    return (empty($val) || $val == 0) ? 'NILL' : number_format($val, 2);
}

// Final payable = net after round-off adjustment
$round_off_val = isset($round_off) ? (float) $round_off : 0;
$final_net = max(0, (float) $net_salary);            // net already includes round_off from controller
$net_before_round = $final_net - $round_off_val;          // gross - deductions before rounding
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salary Slip – <?= $unique_slip_no ?></title>
    <style>
        /* ── Screen: action bar ── */
        #actionBar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #1e1b4b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 24px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.25);
            gap: 12px;
        }

        #actionBar .slip-label {
            color: #c7d2fe;
            font-size: 0.85rem;
            font-family: Arial, sans-serif;
        }

        #actionBar .slip-label strong {
            color: #fff;
            font-size: 1rem;
        }

        .btn-bar {
            padding: 8px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: Arial, sans-serif;
            transition: opacity .2s;
        }

        .btn-bar:hover {
            opacity: 0.88;
        }

        .btn-download {
            background: #4f46e5;
            color: #fff;
        }

        .btn-close-bar {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .btn-close-bar:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        /* ── Print: hide action bar ── */
        @media print {
            #actionBar {
                display: none !important;
            }

            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }

            .slip-container {
                border: 2px solid #000;
                margin: 0;
                padding: 20px;
                max-width: 100%;
            }

            .watermark-bg {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .col-header,
            .net-salary-row th,
            .totals-row,
            .round-off-row {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                margin: 10mm;
            }
        }

        /* ── General ── */
        body {
            font-family: 'Arial', sans-serif;
            color: #000;
            background: #f4f4f4;
            font-size: 13px;
            margin: 0;
            padding: 70px 20px 20px 20px;
            /* top pad for action bar */
            box-sizing: border-box;
        }

        .slip-container {
            max-width: 850px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 25px;
            background: #fff;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        .watermark-bg {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            opacity: 0.08;
            filter: grayscale(100%);
            z-index: 0;
            pointer-events: none;
        }

        .header-table,
        .emp-details,
        .salary-table,
        .words-row,
        .signatures {
            position: relative;
            z-index: 1;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-title {
            font-size: 26px;
            font-weight: bold;
            margin: 0;
        }

        .tagline {
            font-size: 10px;
            font-weight: bold;
            margin: 2px 0 5px 0;
        }

        .emp-details {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .emp-details td {
            padding: 4px 0;
        }

        .emp-details .label {
            font-weight: bold;
            width: 18%;
        }

        .emp-details .colon {
            width: 2%;
            text-align: center;
            font-weight: bold;
        }

        .emp-details .value {
            width: 30%;
            text-transform: uppercase;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-bottom: 15px;
        }

        .salary-table th,
        .salary-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        .salary-table th {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        .col-header {
            background-color: #f9f9f9;
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .totals-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        /* Net salary row */
        .net-salary-row th {
            font-size: 15px;
            text-align: right;
            padding: 10px;
            background-color: #eee;
        }

        /* ── NEW: Round-off adjustment row below net salary ── */
        .round-off-row td {
            font-size: 12px;
            font-style: italic;
            background-color: #fafafa;
            border: 1px solid #ccc;
            padding: 5px 8px;
            color: #555;
        }

        .round-off-row td:last-child {
            text-align: right;
            font-weight: 600;
            color: #222;
        }

        /* Final payable row */
        .final-payable-row th {
            font-size: 16px;
            text-align: right;
            padding: 11px 10px;
            color: black;
            background-color: #eee;
            /* background: #1e1b4b; color: #fff; */

        }

        .words-row {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 50px;
            border-left: 3px solid #000;
            padding-left: 10px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            padding-top: 30px;
        }

        .sig-box {
            width: 200px;
            text-align: center;
            padding-top: 5px;
            font-weight: bold;
        }

        .sig-box img {
            height: 80px;
            width: auto;
            display: block;
            margin: 0 auto 10px auto;
        }
    </style>
</head>

<body>

    <!-- ══════════════════════════════════════════
         ACTION BAR (hidden on print)
    ══════════════════════════════════════════ -->
    <div id="actionBar">
        <div class="slip-label">
            <strong>Salary Slip</strong><br>
            <?= htmlspecialchars($emp_name) ?> &mdash;
            <?= date('F Y', strtotime($slip_month)) ?>
            &nbsp;|&nbsp; <span style="opacity:.7;">#<?= $unique_slip_no ?></span>
        </div>
        <div style="display:flex;gap:10px;">
            <button class="btn-bar btn-download" onclick="downloadPDF()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Download / Print PDF
            </button>
            <button class="btn-bar btn-close-bar" onclick="window.close()">
                ✕ Close
            </button>
        </div>
    </div>


    <!-- ══════════════════════════════════════════
         SALARY SLIP DOCUMENT
    ══════════════════════════════════════════ -->
    <div class="slip-container" id="slipDoc">
        <img src="<?= base_url('imgs/logo-without-bg.png') ?>" class="watermark-bg" alt="Watermark">

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width:15%;vertical-align:middle;">
                    <img src="<?= base_url('imgs/logo-without-bg.png') ?>" alt="Logo"
                        style="max-width:90px;height:auto;">
                </td>
                <td style="width:70%;text-align:center;vertical-align:middle;">
                    <h1 class="company-title">SUROPRIYO ENTERPRISE</h1>
                    <p class="tagline">DREAM BIG. MAKE THE WORLD SMALL.</p>
                    <div style="font-size:14px;font-weight:bold;text-transform:uppercase;margin-top:5px;">
                        <?= empty($branch) ? '' : $branch . ' BRANCH' ?>
                    </div>
                    <div style="font-size:15px;font-weight:bold;text-decoration:underline;margin-top:4px;">
                        SALARY SLIP OF <?= strtoupper(date('F Y', strtotime($slip_month))) ?>
                    </div>
                </td>
                <td style="width:15%;text-align:right;vertical-align:top;">
                    <small>#<?= $unique_slip_no ?></small>
                </td>
            </tr>
        </table>

        <!-- Employee Details -->
        <table class="emp-details">
            <tr>
                <td class="label">Invoice No</td>
                <td class="colon">:</td>
                <td class="value" style="font-weight:bold;color:#d00;"><?= $unique_slip_no ?></td>
                <td class="label">Employee ID</td>
                <td class="colon">:</td>
                <td class="value"><?= $seemp_id ?></td>
            </tr>
            <tr>
                <td class="label">Employee Name</td>
                <td class="colon">:</td>
                <td class="value"><?= $emp_name ?></td>
                <td class="label">Pay Days</td>
                <td class="colon">:</td>
                <td class="value"><?= $pay_days ?></td>
            </tr>
            <tr>
                <td class="label">Designation</td>
                <td class="colon">:</td>
                <td class="value"><?= $designation ?></td>
                <td class="label">ESI No.</td>
                <td class="colon">:</td>
                <td class="value"><?= empty($esi_no) ? 'NILL' : $esi_no ?></td>
            </tr>
            <tr>
                <td class="label">Bank A/C</td>
                <td class="colon">:</td>
                <td class="value"><?= empty($bank_ac) ? 'NILL' : $bank_ac ?></td>
                <td class="label">IFS Code</td>
                <td class="colon">:</td>
                <td class="value"><?= empty($ifsc_code) ? 'NILL' : $ifsc_code ?></td>
            </tr>
            <tr>
                <td class="label">Issue Date</td>
                <td class="colon">:</td>
                <td class="value"><?= date('d-M-Y') ?></td>
                <td class="label">Mode of Pay</td>
                <td class="colon">:</td>
                <td class="value">BANK TRANSFER</td>
            </tr>
        </table>

        <!-- Salary Table -->
        <table class="salary-table">
            <thead>
                <tr>
                    <th colspan="2" style="border-right:2px solid #000;">EARNING</th>
                    <th colspan="2">DEDUCTION</th>
                </tr>
                <tr>
                    <th class="col-header">Salary Head</th>
                    <th class="col-header text-right" style="border-right:2px solid #000;">Amount</th>
                    <th class="col-header">Salary Head</th>
                    <th class="col-header text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic</td>
                    <td class="text-right" style="border-right:2px solid #000;"><?= fmtVal($basic) ?></td>
                    <td>Provident Fund</td>
                    <td class="text-right"><?= fmtVal($pf) ?></td>
                </tr>
                <tr>
                    <td>Transport / House Allowance</td>
                    <td class="text-right" style="border-right:2px solid #000;"><?= fmtVal($transport) ?></td>
                    <td>ESI</td>
                    <td class="text-right"><?= fmtVal($esi_deduction) ?></td>
                </tr>
                <tr>
                    <td>Incentive</td>
                    <td class="text-right" style="border-right:2px solid #000;"><?= fmtVal($incentive) ?></td>
                    <td>Profession TAX</td>
                    <td class="text-right"><?= fmtVal($prof_tax) ?></td>
                </tr>

                <tr>
                    <td>Overtime </td>
                    <td class="text-right" style="border-right:2px solid #000;"><?= fmtVal($overtime) ?></td>
                    <td>Late Fees / NPL</td>
                    <td class="text-right"><?= fmtVal($late_fees) ?></td>
                </tr>
                <tr>
                    <?php if (empty($bonus_amt) || $bonus_amt > 0): ?>
                        <td style="color:#d00;font-weight:bold;">Annual Bonus</td>
                        <td class="text-right" style="color:#d00;font-weight:bold; border-right:2px solid #000;">
                            <?= fmtVal($bonus_amt) ?></td>
                    <?php else: ?>
                        <td>&nbsp;</td>
                        <td class="text-right">&nbsp;</td>
                    <?php endif; ?>

                    <td>Loss of Pay / Other</td>
                    <td class="text-right"><?= fmtVal($loss_of_pay) ?></td>
                </tr>


                <tr>

                    <td>&nbsp;</td>
                    <td class="text-right">&nbsp;</td>


                    <td>Loan (Office / Bank)</td>
                    <td class="text-right"><?= fmtVal($loan) ?></td>
                </tr>
                <!-- Totals -->
                <tr class="totals-row">
                    <td>Total Earning</td>
                    <td class="text-right" style="border-right:2px solid #000;"><?= fmtVal($gross_earnings) ?></td>
                    <td>Total Deduction</td>
                    <td class="text-right"><?= fmtVal($total_deductions) ?></td>
                </tr>
                <!-- NET SALARY (before round-off) -->
                <tr class="net-salary-row">
                    <th colspan="3" style="border-right:1px solid #000; background:#eee;">
                        NET SALARY (Before Round Off)
                    </th>
                    <th class="text-right" style="background:#eee;">
                        ₹ <?= number_format($net_before_round, 2) ?>
                    </th>
                </tr>
                <!-- ── ROUND OFF ROW (after Net Salary, as requested) ── -->
                <tr class="round-off-row">
                    <td colspan="3" style="border-right:1px solid #000;">
                        Round Off Adjustment
                        <span style="font-size:11px;color:#888;">
                            (added to arrive at final payable amount)
                        </span>
                    </td>
                    <td>
                        <?php
                        if ($round_off_val == 0) {
                            echo 'NILL';
                        } elseif ($round_off_val > 0) {
                            echo '+ ' . number_format($round_off_val, 2);
                        } else {
                            echo '– ' . number_format(abs($round_off_val), 2);
                        }
                        ?>
                    </td>
                </tr>
                <!-- FINAL NET PAYABLE -->
                <tr class="final-payable-row">
                    <th colspan="3">
                        FINAL NET PAYABLE
                    </th>
                    <th class="text-right">₹ <?= number_format($final_net, 2) ?></th>
                </tr>
            </tbody>
        </table>

        <!-- Amount in Words -->
        <div class="words-row">
            In words: <?= numberToWords($final_net) ?>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-box" style="margin-top:-30px;">
                <img src="<?= base_url('imgs/sign.png') ?>" alt="Company Signature">
                <div style="border-top:1px solid #000;padding-top:5px;">Company's Signature</div>
            </div>
            <div class="sig-box" style="margin-top:50px;">
                <div style="border-top:1px solid #000;padding-top:5px;">Employee's Signature</div>
            </div>
        </div>

    </div><!-- /slip-container -->

    <script>
        // Download/Print — opens native browser print dialog
        // User can choose "Save as PDF" in the print dialog
        function downloadPDF() {
            window.print();
        }

        // NOTE: Auto-print removed intentionally.

    </script>
</body>

</html>