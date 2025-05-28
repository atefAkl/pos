<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use App\Models\ProductOffer;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Models\ProductFile;
use App\Models\File;
use App\Models\Supplier;
use App\Models\Brand;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier', 'brand']);

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%");
            });
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }

        // Filter by type (product/service)
        if ($request->has('type') && in_array($request->type, ['product', 'service'])) {
            $query->where('is_service', $request->type === 'service');
        }

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('active', $request->status);
        }

        // Filter by stock status
        if ($request->has('stock')) {
            if ($request->stock === 'low') {
                $query->where('quantity', '<=', DB::raw('alert_quantity'))
                    ->where('quantity', '>', 0);
            } elseif ($request->stock === 'out') {
                $query->where('quantity', '<=', 0);
            }
        }

        // Order by creation date by default
        $products = $query->latest()->paginate(25);

        $categories = Category::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Generate a new barcode with auto-increment by 10
     *
     * @return \Illuminate\Http\Response
     */
    public function generateBarcode()
    {
        // Get the latest barcode and increment by 10
        $latestBarcode = Product::max('barcode');
        $newBarcode = $latestBarcode ? ((int)$latestBarcode + 10) : 1000000000000; // Start with 13 digits if no barcode exists

        return response()->json([
            'success' => true,
            'barcode' => (string)$newBarcode
        ]);
    }

    /**
     * Generate a new product code
     *
     * @return \Illuminate\Http\Response
     */
    public function generateProductCode()
    {
        // Get the latest product code and increment by 1
        $latestCode = Product::whereNotNull('code')
            ->where('code', 'like', 'PRD-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latestCode) {
            $number = (int) str_replace('PRD-', '', $latestCode->code) + 1;
        } else {
            $number = 1000; // Starting number
        }

        $newCode = 'PRD-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'code' => $newCode
        ]);
    }

    /**
     * Generate a new SKU
     * 
     * @return \Illuminate\Http\Response
     */
    public function generateSKU()
    {
        // Generate a random 8-character alphanumeric SKU
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $sku = 'SKU-';

        for ($i = 0; $i < 8; $i++) {
            $sku .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Make sure SKU is unique
        while (Product::where('sku', $sku)->exists()) {
            $sku = 'SKU-';
            for ($i = 0; $i < 8; $i++) {
                $sku .= $characters[rand(0, strlen($characters) - 1)];
            }
        }

        return response()->json([
            'success' => true,
            'sku' => $sku
        ]);
    }

    public function create(Request $request)
    {
        // جلب الفئات من المستوى الثالث مع مسارها الكامل
        $categories = Category::getThirdLevelWithPath();

        // الحصول على معرف الفئة من الطلب إذا كان موجوداً
        $selectedCategoryId = $request->query('category_id');

        $suppliers = Supplier::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        // Generate initial codes
        $latestBarcode = Product::max('barcode');
        $newBarcode = $latestBarcode ? ((int)$latestBarcode + 10) : 1000000000000;

        // Generate initial product code
        $latestCode = Product::whereNotNull('code')
            ->where('code', 'like', 'PRD-%')
            ->orderBy('id', 'desc')
            ->first();
        $newProductCode = $latestCode ? 'PRD-' . str_pad((int)str_replace('PRD-', '', $latestCode->code) + 1, 4, '0', STR_PAD_LEFT) : 'PRD-1000';

        return view('products.create', compact('categories', 'selectedCategoryId', 'suppliers', 'brands', 'newBarcode', 'newProductCode'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:products',
            'barcode' => 'nullable|string|unique:products',
            'sku' => 'nullable|string|unique:products',
            'price' => 'required|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'wholesale_quantity' => 'nullable|integer|min:1',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'quantity' => 'required_if:is_service,false|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:50',
            'alert_quantity' => 'required_if:is_service,false|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_service' => 'boolean',
            'service_duration' => 'required_if:is_service,true|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            $uploadPath = 'uploads/products';
            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0777, true);
            }
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path($uploadPath), $fileName);
            $data['image'] = $uploadPath . '/' . $fileName;
        }

        // Set default values
        $data['retail_price'] = $data['retail_price'] ?? $data['price'];
        $data['active'] = $request->has('active');
        $data['is_service'] = $request->has('is_service');

        // If it's a service, set quantity to 0
        if ($data['is_service']) {
            $data['quantity'] = 0;
            $data['alert_quantity'] = 0;
        }

        Product::create($data);
        return redirect()->route('products.index')
            ->with('success', 'تم إضافة ' . ($data['is_service'] ? 'الخدمة' : 'المنتج') . ' بنجاح');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'offers' => function ($query) {
            $query->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        }]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();
        $taxes = Tax::where('is_active', true)->get();

        // تحميل العروض النشطة للمنتج
        $product->load(['offers' => function ($query) {
            $query->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->orderBy('end_date', 'desc');
        }]);

        return view('products.edit_enhanced', compact(
            'product',
            'categories',
            'suppliers',
            'brands',
            'units',
            'taxes'
        ));
    }

    /**
     * طباعة الباركود للمنتج
     */
    public function printBarcode(Product $product)
    {
        if (empty($product->barcode)) {
            return back()->with('error', 'لا يوجد باركود لهذا المنتج');
        }

        $barcodeType = 'C128';
        $barcodeHeight = 50;
        $barcodeWidth = 2;

        return view('products.barcodes.print', compact('product', 'barcodeType', 'barcodeHeight', 'barcodeWidth'));
    }

    /**
     * إنشاء عرض خاص للمنتج
     */
    public function createOffer(Product $product)
    {
        return view('products.offers.create', compact('product'));
    }

    /**
     * Update the media (images) of the specified product.
     */
    /**
     * تحديث المعلومات الأساسية للمنتج
     */
    public function updateBasic(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:products,code,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'is_service' => 'boolean',
        ]);

        $product->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث المعلومات الأساسية بنجاح');
    }

    /**
     * تحديث معلومات التسعير والمخزون للمنتج
     */
    public function updatePricing(Request $request, Product $product)
    {
        $request->validate([
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
            'quantity' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'has_expiry' => 'boolean',
            'expiry_date' => 'nullable|date',
        ]);

        $product->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث معلومات التسعير والمخزون بنجاح');
    }

    /**
     * تحديث التفاصيل الإضافية للمنتج
     */
    public function updateDetails(Request $request, Product $product)
    {
        $request->validate([
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'material' => 'nullable|string',
            'features' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $product->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث التفاصيل الإضافية بنجاح');
    }

    /**
     * تحديث وسائط المنتج (الصور)
     */
    /**
     * تحديث الصورة الرئيسية فقط
     */
    public function updateMainImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|max:4096',
            'image_alt' => 'nullable|string|max:255',
        ]);
        $uploadPath = 'uploads/products/gallery';
        if ($request->hasFile('image')) {
            $oldPath = public_path('uploads/' . $product->image);
            if ($product->image && file_exists($oldPath)) {
                unlink($oldPath);
            }
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path($uploadPath), $fileName);
            $product->update([
                'image' => 'products/gallery/' . $fileName,
                'image_alt' => $request->input('image_alt'),
            ]);
        }
        return redirect()->back()->with('success', 'تم تحديث الصورة الرئيسية بنجاح');
    }

    /**
     * تحديث معرض الصور فقط
     */
    public function updateGallery(Request $request, Product $product)
    {
        $request->validate([
            'gallery.*' => 'required|image|max:4096',
        ]);
        $uploadPath = 'uploads/products/gallery';
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $fileName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path($uploadPath), $fileName);
                $product->images()->create([
                    'path' => 'products/gallery/' . $fileName,
                ]);
            }
        }
        return redirect()->back()->with('success', 'تم تحديث معرض الصور بنجاح');
    }

    /**
     * تحديث صورة الباركود فقط (مثال: يمكن رفع صورة باركود جديدة)
     */
    public function updateBarcode(Request $request, Product $product)
    {
        // إذا كان هناك صورة باركود مرفوعة
        $request->validate([
            'barcode_image' => 'nullable|image|max:4096',
        ]);
        if ($request->hasFile('barcode_image')) {
            $uploadPath = 'uploads/products/barcodes';
            $fileName = time() . '_' . $request->file('barcode_image')->getClientOriginalName();
            $request->file('barcode_image')->move(public_path($uploadPath), $fileName);
            $product->update([
                'barcode_image' => 'products/barcodes/' . $fileName,
            ]);
        }
        return redirect()->back()->with('success', 'تم تحديث صورة الباركود بنجاح');
    }

    /**
     * تحديث صورة إضافية (مثال: صورة خلفية)
     */
    public function updateExtraImage(Request $request, Product $product)
    {
        $request->validate([
            'extra_image' => 'required|image|max:2048',
        ]);

        // حذف الصورة القديمة إذا كانت موجودة
        if ($product->extra_image && Storage::disk('public')->exists('uploads/' . $product->extra_image)) {
            Storage::disk('public')->delete('uploads/' . $product->extra_image);
        }

        // رفع وتخزين الصورة الجديدة
        $extraImagePath = $request->file('extra_image')->store('products/extra', 'public');
        $extraImagePath = str_replace('products/extra/', '', $extraImagePath);

        // تحديث الصورة في قاعدة البيانات
        $product->update([
            'extra_image' => $extraImagePath,
        ]);

        return redirect()->back()->with('success', 'تم تحديث الصورة الإضافية بنجاح.');
    }
    
    /**
     * رفع ملف جديد للمنتج
     */
    public function uploadFile(Request $request)
    {
        try {
            // التحقق من البيانات
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:10240', // الحد الأقصى 10 ميجابايت
                'display_name' => 'required|string|max:255',
                'category' => 'required|string|in:product_image,gallery_image,barcode,document,other',
                'alt_text' => 'nullable|string|max:255',
                'related_id' => 'required|integer',
                'related_type' => 'required|string|in:product',
                'is_active' => 'nullable|boolean',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors(),
                ], 422);
            }
            
            // التحقق من وجود المنتج
            $product = Product::find($request->related_id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنتج غير موجود',
                ], 404);
            }
            
            // رفع الملف
            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->getClientOriginalExtension();
            $mimeType = $uploadedFile->getMimeType();
            $size = $uploadedFile->getSize();
            
            // إنشاء اسم فريد للملف
            $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
            
            // تحديد مسار التخزين حسب الفئة
            $storagePath = 'products/' . $product->id . '/' . $request->category;
            
            // تخزين الملف
            $filePath = $uploadedFile->storeAs($storagePath, $fileName, 'public');
            
            // إنشاء سجل الملف في قاعدة البيانات
            $file = File::create([
                'path' => $filePath,
                'name' => $originalName,
                'display_name' => $request->display_name,
                'mime_type' => $mimeType,
                'extension' => $extension,
                'size' => $size,
                'alt_text' => $request->alt_text ?? $request->display_name,
            ]);
            
            // إنشاء العلاقة مع المنتج
            $isActive = $request->has('is_active') ? (bool)$request->is_active : true;
            
            // تحديد الترتيب التالي لهذه الفئة
            $maxOrder = ProductFile::where('product_id', $product->id)
                ->where('category', $request->category)
                ->max('order') ?? 0;
            
            $productFile = ProductFile::create([
                'product_id' => $product->id,
                'file_id' => $file->id,
                'category' => $request->category,
                'is_active' => $isActive,
                'order' => $maxOrder + 1,
            ]);
            
            // إضافة معلومات إضافية للرد
            $productFile->load('file');
            $productFile->category_name = $productFile->getCategoryNameAttribute();
            $productFile->category_icon = $productFile->getCategoryIconAttribute();
            
            // إضافة URL للملف
            $file->url = Storage::url($file->path);
            
            // إضافة حجم الملف بصيغة مقروءة
            $file->formatted_size = $this->formatFileSize($file->size);
            
            // إضافة أيقونة الملف حسب نوعه
            $file->icon = $this->getFileIcon($file->mime_type, $file->extension);
            
            return response()->json([
                'success' => true,
                'message' => 'تم رفع الملف بنجاح',
                'file' => $file,
                'product_file' => $productFile,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * تنسيق حجم الملف بصيغة مقروءة
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * الحصول على أيقونة الملف حسب نوعه
     */
    private function getFileIcon($mimeType, $extension)
    {
        $extension = strtolower($extension);
        
        // أيقونات حسب نوع MIME
        if (strpos($mimeType, 'image/') === 0) {
            return 'fas fa-file-image';
        } elseif (strpos($mimeType, 'video/') === 0) {
            return 'fas fa-file-video';
        } elseif (strpos($mimeType, 'audio/') === 0) {
            return 'fas fa-file-audio';
        } elseif (strpos($mimeType, 'text/') === 0) {
            return 'fas fa-file-alt';
        } elseif ($mimeType === 'application/pdf') {
            return 'fas fa-file-pdf';
        } elseif (in_array($mimeType, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'fas fa-file-word';
        } elseif (in_array($mimeType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
            return 'fas fa-file-excel';
        } elseif (in_array($mimeType, ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'])) {
            return 'fas fa-file-powerpoint';
        } elseif (in_array($mimeType, ['application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'])) {
            return 'fas fa-file-archive';
        }
        
        // أيقونات حسب الامتداد
        $iconsByExtension = [
            'pdf' => 'fas fa-file-pdf',
            'doc' => 'fas fa-file-word',
            'docx' => 'fas fa-file-word',
            'xls' => 'fas fa-file-excel',
            'xlsx' => 'fas fa-file-excel',
            'ppt' => 'fas fa-file-powerpoint',
            'pptx' => 'fas fa-file-powerpoint',
            'zip' => 'fas fa-file-archive',
            'rar' => 'fas fa-file-archive',
            '7z' => 'fas fa-file-archive',
            'txt' => 'fas fa-file-alt',
            'html' => 'fas fa-file-code',
            'css' => 'fas fa-file-code',
            'js' => 'fas fa-file-code',
            'json' => 'fas fa-file-code',
            'xml' => 'fas fa-file-code',
            'csv' => 'fas fa-file-csv',
        ];
        
        return $iconsByExtension[$extension] ?? 'fas fa-file';
    }
    
    /**
     * حذف ملف من المنتج
     */
    public function deleteFile(Request $request, $fileId)
    {
        try {
            // البحث عن الملف
            $file = File::findOrFail($fileId);
            
            // البحث عن علاقة الملف بالمنتج
            $productFile = ProductFile::where('file_id', $fileId)->first();
            
            if (!$productFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'الملف غير مرتبط بأي منتج',
                ], 404);
            }
            
            // حذف الملف من التخزين
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
            
            // حذف العلاقة والملف من قاعدة البيانات
            $productFile->delete();
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
     * تبديل حالة تنشيط الملف
     */
    public function toggleFileActive(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_file_id' => 'required|integer',
                'is_active' => 'required|boolean',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors(),
                ], 422);
            }
            
            // البحث عن علاقة الملف بالمنتج
            $productFile = ProductFile::findOrFail($request->product_file_id);
            
            // تحديث حالة التنشيط
            $productFile->is_active = (bool)$request->is_active;
            $productFile->save();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الملف بنجاح',
                'product_file' => $productFile,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة الملف: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * الحصول على ملفات المنتج عبر API
     */
    public function getProductFiles(Product $product)
    {
        try {
            // الحصول على ملفات المنتج مع علاقاتها
            $productFiles = $product->productFiles()
                ->with('file')
                ->orderBy('category')
                ->orderBy('order')
                ->get()
                ->map(function($productFile) {
                    // إضافة معلومات إضافية لكل ملف
                    $productFile->category_name = $productFile->getCategoryNameAttribute();
                    $productFile->category_icon = $productFile->getCategoryIconAttribute();
                    return $productFile;
                });
            
            // إضافة صور المنتج القديمة للتوافق مع النظام القديم
            $legacyImages = $this->getLegacyProductImages($product);
            
            return response()->json([
                'success' => true,
                'files' => $productFiles,
                'legacy_images' => $legacyImages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب ملفات المنتج: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * الحصول على صور المنتج القديمة للتوافق مع النظام القديم
     */
    private function getLegacyProductImages(Product $product)
    {
        $legacyImages = [];
        
        // إضافة صور المنتج من النظام القديم
        $productImages = $product->images()->get();
        
        foreach ($productImages as $image) {
            $legacyImages[] = [
                'id' => $image->id,
                'url' => asset('uploads/' . $image->image),
                'name' => $image->image,
                'type' => 'legacy_image',
            ];
        }
        
        // إضافة الصورة الرئيسية إذا كانت موجودة
        if ($product->image) {
            $legacyImages[] = [
                'id' => 'main_' . $product->id,
                'url' => asset('uploads/' . $product->image),
                'name' => $product->image,
                'type' => 'main_image',
            ];
        }
        
        // إضافة الصورة الإضافية إذا كانت موجودة
        if ($product->extra_image) {
            $legacyImages[] = [
                'id' => 'extra_' . $product->id,
                'url' => asset('uploads/' . $product->extra_image),
                'name' => $product->extra_image,
                'type' => 'extra_image',
            ];
        }
        
        return $legacyImages;
    }
    
    /**
     * تحويل صور المنتج القديمة إلى النظام الجديد
     */
    public function migrateLegacyImages(Product $product)
    {
        try {
            $migratedCount = 0;
            
            // تحويل الصورة الرئيسية
            if ($product->image) {
                $sourcePath = public_path('uploads/' . $product->image);
                if (file_exists($sourcePath)) {
                    // إنشاء ملف جديد
                    $file = $this->createFileFromPath($sourcePath, $product->image, 'الصورة الرئيسية', 'products/' . $product->id . '/product_image');
                    
                    // إنشاء العلاقة مع المنتج
                    ProductFile::create([
                        'product_id' => $product->id,
                        'file_id' => $file->id,
                        'category' => 'product_image',
                        'is_active' => true,
                        'order' => 1,
                    ]);
                    
                    $migratedCount++;
                }
            }
            
            // تحويل صور المعرض
            $productImages = $product->images()->get();
            $order = 1;
            
            foreach ($productImages as $image) {
                $sourcePath = public_path('uploads/' . $image->image);
                if (file_exists($sourcePath)) {
                    // إنشاء ملف جديد
                    $file = $this->createFileFromPath($sourcePath, $image->image, 'صورة معرض', 'products/' . $product->id . '/gallery_image');
                    
                    // إنشاء العلاقة مع المنتج
                    ProductFile::create([
                        'product_id' => $product->id,
                        'file_id' => $file->id,
                        'category' => 'gallery_image',
                        'is_active' => true,
                        'order' => $order++,
                    ]);
                    
                    $migratedCount++;
                }
            }
            
            // تحويل الصورة الإضافية
            if ($product->extra_image) {
                $sourcePath = public_path('uploads/' . $product->extra_image);
                if (file_exists($sourcePath)) {
                    // إنشاء ملف جديد
                    $file = $this->createFileFromPath($sourcePath, $product->extra_image, 'صورة إضافية', 'products/' . $product->id . '/other');
                    
                    // إنشاء العلاقة مع المنتج
                    ProductFile::create([
                        'product_id' => $product->id,
                        'file_id' => $file->id,
                        'category' => 'other',
                        'is_active' => true,
                        'order' => 1,
                    ]);
                    
                    $migratedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحويل ' . $migratedCount . ' ملف بنجاح',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحويل الملفات: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * إنشاء ملف جديد من مسار ملف موجود
     */
    private function createFileFromPath($sourcePath, $originalName, $displayName, $targetDir)
    {
        // الحصول على معلومات الملف
        $mimeType = mime_content_type($sourcePath);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $size = filesize($sourcePath);
        
        // إنشاء اسم فريد للملف
        $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
        
        // نسخ الملف إلى المسار الجديد
        $targetPath = $targetDir . '/' . $fileName;
        Storage::disk('public')->put($targetPath, file_get_contents($sourcePath));
        
        // إنشاء سجل الملف في قاعدة البيانات
        return File::create([
            'path' => $targetPath,
            'name' => $originalName,
            'display_name' => $displayName,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $size,
            'alt_text' => $displayName,
        ]);
    }

    /**
     * حفظ العرض الخاص بالمنتج
     */
    public function storeOffer(Request $request, Product $product)
    {
        $validated = $request->validate([
            'offer_price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        // إنشاء علاقة مع نموذج Offer إذا كان موجوداً
        if (method_exists($product, 'offers')) {
            $product->offers()->create($validated);
            return redirect()->route('products.show', $product)
                ->with('success', 'تم إضافة العرض بنجاح');
        }

        return back()->with('error', 'لا يمكن إضافة عرض لهذا المنتج');
    }

    public function update(Request $request, Product $product)
    {
        return public_path();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:products,code,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'wholesale_quantity' => 'nullable|integer|min:1',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'quantity' => 'required_if:is_service,false|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:50',
            'alert_quantity' => 'required_if:is_service,false|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_service' => 'boolean',
            'service_duration' => 'required_if:is_service,true|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            $uploadPath = 'uploads/products/gallery';
            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0777, true);
            }
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path($uploadPath), $fileName);
            $data['image'] = $uploadPath . '/' . $fileName;
        }

        // Set default values
        $data['retail_price'] = $data['retail_price'] ?? $data['price'];
        $data['active'] = $request->has('active');
        $data['is_service'] = $request->has('is_service');

        // If it's a service, set quantity to 0
        if ($data['is_service']) {
            $data['quantity'] = 0;
            $data['alert_quantity'] = 0;
        }

        $product->update($data);
        return redirect()->route('products.index')
            ->with('success', 'تم تحديث ' . ($data['is_service'] ? 'الخدمة' : 'المنتج') . ' بنجاح');
    }

    /**
     * Toggle product status (active/inactive)
     */
    public function toggleStatus(Product $product)
    {
        $product->update([
            'active' => !$product->active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة ' . ($product->is_service ? 'الخدمة' : 'المنتج') . ' بنجاح',
            'is_active' => $product->active
        ]);
    }

    /**
     * Update a specific field for a product
     */
    public function updateField(Product $product, Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        // Validate the field is allowed to be updated
        $allowedFields = ['price', 'retail_price', 'wholesale_price', 'wholesale_quantity', 'quantity', 'alert_quantity'];

        if (!in_array($field, $allowedFields)) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تحديث هذا الحقل'
            ], 400);
        }

        // Validate the value based on field type
        $validator = Validator::make($request->all(), [
            'value' => [
                'required',
                function ($attribute, $value, $fail) use ($field) {
                    if (in_array($field, ['price', 'retail_price', 'wholesale_price']) && !is_numeric($value)) {
                        $fail('يجب أن يكون السعر رقماً');
                    } elseif (in_array($field, ['wholesale_quantity', 'quantity', 'alert_quantity']) && !is_numeric($value)) {
                        $fail('يجب أن تكون الكمية رقماً صحيحاً');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Update the field
        $product->update([$field => $value]);

        return response()->json([
            'success' => true,
            'message' => 'تم التحديث بنجاح',
            'formatted_value' => $this->formatFieldValue($field, $value)
        ]);
    }

    /**
     * Format field value for display
     */
    private function formatFieldValue($field, $value)
    {
        if (in_array($field, ['price', 'retail_price', 'wholesale_price'])) {
            return number_format($value, 2) . ' ر.س';
        }
        return $value;
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('products.import');
    }

    /**
     * Import products from file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
            'update_existing' => 'boolean'
        ]);

        try {
            $import = new ProductsImport($request->boolean('update_existing'));
            Excel::import($import, $request->file('file'));

            $imported = $import->getRowCount();
            $updated = $import->getUpdatedCount();

            return redirect()->route('products.index')
                ->with('success', sprintf('تم استيراد %d منتج وتحديث %d منتج بنجاح', $imported, $updated));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء استيراد الملف: ' . $e->getMessage());
        }
    }

    /**
     * Export products to Excel
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'xlsx');
        $filename = 'products_' . now()->format('Y-m-d_H-i-s');

        if ($type === 'pdf') {
            $products = Product::with(['category', 'supplier', 'brand'])->get();
            $pdf = PDF::loadView('exports.products_pdf', compact('products'));
            return $pdf->download($filename . '.pdf');
        }

        return Excel::download(new \App\Exports\ProductsExport, $filename . '.' . $type);
    }

    public function destroy($id)
    {
        // Check if product has any related records
        $product = Product::find($id);
        return $product;
        if ($product->invoiceItems()->exists()) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف ' . ($product->is_service ? 'الخدمة' : 'المنتج') . ' لأنه مرتبط بفواتير سابقة');
        }

        // Delete product image if exists
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }

        $isService = $product->is_service;
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'تم حذف ' . ($isService ? 'الخدمة' : 'المنتج') . ' بنجاح');
    }
}
