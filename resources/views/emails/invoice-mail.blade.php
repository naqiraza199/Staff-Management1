<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
        .email-box {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 600px; margin: auto;
        }
        .email-header { font-size: 20px; font-weight: bold; margin-bottom: 15px; }
        .email-footer { margin-top: 20px; font-size: 12px; color: #666; }
        .btn {
    display: inline-block;
    padding: 8px 13px;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-box">
        <div class="email-header">Invoice #{{ $invoice->id }}</div>
        <p>Dear {{ $invoice->client->name ?? 'Client' }},</p>
        <p>Please find your invoice attached in PDF format.</p>

        <p>
            <a href="{{ url("/invoices/{$invoice->id}/print") }}" class="btn">View Invoice</a>
        </p>

        <div class="email-footer">
            Thank you for your business! <br>
            {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
