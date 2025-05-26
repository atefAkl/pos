<form id="detailsForm" action="{{ route('products.update-details', $product) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="barcode" class="form-label">الباركود</label>
                <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                       id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                @error('barcode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                       id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                @error('sku')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="notes" class="form-label">ملاحظات إضافية</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" 
                  name="notes" rows="3">{{ old('notes', $product->notes) }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">المنتج مفعل</label>
    </div>
    
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-info-circle"></i> حفظ التفاصيل
        </button>
    </div>
</form>
