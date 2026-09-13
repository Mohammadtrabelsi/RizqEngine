<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * The document chain breadcrumb for the order workflows. Two paths are
 * supported and rendered depending on which documents are supplied:
 *
 *   Bon de Commande → Devis → Commande → Facture                    (classic path)
 *   Bon de Commande → Devis → Commande → Bon de Livraison → Facture (delivery-note path)
 *
 * The delivery-note path is used whenever a Bon de Livraison is supplied or the
 * current step is the delivery note; otherwise the classic path is shown. The
 * step list (labels, references and links) is assembled here so the template
 * carries no @php.
 */
class DocumentChain extends Component
{
    /** @var list<array{key: string, label: string, ref: ?string, url: ?string}> */
    public array $steps;

    public function __construct(
        public string $current,
        public mixed $quotation = null,
        public mixed $bonCommande = null,
        public mixed $commande = null,
        public mixed $bonLivraison = null,
        public mixed $sale = null,
    ) {
        $bonCommandeStep = ['key' => 'bon_commande', 'label' => __('boncommande.bon_commande'), 'ref' => $bonCommande->reference ?? null, 'url' => $bonCommande ? route('bon-commandes.show', $bonCommande->id) : null];
        $quotationStep = ['key' => 'quotation', 'label' => __('boncommande.devis'), 'ref' => $quotation->reference ?? null, 'url' => $quotation ? route('quotations.show', $quotation->id) : null];
        $commandeStep = ['key' => 'commande', 'label' => __('commande.commande'), 'ref' => $commande->reference ?? null, 'url' => $commande ? route('commandes.show', $commande->id) : null];
        $saleStep = ['key' => 'sale', 'label' => __('commande.facture'), 'ref' => $sale->reference ?? null, 'url' => $sale ? route('sales.show', $sale->id) : null];

        if ($bonLivraison !== null || $current === 'bon_livraison') {
            $this->steps = [
                $bonCommandeStep,
                $quotationStep,
                $commandeStep,
                ['key' => 'bon_livraison', 'label' => __('bonlivraison.bon_livraison'), 'ref' => $bonLivraison->reference ?? null, 'url' => $bonLivraison ? route('bon-livraisons.show', $bonLivraison->id) : null],
                $saleStep,
            ];

            return;
        }

        $this->steps = [
            $bonCommandeStep,
            $quotationStep,
            $commandeStep,
            $saleStep,
        ];
    }

    public function render(): View
    {
        return view('components.document-chain');
    }
}
