<div class="row">
    <!-- قسم التسعير -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">التسعير</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="price" class="form-label">سعر التكلفة <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price', $product->price) }}" required>
                        <span class="input-group-text">ج.م</span>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="retail_price" class="form-label">سعر البيع <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" class="form-control @error('retail_price') is-invalid @enderror" 
                               id="retail_price" name="retail_price" value="{{ old('retail_price', $product->retail_price) }}" required>
                        <span class="input-group-text">ج.م</span>
                        @error('retail_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="text-muted">هامش الربح: <span id="profitMargin">0</span>%</small>
                </div>

                <div class="mb-3">
                    <label for="wholesale_price" class="form-label">سعر الجملة</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" class="form-control @error('wholesale_price') is-invalid @enderror" 
                               id="wholesale_price" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price) }}">
                        <span class="input-group-text">ج.م</span>
                        @error('wholesale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="wholesale_quantity" class="form-label">الحد الأدنى لكمية الجملة</label>
                    <input type="number" min="0" class="form-control @error('wholesale_quantity') is-invalid @enderror" 
                           id="wholesale_quantity" name="wholesale_quantity" value="{{ old('wholesale_quantity', $product->wholesale_quantity) }}">
                    @error('wholesale_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- قسم المخزون -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">المخزون</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="quantity" class="form-label">الكمية المتوفرة <span class="text-danger">*</span></label>
                    <input type="number" min="0" class="form-control @error('quantity') is-invalid @enderror" 
                           id="quantity" name="quantity" value="{{ old('quantity', $product->quantity) }}" required>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="alert_quantity" class="form-label">حد التنبيه <span class="text-danger">*</span></label>
                    <input type="number" min="0" class="form-control @error('alert_quantity') is-invalid @enderror" 
                           id="alert_quantity" name="alert_quantity" value="{{ old('alert_quantity', $product->alert_quantity) }}" required>
                    @error('alert_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">سيتم إشعارك عندما تقل الكمية عن هذا الحد</small>
                </div>

                <div class="mb-3">
                    <label for="reorder_level" class="form-label">حد إعادة الطلب</label>
                    <input type="number" min="0" class="form-control @error('reorder_level') is-invalid @enderror" 
                           id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}">
                    @error('reorder_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">الحد الأدنى للمخزون قبل إعادة الطلب</small>
                </div>

                <div class="mb-3">
                    <label for="unit_id" class="form-label">وحدة القياس</label>
                    <select class="form-select @error('unit_id') is-invalid @enderror" 
                            id="unit_id" name="unit_id">
                        <option value="">اختر وحدة القياس</option>
                        @foreach(\App\Models\Unit::active()->get() as $unit)
                            <option value="{{ $unit->id }}" 
                                {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// حساب هامش الربح تلقائياً
function calculateProfitMargin() {
    const cost = parseFloat(document.getElementById('price').value) || 0;
    const price = parseFloat(document.getElementById('retail_price').value) || 0;
    
    if (cost > 0 && price > 0) {
        const profit = ((price - cost) / cost) * 100;
        document.getElementById('profitMargin').textContent = profit.toFixed(2);
    } else {
        document.getElementById('profitMargin').textContent = '0.00';
    }
}

// استدعاء الدالة عند تغيير السعر أو سعر التكلفة
document.getElementById('price').addEventListener('input', calculateProfitMargin);
document.getElementById('retail_price').addEventListener('input', calculateProfitMargin);

// حساب القيمة الأولية عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', calculateProfitMargin);
</script>
@endpush
