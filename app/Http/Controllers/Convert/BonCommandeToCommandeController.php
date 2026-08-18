<?php

namespace App\Http\Controllers\Convert;

use App\Exceptions\ConversionException;
use App\Models\BonCommande;
use App\Services\CommandeService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Bon de Commande → Commande.
 */
class BonCommandeToCommandeController extends Controller
{
    public function __construct(private readonly CommandeService $commandes) {}

    public function __invoke(BonCommande $bonCommande)
    {
        abort_if(Gate::denies('convert_bon_commandes'), 403);

        try {
            $commande = $this->commandes->createFromBonCommande($bonCommande);
        } catch (ConversionException $e) {
            return redirect()->route('bon-commandes.show', $bonCommande)->with('error', $e->getMessage());
        }

        session()->flash('success', trans('commande.created-from-bon-commande', [
            'reference' => $commande->reference,
        ]));

        return redirect()->route('commandes.show', $commande);
    }
}
