<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Invoice {{ $purchase->reference }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #333333;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .invoice-wrapper { padding: 24px 28px; }

        /* Header */
        .invoice-header { width: 100%; margin-bottom: 24px; }
        .invoice-header td { vertical-align: top; }
        .invoice-header .logo img { width: 170px; }
        .invoice-header .doc-title {
            text-align: right;
            font-size: 26px;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 1px;
        }
        .invoice-header .doc-ref {
            text-align: right;
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }
        .invoice-header .doc-ref strong { color: #111827; }

        /* Info panels */
        .info-table { width: 100%; border-collapse: separate; border-spacing: 12px 0; margin-bottom: 28px; }
        .info-table td {
            vertical-align: top;
            width: 33.33%;
            background: #f9fafb;
            border: 1px solid #eef0f3;
            border-radius: 6px;
            padding: 14px 16px;
        }
        .info-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
        }
        .info-name { font-weight: bold; font-size: 13px; color: #111827; margin-bottom: 2px; }
        .info-line { color: #4b5563; }
        .info-row { margin-bottom: 2px; }
        .info-row .label { color: #6b7280; }
        .info-row .value { font-weight: bold; color: #111827; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-paid { background: #dcfce7; color: #15803d; }
        .badge-unpaid { background: #fee2e2; color: #b91c1c; }
        .badge-partial { background: #fef3c7; color: #b45309; }
        .badge-status { background: #e0e7ff; color: #4338ca; }

        /* Items table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .items-table thead th {
            background: #1f2937;
            color: #ffffff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 10px 12px;
            text-align: left;
        }
        .items-table thead th.text-right { text-align: right; }
        .items-table thead th.text-center { text-align: center; }
        .items-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #eef0f3;
            vertical-align: top;
        }
        .items-table tbody tr:nth-child(even) { background: #fafbfc; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .product-name { font-weight: bold; color: #111827; }
        .product-code {
            display: inline-block;
            margin-top: 3px;
            padding: 1px 7px;
            border-radius: 4px;
            background: #d1fae5;
            color: #047857;
            font-size: 10px;
            font-weight: bold;
        }

        /* Totals */
        .totals-wrap { width: 100%; }
        .totals-wrap td { vertical-align: top; }
        .totals-table { width: 300px; border-collapse: collapse; float: right; }
        .totals-table td { padding: 7px 12px; }
        .totals-table .label { color: #4b5563; }
        .totals-table .amount { text-align: right; font-weight: bold; color: #111827; }
        .totals-table .grand td {
            background: #1f2937;
            color: #ffffff;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px;
        }
        .totals-table .grand .amount { color: #ffffff; }

        .invoice-footer {
            margin-top: 60px;
            text-align: center;
            color: #9ca3af;
            font-size: 11px;
            font-style: italic;
            border-top: 1px solid #eef0f3;
            padding-top: 14px;
        }
    </style>
</head>
<body>
<div class="invoice-wrapper">

    {{-- Header --}}
    <table class="invoice-header">
        <tr>
            <td class="logo">
                <img src="{{ public_path('images/logo-dark.png') }}" alt="{{ settings()->company_name }}">
            </td>
            <td>
                <div class="doc-title">INVOICE</div>
                <div class="doc-ref">Reference: <strong>{{ $purchase->reference }}</strong></div>
                <div class="doc-ref">Invoice No: <strong>INV/{{ $purchase->reference }}</strong></div>
            </td>
        </tr>
    </table>

    {{-- Info panels --}}
    <table class="info-table">
        <tr>
            <td>
                <div class="info-title">Company Info</div>
                <div class="info-name">{{ settings()->company_name }}</div>
                <div class="info-line">{{ settings()->company_address }}</div>
                <div class="info-line">Email: {{ settings()->company_email }}</div>
                <div class="info-line">Phone: {{ settings()->company_phone }}</div>
            </td>
            <td>
                <div class="info-title">Supplier Info</div>
                <div class="info-name">{{ $supplier->supplier_name }}</div>
                <div class="info-line">{{ $supplier->address }}</div>
                <div class="info-line">Email: {{ $supplier->supplier_email ?: '—' }}</div>
                <div class="info-line">Phone: {{ $supplier->supplier_phone ?: '—' }}</div>
            </td>
            <td>
                <div class="info-title">Invoice Info</div>
                <div class="info-row">
                    <span class="label">Date:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Status:</span>
                    <span class="badge badge-status">{{ $purchase->status }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Payment:</span>
                    @php
                        $ps = strtolower($purchase->payment_status);
                        $psClass = $ps === 'paid' ? 'badge-paid' : ($ps === 'partial' ? 'badge-partial' : 'badge-unpaid');
                    @endphp
                    <span class="badge {{ $psClass }}">{{ $purchase->payment_status }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th class="text-right">Unit Price</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Tax</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchase->purchaseDetails as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product_name }}</div>
                        <span class="product-code">{{ $item->product_code }}</span>
                    </td>
                    <td class="text-right">{{ format_currency($item->unit_price) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ format_currency($item->product_discount_amount) }}</td>
                    <td class="text-right">{{ format_currency($item->product_tax_amount) }}</td>
                    <td class="text-right"><strong>{{ format_currency($item->sub_total) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #9ca3af;">
                        No products on this purchase.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals-wrap">
        <tr>
            <td>
                <table class="totals-table">
                    <tr>
                        <td class="label">Discount ({{ $purchase->discount_percentage }}%)</td>
                        <td class="amount">{{ format_currency($purchase->discount_amount) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tax ({{ $purchase->tax_percentage }}%)</td>
                        <td class="amount">{{ format_currency($purchase->tax_amount) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Shipping</td>
                        <td class="amount">{{ format_currency($purchase->shipping_amount) }}</td>
                    </tr>
                    <tr class="grand">
                        <td>{{ __('withholding.total_ttc') }}</td>
                        <td class="amount">{{ format_currency($purchase->total_amount) }}</td>
                    </tr>
                    @if($purchase->withholding_amount > 0)
                        @foreach($purchase->withholdingTaxes as $line)
                            <tr>
                                <td class="label">{{ $line->name }} ({{ rtrim(rtrim(number_format($line->rate, 3), '0'), '.') }}%)</td>
                                <td class="amount">(-) {{ format_currency($line->amount) }}</td>
                            </tr>
                        @endforeach
                        <tr class="grand">
                            <td>{{ __('withholding.net_payable') }}</td>
                            <td class="amount">{{ format_currency($purchase->net_payable) }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <div class="invoice-footer">
        {{ settings()->company_name }} &copy; {{ date('Y') }}. Thank you for your business.
    </div>

</div>
</body>
</html>
