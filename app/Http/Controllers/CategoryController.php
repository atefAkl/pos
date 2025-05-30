<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategorySearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected $categorySearchService;

    public function __construct(CategorySearchService $categorySearchService)
    {
        $this->categorySearchService = $categorySearchService;
    }

    /**
     * عرض قائمة الفئات مع إمكانية البحث والتصفية
     */
    public function index(Request $request)
    {
        $result = $this->categorySearchService->search($request);
        
        // إذا كان الطلب AJAX، نرجع جزء العرض فقط
        if ($request->ajax() || $request->has('ajax')) {
            return view('products.categories.partials.category_tree', $result)->render();
        }
            
        return view('products.categories.index', $result);
    }

    /**
     * Show the form for creating a new category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $parentId = $request->query('parent_id');
        
        $parent = null;
        $level = 1;
        
        if ($parentId) {
            $parent = Category::findOrFail($parentId);
            $level = $parent->level + 1;
            
            if ($level > 3) {
                return redirect()->back()
                    ->with('error', 'لا يمكن إضافة أكثر من 3 مستويات من التصنيفات');
            }
        }
        
        // جلب جميع الفئات النشطة من المستوى الأول مع فرزها بالاسم
        $parentCategories = Category::where('level', '<', $level)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        // جلب الفئات الفرعية النشطة للفئة الحالية مع فرزها بالاسم
        $subCategories = $parent 
            ? Category::where('parent_id', $parent->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get() 
            : collect();
        
        return view('products.categories.create', compact('parent', 'level', 'parentCategories', 'subCategories'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'level' => 'required|integer|min:1|max:3'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $category = new Category([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'parent_id' => $request->parent_id,
                'level' => $request->level
            ]);

            $category->save();

            return redirect()->route('categories.index')
                ->with('success', 'تم إضافة الفئة بنجاح');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء محاولة إضافة الفئة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Category $category)
    {
        $category->load(['products' => function ($query) {
            $query->where('active', true);
        }]);
        return view('products.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {

        $parentCategories = Category::where('level', 1)
            ->where('id', '!=', $category->id)
            ->where('is_active', true)
            ->get();
            
        $subCategories = $category->parent_id 
            ? Category::where('parent_id', $category->parent_id)
                ->where('id', '!=', $category->id)
                ->where('is_active', true)
                ->get() 
            : collect();
            
        return view('products.categories.edit', compact('category', 'parentCategories', 'subCategories'));
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value == $category->id) {
                        $fail('لا يمكن تعيين الفئة كأب لنفسها');
                    }
                    
                    // Check for circular reference
                    $parent = Category::find($value);
                    while ($parent) {
                        if ($parent->id == $category->id) {
                            $fail('تأكد من عدم وجود تكرار في التصنيفات الأبوية');
                            break;
                        }
                        $parent = $parent->parent;
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // حفظ القيم القديمة للمقارنة
            $oldParentId = $category->parent_id;
            $oldLevel = $category->level;
            
            // تحديث البيانات
            $category->name = $request->name;
            $category->description = $request->description;
            $category->is_active = $request->has('is_active') ? (bool)$request->is_active : $category->is_active;
            
            // تحديث الأب إذا تغير
            if ($request->has('parent_id') && $request->parent_id != $oldParentId) {
                $category->parent_id = $request->parent_id;
                // تحديث المستوى بناءً على الأب الجديد
                if ($request->parent_id) {
                    $parent = Category::find($request->parent_id);
                    $category->level = $parent->level + 1;
                } else {
                    $category->level = 1;
                }
            }
            
            $category->save();
            
            // تحديث المستويات الفرعية إذا تغير الأب
            if ($oldParentId != $category->parent_id) {
                $this->updateCategoryLevels($category);
            }

            return redirect()->route('categories.index')
                ->with('success', 'تم تحديث الفئة بنجاح');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء محاولة تحديث الفئة: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Recursively update category levels
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    protected function updateCategoryLevels(Category $category)
    {
        $children = $category->children;
        
        foreach ($children as $child) {
            $child->level = $category->level + 1;
            
            // Prevent more than 3 levels
            if ($child->level > 3) {
                $child->level = 3;
            }
            
            $child->save();
            
            // Update children recursively
            if ($child->children->isNotEmpty()) {
                $this->updateCategoryLevels($child);
            }
        }
    }

    /**
     * التحقق مما إذا كان يمكن حذف الفئة
     *
     * @param  \App\Models\Category  $category
     * @return array
     */
    protected function canDeleteCategory($category)
    {
        
        if ($category->products()->count() > 0) {
            return [
                'can_delete' => false,
                'message' => 'لا يمكن حذف الفئة لأنها تحتوي على منتجات'
            ];
        }
        
        if ($category->children()->count() > 0) {
            return [
                'can_delete' => false,
                'message' => 'لا يمكن حذف الفئة لأنها تحتوي على فئات فرعية'
            ];
        }
        
        return ['can_delete' => true];
    }
    
    /**
     * حذف الفئة من قاعدة البيانات
     *
     * @param  \App\Models\Category  $category
     * @return bool
     */
    protected function deleteCategory($category)
    {
        try {
            $category->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('فشل في حذف الفئة: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove the specified category from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        
        // التحقق من إمكانية الحذف
        $canDelete = $this->canDeleteCategory($category);
        if (!$canDelete['can_delete']) {
            return redirect()->back()->with('error', $canDelete['message']);
        }
        
        // تنفيذ عملية الحذف
        $deleted = $this->deleteCategory($category);
        
        if ($deleted) {
            return redirect()->route('categories.index')
                ->with('success', 'تم حذف الفئة بنجاح');
        }
        
        return redirect()->back()
            ->with('error', 'حدث خطأ غير متوقع أثناء محاولة حذف الفئة');
    }
    
    /**
     * Get sub-categories for a parent category (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubCategories(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:categories,id',
            'level' => 'required|integer|min:1|max:2'
        ]);
        
        $categories = Category::where('parent_id', $request->parent_id)
            ->where('is_active', true)
            ->get(['id', 'name']);
            
        return response()->json($categories);
    }
}
