<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFile extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'file_id',
        'category',
        'is_active',
        'order'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];
    
    /**
     * العلاقة مع المنتج
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * العلاقة مع الملف
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
    
    /**
     * فئات الملفات المتاحة
     */
    public static function getCategories(): array
    {
        return [
            'product_image' => 'صورة المنتج',
            'gallery_image' => 'صورة معرض',
            'purchase_invoice' => 'فاتورة مشتريات',
            'stock_report' => 'تقرير مخزون',
            'barcode' => 'باركود',
            'document' => 'مستند',
            'other' => 'أخرى'
        ];
    }
    
    /**
     * الحصول على اسم الفئة بالعربية
     */
    public function getCategoryNameAttribute(): string
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? $this->category;
    }
    
    /**
     * الحصول على أيقونة الفئة
     */
    public function getCategoryIconAttribute(): string
    {
        $icons = [
            'product_image' => 'fas fa-image',
            'gallery_image' => 'fas fa-images',
            'purchase_invoice' => 'fas fa-file-invoice-dollar',
            'stock_report' => 'fas fa-chart-bar',
            'barcode' => 'fas fa-barcode',
            'document' => 'fas fa-file-alt',
            'other' => 'fas fa-file'
        ];
        
        return $icons[$this->category] ?? 'fas fa-file';
    }
}
