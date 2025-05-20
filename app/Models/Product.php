<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'barcode',
        'sku',
        'price',
        'tax_id',
        'tax_rate',
        'retail_price',
        'wholesale_price',
        'wholesale_quantity',
        'quantity',
        'unit_id',
        'unit',
        'weight',
        'dimensions',
        'alert_quantity',
        'reorder_level',
        'category_id',      // Level 3 category (child category)
        'sub_category_id',  // Level 2 category
        'parent_category_id', // Level 1 category
        'supplier_id',
        'brand_id',
        'description',
        'image',
        'is_service',
        'service_duration',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'weight' => 'decimal:3',
        'is_service' => 'boolean',
        'is_active' => 'boolean',
        'wholesale_quantity' => 'integer',
        'service_duration' => 'integer',
        'reorder_level' => 'integer',
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'parent_category_id' => 'integer',
        'tax_id' => 'integer',
        'unit_id' => 'integer'
    ];
    
    protected $appends = [
        'image_url',
        'profit_margin', 
        'stock_value', 
        'is_low_stock', 
        'barcode_image_url',
        'full_category_path'
    ];
    
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            // Auto-fill category hierarchy when saving
            if ($product->isDirty('category_id') && !is_null($product->category_id)) {
                $category = Category::find($product->category_id);
                if ($category) {
                    $product->sub_category_id = $category->parent_id;
                    if (!is_null($category->parent_id)) {
                        $parentCategory = Category::find($category->parent_id);
                        if ($parentCategory) {
                            $product->parent_category_id = $parentCategory->parent_id;
                        }
                    }
                }
            }
        });
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-product.png');
    }

    /**
     * Get the category that owns the product (child category - level 3)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the sub-category that owns the product (level 2)
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    /**
     * Get the parent category that owns the product (level 1)
     */
    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

    /**
     * Get the full category path (e.g., Parent > Sub > Child)
     */
    public function getFullCategoryPathAttribute(): string
    {
        $path = [];
        
        if ($this->parentCategory) {
            $path[] = $this->parentCategory->name;
        }
        
        if ($this->subCategory) {
            $path[] = $this->subCategory->name;
        }
        
        if ($this->category) {
            $path[] = $this->category->name;
        }
        
        return implode(' > ', $path);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    
    /**
     * Get the unit that owns the product
     */
    public function unitOfMeasure()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    
    /**
     * Get the tax that applies to the product
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    
    public function getSellingPrice($quantity = 1)
    {
        if ($this->wholesale_price && $this->wholesale_quantity && $quantity >= $this->wholesale_quantity) {
            return $this->wholesale_price;
        }
        return $this->retail_price ?? $this->price;
    }
    
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    public function scopeProduct($query)
    {
        return $query->where('is_service', false);
    }
    
    public function scopeService($query)
    {
        return $query->where('is_service', true);
    }
}
