<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Tax;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductSettingsController extends Controller
{
    /**
     * Display the product settings dashboard.
     */
    public function index()
    {
        $units = Unit::orderBy('name')->get();
        $taxes = Tax::orderBy('name')->get();
        $categories = Category::where('parent_id', null)->with('children')->get();
        $brands = Brand::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        
        return view('settings.products.index', compact('units', 'taxes', 'categories', 'brands', 'suppliers'));
    }
    
    /**
     * Initialize default settings for a new installation.
     */
    public function initializeDefaults()
    {
        // Create default units if none exist
        if (Unit::count() === 0) {
            $defaultUnits = [
                [
                    'name' => 'قطعة',
                    'code' => 'PCS',
                    'description' => 'الوحدة الافتراضية للمنتجات الفردية',
                    'is_active' => true,
                    'is_default' => true,
                ],
                [
                    'name' => 'كيلوجرام',
                    'code' => 'KG',
                    'description' => 'وحدة قياس للوزن',
                    'is_active' => true,
                    'is_default' => false,
                ],
                [
                    'name' => 'لتر',
                    'code' => 'L',
                    'description' => 'وحدة قياس للسوائل',
                    'is_active' => true,
                    'is_default' => false,
                ],
                [
                    'name' => 'متر',
                    'code' => 'M',
                    'description' => 'وحدة قياس للطول',
                    'is_active' => true,
                    'is_default' => false,
                ],
                [
                    'name' => 'ساعة',
                    'code' => 'HR',
                    'description' => 'وحدة قياس للوقت (للخدمات)',
                    'is_active' => true,
                    'is_default' => false,
                ],
            ];
            
            foreach ($defaultUnits as $unit) {
                Unit::create($unit);
            }
        }
        
        // Create default taxes if none exist
        if (Tax::count() === 0) {
            $defaultTaxes = [
                [
                    'name' => 'بدون ضريبة',
                    'rate' => 0,
                    'description' => 'لا توجد ضريبة مطبقة',
                    'is_active' => true,
                    'is_default' => true,
                ],
                [
                    'name' => 'ضريبة القيمة المضافة',
                    'rate' => 15,
                    'description' => 'ضريبة القيمة المضافة بنسبة 15%',
                    'is_active' => true,
                    'is_default' => false,
                ],
                [
                    'name' => 'ضريبة مخفضة',
                    'rate' => 5,
                    'description' => 'ضريبة مخفضة بنسبة 5%',
                    'is_active' => true,
                    'is_default' => false,
                ],
            ];
            
            foreach ($defaultTaxes as $tax) {
                Tax::create($tax);
            }
        }
        
        return redirect()->route('settings.products.index')
            ->with('success', 'تم تهيئة الإعدادات الافتراضية بنجاح');
    }
}
