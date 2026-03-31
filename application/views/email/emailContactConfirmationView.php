<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Received - Suropriyo Enterprise</title>
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

                    <!-- Header Section -->
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
                                Contact Form Received
                            </p>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding:50px 40px;">

                            <!-- Greeting -->
                            <h2 style="color:#1f2937;font-size:24px;font-weight:600;margin:0 0 20px 0;line-height:1.3;">
                                Dear <?= $name ?>,
                            </h2>

                            <!-- Main Message -->
                            <p style="font-size:16px;margin:0 0 25px 0;line-height:1.7;color:#374151;">
                                Thank you for contacting <strong>Suropriyo Enterprise</strong>.
                                We have received your message regarding <strong><?= $subject ?></strong>
                                and will get back to you within <strong>24-48 business hours</strong>.
                            </p>

                            <!-- Message Summary Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="background-color:#f8fafc;border-left:4px solid #3b82f6;padding:25px;margin:30px 0;border-radius:0 8px 8px 0;-webkit-border-radius:0 8px 8px 0;">
                                <tr>
                                    <td>
                                        <h3 style="color:#1e40af;font-size:18px;font-weight:600;margin:0 0 12px 0;">
                                            📝 Your Message Summary
                                        </h3>
                                        <p style="font-size:15px;margin:0 0 15px 0;line-height:1.6;color:#374151;">
                                            <strong>Subject:</strong> <?= $subject ?><br>
                                            <strong>Message:</strong> <?= nl2br(htmlspecialchars($message)) ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Contact Information Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="background-color:#f1f5f9;border-radius:8px;-webkit-border-radius:8px;text-align:center;padding:20px;margin:30px 0;">
                                <tr>
                                    <td>
                                        <p style="color:#1e40af;font-size:16px;font-weight:600;margin:0 0 5px 0;">
                                            📞 Need Immediate Assistance?
                                        </p>
                                        <p style="color:#6b7280;font-size:14px;margin:0;">
                                            Call us: <a href="tel:+91 89811 74517"
                                                style="color:#3b82f6;text-decoration:none;font-weight:500;">+91 87772 70124</a><br>
                                            Email us: <a href="mailto:suropriyoenterprise@gmail.com"
                                                style="color:#3b82f6;text-decoration:none;font-weight:500;">suropriyoenterprise@gmail.com</a>
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
                                <strong>The Suropriyo Enterprise Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td align="center"
                            style="background-color:#1f2937;padding:35px 30px;color:#9ca3af;text-align:center;">
                            <p style="font-size:14px;margin:0 0 20px 0;line-height:1.5;">
                                <strong>Suropriyo Enterprise</strong><br>
                                Kolkata, West Bengal, India
                            </p>
                            <p style="font-size:13px;margin:0;">
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