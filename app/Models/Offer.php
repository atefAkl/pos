<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Offer extends Model
{
    use HasFactory;

    /**
     * الحقول التي يمكن تعبئتها
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'offer_price',
        'start_date',
        'end_date',
        'is_active',
        'description',
    ];

    /**
     * تحويل أنواع الحقول
     *
     * @var array<string, string>
     */
    protected $casts = [
        'offer_price' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع نموذج المنتج
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * نطاق الاستعلام للعروض النشطة فقط
     */
    public function scopeActive(Builder $query): void
    {
        $now = now();
        $query->where('is_active', true)
              ->where('start_date', '<=', $now)
              ->where('end_date', '>=', $now);
    }
}
