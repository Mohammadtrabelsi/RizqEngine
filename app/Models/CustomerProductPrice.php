<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A negotiated sale price for a single product/customer pair.
 *
 * When present it overrides the product's default {@see Product::$product_price}
 * on sale documents for that customer. The price is stored as an integer in the
 * same unit as product_price.
 *
 * @property int $id
 * @property int $customer_id
 * @property int $product_id
 * @property int $price
 */
class CustomerProductPrice extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    /** @var array<string, string> */
    protected $casts = [
        'price' => 'integer',
    ];

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
