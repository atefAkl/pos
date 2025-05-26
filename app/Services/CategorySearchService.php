<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategorySearchService
{
    /**
     * البحث عن الفئات بناءً على معايير البحث
     *
     * @param Request $request
     * @return array
     */
    public function search(Request $request): array
    {
        $query = $this->buildBaseQuery($request);
        $categories = $this->executeQuery($query, $request);
        $searchParams = $this->getSearchParams($request);

        return [
            'categories' => $categories,
            'searchParams' => $searchParams
        ];
    }

    /**
     * بناء الاستعلام الأساسي
     */
    protected function buildBaseQuery(Request $request)
    {
        $query = Category::query();

        // تصفية حسب الفئة الأب
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        } else {
            $query->whereNull('parent_id');
        }

        return $query;
    }

    /**
     * تنفيذ الاستعلام مع التحميل المتعمد للعلاقات
     */
    protected function executeQuery($query, Request $request)
    {
        return $query->with(['children' => function($q) use ($request) {
            $this->applyChildrenFilters($q, $request);
        }])
        ->withCount('products')
        ->orderBy('name')
        ->get();
    }

    /**
     * تطبيق الفلاتر على الفئات الفرعية
     */
    protected function applyChildrenFilters($query, Request $request)
    {
        $query->withCount('products');

        if ($request->filled('search')) {
            $this->applySearchFilter($query, $request->input('search'));
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $query->orderBy('name');
    }

    /**
     * تطبيق فلتر البحث
     */
    protected function applySearchFilter($query, string $searchTerm)
    {
        $searchTerm = '%' . $searchTerm . '%';
        $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'LIKE', $searchTerm)
              ->orWhere('description', 'LIKE', $searchTerm);
        });
    }

    /**
     * الحصول على معلمات البحث
     */
    protected function getSearchParams(Request $request): array
    {
        $searchParams = $request->only(['search', 'is_active']);
        
        if ($request->has('parent_id')) {
            $searchParams['parent_id'] = $request->parent_id;
        }

        return $searchParams;
    }
    
    /**
     * الحصول على الفئات مع تطبيق الفلاتر (للاستخدام في طلبات AJAX)
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesWithFilters(Request $request)
    {
        $query = Category::query();
        
        // تصفية حسب الفئة الأب
        if ($request->has('parent_id') && !empty($request->parent_id)) {
            $query->where('parent_id', $request->parent_id);
        } else {
            $query->whereNull('parent_id');
        }
        
        // تصفية حسب مصطلح البحث
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm);
            });
        }
        
        // تصفية حسب الحالة (نشط/غير نشط)
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }
        
        return $query->withCount('products')
                    ->orderBy('name')
                    ->get();
    }
}
