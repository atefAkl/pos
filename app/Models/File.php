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
        'formatted_size'
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
        $icons = [
            'image' => 'fas fa-image',
            'pdf' => 'fas fa-file-pdf',
            'doc' => 'fas fa-file-word',
            'xls' => 'fas fa-file-excel',
            'ppt' => 'fas fa-file-powerpoint',
            'zip' => 'fas fa-file-archive',
            'txt' => 'fas fa-file-alt',
            'audio' => 'fas fa-file-audio',
            'video' => 'fas fa-file-video',
            'code' => 'fas fa-file-code',
            'default' => 'fas fa-file'
        ];

        $type = explode('/', $this->mime_type)[0];
        $ext = $this->extension;

        if ($type === 'image') return $icons['image'];
        if ($type === 'audio') return $icons['audio'];
        if ($type === 'video') return $icons['video'];
        if ($ext === 'pdf') return $icons['pdf'];
        if (in_array($ext, ['doc', 'docx'])) return $icons['doc'];
        if (in_array($ext, ['xls', 'xlsx', 'csv'])) return $icons['xls'];
        if (in_array($ext, ['ppt', 'pptx'])) return $icons['ppt'];
        if (in_array($ext, ['zip', 'rar', 'tar', 'gz'])) return $icons['zip'];
        if (in_array($ext, ['txt', 'log', 'md'])) return $icons['txt'];
        if (in_array($ext, ['html', 'css', 'js', 'php', 'json', 'xml'])) return $icons['code'];

        return $icons['default'];
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
