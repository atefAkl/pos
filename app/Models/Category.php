<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'parent_id',
        'level'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (is_null($category->parent_id)) {
                $category->level = 1;
            } else {
                $parent = Category::findOrFail($category->parent_id);
                $category->level = $parent->level + 1;
                
                // Prevent more than 3 levels
                if ($category->level > 3) {
                    throw new \Exception('لا يمكن إنشاء أكثر من 3 مستويات من التصنيفات');
                }
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Get all descendants
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Get all ancestors
    public function ancestors()
    {
        return $this->belongsTo(Category::class, 'parent_id')->with('ancestors');
    }

    // Check if category is a parent
    public function isParent()
    {
        return $this->level === 1;
    }

    // Check if category is a sub-category
    public function isSubCategory()
    {
        return $this->level === 2;
    }

    // Check if category is a child-category
    public function isChildCategory()
    {
        return $this->level === 3;
    }
    
    /**
     * Check if the category is a descendant of another category
     *
     * @param Category $parent
     * @return bool
     */
    /**
     * الحصول على الفئات من المستوى الثالث مع مسارها الكامل
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getThirdLevelWithPath()
    {
        // جلب الفئات من المستوى الثالث فقط
        $categories = static::where('level', 3)
            ->with(['parent.parent']) // تحميل الأجداد
            ->orderBy('name')
            ->get();
            
        // إضافة مسار الاسم الكامل
        return $categories->map(function($category) {
            $path = [];
            
            // إضافة الجد (المستوى الأول) إذا موجود
            if ($category->parent && $category->parent->parent) {
                $path[] = $category->parent->parent->name;
            }
            
            // إضافة الأب (المستوى الثاني) إذا موجود
            if ($category->parent) {
                $path[] = $category->parent->name;
            }
            
            // إضافة الاسم الحالي (المستوى الثالث)
            $path[] = $category->name;
            
            // تعيين المسار الكامل كخاصية جديدة
            $category->full_path = implode(' > ', $path);
            
            return $category;
        });
    }
    
    /**
     * Check if the category is a descendant of another category
     *
     * @param Category $parent
     * @return bool
     */
    public function isDescendantOf(Category $parent)
    {
        $current = $this->parent;
        
        while ($current) {
            if ($current->id === $parent->id) {
                return true;
            }
            $current = $current->parent;
        }
        
        return false;
    }

    // Get all active categories
    public static function getActiveCategories()
    {
        return self::where('is_active', true)
            ->where('level', 1)
            ->with(['children' => function($query) {
                $query->where('is_active', true)
                    ->with(['children' => function($query) {
                        $query->where('is_active', true);
                    }]);
            }])
            ->get();
    }
}
