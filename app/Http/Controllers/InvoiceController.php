<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PDF;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['customer', 'createdBy'])
            ->latest()
            ->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::where('active', true)
            ->where('quantity', '>', 0)
            ->get();
        return view('invoices.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create invoice
            $invoice = new Invoice();
            $invoice->customer_id = $request->customer_id;
            $invoice->invoice_number = 'INV-' . time();
            $invoice->tax = $request->tax ?? 0;
            $invoice->discount = $request->discount ?? 0;
            $invoice->paid_amount = $request->paid_amount;
            $invoice->notes = $request->notes;
            $invoice->created_by = auth()->id();
            
            $subtotal = 0;
            
            // Calculate totals and create items
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception('الكمية المطلوبة غير متوفرة للمنتج: ' . $product->name);
                }

                $itemTotal = $product->price * $item['quantity'];
                $subtotal += $itemTotal;

                // Update product quantity
                $product->quantity -= $item['quantity'];
                $product->save();
            }

            $invoice->subtotal = $subtotal;
            $invoice->total = $subtotal + $invoice->tax - $invoice->discount;
            $invoice->save();

            // Create invoice items
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $product->price * $item['quantity']
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'createdBy']);
        return view('invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        try {
            DB::beginTransaction();

            // Restore product quantities
            foreach ($invoice->items as $item) {
                $product = $item->product;
                $product->quantity += $item->quantity;
                $product->save();
            }

            $invoice->delete();

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'تم حذف الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الفاتورة');
        }
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'createdBy']);
        
        $pdf = PDF::loadView('invoices.print', compact('invoice'));
        
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($invoice) {
                    $remainingAmount = $invoice->total - $invoice->paid_amount;
                    if ($value > $remainingAmount) {
                        $fail('المبلغ المدفوع أكبر من المبلغ المتبقي');
                    }
                },
            ],
            'payment_method' => 'required|in:cash,card',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // تحديث المبلغ المدفوع وحالة الفاتورة
            $invoice->paid_amount += $request->amount;
            $invoice->status = $invoice->paid_amount >= $invoice->total ? 'paid' : 'partially_paid';
            $invoice->save();

            // إنشاء سجل الدفع
            $invoice->payments()->create([
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'created_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'تم تسجيل الدفعة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تسجيل الدفعة');
        }
    }
}
