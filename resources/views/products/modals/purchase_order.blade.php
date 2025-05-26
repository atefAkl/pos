<!-- نافذة طلب شراء -->
<div class="modal fade" id="purchaseOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إنشاء طلب شراء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('purchase-orders.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">معلومات المنتج</h6>
                                    <div class="mb-2">
                                        <strong>الاسم:</strong> {{ $product->name }}
                                    </div>
                                    <div class="mb-2">
                                        <strong>الكود:</strong> {{ $product->code }}
                                    </div>
                                    <div class="mb-2">
                                        <strong>الكمية المتوفرة:</strong> {{ $product->quantity }} {{ $product->unit->name ?? '' }}
                                    </div>
                                    <div>
                                        <strong>آخر سعر شراء:</strong> {{ number_format($product->last_purchase_price, 2) }} {{ config("settings.currency_symbol") }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">تفاصيل الطلب</h6>

                                    <div class="mb-3">
                                        <label for="supplier_id" class="form-label">المورد <span class="text-danger">*</span></label>
                                        <select class="form-select" id="supplier_id" name="supplier_id" required>
                                            <option value="">اختر المورد</option>
                                            @if($product->supplier_id)
                                            <option value="{{ $product->supplier_id }}" selected>
                                                {{ $product->supplier->name }}
                                            </option>
                                            @endif
                                            @foreach(\App\Models\Supplier::active()->get() as $supplier)
                                            @if($supplier->id != $product->supplier_id)
                                            <option value="{{ $supplier->id }}">
                                                {{ $supplier->name }}
                                            </option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">الكمية المطلوبة <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" min="1" class="form-control" id="quantity"
                                                name="quantity" value="{{ max(1, $product->reorder_level - $product->quantity) }}" required>
                                            <span class="input-group-text">{{ $product->unit->name ?? 'وحدة' }}</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="expected_price" class="form-label">السعر المتوقع للوحدة</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                id="expected_price" name="expected_price"
                                                value="{{ $product->last_purchase_price }}">
                                            <span class="input-group-text">{{ config("settings.currency_symbol") }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات إضافية</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> إرسال طلب الشراء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<span style="display: none;" class="product_qty">{{ $product->quantity }}</span>

<span style="display: none;" class="product_reorder_level">{{ $product->reorder_level }}</span>

@push('scripts')
<script>
    // حساب الكمية المطلوبة بناءً على حد إعادة الطلب
    function calculateRequiredQuantity() {
        const currentQuantity = document.querySelector('span.product_qty').textContent;
        const reorderLevel = document.querySelector('span.product_reorder_level').textContent;

        if (currentQuantity < reorderLevel) {
            document.getElementById('quantity').value = reorderLevel - currentQuantity;
        } else {
            document.getElementById('quantity').value = 1;
        }
    }

    // تحديث السعر المتوقع عند تغيير المورد
    document.getElementById('supplier_id').addEventListener('change', function() {
        // يمكنك إضافة منطق لجلب آخر سعر شراء من هذا المورد
        // هذا مثال بسيط
        const supplierId = this.value;
        if (supplierId) {
            // يمكنك إضافة استدعاء AJAX للحصول على آخر سعر شراء من هذا المورد
            // document.getElementById('expected_price').value = lastPurchasePrice;
        }
    });

    // حساب الكمية المطلوبة عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', calculateRequiredQuantity);
</script>
@endpush