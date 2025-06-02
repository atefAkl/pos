<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory; // Assuming you might want to use factories later

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'sub_total',
    ];

    /**
     * Get the sale that owns the item.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the product associated with the sale item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
