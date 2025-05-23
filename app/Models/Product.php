<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Models\Offer;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

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
        'category_id', // Level 1 category
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
                    $product->category_id = $category->id;
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

    // Media Helper methods
    public function addMediaToGallery($file)
    {
        return $this->addMedia($file)->toMediaCollection('gallery');
    }
    
    public function getGalleryMedia()
    {
        return $this->getMedia('gallery');
    }
    
    public function addDocument($file)
    {
        return $this->addMedia($file)->toMediaCollection('documents');
    }
    
    public function getDocuments()
    {
        return $this->getMedia('documents');
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
    
    /**
     * العلاقة مع العروض الخاصة بالمنتج
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
    
    /**
     * الحصول على العرض النشط الحالي للمنتج
     */
    public function activeOffer()
    {
        return $this->hasOne(Offer::class)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest();
    }
    
    public function getSellingPrice($quantity = 1)
    {
        if ($this->wholesale_price && $this->wholesale_quantity && $quantity >= $this->wholesale_quantity) {
            return $this->wholesale_price;
        }
        return $this->retail_price ?? $this->price;
    }
    
    /**
     * حساب هامش الربح كنسبة مئوية
     */
    public function getProfitMarginAttribute()
    {
        if (!$this->price || $this->price <= 0) {
            return 0;
        }
        
        $sellingPrice = $this->retail_price ?? $this->price;
        $cost = $this->price;
        
        if ($sellingPrice <= $cost) {
            return 0;
        }
        
        return round((($sellingPrice - $cost) / $cost) * 100, 2);
    }
    
    /**
     * حساب القيمة الإجمالية للمخزون (الكمية × سعر التكلفة)
     */
    public function getStockValueAttribute()
    {
        $quantity = $this->quantity ?? 0;
        $cost = $this->price ?? 0;
        
        return $quantity * $cost;
    }
    
    /**
     * التحقق مما إذا كانت كمية المخزون أقل من أو تساوي مستوى التنبيه
     */
    public function getIsLowStockAttribute()
    {
        $quantity = $this->quantity ?? 0;
        $alertQuantity = $this->alert_quantity ?? 0;
        
        return $quantity <= $alertQuantity;
    }
    
    /**
     * إنشاء رابط صورة الباركود
     */
    public function getBarcodeImageUrlAttribute()
    {
        if (empty($this->barcode)) {
            return null;
        }
        
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($this->barcode, $generator::TYPE_CODE_128);
            $base64 = 'data:image/png;base64,' . base64_encode($barcodeData);
            return $base64;
        } catch (\Exception $e) {
            \Log::error('Failed to generate barcode: ' . $e->getMessage());
            return null;
        }
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
