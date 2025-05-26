<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * تحديث بيانات الصورة (alt, name)
     */
    public function update(Request $request, ProductImage $image)
    {
        $request->validate([
            'alt' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
        ]);
        $image->alt = $request->input('alt');
        $image->name = $request->input('name');
        $image->save();
        return back()->with('success', 'تم تحديث بيانات الصورة بنجاح');
    }

    /**
     * تغيير الصورة نفسها مع الحفاظ على نفس السجل
     */
    public function replace(Request $request, ProductImage $image)
    {
        $request->validate([
            'new_image' => 'required|image|max:2048',
        ]);
        // حذف الصورة القديمة من public/uploads/products/gallery
        $oldPath = public_path('uploads/' . $image->path);
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
        // حفظ الصورة الجديدة في uploads/products/gallery
        $uploadPath = 'uploads/products/gallery';
        $fileName = time() . '_' . $request->file('new_image')->getClientOriginalName();
        $request->file('new_image')->move(public_path($uploadPath), $fileName);
        $image->path = 'products/gallery/' . $fileName;
        $image->save();
        return back()->with('success', 'تم تغيير الصورة بنجاح');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductImage  $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(String $id)
    {
        $image = ProductImage::find($id);
        $path = $image->path;

        try {
            // حذف الملف من التخزين
            Storage::delete($image->path);
            
            // حذف السجل من قاعدة البيانات
            $image->delete();
            
            return back()->with('success', 'تم حذف الصورة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف الصورة: ' . $e->getMessage());
        }
    }
}
