<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    protected $table = 'quotation_items'; // Explicitly define the table name

    protected $fillable = [
        'quotation_id',
        'product_id',
        'quantity',
        'price',
        'sub_total',
    ];

    /**
     * Get the quotation that owns the item.
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Get the product associated with the quotation item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
