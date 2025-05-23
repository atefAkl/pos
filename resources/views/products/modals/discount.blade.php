<!-- نافذة إضافة خصم -->
<div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تطبيق خصم على المنتج</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('products.discount', $product) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="discount_type" class="form-label">نوع الخصم</label>
                        <select class="form-select" id="discount_type" name="discount_type" required>
                            <option value="percentage">نسبة مئوية %</option>
                            <option value="fixed">مبلغ ثابت</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="discount_value" class="form-label">قيمة الخصم</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control" 
                                   id="discount_value" name="discount_value" required>
                            <span class="input-group-text" id="discount_suffix">%</span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="discount_start_date" class="form-label">تاريخ البداية</label>
                            <input type="datetime-local" class="form-control" 
                                   id="discount_start_date" name="start_date" 
                                   value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="discount_end_date" class="form-label">تاريخ النهاية</label>
                            <input type="datetime-local" class="form-control" 
                                   id="discount_end_date" name="end_date"
                                   value="{{ now()->addWeek()->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="discount_reason" class="form-label">سبب الخصم (اختياري)</label>
                        <textarea class="form-control" id="discount_reason" name="reason" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ الخصم</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// تغيير لاحقة قيمة الخصم حسب النوع
document.getElementById('discount_type').addEventListener('change', function() {
    const suffix = this.value === 'percentage' ? '%' : '{{ currency_symbol() }}';
    document.getElementById('discount_suffix').textContent = suffix;
    
    // تحديث الحد الأقصى للقيمة إذا كانت نسبة مئوية
    const valueInput = document.getElementById('discount_value');
    if (this.value === 'percentage') {
        valueInput.setAttribute('max', '100');
    } else {
        valueInput.removeAttribute('max');
    }
});
</script>
@endpush
