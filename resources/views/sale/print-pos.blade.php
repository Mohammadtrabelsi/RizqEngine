<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @page {
            margin: 8px 5px;
        }
        * {
            font-size: 12px;
            line-height: 18px;
            font-family: 'Ubuntu', sans-serif;
        }
        h2 {
            font-size: 16px;
        }
        td,
        th,
        tr,
        table {
            border-collapse: collapse;
        }
        tr {border-bottom: 1px dashed #ddd;}
        td,th {padding: 7px 0;width: 50%;}

        table {width: 100%;}
        tfoot tr th:first-child {text-align: left;}

        .centered {
            text-align: center;
            align-content: center;
        }
        small{font-size:11px;}

        @media print {
            * {
                font-size:12px;
                line-height: 20px;
            }
            td,th {padding: 5px 0;}
            .hidden-print {
                display: none !important;
            }
            tbody::after {
                content: '';
                display: block;
                page-break-after: always;
                page-break-inside: auto;
                page-break-before: avoid;
            }
        }
    </style>
</head>
<body>

<div style="max-width:400px;margin:0 auto">
    <div id="receipt-data">
        <div class="centered">
            <h2 style="margin-bottom: 5px">{{ settings()->company_name }}</h2>

            <p style="font-size: 11px;line-height: 15px;margin-top: 0">
                {{ settings()->company_email }}, {{ settings()->company_phone }}
                <br>{{ settings()->company_address }}
            </p>
        </div>
        <p>
            Date: {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}<br>
            Reference: {{ $sale->reference }}<br>
            Name: {{ $sale->customer_name }}
        </p>
        <div class="receipt-items">
            @foreach($sale->saleDetails as $saleDetail)
                <div style="display:flex;justify-content:space-between;border-bottom:1px dashed #ddd;padding:5px 0;">
                    <span>{{ $saleDetail->product->product_name }}
                        ({{ $saleDetail->quantity }} x {{ format_currency($saleDetail->price) }})</span>
                    <span style="text-align:right;">{{ format_currency($saleDetail->sub_total) }}</span>
                </div>
            @endforeach

            @if($sale->tax_percentage)
                <div style="display:flex;justify-content:space-between;border-bottom:1px dashed #ddd;padding:5px 0;font-weight:bold;">
                    <span>Tax ({{ $sale->tax_percentage }}%)</span><span>{{ format_currency($sale->tax_amount) }}</span>
                </div>
            @endif
            @if($sale->discount_percentage)
                <div style="display:flex;justify-content:space-between;border-bottom:1px dashed #ddd;padding:5px 0;font-weight:bold;">
                    <span>Discount ({{ $sale->discount_percentage }}%)</span><span>{{ format_currency($sale->discount_amount) }}</span>
                </div>
            @endif
            @if($sale->shipping_amount)
                <div style="display:flex;justify-content:space-between;border-bottom:1px dashed #ddd;padding:5px 0;font-weight:bold;">
                    <span>Shipping</span><span>{{ format_currency($sale->shipping_amount) }}</span>
                </div>
            @endif
            <div style="display:flex;justify-content:space-between;border-bottom:1px dashed #ddd;padding:5px 0;font-weight:bold;">
                <span>Grand Total</span><span>{{ format_currency($sale->total_amount) }}</span>
            </div>
        </div>
        <div class="receipt-payment" style="margin-top:10px;">
            <div style="display:flex;background-color:#ddd;">
                <div class="centered" style="padding:5px;width:50%;">Paid By: {{ $sale->payment_method }}</div>
                <div class="centered" style="padding:5px;width:50%;">Amount: {{ format_currency($sale->paid_amount) }}</div>
            </div>
            <div class="centered" style="margin-top:10px;">
                {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeSVG($sale->reference, 'C128', 1, 25, 'black', false) !!}
            </div>
        </div>
    </div>
</div>

</body>
</html>
