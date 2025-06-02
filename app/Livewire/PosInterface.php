<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosInterface extends Component
{
    public $products = [];
    public $customers = [];
    public $cartItems = [];
    public $cartTotal = 0.00;
    public $selectedCustomerId = null;
    public $notes = '';
    public $searchProduct = '';

    public function mount()
    {
        $this->customers = Customer::where('is_active', 1)->orderBy('name')->get();
        // Load initial set of products, render() will handle filtering or reloading if search is used
        $this->products = Product::where('is_active', 1)->orderBy('name')->limit(20)->get(); // Limiting initial load for performance
    }

    public function render()
    {
        $productsToDisplay = [];
        if (!empty(trim($this->searchProduct))) {
            $productsToDisplay = Product::where('is_active', 1)
                                      ->where(function ($query) {
                                          $query->where('name', 'like', '%'.trim($this->searchProduct).'%')
                                                ->orWhere('barcode', 'like', '%'.trim($this->searchProduct).'%');
                                      })
                                      ->orderBy('name')
                                      ->get();
        } else {
            // If search is empty, use the initially loaded products
            // Or, to always fetch fresh data (even if search is empty), uncomment the line below and remove the $this->products assignment in mount()
            // $productsToDisplay = Product::where('is_active', 1)->orderBy('name')->get();
            $productsToDisplay = $this->products; // Uses products loaded in mount()
        }

        return view('livewire.pos-interface', [
            'productsToDisplay' => $productsToDisplay,
        ]);
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
        $this->cartTotal = 0;
        foreach ($this->cartItems as $item) {
            $this->cartTotal += $item['price'] * $item['quantity'];
        }
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
            'selectedCustomerId' => 'required|exists:customers,id',
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
            $sale = Sale::create([
                'customer_id' => $this->selectedCustomerId,
                'user_id' => Auth::id(), // Assuming the logged-in user is the seller
                'total_amount' => $this->cartTotal,
                'notes' => $this->notes,
            ]);

            // 2. Create SaleItem records and update product quantities
            foreach ($this->cartItems as $productId => $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'sub_total' => $item['price'] * $item['quantity'],
                ]);

                // Update product stock
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->quantity -= $item['quantity'];
                    $product->save();
                }
            }

            // NEW: 3. Create Invoice
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT), // Example: INV-000001
                'customer_id' => $sale->customer_id,
                'subtotal' => $sale->total_amount, // Assuming subtotal is the same as sale total for now
                'tax' => 0, // Assuming no tax for now, can be calculated if needed
                'discount' => 0, // Assuming no discount for now, can be calculated if needed
                'total' => $sale->total_amount,
                'status' => 'paid', // Sale is processed and paid immediately
                'paid_amount' => $sale->total_amount,
                'notes' => $sale->notes, // Or specific invoice notes
                'created_by' => Auth::id(),
            ]);

            // NEW: 4. Create Payment
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total,
                'payment_method' => 'cash', // Default payment method, can be made dynamic later
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
