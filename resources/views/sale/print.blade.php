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
        .header { width: 100%; margin-bottom: 8px; }
        .header td { vertical-align: middle; }
        .header .brand { text-align: right; }
        .header .brand img { max-height: 90px; max-width: 240px; }
        .header .brand .company { font-weight: bold; font-size: 18px; }
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
@php
    $embedLogo = function (?string $path) {
        if (! $path || ! is_file($path)) {
            return null;
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = $ext === 'svg' ? 'image/svg+xml' : ($ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : 'image/png');

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
    };

    // White-label client logo, displayed before the application logo.
    $clientLogoData = settings()->client_logo
        ? $embedLogo(storage_path('app/public/'.settings()->client_logo))
        : null;

    // Application logo (configured site logo, else bundled default).
    $appLogoData = ($p = settings()->site_logo ? storage_path('app/public/'.settings()->site_logo) : null)
        ? $embedLogo($p)
        : $embedLogo(public_path('images/logo-dark.png'));
@endphp
<div class="page">
    <table class="header">
        <tr>
            <td>
                <div class="invoice-title">FACTURE</div>
            </td>
            <td class="brand">
                @if($clientLogoData || $appLogoData)
                    @if($clientLogoData)
                        <img src="{{ $clientLogoData }}" alt="Client logo" style="max-height:48px;vertical-align:middle;">
                    @endif
                    @if($appLogoData)
                        <img src="{{ $appLogoData }}" alt="Logo" style="max-height:48px;vertical-align:middle;">
                    @endif
                @else
                    <div class="company">{{ strtoupper(settings()->company_name) }}</div>
                @endif
            </td>
        </tr>
    </table>
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
                @if(settings()->company_tax_id)
                    <div class="party-line">M.F. : {{ settings()->company_tax_id }}</div>
                @endif
            </td>
            <td class="right">
                <div class="party-label">À L'ATTENTION DE</div>
                <div class="party-line party-name">{{ $customer->customer_name }}</div>
                <div class="party-line">{{ $customer->customer_phone }}</div>
                <div class="party-line">{{ $customer->address }}</div>
                @if($customer->tax_identification_number)
                    <div class="party-line">M.F. : {{ $customer->tax_identification_number }}</div>
                @endif
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

    @php
        // Legally-required Tunisian invoice figures. RAS is deducted after the
        // TTC; the timbre fiscal (droit de timbre) is a fixed per-invoice stamp
        // added to the amount actually due.
        $stampAmount = (float) (settings()->fiscal_stamp_amount ?? 0);
        $withholdingAmount = (float) $sale->withholding_amount;
        $netPayable = round($sale->total_amount + $stampAmount - $withholdingAmount, 3);
    @endphp

    <table class="totals">
        <tr>
            <td class="label">Total HT :</td>
            <td class="value">{{ format_currency($sale->total_amount - $sale->tax_amount + $sale->discount_amount) }}</td>
        </tr>
        @if($sale->discount_amount > 0)
            <tr>
                <td class="label">Remise :</td>
                <td class="value">- {{ format_currency($sale->discount_amount) }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">TVA ({{ $sale->tax_percentage }}%) :</td>
            <td class="value">{{ format_currency($sale->tax_amount) }}</td>
        </tr>
        @if($stampAmount > 0)
            <tr>
                <td class="label">Timbre fiscal :</td>
                <td class="value">{{ format_currency($stampAmount) }}</td>
            </tr>
        @endif
        <tr class="grand">
            <td class="label">TOTAL TTC :</td>
            <td class="value">{{ format_currency($sale->total_amount + $stampAmount) }}</td>
        </tr>
        @if($withholdingAmount > 0)
            @foreach($sale->withholdingTaxes as $line)
                <tr>
                    <td class="label">{{ $line->name }} ({{ rtrim(rtrim(number_format($line->rate, 3), '0'), '.') }}%) :</td>
                    <td class="value">- {{ format_currency($line->amount) }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="label">Retenue à la source :</td>
                <td class="value">- {{ format_currency($withholdingAmount) }}</td>
            </tr>
            <tr class="grand">
                <td class="label">NET À PAYER :</td>
                <td class="value">{{ format_currency($netPayable) }}</td>
            </tr>
        @endif
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

    @if(settings()->invoice_legal_mention)
        <div style="margin-top: 22px; font-size: 11px; color: #444;">
            {!! nl2br(e(settings()->invoice_legal_mention)) !!}
        </div>
    @endif

    <div class="thanks">MERCI DE VOTRE CONFIANCE</div>
</div>
</body>
</html>
