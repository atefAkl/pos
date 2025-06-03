<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosInterface extends Component
{
    public $products = [];
    public $cartItems = [];
    public $cartTotal = 0.00;
    public $cartSubtotal = 0.00;
    public $cartVat = 0.00;
    public $vatRate = 0.15; // 15% VAT
    public $selectedCustomerId = null;
    public $notes = '';
    public $searchProduct = '';
    public $paymentMethod = 'cash'; // Default payment method
    public $searchCustomer = '';

    // تم حذف متغير customers لأنه لم يعد ضروريًا

    public function render()
    {
        // المنتجات
        if (!empty(trim($this->searchProduct))) {
            $productsToDisplay = Product::where('is_active', 1)
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . trim($this->searchProduct) . '%')
                        ->orWhere('barcode', 'like', '%' . trim($this->searchProduct) . '%');
                })
                ->orderBy('name')
                ->get();
        } else {
            $productsToDisplay = Product::where('is_active', 1)->orderBy('name')->limit(20)->get();
        }

        // العملاء
        if (!empty(trim($this->searchCustomer))) {
            $customersToDisplay = Customer::where('is_active', 1)
                ->where(function ($query) {
                    $search = trim($this->searchCustomer);
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%");
                })
                ->orderBy('name')
                ->get();
        } else {
            $customersToDisplay = Customer::where('is_active', 1)->orderBy('name')->get();
        }

        return view('livewire.pos-interface', [
            'productsToDisplay' => $productsToDisplay,
            'customersToDisplay' => $customersToDisplay,
        ]);
    }
    public function mount()
    {
        // تحميل المنتجات فقط عند البداية
        $this->products = Product::where('is_active', 1)->orderBy('name')->limit(20)->get(); // Limiting initial load for performance
    }



    public function addItemToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            // يمكنك إضافة رسالة خطأ هنا إذا أردت
            return;
        }

        if (isset($this->cartItems[$productId])) {
            // المنتج موجود بالفعل، قم بزيادة الكمية
            $this->cartItems[$productId]['quantity']++;
        } else {
            // المنتج غير موجود، قم بإضافته
            $this->cartItems[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->retail_price,
                'quantity' => 1,
            ];
        }
        $this->calculateCartTotal();
    }

    public function calculateCartTotal()
    {
        $this->cartSubtotal = 0;
        foreach ($this->cartItems as $item) {
            $this->cartSubtotal += $item['price'] * $item['quantity'];
        }
        $this->cartVat = $this->cartSubtotal * $this->vatRate;
        $this->cartTotal = $this->cartSubtotal + $this->cartVat;
    }

    public function increaseQuantity($productId)
    {
        if (isset($this->cartItems[$productId])) {
            $this->cartItems[$productId]['quantity']++;
            $this->calculateCartTotal();
        }
    }

    public function decreaseQuantity($productId)
    {
        if (isset($this->cartItems[$productId])) {
            if ($this->cartItems[$productId]['quantity'] > 1) {
                $this->cartItems[$productId]['quantity']--;
            } else {
                unset($this->cartItems[$productId]); // Remove item if quantity becomes 0 or less
            }
            $this->calculateCartTotal();
        }
    }

    public function removeItem($productId)
    {
        if (isset($this->cartItems[$productId])) {
            unset($this->cartItems[$productId]);
            $this->calculateCartTotal();
        }
    }

    public function processSale()
    {
        $this->validate([
            'cartItems' => 'required|array|min:1',
            'selectedCustomerId' => 'nullable|exists:customers,id',
            'cartTotal' => 'required|numeric|min:0.01'
        ], [
            'cartItems.required' => 'السلة فارغة. يرجى إضافة منتجات أولاً.',
            'cartItems.min' => 'السلة فارغة. يرجى إضافة منتجات أولاً.',
            'selectedCustomerId.required' => 'يرجى اختيار العميل.',
            'selectedCustomerId.exists' => 'العميل المختار غير صالح.',
            'cartTotal.min' => 'إجمالي الفاتورة يجب أن يكون أكبر من صفر.'
        ]);

        DB::transaction(function () {
            // 1. Create Sale record
            $sale = Quotation::create([
                'customer_id' => $this->selectedCustomerId ?? 1,
                'user_id' => Auth::id(), // Assuming the logged-in user is the seller
                'total_amount' => $this->cartTotal,
                'notes' => $this->notes,
            ]);

            // NEW: 3. Create Invoice
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT), // Example: INV-000001
                'customer_id' => $sale->customer_id ?? 1,
                'subtotal' => $sale->total_amount, // Assuming subtotal is the same as sale total for now
                'tax' => 0, // Assuming no tax for now, can be calculated if needed
                'discount' => 0, // Assuming no discount for now, can be calculated if needed
                'total' => $sale->total_amount,
                'status' => 'paid', // Sale is processed and paid immediately
                'paid_amount' => $sale->total_amount,
                'notes' => $sale->notes, // Or specific invoice notes
                'created_by' => Auth::id(),
            ]);

            // 2. Create SaleItem records and update product quantities
            foreach ($this->cartItems as $productId => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'sub_total' => $item['price'] * $item['quantity'],
                    'total' => $item['price'] * $item['quantity'], // إضافة الحقل total
                ]);

                // Update product stock
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->quantity -= $item['quantity'];
                    $product->save();
                }
            }

            // NEW: 4. Create Payment
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total,
                'payment_method' => $this->paymentMethod, // Use selected payment method
                'notes' => 'Payment for Sale ID: ' . $sale->id . ', Invoice: ' . $invoice->invoice_number,
                'created_by' => Auth::id(),
            ]);

            // OLD: 3. Reset cart (Now step 5)

            $this->cartItems = [];
            $this->cartTotal = 0.00;
            $this->selectedCustomerId = null;
            $this->notes = '';
            $this->searchProduct = ''; // Optionally reset search
            // Refresh products list if needed, or rely on current display
            $this->products = Product::where('is_active', 1)->orderBy('name')->limit(20)->get();

            session()->flash('success', 'تمت عملية البيع بنجاح! رقم الفاتورة: ' . $sale->id);
            // $this->dispatch('saleProcessed'); // For potential JS listeners
        });
    }
}
