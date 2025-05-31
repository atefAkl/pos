<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'path',
        'file_name',
        'original_name',
        'display_name',
        'mime_type',
        'extension',
        'size',
        'alt_text'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'url',
        'icon',
        'formatted_size',
        'icon_color_class'
    ];

    /**
     * العلاقة مع المنتجات
     */
    public function productFiles(): HasMany
    {
        return $this->hasMany(ProductFile::class);
    }

    /**
     * الحصول على رابط الملف
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    /**
     * الحصول على أيقونة مناسبة حسب نوع الملف
     */
    public function getIconAttribute(): string
    {
        return $this->getFileIcon($this->mime_type, $this->extension, $this->category);
    }

    /**
     * الحصول على فئة اللون لأيقونة الملف
     */
    public function getIconColorClassAttribute(): string
    {
        $icon = $this->icon; // Access the existing icon attribute
        $colorClass = 'text-secondary'; // Default color with text- prefix

        if (strpos($icon, 'file-word') !== false) {
            $colorClass = 'text-primary';
        } elseif (strpos($icon, 'file-excel') !== false || strpos($icon, 'file-csv') !== false) {
            $colorClass = 'text-success';
        } elseif (strpos($icon, 'file-powerpoint') !== false) {
            $colorClass = 'text-warning';
        } elseif (strpos($icon, 'file-pdf') !== false) {
            $colorClass = 'text-danger';
        } elseif (strpos($icon, 'file-image') !== false) {
            $colorClass = 'text-info';
        } elseif (strpos($icon, 'file-video') !== false) {
            $colorClass = 'text-danger'; 
        } elseif (strpos($icon, 'file-audio') !== false) {
            $colorClass = 'text-info';
        } elseif (strpos($icon, 'file-archive') !== false) {
            $colorClass = 'text-secondary';
        } elseif (strpos($icon, 'file-code') !== false) {
            $colorClass = 'text-dark';
        } elseif (strpos($icon, 'fa-barcode') !== false) { // Check for barcode icon
            $colorClass = 'text-warning';
        }

        return $colorClass;
    }

    /**
     * تحديد الأيقونة المناسبة للملف بناءً على نوعه وامتداده وفئته
     */
    private function getFileIcon($mimeType, $extension, $category = null): string
    {
        // تحويل الامتداد إلى أحرف صغيرة
        $extension = strtolower($extension);
        
        // أيقونات حسب فئة الملف
        $categoryIcons = [
            'product_image' => 'fas fa-image',
            'gallery_image' => 'fas fa-images',
            'barcode' => 'fas fa-barcode',
            'document' => 'fas fa-file-alt',
            'other' => 'fas fa-file',
        ];
        
        // أيقونات حسب نوع الملف
        $extensionIcons = [
            // مستندات نصية
            'doc' => 'fas fa-file-word',
            'docx' => 'fas fa-file-word',
            'odt' => 'fas fa-file-word',
            'rtf' => 'fas fa-file-alt',
            'txt' => 'fas fa-file-alt',
            
            // جداول بيانات
            'xls' => 'fas fa-file-excel',
            'xlsx' => 'fas fa-file-excel',
            'ods' => 'fas fa-file-excel',
            'csv' => 'fas fa-file-csv',
            
            // عروض تقديمية
            'ppt' => 'fas fa-file-powerpoint',
            'pptx' => 'fas fa-file-powerpoint',
            'odp' => 'fas fa-file-powerpoint',
            
            // PDF
            'pdf' => 'fas fa-file-pdf',
            
            // صور
            'jpg' => 'fas fa-file-image',
            'jpeg' => 'fas fa-file-image',
            'png' => 'fas fa-file-image',
            'gif' => 'fas fa-file-image',
            'svg' => 'fas fa-file-image',
            'webp' => 'fas fa-file-image',
            'bmp' => 'fas fa-file-image',
            'tiff' => 'fas fa-file-image',
            
            // فيديو
            'mp4' => 'fas fa-file-video',
            'avi' => 'fas fa-file-video',
            'mov' => 'fas fa-file-video',
            'wmv' => 'fas fa-file-video',
            'mkv' => 'fas fa-file-video',
            'webm' => 'fas fa-file-video',
            
            // صوت
            'mp3' => 'fas fa-file-audio',
            'wav' => 'fas fa-file-audio',
            'ogg' => 'fas fa-file-audio',
            'flac' => 'fas fa-file-audio',
            
            // أرشيف
            'zip' => 'fas fa-file-archive',
            'rar' => 'fas fa-file-archive',
            '7z' => 'fas fa-file-archive',
            'tar' => 'fas fa-file-archive',
            'gz' => 'fas fa-file-archive',
            
            // كود
            'html' => 'fas fa-file-code',
            'css' => 'fas fa-file-code',
            'js' => 'fas fa-file-code',
            'php' => 'fas fa-file-code',
            'py' => 'fas fa-file-code',
            'java' => 'fas fa-file-code',
            'c' => 'fas fa-file-code',
            'cpp' => 'fas fa-file-code',
            'json' => 'fas fa-file-code',
            'xml' => 'fas fa-file-code',
            
            // قواعد بيانات
            'sql' => 'fas fa-database',
            'db' => 'fas fa-database',
            'sqlite' => 'fas fa-database',
            
            // تنفيذية
            'exe' => 'fas fa-cogs',
            'msi' => 'fas fa-cogs',
            'bat' => 'fas fa-terminal',
            'sh' => 'fas fa-terminal',
            
            // أخرى
            'ttf' => 'fas fa-font',
            'otf' => 'fas fa-font',
            'woff' => 'fas fa-font',
            'woff2' => 'fas fa-font',
        ];
        
        // 1. أولاً: تحقق من الامتداد
        if (isset($extensionIcons[$extension])) {
            return $extensionIcons[$extension];
        }
        
        // 2. ثانياً: تحقق من نوع MIME
        if ($mimeType) {
            $mime = strtolower($mimeType);
            
            if (strpos($mime, 'image/') === 0) {
                return 'fas fa-file-image';
            } elseif (strpos($mime, 'video/') === 0) {
                return 'fas fa-file-video';
            } elseif (strpos($mime, 'audio/') === 0) {
                return 'fas fa-file-audio';
            } elseif (strpos($mime, 'text/') === 0) {
                return 'fas fa-file-alt';
            } elseif (strpos($mime, 'application/pdf') === 0) {
                return 'fas fa-file-pdf';
            } elseif (strpos($mime, 'application/msword') === 0 || strpos($mime, 'application/vnd.openxmlformats-officedocument.wordprocessingml') === 0) {
                return 'fas fa-file-word';
            } elseif (strpos($mime, 'application/vnd.ms-excel') === 0 || strpos($mime, 'application/vnd.openxmlformats-officedocument.spreadsheetml') === 0) {
                return 'fas fa-file-excel';
            } elseif (strpos($mime, 'application/vnd.ms-powerpoint') === 0 || strpos($mime, 'application/vnd.openxmlformats-officedocument.presentationml') === 0) {
                return 'fas fa-file-powerpoint';
            } elseif (strpos($mime, 'application/zip') === 0 || strpos($mime, 'application/x-rar') === 0 || strpos($mime, 'application/x-7z-compressed') === 0) {
                return 'fas fa-file-archive';
            }
        }
        
        // 3. ثالثاً: تحقق من الفئة
        if ($category && isset($categoryIcons[$category])) {
            return $categoryIcons[$category];
        }
        
        // 4. أيقونة افتراضية
        return 'fas fa-file';
    }

    /**
     * تنسيق حجم الملف ليكون سهل القراءة
     */
    public function getFormattedSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->size;
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * التحقق مما إذا كان الملف صورة
     */
    public function isImage(): bool
    {
        return strpos($this->mime_type, 'image/') === 0;
    }

    /**
     * التحقق مما إذا كان الملف مستند PDF
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}
