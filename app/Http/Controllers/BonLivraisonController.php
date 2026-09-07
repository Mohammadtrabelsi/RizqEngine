<?php

namespace App\Http\Controllers;

use App\Exceptions\ConversionException;
use App\Models\BonLivraison;
use App\Services\BonLivraisonService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class BonLivraisonController extends Controller
{
    public function __construct(private readonly BonLivraisonService $bonLivraisons) {}

    public function index()
    {
        abort_if(Gate::denies('access_bon_livraisons'), 403);

        return view('bonlivraison.index');
    }

    public function show(BonLivraison $bonLivraison)
    {
        abort_if(Gate::denies('show_bon_livraisons'), 403);

        [$bonLivraison, $customer] = $this->bonLivraisons->showData($bonLivraison);

        return view('bonlivraison.show', compact('bonLivraison', 'customer'));
    }

    public function deliver(BonLivraison $bonLivraison)
    {
        abort_if(Gate::denies('deliver_bon_livraisons'), 403);

        try {
            $this->bonLivraisons->markDelivered($bonLivraison);
        } catch (ConversionException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('bonlivraison.delivered'));

        return redirect()->route('bon-livraisons.show', $bonLivraison);
    }

    public function destroy(BonLivraison $bonLivraison)
    {
        abort_if(Gate::denies('delete_bon_livraisons'), 403);

        $this->bonLivraisons->delete($bonLivraison);

        session()->flash('warning', trans('bonlivraison.deleted'));

        return redirect()->route('bon-livraisons.index');
    }
}
