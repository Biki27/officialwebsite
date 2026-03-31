<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Application Received - Suropriyo Enterprise</title>
</head>

<body
    style="margin:0;padding:0;background-color:#f4f7fa;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.6;color:#374151;">

    <!-- Main Container Table -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background-color:#f4f7fa;padding:40px 20px;">
        <tr>
            <td align="center">

                <!-- Email Content Table - 600px max width -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:600px;background-color:#ffffff;border-radius:12px;-webkit-border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.1);-webkit-box-shadow:0 10px 30px rgba(0,0,0,0.1);">

                    <!-- Header Section - Green theme for Admin -->
                    <tr>
                        <td align="center"
                            style="background-color:#1e3a8a;padding:40px 30px;color:#ffffff;text-align:center;">
                            <!-- Company Name -->
                            <h1
                                style="font-size:28px;font-weight:bold;margin:0 0 15px 0;letter-spacing:-0.5px;color:#ffffff;text-shadow:0 2px 4px rgba(0,0,0,0.1);">
                                SUROPRIYO ENTERPRISE
                            </h1>

                            <!-- Logo Container -->
                            <table role="presentation" width="70" cellspacing="0" cellpadding="0" border="0"
                                style="margin:0 auto 20px auto;">
                                <tr>
                                    <td>
                                        <img src="https://ci3.googleusercontent.com/mail-sig/AIorK4zGOdpEc6LysCJSIwHEgpTg284tu3V52EVXTWCWoUs1HmKcg9k0Ttd5UwXYr_RuAqlCcEBAEGKJ4uiY"
                                            alt="Suropriyo Enterprise Logo" width="70" height="75"
                                            style="display:block;border-radius:50%;-webkit-border-radius:50%;box-shadow:0 4px 12px rgba(0,0,0,0.2);-webkit-box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                                    </td>
                                </tr>
                            </table>

                            <!-- Subtitle -->
                            <p style="font-size:16px;margin:0;font-weight:400;opacity:0.9;">
                                New Application Alert
                            </p>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding:50px 40px;">

                            <!-- Main Heading -->
                            <h2 style="color:#1f2937;font-size:24px;font-weight:600;margin:0 0 20px 0;line-height:1.3;">
                                New Job Application Received
                            </h2>

                            <!-- Intro Message -->
                            <p style="font-size:16px;margin:0 0 25px 0;line-height:1.7;color:#374151;">
                                A new application has been submitted for <strong><?= $position ?></strong>.
                                Please review the applicant details below and take appropriate action.
                            </p>

                            <!-- Applicant Info Card -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="background-color:#f8fafc;border-left:4px solid #1e3a8a;padding:25px;margin:30px 0;border-radius:0 8px 8px 0;-webkit-border-radius:0 8px 8px 0;">
                                <tr>
                                    <td>
                                        <h3 style="color:#1e3a8a;font-size:18px;font-weight:600;margin:0 0 20px 0;">
                                            👤 Applicant Details
                                        </h3>

                                        <!-- Info Grid - 2 columns desktop, 1 column mobile -->
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                            border="0">
                                            <tr>
                                                <td width="48%" valign="top" style="padding:0 2% 15px 0;">
                                                    <table role="presentation" width="100%" cellspacing="0"
                                                        cellpadding="0" border="0"
                                                        style="background-color:#ffffff;padding:15px 20px;border-radius:8px;-webkit-border-radius:8px;border:1px solid #e5e7eb;">
                                                        <tr>
                                                            <td
                                                                style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:5px;padding-bottom:5px;">
                                                                Full Name
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:16px;font-weight:600;color:#1f2937;">
                                                                <?= $name ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="48%" valign="top" style="padding:0 0 15px 2%;">
                                                    <table role="presentation" width="100%" cellspacing="0"
                                                        cellpadding="0" border="0"
                                                        style="background-color:#ffffff;padding:15px 20px;border-radius:8px;-webkit-border-radius:8px;border:1px solid #e5e7eb;">
                                                        <tr>
                                                            <td
                                                                style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:5px;padding-bottom:5px;">
                                                                Email
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:16px;font-weight:600;color:#1f2937;">
                                                                <?= $email ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="48%" valign="top" style="padding:0 2% 15px 0;">
                                                    <table role="presentation" width="100%" cellspacing="0"
                                                        cellpadding="0" border="0"
                                                        style="background-color:#ffffff;padding:15px 20px;border-radius:8px;-webkit-border-radius:8px;border:1px solid #e5e7eb;">
                                                        <tr>
                                                            <td
                                                                style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:5px;padding-bottom:5px;">
                                                                Phone
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:16px;font-weight:600;color:#1f2937;">
                                                                <?= $phone ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="48%" valign="top" style="padding:0 0 15px 2%;">
                                                    <table role="presentation" width="100%" cellspacing="0"
                                                        cellpadding="0" border="0"
                                                        style="background-color:#ffffff;padding:15px 20px;border-radius:8px;-webkit-border-radius:8px;border:1px solid #e5e7eb;">
                                                        <tr>
                                                            <td
                                                                style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:5px;padding-bottom:5px;">
                                                                Applied For
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:16px;font-weight:600;color:#1f2937;">
                                                                <?= $position ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top:15px;">
                                                    <table role="presentation" width="100%" cellspacing="0"
                                                        cellpadding="0" border="0"
                                                        style="background-color:#ffffff;padding:15px 20px;border-radius:8px;-webkit-border-radius:8px;border:1px solid #e5e7eb;">
                                                        <tr>
                                                            <td
                                                                style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:5px;padding-bottom:5px;">
                                                                Application Date
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:16px;font-weight:600;color:#1f2937;">
                                                                <?= date('Y-m-d') ?>
                                                                <span
                                                                    style="background-color:#dcfce7;color:#1e3a8a;padding:6px 12px;border-radius:20px;-webkit-border-radius:20px;font-size:13px;font-weight:600;text-transform:uppercase;">New</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                            <!-- Action Buttons -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="background-color:#f1f5f9;border-radius:8px;-webkit-border-radius:8px;text-align:center;padding:20px;margin:30px 0;">
                                <tr>
                                    <td>
                                        <p style="color:#1e40af;font-size:16px;font-weight:600;margin:0 0 15px 0;">
                                            ⚡ Quick Actions
                                        </p>
                                        <!-- <p style="color:#6b7280;font-size:14px;margin:0 0 10px 0;">
                                            <a href="" style="background-color:#1e3a8a;color:#ffffff !important;padding:12px 24px;border-radius:6px;-webkit-border-radius:6px;text-decoration:none;font-weight:600;display:inline-block;font-size:14px;">Review Application</a>
                                        </p> -->
                                        <p style="color:#6b7280;font-size:14px;margin:0;">
                                            Review and Take action against application.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td
                                        style="border:none;border-top:1px solid #e5e7eb;margin:40px 0;height:1px;font-size:1px;line-height:1px;">
                                    </td>
                                </tr>
                            </table>

                            <!-- Signature -->
                            <p style="color:#6b7280;font-size:14px;margin:0 0 25px 0;line-height:1.5;">
                                Best regards,<br>
                                <strong>HR Automation System</strong><br>
                                <span style="font-size:12px;">New applications are processed automatically</span>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td align="center"
                            style="background-color:#1f2937;padding:35px 30px;color:#9ca3af;text-align:center;">
                            <p style="font-size:14px;margin:0 0 20px 0;line-height:1.5;">
                                <strong>Suropriyo Enterprise</strong><br>
                                kolkata, West Bengal, India
                            </p>
                            <p style="font-size:13px;margin:0;">
                                <a href="https://suropriyo.in"
                                    style="color:#9ca3af;text-decoration:none;margin:0 10px;">www.suropriyo.in</a><br>
                                <!-- <a href="tel:+91 80176 69102"
                                    style="color:#9ca3af;text-decoration:none;margin:0 10px;">+91 80176 69102</a> -->
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>