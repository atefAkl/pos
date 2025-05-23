<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'description',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'rate' => 'float',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function active()
    {
        return self::where('is_active', true);
    }
}
