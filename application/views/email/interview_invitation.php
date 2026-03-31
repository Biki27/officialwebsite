<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Interview Invitation</title>
</head>

<body style="margin:0;padding:0;background:#f4f7fa;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;background:#f4f7fa;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:10px;overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background:#1e3a8a;color:#ffffff;padding:30px;">
                            <h1 style="margin:0;font-size:26px;">SUROPRIYO ENTERPRISE</h1>
                            <p style="margin:5px 0 0 0;">Interview Invitation</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">

                            <h2 style="margin-top:0;">Dear <?= htmlspecialchars($name) ?>,</h2>

                            <p>
                                Thank you for applying for the position of
                                <strong><?= htmlspecialchars($position) ?></strong>
                                at <strong>Suropriyo Enterprise</strong>.
                            </p>

                            <p>
                                We are pleased to invite you for a
                                <strong><?= htmlspecialchars($round) ?></strong>. Please find the interview details
                                below:
                            </p>


                            <!-- Interview Details -->
                            <table width="100%"
                                style="background:#f1f5f9;border-radius:8px;padding:20px;margin:25px 0;">
                                <tr>
                                    <td>

                                        <p><strong>📅 Interview Date:</strong> <?= htmlspecialchars($date) ?></p>

                                        <p><strong>⏰ Interview Time:</strong> <?= htmlspecialchars($time) ?></p>

                                        <p><strong>📍 Interview Location:</strong> <?= htmlspecialchars($location) ?>
                                        </p>

                                        <p><strong>👤 Interviewer:</strong> <?= htmlspecialchars($hr_name) ?></p>

                                    </td>
                                </tr>
                            </table>

                            <p>
                                Please carry your updated CV with an attached photo. If you have prior work experience,
                                kindly bring your salary slips for the last 3 months or bank transaction history
                            </p>
                            <p>
                                Please arrive at least <strong>10 minutes before</strong> the scheduled time.
                            </p>

                            <p>
                                If you have any questions or need to reschedule, feel free to contact us.
                            </p>

                            <p>
                                Best Regards,<br>
                                <?= htmlspecialchars($hr_name) ?><br>
                                <a href="mailto:eshitasen07@gmail.com"
                                    style="color:#3b82f6;text-decoration:none;font-weight:500;">eshitasen07@gmail.com</a><br>
                                Suropriyo Enterprise
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="background-color:#1f2937;padding:35px 30px;color:#9ca3af;text-align:center;">
                            <p style="font-size:14px;margin:0 0 20px 0;line-height:1.5;">
                                <strong>Suropriyo Enterprise</strong><br>
                                Kolkata, West Bengal, India
                            </p>
                            <p style="font-size:13px;margin:0;">
                                <a href="https://suropriyo.in"
                                    style="color:#9ca3af;text-decoration:none;margin:0 10px;">www.suropriyo.in</a><br>
                                <a href="tel:+91 80176 69102"
                                    style="color:#9ca3af;text-decoration:none;margin:0 10px;">+91 80176 69102</a><br>
                                <a href="tel:+91 87772 70124"
                                    style="color:#9ca3af;text-decoration:none;margin:0 10px;">+91 87772 70124 </a>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>