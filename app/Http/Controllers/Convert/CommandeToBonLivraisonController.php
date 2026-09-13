<?php

namespace App\Http\Controllers\Convert;

use App\Exceptions\ConversionException;
use App\Http\Requests\StoreBonLivraisonFromCommandeRequest;
use App\Models\Commande;
use App\Services\BonLivraisonService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Commande → Bon de Livraison (delivery note), with optional partial shipping:
 * an optional `quantities` map (commande_detail id => quantity) delivers only a
 * subset of the ordered quantities; its absence ships everything still due.
 */
class CommandeToBonLivraisonController extends Controller
{
    public function __construct(private readonly BonLivraisonService $bonLivraisons) {}

    public function __invoke(StoreBonLivraisonFromCommandeRequest $request, Commande $commande)
    {
        abort_if(Gate::denies('convert_commandes_to_bon_livraison'), 403);

        try {
            $bonLivraison = $this->bonLivraisons->createFromCommande($commande, $request->quantities());
        } catch (ConversionException $e) {
            return redirect()->route('commandes.show', $commande)->with('error', $e->getMessage());
        }

        session()->flash('success', trans('bonlivraison.created-from-commande', [
            'reference' => $bonLivraison->reference,
        ]));

        return redirect()->route('bon-livraisons.show', $bonLivraison);
    }
}
