<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('finance.voucher') }} {{ $outing->reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1a1a1a; background: #f4f2ee; font-size: 13px; }
        .page { padding: 45px 55px; }
        .title { font-size: 48px; font-weight: bold; letter-spacing: 2px; line-height: 1; margin-bottom: 10px; }
        .subtitle { font-size: 15px; margin-bottom: 22px; color: #444; }
        .pill { display: inline-block; border: 1px solid #1a1a1a; border-radius: 20px; padding: 6px 18px; font-size: 13px; margin-right: 8px; }
        .divider { border: none; border-top: 1px solid #1a1a1a; margin: 22px 0; }
        .meta { width: 100%; margin-bottom: 24px; }
        .meta td { vertical-align: top; padding-bottom: 6px; }
        .label { font-weight: bold; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.items th, table.items td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #ccc; }
        table.items td.amount, table.items th.amount { text-align: right; }
        .total-row td { font-weight: bold; font-size: 16px; border-top: 2px solid #1a1a1a; border-bottom: none; padding-top: 12px; }
        .footer { margin-top: 40px; font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <div class="page">
        <div class="title">{{ __('finance.voucher') }}</div>
        <div class="subtitle">{{ config('app.name') }}</div>
        <div>
            <span class="pill">{{ $outing->reference }}</span>
            <span class="pill">{{ $outing->date->format('d/m/Y') }}</span>
        </div>
        <hr class="divider">

        <table class="meta">
            <tr>
                <td width="50%"><span class="label">{{ __('finance.location') }}:</span> {{ $outing->location ?: '—' }}</td>
                <td width="50%"><span class="label">{{ __('finance.purpose') }}:</span> {{ $outing->purpose ?: '—' }}</td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">{{ __('finance.participants') }}:</span>
                    {{ !empty($outing->participants) ? implode(', ', $outing->participants) : '—' }}
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>{{ __('finance.category') }}</th>
                    <th class="amount">{{ __('finance.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['food','gas','water','transport','misc'] as $cat)
                    <tr>
                        <td>{{ __('finance.cat_'.$cat) }}</td>
                        <td class="amount">{{ number_format((float) $outing->{$cat}, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>{{ __('finance.total') }}</td>
                    <td class="amount">{{ number_format($outing->total(), 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($outing->note)
            <p style="margin-top:18px;"><span class="label">{{ __('finance.note') }}:</span> {{ $outing->note }}</p>
        @endif

        <div class="footer">
            {{ __('finance.voucher') }} {{ $outing->reference }} — {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
