<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Quotation;
use App\Services\QuotationService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class QuotationController extends Controller
{
    public function __construct(private readonly QuotationService $quotations) {}

    public function index()
    {
        abort_if(Gate::denies('access_quotations'), 403);

        return view('quotation.index');

    }

    public function create()
    {
        abort_if(Gate::denies('create_quotations'), 403);

        Cart::instance('quotation')->destroy();

        return view('quotation.create');
    }

    public function store(StoreQuotationRequest $request)
    {
        $this->quotations->create($request->validated() + ['date' => $request->date]);

        session()->flash('success', trans('quotation.quotation-created'));

        return redirect()->route('quotations.index');
    }

    public function show(Quotation $quotation)
    {
        abort_if(Gate::denies('show_quotations'), 403);

        $customer = $this->quotations->customerFor($quotation);

        return view('quotation.show', compact('quotation', 'customer'));
    }

    public function edit(Quotation $quotation)
    {
        abort_if(Gate::denies('edit_quotations'), 403);

        $this->quotations->loadCart($quotation);

        return view('quotation.edit', compact('quotation'));
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        $this->quotations->update($quotation, $request->validated() + ['date' => $request->date]);

        session()->flash('info', trans('quotation.quotation-updated'));

        return redirect()->route('quotations.index');
    }

    public function destroy(Quotation $quotation)
    {
        abort_if(Gate::denies('delete_quotations'), 403);

        $this->quotations->delete($quotation);

        session()->flash('warning', trans('quotation.quotation-deleted'));

        return redirect()->route('quotations.index');
    }
}
