<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Helper: Numbers to Words (Indian system)
function numberToWords($num) {
    $ones = [0=>"Zero",1=>"One",2=>"Two",3=>"Three",4=>"Four",5=>"Five",6=>"Six",7=>"Seven",8=>"Eight",9=>"Nine",10=>"Ten",11=>"Eleven",12=>"Twelve",13=>"Thirteen",14=>"Fourteen",15=>"Fifteen",16=>"Sixteen",17=>"Seventeen",18=>"Eighteen",19=>"Nineteen"];
    $tens  = [0=>"Zero",1=>"Ten",2=>"Twenty",3=>"Thirty",4=>"Forty",5=>"Fifty",6=>"Sixty",7=>"Seventy",8=>"Eighty",9=>"Ninety"];
    $hundreds = ["Hundred","Thousand","Million","Billion"];
    if ($num == 0) return "Zero Rupees Only";
    $num = number_format($num, 2, ".", "");
    $num_arr  = explode(".", $num);
    $wholenum = $num_arr[0];
    $decnum   = $num_arr[1];
    $whole_arr = array_reverse(explode(",", number_format($wholenum)));
    krsort($whole_arr);
    $rettxt = "";
    foreach ($whole_arr as $key => $i) {
        if ($i < 20)       { $rettxt .= $ones[intval($i)]; }
        elseif ($i < 100)  { $rettxt .= $tens[substr($i,0,1)]." ".$ones[substr($i,1,1)]; }
        else               { $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]." ".$tens[substr($i,1,1)]." ".$ones[substr($i,2,1)]; }
        if ($key > 0)      { $rettxt .= " ".$hundreds[$key]." "; }
    }
    if (intval($decnum) > 0) { $rettxt .= " and ".$decnum."/100"; }
    return trim($rettxt)." Rupees Only";
}

// Helper: NILL for zero/empty
function fmtVal($val) {
    return (empty($val) || $val == 0) ? 'NILL' : number_format($val, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Slip — <?= htmlspecialchars($emp_name) ?> — <?= date('F Y', strtotime($slip_month)) ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Calibri', 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            background: #d0dce8;
            padding: 28px 16px;
            color: #111;
        }

        /* ── OUTER PAGE ── */
        .page {
            position: relative;
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            min-height: 1050px;
            overflow: hidden;
            box-shadow: 0 6px 32px rgba(0,0,0,0.22);
        }

        /* ── BLUE GRID WATERMARK (CSS only — matches the official slip background) ── */
        .page::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                repeating-linear-gradient(
                    90deg,
                    rgba(100,180,230,0.18) 0px,
                    rgba(100,180,230,0.18) 1px,
                    transparent 1px,
                    transparent 28px
                ),
                repeating-linear-gradient(
                    0deg,
                    rgba(100,180,230,0.18) 0px,
                    rgba(100,180,230,0.18) 1px,
                    transparent 1px,
                    transparent 28px
                );
            pointer-events: none;
            z-index: 0;
        }

        /* ── LARGE FAINT LOGO WATERMARK (centre of page) ── */
        .logo-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 400px;
            object-fit: contain;
            opacity: 0.07;
            pointer-events: none;
            z-index: 0;
        }

        /* All real content above watermarks */
        .content {
            position: relative;
            z-index: 1;
            padding: 32px 44px 36px;
        }

        /* ── HEADER ── */
        .header { text-align: center; padding-bottom: 18px; }

        .header-logo {
            width: 88px;
            height: 88px;
            object-fit: contain;
            display: block;
            margin: 0 auto 10px;
        }

        .company-name {
            font-size: 34px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #111;
            font-family: 'Arial Black', 'Arial Bold', Arial, sans-serif;
            line-height: 1;
        }

        .branch-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-top: 5px;
            letter-spacing: 0.4px;
        }

        .slip-title {
            display: inline-block;
            font-size: 17px;
            font-weight: 800;
            text-decoration: underline;
            text-underline-offset: 3px;
            color: #111;
            margin-top: 16px;
            letter-spacing: 0.4px;
        }

        /* ── EMPLOYEE DETAILS ── */
        .emp-section { margin-top: 20px; margin-bottom: 22px; }

        .emp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .emp-table td { padding: 4px 2px; vertical-align: top; }
        .emp-table .lbl  { font-weight: 600; width: 120px; white-space: nowrap; }
        .emp-table .coln { width: 14px; text-align: center; font-weight: 600; }
        .emp-table .val  { min-width: 140px; }
        .emp-table .lbl2 { font-weight: 600; width: 100px; white-space: nowrap; padding-left: 30px; }

        /* ── SALARY TABLE ── */
        .sal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .sal-table th, .sal-table td {
            border: 1px solid #5aaecc;
            padding: 6px 10px;
        }

        /* Big EARNING / DEDUCTION header */
        .sal-table .th-earn {
            background: #deeef8;
            color: #111;
            font-size: 15px;
            font-weight: 800;
            text-align: center;
            border: 1.5px solid #5aaecc;
            border-right: 2px solid #5aaecc;
        }
        .sal-table .th-deduct {
            background: #deeef8;
            color: #111;
            font-size: 15px;
            font-weight: 800;
            text-align: center;
            border: 1.5px solid #5aaecc;
        }

        /* Sub-header */
        .sal-table .subh {
            background: #f4f9fd;
            font-weight: 700;
            font-size: 13px;
            text-align: left;
            color: #333;
        }
        .sal-table .subh-div {
            background: #f4f9fd;
            font-weight: 700;
            font-size: 13px;
            text-align: left;
            color: #333;
            border-right: 2px solid #5aaecc !important;
        }

        /* Data rows */
        .sal-table td.div-right { border-right: 2px solid #5aaecc !important; }
        .sal-table tbody tr:nth-child(even) td { background: rgba(180,220,240,0.13); }

        /* Totals */
        .sal-table .totals td {
            font-weight: 800;
            font-size: 13.5px;
            background: #deeef8 !important;
            border-top: 2px solid #5aaecc;
        }

        /* Net Salary */
        .sal-table .net-row td {
            background: #f0faff !important;
            font-weight: 800;
            font-size: 14.5px;
            border-top: 2px solid #5aaecc;
        }
        .sal-table .net-row .net-label { text-align: center; }

        /* In words */
        .sal-table .words-row td {
            font-weight: 700;
            font-size: 13px;
            background: #f8fdff !important;
        }
        .sal-table .words-row .words-val { font-weight: 400; font-style: italic; }

        /* ── SIGNATURES ── */
        .sig-section {
            margin-top: 52px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .sig-block { text-align: center; }

        .sig-logo {
            width: 115px;
            height: 75px;
            object-fit: contain;
            display: block;
            margin: 0 auto 8px;
        }
        .sig-line {
            border-top: 1.5px solid #333;
            padding-top: 6px;
            font-size: 13px;
            font-weight: 600;
            width: 200px;
        }

        /* ── PRINT ── */
        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; margin: 0; max-width: 100%; }
            .page::before,
            .sal-table .th-earn,
            .sal-table .th-deduct,
            .sal-table .subh,
            .sal-table .subh-div,
            .sal-table .totals td,
            .sal-table .net-row td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page { size: A4; margin: 8mm; }
        }
    </style>
</head>
<body>

<div class="page">

    <!-- Blue grid via CSS ::before -->

    <!-- Large faint logo watermark -->
    <img
        class="logo-watermark"
        src="<?= base_url('imgs/logo-without-bg.png') ?>"
        alt=""
        onerror="this.style.display='none'"
    >

    <div class="content">

        <!-- ══ HEADER ══ -->
        <div class="header">
            <img
                class="header-logo"
                src="<?= base_url('imgs/logo-without-bg.png') ?>"
                alt="Suropriyo Enterprise"
                onerror="this.style.display='none'"
            >
            <div class="company-name">SUROPRIYO ENTERPRISE</div>
            <?php if (!empty($branch)): ?>
            <div class="branch-name"><?= htmlspecialchars(strtoupper($branch)) ?> BRANCH</div>
            <?php endif; ?>
            <div class="slip-title">
                SALARY SLIP OF <?= strtoupper(date('F', strtotime($slip_month))) ?>
            </div>
        </div>

        <!-- ══ EMPLOYEE DETAILS ══ -->
        <div class="emp-section">
            <table class="emp-table">
                <tr>
                    <td class="lbl">Employee Name</td>
                    <td class="coln">:</td>
                    <td class="val"><?= htmlspecialchars($emp_name) ?></td>
                    <td class="lbl2">Employee ID</td>
                    <td class="coln">:</td>
                    <td class="val"><?= !empty($seemp_id) ? htmlspecialchars($seemp_id) : 'NILL' ?></td>
                </tr>
                <tr>
                    <td class="lbl">ESI No.</td>
                    <td class="coln">:</td>
                    <td class="val"><?= !empty($esi_no) ? htmlspecialchars($esi_no) : 'NILL' ?></td>
                    <td class="lbl2">Pay Days</td>
                    <td class="coln">:</td>
                    <td class="val"><?= htmlspecialchars($pay_days) ?></td>
                </tr>
                <tr>
                    <td class="lbl">Designation</td>
                    <td class="coln">:</td>
                    <td class="val" colspan="4"><?= htmlspecialchars($designation) ?></td>
                </tr>
                <tr>
                    <td class="lbl">Mode of Pay</td>
                    <td class="coln">:</td>
                    <td class="val">BANK TRANSFER</td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="lbl">Bank A/C</td>
                    <td class="coln">:</td>
                    <td class="val"><?= !empty($bank_ac) ? htmlspecialchars($bank_ac) : '' ?></td>
                    <td class="lbl2">Date</td>
                    <td class="coln">:</td>
                    <td class="val"><?= date('d-m-Y') ?></td>
                </tr>
                <tr>
                    <td class="lbl">IFS Code</td>
                    <td class="coln">:</td>
                    <td class="val" colspan="4"><?= !empty($ifsc_code) ? htmlspecialchars($ifsc_code) : '' ?></td>
                </tr>
            </table>
        </div>

        <!-- ══ SALARY TABLE ══ -->
        <table class="sal-table">
            <thead>
                <tr>
                    <th class="th-earn"   colspan="2">EARNING</th>
                    <th class="th-deduct" colspan="2">DEDUCTION</th>
                </tr>
                <tr>
                    <th class="subh"     style="width:26%;">Salary Head</th>
                    <th class="subh-div" style="width:24%;">Amount</th>
                    <th class="subh"     style="width:26%;">Salary Head</th>
                    <th class="subh"     style="width:24%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="div-right">Basic</td>
                    <td class="div-right"><?= ($basic > 0) ? number_format($basic,2) : '' ?></td>
                    <td>Provident Fund</td>
                    <td><?= fmtVal($pf) ?></td>
                </tr>
                <tr>
                    <td class="div-right">Transport/House Allowance</td>
                    <td class="div-right"><?= fmtVal($transport) ?></td>
                    <td>ESI</td>
                    <td><?= fmtVal($esi_deduction) ?></td>
                </tr>
                <tr>
                    <td class="div-right">Incentive</td>
                    <td class="div-right"><?= fmtVal($incentive) ?></td>
                    <td>Profession TAX</td>
                    <td><?= fmtVal($prof_tax) ?></td>
                </tr>
                <tr>
                    <td class="div-right">Overtime/Half Day</td>
                    <td class="div-right"><?= fmtVal($overtime) ?></td>
                    <td>Late Fees/NPL</td>
                    <td><?= fmtVal($late_fees) ?></td>
                </tr>
                <tr>
                    <td class="div-right">Round Off</td>
                    <td class="div-right"><?= fmtVal($round_off) ?></td>
                    <td>Loss of Pay/Other</td>
                    <td><?= fmtVal($loss_of_pay) ?></td>
                </tr>
                <tr>
                    <td class="div-right">Gross</td>
                    <td class="div-right"><?= ($gross_earnings > 0) ? number_format($gross_earnings,2) : '' ?></td>
                    <td>Loan (Office/Bank)</td>
                    <td><?= fmtVal($loan) ?></td>
                </tr>

                <!-- Totals row -->
                <tr class="totals">
                    <td class="div-right"><strong>Total Addition</strong></td>
                    <td class="div-right"><strong><?= number_format($gross_earnings, 2) ?></strong></td>
                    <td><strong>Total Deduction</strong></td>
                    <td><strong><?= number_format($total_deductions, 2) ?></strong></td>
                </tr>

                <!-- Net Salary row -->
                <tr class="net-row">
                    <td colspan="3" class="net-label"><strong>NET SALARY</strong></td>
                    <td><strong><?= number_format($net_salary, 2) ?></strong></td>
                </tr>

                <!-- In words row -->
                <tr class="words-row">
                    <td><strong>In words</strong></td>
                    <td colspan="3" class="words-val"><?= numberToWords($net_salary) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- ══ SIGNATURES ══ -->
        <div class="sig-section">

            <!-- Company signature: actual logo image (matches the official slip) -->
            <div class="sig-block">
                <img
                    class="sig-logo"
                    src="<?= base_url('imgs/logo-without-bg.png') ?>"
                    alt="Company Signature"
                    onerror="this.style.display='none'"
                >
                <div class="sig-line">Company Signature</div>
            </div>

            <!-- Employee signature blank -->
            <div class="sig-block">
                <div style="height:75px;"></div>
                <div class="sig-line">Employee's Signature</div>
            </div>

        </div>

    </div><!-- /.content -->
</div><!-- /.page -->

<script>
    window.onload = function () {
        setTimeout(function () { window.print(); }, 600);
    };
</script>

</body>
</html>
