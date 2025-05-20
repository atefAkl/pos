<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Brand;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Support\Facades\Storage;

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

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        // Generate initial barcode
        $latestBarcode = Product::max('barcode');
        $newBarcode = $latestBarcode ? ((int)$latestBarcode + 10) : 1000000000000;

        return view('products.create', compact('categories', 'suppliers', 'brands', 'newBarcode'));
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
        $product->load('category');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
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

        return Excel::download(new ProductsExport, $filename . '.' . $type);
    }

    public function destroy(Product $product)
    {
        // Check if product has any related records
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
