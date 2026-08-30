<?php

namespace App\Http\Controllers;

use App\Exceptions\ConversionException;
use App\Http\Requests\UpdateBonCommandeRequest;
use App\Models\BonCommande;
use App\Models\Customer;
use App\Services\BonCommandeService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class BonCommandeController extends Controller
{
    public function __construct(private readonly BonCommandeService $bonCommandes) {}

    public function index()
    {
        abort_if(Gate::denies('access_bon_commandes'), 403);

        return view('boncommande.index');
    }

    public function show(BonCommande $bonCommande)
    {
        abort_if(Gate::denies('show_bon_commandes'), 403);

        $bonCommande->load(['bonCommandeDetails.product', 'quotation', 'commande.sale']);
        $customer = Customer::findOrFail($bonCommande->customer_id);

        return view('boncommande.show', compact('bonCommande', 'customer'));
    }

    public function edit(BonCommande $bonCommande)
    {
        abort_if(Gate::denies('edit_bon_commandes'), 403);

        if ($bonCommande->status !== BonCommande::STATUS_DRAFT) {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', trans('boncommande.only-draft-editable'));
        }

        $this->bonCommandes->loadCart($bonCommande);

        return view('boncommande.edit', compact('bonCommande'));
    }

    public function update(UpdateBonCommandeRequest $request, BonCommande $bonCommande)
    {
        if ($bonCommande->status !== BonCommande::STATUS_DRAFT) {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', trans('boncommande.only-draft-editable'));
        }

        $this->bonCommandes->update($bonCommande, $request->all());

        session()->flash('info', trans('boncommande.updated'));

        return redirect()->route('bon-commandes.show', $bonCommande);
    }

    public function confirm(BonCommande $bonCommande)
    {
        abort_if(Gate::denies('confirm_bon_commandes'), 403);

        try {
            $this->bonCommandes->confirm($bonCommande);
        } catch (ConversionException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('boncommande.confirmed'));

        return redirect()->route('bon-commandes.show', $bonCommande);
    }

    public function cancel(BonCommande $bonCommande)
    {
        abort_if(Gate::denies('edit_bon_commandes'), 403);

        try {
            $this->bonCommandes->cancel($bonCommande);
        } catch (ConversionException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        session()->flash('warning', trans('boncommande.cancelled'));

        return redirect()->route('bon-commandes.show', $bonCommande);
    }

    public function destroy(BonCommande $bonCommande)
    {
        abort_if(Gate::denies('delete_bon_commandes'), 403);

        Cart::instance('bon_commande')->destroy();

        $bonCommande->delete();

        session()->flash('warning', trans('boncommande.deleted'));

        return redirect()->route('bon-commandes.index');
    }
}
