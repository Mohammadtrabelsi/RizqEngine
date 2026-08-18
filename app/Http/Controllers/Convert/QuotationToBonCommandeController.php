<?php

namespace App\Http\Controllers\Convert;

use App\Exceptions\ConversionException;
use App\Models\Quotation;
use App\Services\BonCommandeService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Devis → Bon de Commande.
 */
class QuotationToBonCommandeController extends Controller
{
    public function __construct(private readonly BonCommandeService $bonCommandes) {}

    public function __invoke(Quotation $quotation)
    {
        abort_if(Gate::denies('convert_quotations'), 403);

        try {
            $bonCommande = $this->bonCommandes->createFromQuotation($quotation);
        } catch (ConversionException $e) {
            return redirect()->route('quotations.index')->with('error', $e->getMessage());
        }

        session()->flash('success', trans('boncommande.created-from-quotation', [
            'reference' => $bonCommande->reference,
        ]));

        return redirect()->route('bon-commandes.show', $bonCommande);
    }
}
