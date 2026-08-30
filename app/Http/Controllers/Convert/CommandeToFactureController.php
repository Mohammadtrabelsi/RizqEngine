<?php

namespace App\Http\Controllers\Convert;

use App\Exceptions\ConversionException;
use App\Exceptions\InsufficientStockException;
use App\Models\Commande;
use App\Services\SaleService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Commande → Facture (Sale). Reuses the existing Sale/invoice implementation.
 */
class CommandeToFactureController extends Controller
{
    public function __construct(private readonly SaleService $sales) {}

    public function __invoke(Commande $commande)
    {
        abort_if(Gate::denies('convert_commandes'), 403);

        try {
            $sale = $this->sales->createFactureFromCommande($commande);
        } catch (ConversionException|InsufficientStockException $e) {
            return redirect()->route('commandes.show', $commande)->with('error', $e->getMessage());
        }

        session()->flash('success', trans('commande.facture-generated', [
            'reference' => $sale->reference,
        ]));

        return redirect()->route('sales.show', $sale);
    }
}
