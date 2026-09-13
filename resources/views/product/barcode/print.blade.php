<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Barcodes</title>
    <link rel="stylesheet" href="{{ public_path('css/print.css') }}">
</head>
<body>
<div class="container">
    <div class="row">
        @foreach($labels as $label)
            <div class="col-xs-3" style="border: 1px solid #dddddd;border-style: dashed;">
                @if($showName)
                    <p style="font-size: 15px;color: #000;margin-top: 15px;margin-bottom: 5px;">
                        {{ $label['name'] }}
                    </p>
                @endif
                <div>
                    {!! $label['svg'] !!}
                </div>
                @if($showCode)
                    <p style="font-size: 13px;color: #000;margin-bottom: 5px;">
                        {{ $label['code'] }}
                    </p>
                @endif
                @if($showPrice)
                    <p style="font-size: 15px;color: #000;font-weight: bold;">
                        {{ __('product.price') }}: {{ format_currency($label['price']) }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>
</div>
</body>
</html>
