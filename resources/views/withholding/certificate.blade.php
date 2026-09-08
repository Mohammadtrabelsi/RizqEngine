<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ __('withholding.certificate_title') }} — {{ $certificate['invoice']['reference'] }}</title>
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
        .wrapper { padding: 28px 32px; }
        h1 { font-size: 20px; text-align: center; text-transform: uppercase; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #6b7280; margin-bottom: 24px; }
        .panels { width: 100%; margin-bottom: 20px; }
        .panels td { vertical-align: top; width: 50%; padding: 8px; }
        .panel { border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px; }
        .panel h3 { margin: 0 0 8px; font-size: 13px; color: #111827; }
        .panel .row { margin-bottom: 3px; }
        .panel .label { color: #6b7280; }
        table.lines { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table.lines th, table.lines td { border: 1px solid #e5e7eb; padding: 8px; text-align: right; }
        table.lines th { background: #f9fafb; text-align: right; }
        table.lines th:first-child, table.lines td:first-child { text-align: left; }
        .totals { width: 45%; margin-left: 55%; border-collapse: collapse; }
        .totals td { padding: 6px 8px; }
        .totals .grand { font-weight: bold; border-top: 2px solid #111827; }
        .signatures { width: 100%; margin-top: 48px; }
        .signatures td { width: 50%; text-align: center; color: #6b7280; }
    </style>
</head>
<body>
<div class="wrapper">
    <h1>{{ __('withholding.certificate_title') }}</h1>
    <p class="subtitle">{{ __('withholding.certificate_subtitle') }}</p>

    <table class="panels">
        <tr>
            <td>
                <div class="panel">
                    <h3>{{ __('withholding.withholder') }}</h3>
                    <div class="row"><span class="label">{{ __('withholding.name') }}:</span> {{ $certificate['company']['name'] }}</div>
                    <div class="row"><span class="label">{{ __('withholding.tax_id') }}:</span> {{ $certificate['company']['tax_id'] }}</div>
                    <div class="row"><span class="label">{{ __('withholding.address') }}:</span> {{ $certificate['company']['address'] }}</div>
                </div>
            </td>
            <td>
                <div class="panel">
                    <h3>{{ __('withholding.beneficiary') }}</h3>
                    <div class="row"><span class="label">{{ __('withholding.name') }}:</span> {{ $certificate['beneficiary']['name'] }}</div>
                    <div class="row"><span class="label">{{ __('withholding.tax_id') }}:</span> {{ $certificate['beneficiary']['tax_id'] }}</div>
                    @if($certificate['beneficiary']['legal_form'])
                        <div class="row"><span class="label">{{ __('withholding.legal_form') }}:</span> {{ $certificate['beneficiary']['legal_form'] }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="row">
        <strong>{{ __('withholding.invoice_reference') }}:</strong> {{ $certificate['invoice']['reference'] }}
        &nbsp;·&nbsp;
        <strong>{{ __('withholding.invoice_date') }}:</strong> {{ $certificate['invoice']['date'] }}
    </div>

    <table class="lines">
        <thead>
        <tr>
            <th>{{ __('withholding.name') }}</th>
            <th>{{ __('withholding.calculation_base') }}</th>
            <th>{{ __('withholding.taxable_amount') }}</th>
            <th>{{ __('withholding.rate') }}</th>
            <th>{{ __('withholding.amount') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($certificate['lines'] as $line)
            <tr>
                <td>{{ $line['name'] }}</td>
                <td>{{ __('withholding.base_'.$line['calculation_base']) }}</td>
                <td>{{ format_currency($line['taxable_amount']) }}</td>
                <td>{{ rtrim(rtrim(number_format($line['rate'], 3), '0'), '.') }}%</td>
                <td>{{ format_currency($line['amount']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>{{ __('withholding.total_ttc') }}</td><td style="text-align:right">{{ format_currency($certificate['total_ttc']) }}</td></tr>
        <tr><td>{{ __('withholding.withholding') }}</td><td style="text-align:right">{{ format_currency($certificate['withholding_total']) }}</td></tr>
        <tr class="grand"><td>{{ __('withholding.net_paid') }}</td><td style="text-align:right">{{ format_currency($certificate['net_paid']) }}</td></tr>
    </table>

    <table class="signatures">
        <tr>
            <td>{{ __('withholding.withholding_date') }}: {{ $certificate['withholding_date'] }}</td>
            <td>{{ __('withholding.signature') }}</td>
        </tr>
    </table>
</div>
</body>
</html>
