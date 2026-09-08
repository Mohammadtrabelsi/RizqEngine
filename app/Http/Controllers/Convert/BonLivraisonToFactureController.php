<?php

namespace App\Http\Controllers\Convert;

use App\Exceptions\ConversionException;
use App\Exceptions\InsufficientStockException;
use App\Models\BonLivraison;
use App\Services\SaleService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Bon de Livraison → Facture (Sale). Reuses the existing Sale/invoice
 * implementation. Optional last step of the delivery-note path.
 */
class BonLivraisonToFactureController extends Controller
{
    public function __construct(private readonly SaleService $sales) {}

    public function __invoke(BonLivraison $bonLivraison)
    {
        abort_if(Gate::denies('convert_bon_livraisons'), 403);

        try {
            $sale = $this->sales->createFactureFromBonLivraison($bonLivraison);
        } catch (ConversionException|InsufficientStockException $e) {
            return redirect()->route('bon-livraisons.show', $bonLivraison)->with('error', $e->getMessage());
        }

        session()->flash('success', trans('bonlivraison.facture-generated', [
            'reference' => $sale->reference,
        ]));

        return redirect()->route('sales.show', $sale);
    }
}
