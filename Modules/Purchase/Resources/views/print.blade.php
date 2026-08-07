<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Details</title>
    <link rel="stylesheet" href="{{ public_path('b3/bootstrap.min.css') }}">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div style="text-align: center;margin-bottom: 25px;">
                <img width="180" src="{{ public_path('images/logo-dark.png') }}" alt="Logo">
                <h4 style="margin-bottom: 20px;">
                    <span>Reference::</span> <strong>{{ $purchase->reference }}</strong>
                </h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xs-4 mb-3 mb-md-0">
                            <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">Company Info:</h4>
                            <div><strong>{{ settings()->company_name }}</strong></div>
                            <div>{{ settings()->company_address }}</div>
                            <div>Email: {{ settings()->company_email }}</div>
                            <div>Phone: {{ settings()->company_phone }}</div>
                        </div>

                        <div class="col-xs-4 mb-3 mb-md-0">
                            <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">Supplier Info:</h4>
                            <div><strong>{{ $supplier->supplier_name }}</strong></div>
                            <div>{{ $supplier->address }}</div>
                            <div>Email: {{ $supplier->supplier_email }}</div>
                            <div>Phone: {{ $supplier->supplier_phone }}</div>
                        </div>

                        <div class="col-xs-4 mb-3 mb-md-0">
                            <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">Invoice Info:</h4>
                            <div>Invoice: <strong>INV/{{ $purchase->reference }}</strong></div>
                            <div>Date: {{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}</div>
                            <div>
                                Status: <strong>{{ $purchase->status }}</strong>
                            </div>
                            <div>
                                Payment Status: <strong>{{ $purchase->payment_status }}</strong>
                            </div>
                        </div>

                    </div>

                    <div class="row" style="margin-top: 30px;">
                        @foreach($purchase->purchaseDetails as $item)
                            <div class="col-xs-6" style="margin-bottom: 20px;">
                                <div class="card">
                                    <div class="card-body">
                                        <div><strong>{{ $item->product_name }}</strong>
                                            <span class="badge badge-success">{{ $item->product_code }}</span>
                                        </div>
                                        <ul class="list-group" style="margin-top: 10px;">
                                            <li class="list-group-item">Net Unit Price <span class="pull-right">{{ format_currency($item->unit_price) }}</span></li>
                                            <li class="list-group-item">Quantity <span class="pull-right">{{ $item->quantity }}</span></li>
                                            <li class="list-group-item">Discount <span class="pull-right">{{ format_currency($item->product_discount_amount) }}</span></li>
                                            <li class="list-group-item">Tax <span class="pull-right">{{ format_currency($item->product_tax_amount) }}</span></li>
                                            <li class="list-group-item">Sub Total <span class="pull-right"><strong>{{ format_currency($item->sub_total) }}</strong></span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-8">
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Discount ({{ $purchase->discount_percentage }}%)</strong> <span class="pull-right">{{ format_currency($purchase->discount_amount) }}</span></li>
                                <li class="list-group-item"><strong>Tax ({{ $purchase->tax_percentage }}%)</strong> <span class="pull-right">{{ format_currency($purchase->tax_amount) }}</span></li>
                                <li class="list-group-item"><strong>Shipping</strong> <span class="pull-right">{{ format_currency($purchase->shipping_amount) }}</span></li>
                                <li class="list-group-item"><strong>Grand Total</strong> <span class="pull-right"><strong>{{ format_currency($purchase->total_amount) }}</strong></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 25px;">
                        <div class="col-xs-12">
                            <p style="font-style: italic;text-align: center">{{ settings()->company_name }} &copy; {{ date('Y') }}.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
