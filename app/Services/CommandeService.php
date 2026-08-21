<?php

namespace App\Services;

use App\Exceptions\ConversionException;
use App\Models\BonCommande;
use App\Models\Commande;
use App\Models\CommandeDetails;
use Illuminate\Support\Facades\DB;

/**
 * Business operations for the Commande — the confirmed customer order created
 * from a Bon de Commande, and the last document before invoicing (Facture).
 *
 * Like {@see BonCommandeService}, the conversion copies the raw integer-cent
 * columns verbatim and is guarded (row lock + explicit check + unique
 * bon_commande_id index) against duplicate conversion.
 */
class CommandeService
{
    /**
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
     * Transform a confirmed Bon de Commande into a Commande.
     *
     * @throws ConversionException when the BC is cancelled or already converted.
     */
    public function createFromBonCommande(BonCommande $bonCommande): Commande
    {
        return DB::transaction(function () use ($bonCommande) {
            // Serialize concurrent conversions of the same Bon de Commande.
            $bonCommande = BonCommande::lockForUpdate()->findOrFail($bonCommande->id);

            if ($bonCommande->isCancelled()) {
                throw new ConversionException(trans('commande.cannot-convert-cancelled'));
            }

            if ($bonCommande->status !== BonCommande::STATUS_CONFIRMED) {
                throw new ConversionException(trans('commande.only-confirmed-convertible'));
            }

            if ($bonCommande->commande()->exists()) {
                throw new ConversionException(
                    trans('commande.already-converted', ['reference' => $bonCommande->reference])
                );
            }

            $commande = new Commande;
            $commande->date = now()->format('Y-m-d');
            $commande->bon_commande_id = $bonCommande->id;
            $commande->customer_id = $bonCommande->customer_id;
            $commande->customer_name = $bonCommande->customer_name;
            $commande->status = Commande::STATUS_PENDING;

            foreach (self::HEADER_COLUMNS as $column) {
                $commande->{$column} = $bonCommande->getRawOriginal($column);
            }

            $commande->save();

            foreach ($bonCommande->bonCommandeDetails as $detail) {
                $line = ['commande_id' => $commande->id];

                foreach (self::LINE_COLUMNS as $column) {
                    $line[$column] = $detail->getRawOriginal($column);
                }

                CommandeDetails::create($line);
            }

            // Mark the source BC as converted so it cannot be converted again
            // or cancelled after the fact.
            $bonCommande->update(['status' => BonCommande::STATUS_CONVERTED]);

            return $commande->refresh();
        });
    }

    /**
     * Confirm a pending Commande (pending → confirmed).
     *
     * @throws ConversionException
     */
    public function confirm(Commande $commande): Commande
    {
        if ($commande->status !== Commande::STATUS_PENDING) {
            throw new ConversionException(trans('commande.only-pending-confirmable'));
        }

        $commande->update(['status' => Commande::STATUS_CONFIRMED]);

        return $commande;
    }
}
