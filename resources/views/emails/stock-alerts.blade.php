<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.stock-alert-subject', ['company' => $company]) }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#20232a;">
    <div style="max-width:640px;margin:0 auto;padding:24px;">
        <h1 style="font-size:20px;margin:0 0 4px;">{{ $company }}</h1>
        <p style="margin:0 0 24px;color:#555;">{{ __('app.stock-alert-intro') }}</p>

        <h2 style="font-size:16px;border-bottom:2px solid #e5e7eb;padding-bottom:6px;">
            {{ __('app.stock-alert-low-heading') }} ({{ $lowStock->count() }})
        </h2>
        @if ($lowStock->isEmpty())
            <p style="color:#888;">{{ __('app.stock-alert-none') }}</p>
        @else
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:24px;">
                <thead>
                    <tr style="text-align:left;color:#666;font-size:12px;text-transform:uppercase;">
                        <th style="padding:6px 8px;">{{ __('app.product') }}</th>
                        <th style="padding:6px 8px;">{{ __('app.code') }}</th>
                        <th style="padding:6px 8px;text-align:right;">{{ __('app.quantity') }}</th>
                        <th style="padding:6px 8px;text-align:right;">{{ __('app.alert') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lowStock as $product)
                        <tr style="border-top:1px solid #eee;">
                            <td style="padding:8px;">{{ $product->product_name }}</td>
                            <td style="padding:8px;color:#666;">{{ $product->product_code }}</td>
                            <td style="padding:8px;text-align:right;font-weight:600;color:{{ $product->product_quantity <= 0 ? '#c0392b' : '#b7791f' }};">
                                {{ $product->product_quantity }}
                            </td>
                            <td style="padding:8px;text-align:right;color:#666;">{{ $product->product_stock_alert }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h2 style="font-size:16px;border-bottom:2px solid #e5e7eb;padding-bottom:6px;">
            {{ __('app.stock-alert-expiring-heading', ['days' => $expiryDays]) }} ({{ $expiring->count() }})
        </h2>
        @if ($expiring->isEmpty())
            <p style="color:#888;">{{ __('app.stock-alert-none') }}</p>
        @else
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left;color:#666;font-size:12px;text-transform:uppercase;">
                        <th style="padding:6px 8px;">{{ __('app.product') }}</th>
                        <th style="padding:6px 8px;">{{ __('app.code') }}</th>
                        <th style="padding:6px 8px;text-align:right;">{{ __('app.quantity') }}</th>
                        <th style="padding:6px 8px;text-align:right;">{{ __('app.expiry-date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expiring as $product)
                        <tr style="border-top:1px solid #eee;">
                            <td style="padding:8px;">{{ $product->product_name }}</td>
                            <td style="padding:8px;color:#666;">{{ $product->product_code }}</td>
                            <td style="padding:8px;text-align:right;">{{ $product->product_quantity }}</td>
                            <td style="padding:8px;text-align:right;color:#b7791f;">
                                {{ optional($product->expiry_date)->format('Y-m-d') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p style="margin-top:32px;font-size:12px;color:#999;">
            {{ __('app.stock-alert-footer') }}
        </p>
    </div>
</body>
</html>
