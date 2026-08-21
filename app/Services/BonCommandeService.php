<?php

namespace App\Services;

use App\Exceptions\ConversionException;
use App\Models\BonCommande;
use App\Models\BonCommandeDetails;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;

/**
 * Business operations for the Bon de Commande (BC) — the customer order created
 * from an accepted Devis (Quotation).
 *
 * The conversion copies the raw integer-cent columns straight across so the
 * customer, lines, quantities, unit prices, discounts, taxes and totals of the
 * Devis are preserved exactly (no float round-trip). Every conversion runs in a
 * single transaction and is guarded — by a row lock, an explicit check and the
 * unique quotation_id index — against duplicate conversion under concurrent
 * requests / double clicks.
 */
class BonCommandeService
{
    /**
     * The monetary + descriptive columns shared by a Quotation and a
     * Bon de Commande, stored as integer cents. Copied verbatim.
     *
     * @var string[]
     */
    private const HEADER_COLUMNS = [
        'tax_percentage',
        'tax_amount',
        'discount_percentage',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'note',
    ];

    /**
     * The line columns shared by quotation_details and bon_commande_details.
     *
     * @var string[]
     */
    private const LINE_COLUMNS = [
        'product_id',
        'product_name',
        'product_code',
        'quantity',
        'price',
        'unit_price',
        'sub_total',
        'product_discount_amount',
        'product_discount_type',
        'product_tax_amount',
    ];

    /**
     * Transform an accepted Devis into a Bon de Commande.
     *
     * @throws ConversionException when the Devis has already been converted.
     */
    public function createFromQuotation(Quotation $quotation): BonCommande
    {
        return DB::transaction(function () use ($quotation) {
            // Serialize concurrent conversions of the same Devis.
            $quotation = Quotation::lockForUpdate()->findOrFail($quotation->id);

            if ($quotation->bonCommande()->exists()) {
                throw new ConversionException(
                    trans('boncommande.already-converted', ['reference' => $quotation->reference])
                );
            }

            $bonCommande = new BonCommande;
            $bonCommande->date = now()->format('Y-m-d');
            $bonCommande->quotation_id = $quotation->id;
            $bonCommande->customer_id = $quotation->customer_id;
            $bonCommande->customer_name = $quotation->customer_name;
            $bonCommande->status = BonCommande::STATUS_DRAFT;

            foreach (self::HEADER_COLUMNS as $column) {
                $bonCommande->{$column} = $quotation->getRawOriginal($column);
            }

            $bonCommande->save();

            foreach ($quotation->quotationDetails as $detail) {
                $line = ['bon_commande_id' => $bonCommande->id];

                foreach (self::LINE_COLUMNS as $column) {
                    $line[$column] = $detail->getRawOriginal($column);
                }

                BonCommandeDetails::create($line);
            }

            return $bonCommande->refresh();
        });
    }

    /**
     * Confirm a draft Bon de Commande (draft → confirmed).
     *
     * @throws ConversionException
     */
    public function confirm(BonCommande $bonCommande): BonCommande
    {
        if ($bonCommande->isCancelled()) {
            throw new ConversionException(trans('boncommande.cannot-confirm-cancelled'));
        }

        if ($bonCommande->status !== BonCommande::STATUS_DRAFT) {
            throw new ConversionException(trans('boncommande.only-draft-confirmable'));
        }

        $bonCommande->update(['status' => BonCommande::STATUS_CONFIRMED]);

        return $bonCommande;
    }

    /**
     * Cancel a Bon de Commande that has not yet become a Commande.
     *
     * @throws ConversionException
     */
    public function cancel(BonCommande $bonCommande): BonCommande
    {
        if ($bonCommande->isConverted()) {
            throw new ConversionException(trans('boncommande.cannot-cancel-converted'));
        }

        $bonCommande->update(['status' => BonCommande::STATUS_CANCELLED]);

        return $bonCommande;
    }

    /**
     * Rebuild a Bon de Commande from the "bon_commande" cart instance,
     * mirroring the existing Devis edit flow.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(BonCommande $bonCommande, array $data): BonCommande
    {
        return DB::transaction(function () use ($bonCommande, $data) {
            $cart = Cart::instance('bon_commande');

            foreach ($bonCommande->bonCommandeDetails as $detail) {
                $detail->delete();
            }

            $bonCommande->update([
                'date' => $data['date'],
                'customer_id' => $data['customer_id'],
                'customer_name' => Customer::findOrFail($data['customer_id'])->customer_name,
                'tax_percentage' => $data['tax_percentage'],
                'discount_percentage' => $data['discount_percentage'],
                'shipping_amount' => $data['shipping_amount'] * 100,
                'total_amount' => $data['total_amount'] * 100,
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
                'tax_amount' => (float) $cart->tax() * 100,
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            foreach ($cart->content() as $cart_item) {
                BonCommandeDetails::create([
                    'bon_commande_id' => $bonCommande->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price * 100,
                    'unit_price' => $cart_item->options->unit_price * 100,
                    'sub_total' => $cart_item->options->sub_total * 100,
                    'product_discount_amount' => $cart_item->options->product_discount * 100,
                    'product_discount_type' => $cart_item->options->product_discount_type,
                    'product_tax_amount' => $cart_item->options->product_tax * 100,
                ]);
            }

            $cart->destroy();

            return $bonCommande->refresh();
        });
    }

    /**
     * Rehydrate the "bon_commande" cart from a stored Bon de Commande so the
     * shared ProductCart Livewire component can edit it (same pattern the Devis
     * edit screen uses).
     */
    public function loadCart(BonCommande $bonCommande): void
    {
        Cart::instance('bon_commande')->destroy();

        $cart = Cart::instance('bon_commande');

        foreach ($bonCommande->bonCommandeDetails as $detail) {
            $cart->add([
                'id' => $detail->product_id,
                'name' => $detail->product_name,
                'qty' => $detail->quantity,
                'price' => $detail->price,
                'weight' => 1,
                'options' => [
                    'product_discount' => $detail->product_discount_amount,
                    'product_discount_type' => $detail->product_discount_type,
                    'sub_total' => $detail->sub_total,
                    'code' => $detail->product_code,
                    'stock' => Product::findOrFail($detail->product_id)->product_quantity,
                    'product_tax' => $detail->product_tax_amount,
                    'unit_price' => $detail->unit_price,
                ],
            ]);
        }
    }
}
