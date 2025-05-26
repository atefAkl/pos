<form id="pricingForm" action="{{ route('products.update-pricing', $product) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="price" class="form-label">السعر الأساسي</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                           id="price" name="price" value="{{ old('price', $product->price) }}" required>
                    <span class="input-group-text">ر.س</span>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="mb-3">
                <label for="cost" class="form-label">سعر التكلفة</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror" 
                           id="cost" name="cost" value="{{ old('cost', $product->cost) }}">
                    <span class="input-group-text">ر.س</span>
                    @error('cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="mb-3">
                <label for="quantity" class="form-label">الكمية المتوفرة</label>
                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                       id="quantity" name="quantity" value="{{ old('quantity', $product->quantity) }}" required>
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-tag"></i> حفظ التسعير والمخزون
        </button>
    </div>
</form>
