<?php

namespace App\Http\Controllers\Convert;

use App\Exceptions\ConversionException;
use App\Exceptions\InsufficientStockException;
use App\Models\Commande;
use App\Services\StockExitService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Commande → Bon de Sortie (consignment / dépôt-vente).
 *
 * Hands the order's lines out to its customer as a consignment exit. The sold
 * portion is invoiced — and warehouse stock updated — later, when the matching
 * Bon d'Entrée regularises the exit.
 */
class CommandeToStockExitController extends Controller
{
    public function __construct(private readonly StockExitService $stockExits) {}

    public function __invoke(Commande $commande)
    {
        abort_if(Gate::denies('convert_commandes_to_stock_exit'), 403);

        try {
            $stockExit = $this->stockExits->createFromCommande($commande);
        } catch (ConversionException|InsufficientStockException $e) {
            return redirect()->route('commandes.show', $commande)->with('error', $e->getMessage());
        }

        session()->flash('success', trans('stockexit.created-from-commande', [
            'reference' => $stockExit->reference,
        ]));

        return redirect()->route('stock-exits.show', $stockExit);
    }
}
