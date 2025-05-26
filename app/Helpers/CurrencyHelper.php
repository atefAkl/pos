<?php

use App\Models\Currency;

if (!function_exists('currency_symbol')) {
    /**
     * الحصول على رمز العملة من الإعدادات
     * 
     * @return string
     */
    function currency_symbol()
    {
        try {
            // محاولة الحصول على رمز العملة الافتراضية من قاعدة البيانات
            $currency = Currency::where('is_default', true)->first();
            
            if ($currency) {
                return $currency->symbol;
            }
            
            // إذا لم تكن هناك عملة افتراضية، نرجع أول عملة نشطة
            $activeCurrency = Currency::active()->first();
            
            if ($activeCurrency) {
                return $activeCurrency->symbol;
            }
            
            // إذا لم تكن هناك عملات، نرجع رمز الريال السعودي كقيمة افتراضية
            return 'ر.س';
        } catch (\Exception $e) {
            // في حالة حدوث أي خطأ، نرجع رمز الريال السعودي كقيمة افتراضية
            return 'ر.س';
        }
    }
}
