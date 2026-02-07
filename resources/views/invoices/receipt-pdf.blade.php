<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .receipt-info { margin-bottom: 30px; }
        .amount { font-size: 24px; font-weight: bold; color: #2563eb; }
        .details { margin-top: 20px; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Receipt</h1>
        <h2>Receipt #{{ $receipt->receipt_number }}</h2>
    </div>

    <div class="receipt-info">
        <p><strong>Receipt Date:</strong> {{ $receipt->receipt_date->format('F d, Y') }}</p>
        <p><strong>Client:</strong> {{ $receipt->client_name }}</p>
        <p><strong>Invoice Number:</strong> {{ $receipt->invoice->invoice_number }}</p>
        
        <div style="margin: 20px 0; padding: 15px; border: 2px solid #2563eb; border-radius: 5px;">
            <p style="margin: 0;"><strong>Amount Received:</strong></p>
            <p class="amount" style="margin: 5px 0 0 0;">${{ number_format($receipt->amount, 2) }}</p>
        </div>
    </div>

    <div class="details">
        <p><strong>Description:</strong> {{ $receipt->description }}</p>
        
        @if(isset($receipt->receipt_data['payment_method']))
            <p><strong>Payment Method:</strong> {{ ucwords(str_replace('_', ' ', $receipt->receipt_data['payment_method'])) }}</p>
        @endif
        
        @if(isset($receipt->receipt_data['reference_number']) && $receipt->receipt_data['reference_number'])
            <p><strong>Reference Number:</strong> {{ $receipt->receipt_data['reference_number'] }}</p>
        @endif
        
        @if(isset($receipt->receipt_data['notes']) && $receipt->receipt_data['notes'])
            <p><strong>Notes:</strong> {{ $receipt->receipt_data['notes'] }}</p>
        @endif
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y \a\t H:i') }}</p>
        <p>Thank you for your payment!</p>
    </div>
</body>
</html>