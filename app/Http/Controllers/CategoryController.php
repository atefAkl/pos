<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('products.categories.index', compact('categories'));
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
        
        $parentCategories = Category::where('level', 1)->where('is_active', true)->get();
        $subCategories = $parent ? Category::where('parent_id', $parent->id)->where('is_active', true)->get() : collect();
        
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
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? $category->is_active,
                'parent_id' => $request->parent_id
            ]);

            // Update children levels if parent changed
            if ($category->wasChanged('parent_id')) {
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
     * Remove the specified category from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->exists()) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الفئة لأنها تحتوي على منتجات');
        }
        
        // Check if category has children
        if ($category->children()->exists()) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الفئة لأنها تحتوي على فئات فرعية');
        }

        try {
            $category->delete();
            return redirect()->route('categories.index')
                ->with('success', 'تم حذف الفئة بنجاح');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء محاولة حذف الفئة: ' . $e->getMessage());
        }
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
