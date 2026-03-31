<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Interview Scheduled - Suropriyo Enterprise</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f7fa;font-family:Arial,Helvetica,sans-serif;color:#374151;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;background:#f4f7fa;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.1);">

                    <tr>
                        <td align="center" style="background:#1e3a8a;color:#ffffff;padding:40px 30px;">
                            <h1 style="margin:0;font-size:28px;letter-spacing:-0.5px;">
                                SUROPRIYO ENTERPRISE
                            </h1>
                            <p style="margin:10px 0 0 0;font-size:16px;opacity:0.9;">
                                <?= htmlspecialchars(strtoupper($round)) ?> Scheduled
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:50px 40px;">

                            <h2 style="margin-top:0;color:#1f2937;">
                                Hello HR,
                            </h2>

                            <p style="line-height:1.7;">
                                A new <strong><?= htmlspecialchars($round) ?></strong> has been successfully scheduled.
                                Below are the details:
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f8fafc;border-left:4px solid #3b82f6;padding:25px;margin:30px 0;border-radius:0 8px 8px 0;">

                                <tr>
                                    <td>
                                        <p><strong>Applicant Name:</strong> <?= htmlspecialchars($name) ?></p>
                                        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                                        <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
                                        <p><strong>Position:</strong> <?= htmlspecialchars($position) ?></p>

                                        <hr style="border:none; border-top:1px solid #e2e8f0; margin:15px 0;">

                                        <p><strong>Interview Type:</strong> <span
                                                style="color:#2563eb; font-weight:bold;"><?= htmlspecialchars(strtoupper($round)) ?></span>
                                        </p>
                                        <p><strong>Interview Date:</strong> <?= htmlspecialchars($date) ?></p>
                                        <p><strong>Interview Time:</strong> <?= htmlspecialchars($time) ?></p>
                                        <p><strong>Interview Location:</strong> <?= htmlspecialchars($location) ?></p>
                                        <p><strong>Scheduled By:</strong> <?= htmlspecialchars($hr_name ?? 'HR Team') ?>
                                        </p>
                                    </td>
                                </tr>

                            </table>

                            <p style="line-height:1.6;">
                                Please ensure the interview arrangements for this
                                <strong><?= htmlspecialchars($round) ?></strong> are prepared accordingly.
                            </p>

                            <p style="color:#6b7280;font-size:14px;">
                                Regards,<br>
                                <strong>Recruitment System</strong>
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background:#1f2937;padding:35px;color:#9ca3af;">
                            <p style="margin:0 0 10px 0;font-size:14px;">
                                <strong>Suropriyo Enterprise</strong><br>
                                Kolkata, West Bengal, India
                            </p>
                            <p style="margin:0;font-size:13px;">
                                <a href="https://suropriyo.in"
                                    style="color:#9ca3af;text-decoration:none;margin:0 10px;">www.suropriyo.in</a> <br>
                                <a href="tel:+91 87772 70124"
                                    style="color:#9ca3af;text-decoration:none;margin:0 10px;">+91 87772 70124</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>