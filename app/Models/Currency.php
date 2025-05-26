<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes;

    /**
     * الحقول التي يمكن تعبئتها
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'symbol_position',
        'decimal_separator',
        'thousands_separator',
        'decimal_places',
        'is_default',
        'is_active',
        'exchange_rate',
        'created_by',
        'updated_by',
    ];

    /**
     * الحقول التي يجب تحويلها إلى أنواع محددة
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'exchange_rate' => 'decimal:6',
        'decimal_places' => 'integer',
    ];

    /**
     * الحصول على العملة الافتراضية
     *
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * الحصول على رمز العملة
     *
     * @return string
     */
    public function getSymbolAttribute($value)
    {
        return $value ?: 'ر.س'; // القيمة الافتراضية للريال السعودي
    }

    /**
     * الحصول على العملات النشطة فقط
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
