<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChildCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'sub_category_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            SubCategory::class,
            'id',
            'id',
            'sub_category_id',
            'category_id'
        );
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
