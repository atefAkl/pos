<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">المورد والعلامة التجارية</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="supplier_id" class="form-label">المورد</label>
                    <select class="form-select @error('supplier_id') is-invalid @enderror" 
                            id="supplier_id" name="supplier_id">
                        <option value="">اختر المورد</option>
                        @foreach(\App\Models\Supplier::active()->get() as $supplier)
                            <option value="{{ $supplier->id }}" 
                                {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="brand_id" class="form-label">العلامة التجارية</label>
                    <select class="form-select @error('brand_id') is-invalid @enderror" 
                            id="brand_id" name="brand_id">
                        <option value="">اختر العلامة التجارية</option>
                        @foreach(\App\Models\Brand::active()->get() as $brand)
                            <option value="{{ $brand->id }}" 
                                {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">الضرائب والخصومات</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="tax_id" class="form-label">الضريبة</label>
                    <select class="form-select @error('tax_id') is-invalid @enderror" 
                            id="tax_id" name="tax_id">
                        <option value="">بدون ضريبة</option>
                        @foreach(\App\Models\Tax::active()->get() as $tax)
                            <option value="{{ $tax->id }}" 
                                {{ old('tax_id', $product->tax_id) == $tax->id ? 'selected' : '' }}
                                data-rate="{{ $tax->rate }}">
                                {{ $tax->name }} ({{ $tax->rate }}%)
                            </option>
                        @endforeach
                    </select>
                    @error('tax_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="discount" class="form-label">الخصم</label>
                    <div class="input-group">
                        <input type="number" min="0" max="100" step="0.01" 
                               class="form-control @error('discount') is-invalid @enderror" 
                               id="discount" name="discount" 
                               value="{{ old('discount', $product->discount) }}">
                        <span class="input-group-text">%</span>
                        @error('discount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">معلومات إضافية</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="weight" class="form-label">الوزن (كجم)</label>
                    <input type="number" step="0.001" min="0" class="form-control @error('weight') is-invalid @enderror" 
                           id="weight" name="weight" value="{{ old('weight', $product->weight) }}">
                    @error('weight')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="dimensions" class="form-label">الأبعاد (سم)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" placeholder="الطول" 
                               class="form-control" name="dimensions[length]" 
                               value="{{ old('dimensions.length', $product->dimensions['length'] ?? '') }}">
                        <span class="input-group-text">×</span>
                        <input type="number" step="0.01" min="0" placeholder="العرض" 
                               class="form-control" name="dimensions[width]" 
                               value="{{ old('dimensions.width', $product->dimensions['width'] ?? '') }}">
                        <span class="input-group-text">×</span>
                        <input type="number" step="0.01" min="0" placeholder="الارتفاع" 
                               class="form-control" name="dimensions[height]" 
                               value="{{ old('dimensions.height', $product->dimensions['height'] ?? '') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">ملاحظات إضافية</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="3">{{ old('notes', $product->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
