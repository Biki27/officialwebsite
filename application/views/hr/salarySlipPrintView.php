<?php defined('BASEPATH') or exit('No direct script access allowed');

/** * Enterprise Slip ID Logic 
 */
$company_prefix = "SE";
$branch_code = !empty($branch) ? strtoupper(substr($branch, 0, 3)) : "HQ";
$month_code = date('Ym', strtotime($slip_month));
$display_id = isset($slip_id) ? $slip_id : 0;
$unique_slip_no = "{$company_prefix}/{$branch_code}/{$month_code}/" . str_pad($display_id, 4, '0', STR_PAD_LEFT);

/**
 * Helper: Convert Numbers to Words (Indian Rupee Format)
 */
function numberToWords($num)
{
    $ones = array(0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine", 10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen");
    $tens = array(0 => "Zero", 1 => "Ten", 2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty", 6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety");
    $hundreds = array("Hundred", "Thousand", "Million", "Billion", "Trillion");

    if ($num == 0) return "Zero";
    $num = number_format($num, 2, ".", "");
    $num_arr = explode(".", $num);
    $wholenum = $num_arr[0];
    $decnum = $num_arr[1];
    $whole_arr = array_reverse(explode(",", number_format($wholenum)));
    krsort($whole_arr);
    $rettxt = "";
    foreach ($whole_arr as $key => $i) {
        $i = (int)$i;
        if ($i == 0) continue;
        if ($i < 20) {
            $rettxt .= $ones[$i];
        } elseif ($i < 100) {
            $rettxt .= $tens[substr($i, 0, 1)] . " " . $ones[substr($i, 1, 1)];
        } else {
            $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0] . " " . $tens[substr($i, 1, 1)] . " " . $ones[substr($i, 2, 1)];
        }
        if ($key > 0) {
            $rettxt .= " " . $hundreds[$key] . " ";
        }
    }
    $res = trim($rettxt) . ($decnum > 0 ? " and " . $decnum . "/100" : "");
    return $res . " Rupees Only";
}

function fmtVal($val)
{
    return (empty($val) || $val == 0) ? 'NILL' : number_format($val, 2);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salary Slip - <?= $unique_slip_no ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #000;
            background: #f4f4f4;
            font-size: 13px;
            margin: 0;
            padding: 20px;
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

        .net-salary-row th {
            font-size: 15px;
            text-align: right;
            padding: 10px;
            background-color: #eee;
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
            /* border-top: 1px solid #000; */
            padding-top: 5px;
            font-weight: bold;
        }

        .sig-box img {
            height: 80px;
            /* Increased height for a better, clearer signature */
            width: auto;
            display: block;
            margin: 0 auto 10px auto;
            /* Centers the image and adds space below */
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
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
            .totals-row {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                margin: 10mm;
            }
        }
    </style>
</head>

<body>
     

    <div class="slip-container">
        <img src="<?= base_url('imgs/logo-without-bg.png') ?>" class="watermark-bg" alt="Watermark">

        <table class="header-table">
            <tr>
                <td style="width: 15%; vertical-align: middle;">
                    <img src="<?= base_url('imgs/logo-without-bg.png') ?>" alt="Logo" style="max-width: 90px; height: auto;">
                </td>
                <td style="width: 70%; text-align: center; vertical-align: middle;">
                    <h1 class="company-title">SUROPRIYO ENTERPRISE</h1>
                    <p class="tagline">DREAM BIG. MAKE THE WORLD SMALL.</p>
                    <div style="font-size: 14px; font-weight: bold; text-transform: uppercase; margin-top: 5px;">
                        <?= empty($branch) ? '' : $branch . ' BRANCH' ?>
                    </div>
                    <div style="font-size: 15px; font-weight: bold; text-decoration: underline; margin-top: 4px;">
                        SALARY SLIP OF <?= strtoupper(date('F Y', strtotime($slip_month))) ?>
                    </div>
                </td>
                <td style="width: 15%; text-align: right; vertical-align: top;">
                    <small>#<?= $unique_slip_no ?></small>
                </td>
            </tr>
        </table>

        <table class="emp-details">
            <tr>
                <td class="label">Invoice No</td>
                <td class="colon">:</td>
                <td class="value" style="font-weight: bold; color: #d00;"><?= $unique_slip_no ?></td>
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

        <table class="salary-table">
            <thead>
                <tr>
                    <th colspan="2" style="border-right: 2px solid #000;">EARNING</th>
                    <th colspan="2">DEDUCTION</th>
                </tr>
                <tr>
                    <th class="col-header">Salary Head</th>
                    <th class="col-header text-right" style="border-right: 2px solid #000;">Amount</th>
                    <th class="col-header">Salary Head</th>
                    <th class="col-header text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic</td>
                    <td class="text-right" style="border-right: 2px solid #000;"><?= fmtVal($basic) ?></td>
                    <td>Provident Fund</td>
                    <td class="text-right"><?= fmtVal($pf) ?></td>
                </tr>
                <tr>
                    <td>Transport/House Allowance</td>
                    <td class="text-right" style="border-right: 2px solid #000;"><?= fmtVal($transport) ?></td>
                    <td>ESI</td>
                    <td class="text-right"><?= fmtVal($esi_deduction) ?></td>
                </tr>
                <tr>
                    <td>Incentive</td>
                    <td class="text-right" style="border-right: 2px solid #000;"><?= fmtVal($incentive) ?></td>
                    <td>Profession TAX</td>
                    <td class="text-right"><?= fmtVal($prof_tax) ?></td>
                </tr>
                <tr>
                    <td>Overtime/Half Day</td>
                    <td class="text-right" style="border-right: 2px solid #000;"><?= fmtVal($overtime) ?></td>
                    <td>Late Fees/NPL</td>
                    <td class="text-right"><?= fmtVal($late_fees) ?></td>
                </tr>
                <tr>
                    <td>Round Off</td>
                    <td class="text-right" style="border-right: 2px solid #000;"><?= fmtVal($round_off) ?></td>
                    <td>Loss of Pay/Other</td>
                    <td class="text-right"><?= fmtVal($loss_of_pay) ?></td>
                </tr>
                <tr>
                    <td>Gross</td>
                    <td class="text-right" style="border-right: 2px solid #000;"><?= fmtVal($gross_earnings) ?></td>
                    <td>Loan (Office/Bank)</td>
                    <td class="text-right"><?= fmtVal($loan) ?></td>
                </tr>

                <tr class="totals-row">
                    <td>Total Addition</td>
                    <td class="text-right" style="border-right: 2px solid #000;"><?= fmtVal($gross_earnings) ?></td>
                    <td>Total Deduction</td>
                    <td class="text-right"><?= fmtVal($total_deductions) ?></td>
                </tr>
                <tr class="net-salary-row">
                    <th colspan="3" style="border-right: 1px solid #000;">NET SALARY</th>
                    <th class="text-right">₹ <?= number_format(max(0, $net_salary), 2) ?></th>
                </tr>
            </tbody>
        </table>

        <div class="words-row">
            In words: <?= numberToWords($net_salary) ?>
        </div>

        <div class="signatures">
            <div class="sig-box" style="margin-top: -30px;">
                <img src="<?= base_url('imgs/sign.png') ?>" alt="Company Signature">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    Company's Signature
                </div>
            </div>
            <div class="sig-box" style="margin-top: 50px;">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    Employee's Signature
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 600);
        }
    </script>
</body>

</html>