<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class QuotationSalesController extends Controller
{
    public function __construct(private readonly QuotationService $quotations) {}

    public function __invoke(Quotation $quotation)
    {
        abort_if(Gate::denies('create_quotation_sales'), 403);

        $this->quotations->loadSaleCart($quotation);

        return view('quotation.quotation-sales.create', [
            'quotation_id' => $quotation->id,
            'sale' => $quotation,
        ]);
    }
}
