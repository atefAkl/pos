<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * رفع ملف جديد
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // الحد الأقصى 10 ميجابايت
            'display_name' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'category' => 'required|string|max:50',
            'is_active' => 'boolean',
            'related_id' => 'required|integer', // معرف الكيان المرتبط (مثل معرف المنتج)
            'related_type' => 'required|string|in:product,user,supplier,customer', // نوع الكيان المرتبط
        ]);
        
        try {
            // الحصول على الملف المرفوع
            $uploadedFile = $request->file('file');
            
            // إنشاء اسم فريد للملف
            $fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $uploadedFile->getClientOriginalExtension();
            
            // تحديد مسار التخزين حسب نوع الكيان وفئة الملف
            $path = $request->input('related_type') . 's/' . $request->input('related_id') . '/' . $request->input('category');
            
            // تخزين الملف
            $filePath = $uploadedFile->storeAs($path, $fileName, 'public');
            
            // إنشاء سجل للملف في قاعدة البيانات
            $file = File::create([
                'path' => $filePath,
                'name' => $uploadedFile->getClientOriginalName(),
                'display_name' => $request->input('display_name'),
                'mime_type' => $uploadedFile->getMimeType(),
                'extension' => $uploadedFile->getClientOriginalExtension(),
                'size' => $uploadedFile->getSize(),
                'alt_text' => $request->input('alt_text') ?: date('Y-m-d H:i:s'),
            ]);
            
            // إذا كان الكيان المرتبط هو منتج
            if ($request->input('related_type') === 'product') {
                $product = Product::findOrFail($request->input('related_id'));
                
                // إذا كانت الفئة هي صورة منتج وتم تعيينها كنشطة، قم بتعيين جميع صور المنتج الأخرى كغير نشطة
                if ($request->input('category') === 'product_image' && $request->input('is_active', true)) {
                    ProductFile::where('product_id', $product->id)
                        ->where('category', 'product_image')
                        ->update(['is_active' => false]);
                }
                
                // إنشاء العلاقة بين الملف والمنتج
                $productFile = ProductFile::create([
                    'product_id' => $product->id,
                    'file_id' => $file->id,
                    'category' => $request->input('category'),
                    'is_active' => $request->input('is_active', true),
                    'order' => ProductFile::where('product_id', $product->id)
                        ->where('category', $request->input('category'))
                        ->max('order') + 1,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'تم رفع الملف بنجاح',
                    'file' => $file,
                    'product_file' => $productFile,
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'تم رفع الملف بنجاح',
                'file' => $file,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * حذف ملف
     */
    public function destroy($id)
    {
        try {
            $file = File::findOrFail($id);
            
            // حذف الملف من التخزين
            Storage::disk('public')->delete($file->path);
            
            // حذف العلاقات مع المنتجات
            $file->productFiles()->delete();
            
            // حذف الملف من قاعدة البيانات
            $file->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الملف بنجاح',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الملف: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * تحديث بيانات الملف
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'display_name' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);
        
        try {
            $file = File::findOrFail($id);
            
            // تحديث بيانات الملف
            $file->update([
                'display_name' => $request->input('display_name'),
                'alt_text' => $request->input('alt_text'),
            ]);
            
            // إذا تم تحديث حالة النشاط للعلاقة مع المنتج
            if ($request->has('is_active') && $request->has('product_file_id')) {
                $productFile = ProductFile::findOrFail($request->input('product_file_id'));
                
                // إذا كانت الفئة هي صورة منتج وتم تعيينها كنشطة، قم بتعيين جميع صور المنتج الأخرى كغير نشطة
                if ($productFile->category === 'product_image' && $request->input('is_active')) {
                    ProductFile::where('product_id', $productFile->product_id)
                        ->where('category', 'product_image')
                        ->where('id', '!=', $productFile->id)
                        ->update(['is_active' => false]);
                }
                
                $productFile->update([
                    'is_active' => $request->input('is_active'),
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات الملف بنجاح',
                'file' => $file,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث بيانات الملف: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * استبدال الملف بملف جديد
     */
    public function replace(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // الحد الأقصى 10 ميجابايت
            'display_name' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
        ]);
        
        try {
            $file = File::findOrFail($id);
            
            // الحصول على الملف المرفوع
            $uploadedFile = $request->file('file');
            
            // إنشاء اسم فريد للملف
            $fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $uploadedFile->getClientOriginalExtension();
            
            // حذف الملف القديم من التخزين
            Storage::disk('public')->delete($file->path);
            
            // تحديد مسار التخزين بناءً على المسار القديم
            $pathParts = explode('/', $file->path);
            array_pop($pathParts); // حذف اسم الملف من المسار
            $path = implode('/', $pathParts);
            
            // تخزين الملف الجديد
            $filePath = $uploadedFile->storeAs($path, $fileName, 'public');
            
            // تحديث بيانات الملف
            $file->update([
                'path' => $filePath,
                'name' => $uploadedFile->getClientOriginalName(),
                'display_name' => $request->input('display_name') ?: $file->display_name,
                'mime_type' => $uploadedFile->getMimeType(),
                'extension' => $uploadedFile->getClientOriginalExtension(),
                'size' => $uploadedFile->getSize(),
                'alt_text' => $request->input('alt_text') ?: $file->alt_text,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم استبدال الملف بنجاح',
                'file' => $file,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استبدال الملف: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * الحصول على فئات الملفات المتاحة
     */
    public function getCategories()
    {
        return response()->json([
            'success' => true,
            'categories' => ProductFile::getCategories(),
        ]);
    }
}
