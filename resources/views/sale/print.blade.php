<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Facture {{ $sale->reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a1a;
            background: #f4f2ee;
            font-size: 13px;
        }
        .page { padding: 45px 55px; }
        .invoice-title {
            font-size: 72px;
            font-weight: bold;
            letter-spacing: 2px;
            line-height: 1;
            margin-bottom: 18px;
        }
        .meta-pills { margin-bottom: 22px; }
        .pill {
            display: inline-block;
            border: 1px solid #1a1a1a;
            border-radius: 20px;
            padding: 7px 22px;
            font-size: 14px;
            margin-right: 10px;
        }
        .divider { border: none; border-top: 1px solid #1a1a1a; margin: 0 0 30px 0; }
        .parties { width: 100%; margin-bottom: 30px; }
        .parties td { vertical-align: top; }
        .parties .right { text-align: right; }
        .party-label { font-weight: bold; font-size: 14px; margin-bottom: 12px; }
        .party-line { margin-bottom: 3px; }
        .party-name { font-weight: bold; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.items thead th {
            background: #1a1a1a;
            color: #f4f2ee;
            text-align: center;
            padding: 14px 12px;
            font-size: 13px;
            letter-spacing: 1px;
        }
        table.items thead th.desc { text-align: left; }
        table.items tbody td {
            border: 1px solid #1a1a1a;
            padding: 13px 12px;
            text-align: center;
        }
        table.items tbody td.desc { text-align: center; }
        .totals { width: 100%; margin-top: 18px; }
        .totals td { padding: 6px 12px; font-size: 16px; }
        .totals .label { text-align: right; font-weight: bold; }
        .totals .value { text-align: right; font-weight: bold; width: 130px; }
        .totals .grand td {
            background: #1a1a1a;
            color: #f4f2ee;
            padding: 14px 12px;
            font-size: 18px;
        }
        .footer { margin-top: 60px; width: 100%; }
        .footer td { vertical-align: top; font-size: 13px; }
        .footer .right { text-align: right; }
        .footer-title { font-weight: bold; margin-bottom: 4px; }
        .thanks {
            text-align: center;
            letter-spacing: 3px;
            padding-top: 18px;
            margin-top: 22px;
            border-top: 1px solid #1a1a1a;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="invoice-title">FACTURE</div>
    <div class="meta-pills">
        <span class="pill">Facture n°{{ $sale->reference }}</span>
        <span class="pill">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/y') }}</span>
    </div>
    <hr class="divider">

    <table class="parties">
        <tr>
            <td>
                <div class="party-label">{{ strtoupper(settings()->company_name) }}</div>
                <div class="party-line">{{ settings()->company_phone }}</div>
                <div class="party-line">{{ settings()->company_email }}</div>
                <div class="party-line">{{ settings()->company_address }}</div>
            </td>
            <td class="right">
                <div class="party-label">À L'ATTENTION DE</div>
                <div class="party-line party-name">{{ $customer->customer_name }}</div>
                <div class="party-line">{{ $customer->customer_phone }}</div>
                <div class="party-line">{{ $customer->address }}</div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="desc">DESCRIPTION</th>
                <th>PRIX</th>
                <th>QUANTITÉ</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleDetails as $item)
                <tr>
                    <td class="desc">{{ $item->product_name }}</td>
                    <td>{{ format_currency($item->unit_price) }}</td>
                    <td>{{ str_pad($item->quantity, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ format_currency($item->sub_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Sous total :</td>
            <td class="value">{{ format_currency($sale->total_amount - $sale->tax_amount + $sale->discount_amount) }}</td>
        </tr>
        <tr>
            <td class="label">TVA ({{ $sale->tax_percentage }}%) :</td>
            <td class="value">{{ format_currency($sale->tax_amount) }}</td>
        </tr>
        <tr class="grand">
            <td class="label">TOTAL :</td>
            <td class="value">{{ format_currency($sale->total_amount) }}</td>
        </tr>
    </table>

    <table class="footer">
        <tr>
            <td>
                <div class="footer-title">Paiement à l'ordre de {{ settings()->company_name }}</div>
                <div>Référence facture : {{ $sale->reference }}</div>
            </td>
            <td class="right">
                <div class="footer-title">Conditions de paiement</div>
                <div>Statut : {{ $sale->payment_status }}</div>
            </td>
        </tr>
    </table>

    <div class="thanks">MERCI DE VOTRE CONFIANCE</div>
</div>
</body>
</html>
