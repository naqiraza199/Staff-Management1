<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Signature Request</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td align="center" style="padding:40px 0;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                <!-- Header -->
                <tr>
                    <td style="background:#2563eb;padding:20px;text-align:center;color:#fff;font-size:22px;font-weight:bold;">
                        📄 Document Signature Request
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:30px 40px;color:#333;font-size:16px;line-height:1.6;">
                        <p style="margin:0 0 16px;">Hello,</p>

                        <p style="margin:0 0 16px;">
                            You have a document pending for signature: 
                            <strong style="color:#111;">{{ $document->name }}</strong>
                        </p>

                        <p style="margin:0 0 24px;">
                            Please review and sign the document by clicking the button below:
                        </p>

                        <!-- Button -->
                        <p style="text-align:center;">
                            <a href="{{ $url }}" 
                               style="display:inline-block;padding:14px 28px;background:#2563eb;color:#fff;
                               text-decoration:none;font-weight:bold;border-radius:6px;">
                                ✍️ Sign Document
                            </a>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f9fafb;padding:20px;text-align:center;font-size:13px;color:#888;">
                        Thank you for using our service.<br>
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
