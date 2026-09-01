<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * The Devis → Bon de Commande → Commande → Facture document chain breadcrumb.
 * The step list (labels, references and links) is assembled here so the
 * template carries no @php.
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
        public mixed $sale = null,
    ) {
        $this->steps = [
            ['key' => 'quotation', 'label' => __('boncommande.devis'), 'ref' => $quotation->reference ?? null, 'url' => $quotation ? route('quotations.show', $quotation->id) : null],
            ['key' => 'bon_commande', 'label' => __('boncommande.bon_commande'), 'ref' => $bonCommande->reference ?? null, 'url' => $bonCommande ? route('bon-commandes.show', $bonCommande->id) : null],
            ['key' => 'commande', 'label' => __('commande.commande'), 'ref' => $commande->reference ?? null, 'url' => $commande ? route('commandes.show', $commande->id) : null],
            ['key' => 'sale', 'label' => __('commande.facture'), 'ref' => $sale->reference ?? null, 'url' => $sale ? route('sales.show', $sale->id) : null],
        ];
    }

    public function render(): View
    {
        return view('components.document-chain');
    }
}
