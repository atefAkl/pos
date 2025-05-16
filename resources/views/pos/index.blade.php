@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- القسم الأيمن - المنتجات -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <!-- بحث عن المنتجات -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="product-search" class="form-control" placeholder="بحث عن منتج...">
                                <button class="btn btn-outline-primary" type="button" id="scan-barcode">
                                    <i class="fas fa-barcode"></i>
                                </button>
                            </div>
                        </div>
                        <!-- تصفية حسب الفئة -->
                        <div class="col-md-6">
                            <select id="category-filter" class="form-select">
                                <option value="">كل الفئات</option>
                            </select>
                        </div>
                    </div>

                    <!-- عرض المنتجات -->
                    <div class="row g-3" id="products-grid">
                        <!-- سيتم تحميل المنتجات هنا -->
                    </div>
                </div>
            </div>
        </div>

        <!-- القسم الأيسر - الفاتورة -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <!-- اختيار العميل -->
                    <div class="mb-3">
                        <label class="form-label">العميل</label>
                        <select id="customer-select" class="form-select">
                            <option value="">اختر العميل</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-balance="{{ $customer->balance }}">
                                {{ $customer->name }} ({{ $customer->phone }})
                            </option>
                            @endforeach
                        </select>
                        <div class="mt-2" id="customer-info" style="display: none;">
                            <small class="text-muted">الرصيد المستحق: <span id="customer-balance">0</span> ج.م</small>
                        </div>
                    </div>

                    <!-- عناصر الفاتورة -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>الإجمالي</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="invoice-items">
                                <!-- سيتم إضافة عناصر الفاتورة هنا -->
                            </tbody>
                        </table>
                    </div>

                    <!-- ملخص الفاتورة -->
                    <div class="border-top pt-3">
                        <div class="row mb-2">
                            <div class="col-6">الإجمالي:</div>
                            <div class="col-6 text-end" id="invoice-total">0 ج.م</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">المدفوع:</label>
                                <input type="number" id="paid-amount" class="form-control" value="0" min="0" step="0.01">
                            </div>
                            <div class="col-6">
                                <label class="form-label">طريقة الدفع:</label>
                                <select id="payment-method" class="form-select">
                                    <option value="cash">نقداً</option>
                                    <option value="card">بطاقة</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">المتبقي:</div>
                            <div class="col-6 text-end" id="remaining-amount">0 ج.م</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات:</label>
                            <textarea id="invoice-notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button id="checkout-btn" class="btn btn-primary" disabled>
                                <i class="fas fa-cash-register"></i> إتمام البيع
                            </button>
                            <button id="clear-cart-btn" class="btn btn-outline-danger">
                                <i class="fas fa-trash"></i> إفراغ السلة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- قالب عنصر المنتج -->
<template id="product-template">
    <div class="col-md-4 col-lg-3">
        <div class="card h-100 product-card" data-id="">
            <div class="card-body">
                <h6 class="card-title mb-2 product-name"></h6>
                <p class="card-text mb-2">
                    <small class="text-muted product-code"></small>
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="product-price"></span>
                    <span class="badge bg-success product-quantity"></span>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- قالب عنصر الفاتورة -->
<template id="invoice-item-template">
    <tr>
        <td class="item-name"></td>
        <td>
            <input type="number" class="form-control form-control-sm item-quantity" min="1" value="1" style="width: 70px">
        </td>
        <td class="item-price"></td>
        <td class="item-total"></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-item">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('styles')
<style>
.product-card {
    cursor: pointer;
    transition: all 0.2s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    const productsGrid = document.getElementById('products-grid');
    const invoiceItems = document.getElementById('invoice-items');
    const checkoutBtn = document.getElementById('checkout-btn');
    const productSearch = document.getElementById('product-search');
    const categoryFilter = document.getElementById('category-filter');
    const customerSelect = document.getElementById('customer-select');
    const paidAmount = document.getElementById('paid-amount');
    const clearCartBtn = document.getElementById('clear-cart-btn');

    // تحميل الفئات
    fetch('/categories')
        .then(response => response.json())
        .then(categories => {
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categoryFilter.appendChild(option);
            });
        });

    // تحميل المنتجات
    function loadProducts(search = '', categoryId = '') {
        productsGrid.innerHTML = '';
        const url = new URL('/pos/products', window.location.origin);
        if (search) url.searchParams.append('search', search);
        if (categoryId) url.searchParams.append('category_id', categoryId);

        fetch(url)
            .then(response => response.json())
            .then(products => {
                products.forEach(product => {
                    const template = document.getElementById('product-template');
                    const clone = template.content.cloneNode(true);
                    const card = clone.querySelector('.product-card');
                    
                    card.dataset.id = product.id;
                    card.querySelector('.product-name').textContent = product.name;
                    card.querySelector('.product-code').textContent = product.code || 'بدون كود';
                    card.querySelector('.product-price').textContent = `${product.price} ج.م`;
                    card.querySelector('.product-quantity').textContent = `متوفر: ${product.quantity}`;

                    card.addEventListener('click', () => addToCart(product));
                    productsGrid.appendChild(clone);
                });
            });
    }

    // إضافة منتج للسلة
    function addToCart(product) {
        const existingItem = cart.find(item => item.product_id === product.id);
        if (existingItem) {
            if (existingItem.quantity < product.quantity) {
                existingItem.quantity++;
                updateCartDisplay();
            } else {
                alert('عذراً، لا توجد كمية كافية من هذا المنتج');
            }
        } else {
            cart.push({
                product_id: product.id,
                name: product.name,
                price: product.price,
                max_quantity: product.quantity,
                quantity: 1
            });
            updateCartDisplay();
        }
    }

    // تحديث عرض السلة
    function updateCartDisplay() {
        invoiceItems.innerHTML = '';
        let total = 0;

        cart.forEach((item, index) => {
            const template = document.getElementById('invoice-item-template');
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('tr');
            
            row.querySelector('.item-name').textContent = item.name;
            const quantityInput = row.querySelector('.item-quantity');
            quantityInput.value = item.quantity;
            quantityInput.max = item.max_quantity;
            row.querySelector('.item-price').textContent = `${item.price} ج.م`;
            row.querySelector('.item-total').textContent = `${(item.price * item.quantity).toFixed(2)} ج.م`;

            quantityInput.addEventListener('change', (e) => {
                const newQuantity = parseInt(e.target.value);
                if (newQuantity > item.max_quantity) {
                    alert('عذراً، لا توجد كمية كافية من هذا المنتج');
                    e.target.value = item.quantity;
                    return;
                }
                item.quantity = newQuantity;
                updateCartDisplay();
            });

            row.querySelector('.remove-item').addEventListener('click', () => {
                cart.splice(index, 1);
                updateCartDisplay();
            });

            total += item.price * item.quantity;
            invoiceItems.appendChild(clone);
        });

        document.getElementById('invoice-total').textContent = `${total.toFixed(2)} ج.م`;
        updateRemaining();
        checkoutBtn.disabled = cart.length === 0 || !customerSelect.value;
    }

    // تحديث المبلغ المتبقي
    function updateRemaining() {
        const total = parseFloat(document.getElementById('invoice-total').textContent);
        const paid = parseFloat(paidAmount.value) || 0;
        const remaining = total - paid;
        document.getElementById('remaining-amount').textContent = `${remaining.toFixed(2)} ج.م`;
    }

    // إفراغ السلة
    clearCartBtn.addEventListener('click', () => {
        cart = [];
        updateCartDisplay();
    });

    // تحديث معلومات العميل
    customerSelect.addEventListener('change', function() {
        const customerInfo = document.getElementById('customer-info');
        const balance = this.options[this.selectedIndex].dataset.balance || 0;
        
        if (this.value) {
            customerInfo.style.display = 'block';
            document.getElementById('customer-balance').textContent = balance;
        } else {
            customerInfo.style.display = 'none';
        }
        
        checkoutBtn.disabled = cart.length === 0 || !this.value;
    });

    // البحث عن المنتجات
    productSearch.addEventListener('input', debounce(() => {
        loadProducts(productSearch.value, categoryFilter.value);
    }, 300));

    // تصفية حسب الفئة
    categoryFilter.addEventListener('change', () => {
        loadProducts(productSearch.value, categoryFilter.value);
    });

    // تحديث المبلغ المتبقي عند تغيير المدفوع
    paidAmount.addEventListener('input', updateRemaining);

    // إتمام عملية البيع
    checkoutBtn.addEventListener('click', () => {
        const data = {
            customer_id: customerSelect.value,
            items: cart.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                price: item.price
            })),
            paid_amount: parseFloat(paidAmount.value) || 0,
            payment_method: document.getElementById('payment-method').value,
            notes: document.getElementById('invoice-notes').value
        };

        fetch('/pos/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('تم إنشاء الفاتورة بنجاح');
                cart = [];
                updateCartDisplay();
                paidAmount.value = 0;
                document.getElementById('invoice-notes').value = '';
                customerSelect.value = '';
                document.getElementById('customer-info').style.display = 'none';
                // يمكن إضافة تحويل لصفحة طباعة الفاتورة هنا
            } else {
                alert(result.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ أثناء إنشاء الفاتورة');
            console.error(error);
        });
    });

    // دالة مساعدة للحد من تكرار الطلبات
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // تحميل المنتجات عند بدء التشغيل
    loadProducts();
});
</script>
@endpush
