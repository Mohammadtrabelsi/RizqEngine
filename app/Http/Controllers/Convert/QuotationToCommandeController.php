<?php

namespace App\Http\Controllers\Convert;

use App\Exceptions\ConversionException;
use App\Models\Quotation;
use App\Services\CommandeService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

/**
 * Devis → Commande (direct, shorter path).
 */
class QuotationToCommandeController extends Controller
{
    public function __construct(private readonly CommandeService $commandes) {}

    public function __invoke(Quotation $quotation)
    {
        abort_if(Gate::denies('convert_quotations_to_commande'), 403);

        try {
            $commande = $this->commandes->createFromQuotation($quotation);
        } catch (ConversionException $e) {
            return redirect()->route('quotations.show', $quotation)->with('error', $e->getMessage());
        }

        session()->flash('success', trans('commande.created-from-devis', [
            'reference' => $commande->reference,
        ]));

        return redirect()->route('commandes.show', $commande);
    }
}
