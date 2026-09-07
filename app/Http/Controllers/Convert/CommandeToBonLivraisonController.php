<?php

namespace App\Http\Controllers\Convert;

use App\Exceptions\ConversionException;
use App\Models\Commande;
use App\Services\BonLivraisonService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Commande → Bon de Livraison (delivery note).
 */
class CommandeToBonLivraisonController extends Controller
{
    public function __construct(private readonly BonLivraisonService $bonLivraisons) {}

    public function __invoke(Commande $commande)
    {
        abort_if(Gate::denies('convert_commandes_to_bon_livraison'), 403);

        try {
            $bonLivraison = $this->bonLivraisons->createFromCommande($commande);
        } catch (ConversionException $e) {
            return redirect()->route('commandes.show', $commande)->with('error', $e->getMessage());
        }

        session()->flash('success', trans('bonlivraison.created-from-commande', [
            'reference' => $bonLivraison->reference,
        ]));

        return redirect()->route('bon-livraisons.show', $bonLivraison);
    }
}
