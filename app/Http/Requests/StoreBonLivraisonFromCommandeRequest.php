<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * Validates the optional partial-shipping quantities when converting a Commande
 * into a Bon de Livraison. The authorization gate mirrors the controller's own
 * `convert_commandes_to_bon_livraison` check.
 */
class StoreBonLivraisonFromCommandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('convert_commandes_to_bon_livraison');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'quantities' => 'sometimes|array',
            'quantities.*' => 'nullable|integer|min:0',
        ];
    }

    /**
     * The requested per-line quantities (commande_detail id => quantity), or null
     * to ship every remaining quantity. Zero/blank entries are dropped so a form
     * that submits every line still ships only the lines with a positive amount.
     *
     * @return array<int, int>|null
     */
    public function quantities(): ?array
    {
        if (! $this->has('quantities')) {
            return null;
        }

        $quantities = [];
        foreach ((array) $this->input('quantities', []) as $detailId => $quantity) {
            $quantity = (int) $quantity;
            if ($quantity > 0) {
                $quantities[(int) $detailId] = $quantity;
            }
        }

        return $quantities;
    }
}
