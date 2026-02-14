<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 40px; }
        .invoice-title { font-size: 36px; font-weight: bold; margin-bottom: 10px; }
        .invoice-number { font-size: 18px; color: #666; }
        .details { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .section { width: 48%; }
        .section-title { font-weight: bold; text-transform: uppercase; color: #666; font-size: 12px; margin-bottom: 10px; }
        .client-name { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f5f5f5; padding: 12px; text-align: left; font-weight: bold; text-transform: uppercase; font-size: 12px; border-bottom: 2px solid #ddd; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .totals { margin-left: auto; width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; }
        .totals-row.total { font-size: 20px; font-weight: bold; border-top: 2px solid #333; padding-top: 12px; margin-top: 8px; }
        .notes { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; }
        .status { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-sent { background: #d1ecf1; color: #0c5460; }
        .status-draft { background: #f8f9fa; color: #6c757d; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .status-cancelled { background: #ffeeba; color: #856404; }
    </style>
</head>
<body>
    <div class="header">
        <div class="invoice-title">INVOICE</div>
        <div class="invoice-number">{{ $invoice->invoice_number }}</div>
        <div style="margin-top: 10px;">
            <span class="status status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
        </div>
    </div>
    <div class="details" style="display: table; width: 100%;">
        <div class="section" style="display: table-cell; width: 48%;">
            <div class="section-title">Billed To:</div>
            <div class="client-name">{{ $invoice->client_name }}</div>
            @if($invoice->client_email)
            <div>{{ $invoice->client_email }}</div>
            @endif
            @if($invoice->client_phone)
            <div>{{ $invoice->client_phone }}</div>
            @endif
            @if($invoice->client_address)
            <div style="margin-top: 10px;">{{ $invoice->client_address }}</div>
            @endif
        </div>
        <div class="section" style="display: table-cell; width: 48%;">
            <div>
                <div class="section-title">Invoice Date:</div>
                <div>{{ $invoice->invoice_date->format('F d, Y') }}</div>
            </div>
            <div style="margin-top: 15px;">
                <div class="section-title">Due Date:</div>
                <div>{{ $invoice->due_date->format('F d, Y') }}</div>
            </div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right" style="width: 80px;">Qty</th>
                <th class="text-right" style="width: 120px;">Unit Price</th>
                <th class="text-right" style="width: 120px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ auth()->user()->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ auth()->user()->currency_symbol }}{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="totals">
        <div class="totals-row">
            <span>Subtotal:</span>
            <span>{{ auth()->user()->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
        </div>
        @if($invoice->tax_rate > 0)
        <div class="totals-row">
            <span>Tax ({{ $invoice->tax_rate }}%):</span>
            <span>{{ auth()->user()->currency_symbol }}{{ number_format($invoice->tax_amount, 2) }}</span>
        </div>
        @endif
        @if($invoice->discount_amount > 0)
        <div class="totals-row">
            <span>Discount:</span>
            <span>-{{ auth()->user()->currency_symbol }}{{ number_format($invoice->discount_amount, 2) }}</span>
        </div>
        @endif
        <div class="totals-row total">
            <span>Total:</span>
            <span>{{ auth()->user()->currency_symbol }}{{ number_format($invoice->total_amount, 2) }}</span>
        </div>
    </div>
    @if($invoice->notes || $invoice->terms)
    <div class="notes">
        @if($invoice->notes)
        <div style="margin-bottom: 20px;">
            <div class="section-title">Notes:</div>
            <div>{{ $invoice->notes }}</div>
        </div>
        @endif
        @if($invoice->terms)
        <div>
            <div class="section-title">Payment Terms:</div>
            <div>{{ $invoice->terms }}</div>
        </div>
        @endif
    </div>
    @endif
    @if($invoice->status === 'paid' && $invoice->paid_date)
    <div style="margin-top: 40px; padding: 15px; background: #d4edda; border-radius: 5px; text-align: center;">
        <strong>PAID</strong> on {{ $invoice->paid_date->format('F d, Y') }}
    </div>
    @endif
</body>
</html>
