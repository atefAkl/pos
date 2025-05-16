<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class POSController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('pos.index', compact('customers'));
    }

    public function getProducts(Request $request)
    {
        $query = Product::where('active', true)
            ->where('quantity', '>', 0);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->with('category')->get();
        
        return Response::json($products);
    }

    public function getCustomers(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->get();
        
        return Response::json($customers);
    }

    public function getProduct(Request $request, $id)
    {
        $product = Product::with('category')
            ->findOrFail($id);
            
        return Response::json($product);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card'
        ]);

        try {
            DB::beginTransaction();

            // حساب إجمالي الفاتورة
            $subtotal = collect($request->items)->sum(function($item) {
                return $item['quantity'] * $item['price'];
            });

            // إنشاء رقم الفاتورة
            $prefix = 'INV';
            $yearMonth = now()->format('ym');
            
            // البحث عن آخر فاتورة لنفس الشهر
            $lastInvoice = Invoice::withTrashed()
                ->where('invoice_number', 'like', $prefix . '-' . $yearMonth . '-%')
                ->latest('invoice_number')
                ->first();

            if ($lastInvoice) {
                // استخراج رقم التسلسل من آخر فاتورة
                $lastNumber = (int) substr($lastInvoice->invoice_number, -5);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            // تنسيق رقم الفاتورة
            $invoiceNumber = $prefix . '-' . $yearMonth . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $request->customer_id,
                'subtotal' => $subtotal,
                'tax' => 0, // يمكن تعديلها حسب الحاجة
                'discount' => 0, // يمكن تعديلها حسب الحاجة
                'total' => $subtotal, // الإجمالي بعد الضريبة والخصم
                'paid_amount' => $request->paid_amount,
                'status' => $request->paid_amount >= $subtotal ? 'paid' : 'partially_paid',
                'notes' => $request->notes,
                'created_by' => Auth::id()
            ]);

            // إضافة منتجات الفاتورة
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // التحقق من توفر الكمية
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("الكمية المطلوبة غير متوفرة للمنتج: {$product->name}");
                }

                // إنشاء عنصر الفاتورة
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price']
                ]);

                // تحديث كمية المنتج
                $product->decrement('quantity', $item['quantity']);
            }

            // تحديث رصيد العميل إذا كان هناك مبلغ متبقي
            if ($invoice->total > $invoice->paid) {
                $customer = Customer::findOrFail($request->customer_id);
                $customer->increment('balance', $invoice->total - $invoice->paid);
            }

            DB::commit();

            return Response::json([
                'success' => true,
                'message' => 'تم إنشاء الفاتورة بنجاح',
                'invoice_id' => $invoice->id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
