<!-- ملخص الفاتورة (Invoice Summary) -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-file-invoice"></i> ملخص الفاتورة</h6>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-6">الإجمالي:</div>
            <div class="col-6 text-end" id="invoice-total">0 SAR</div>
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
            <div class="col-6 text-end" id="remaining-amount">0 SAR</div>
        </div>
        <div class="mb-3">
            <label class="form-label">ملاحظات:</label>
            <textarea id="invoice-notes" class="form-control" rows="2"></textarea>
        </div>
        <div class="d-grid gap-2">
            <button id="checkout-btn" class="btn btn-primary" disabled>
                <i class="fas fa-cash-register"></i> إتمام البيع
            </button>
        </div>
    </div>
</div>
