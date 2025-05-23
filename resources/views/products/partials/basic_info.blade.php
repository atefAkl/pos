<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">اسم المنتج <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" 
               id="name" name="name" value="{{ old('name', $product->name) }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="category_id" class="form-label">الفئة الرئيسية <span class="text-danger">*</span></label>
        <select class="form-select @error('category_id') is-invalid @enderror" 
                id="category_id" name="category_id" required>
            <option value="">اختر الفئة</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" 
                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="code" class="form-label">كود المنتج</label>
        <div class="input-group">
            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                   id="code" name="code" value="{{ old('code', $product->code) }}">
            <button class="btn btn-outline-secondary" type="button" id="generateCode">
                <i class="fas fa-sync-alt"></i> توليد
            </button>
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <label for="barcode" class="form-label">الباركود</label>
        <div class="input-group">
            <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                   id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
            <button class="btn btn-outline-secondary" type="button" id="generateBarcode">
                <i class="fas fa-barcode"></i> توليد
            </button>
            @error('barcode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12 mb-3">
        <label for="description" class="form-label">الوصف المختصر</label>
        <textarea class="form-control @error('description') is-invalid @enderror" 
                  id="description" name="description" rows="2">{{ old('description', $product->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" 
                   id="is_active" name="is_active" value="1" 
                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">نشط</label>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" 
                   id="is_service" name="is_service" value="1"
                   {{ old('is_service', $product->is_service) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_service">خدمة (وليس منتج)</label>
        </div>
    </div>
</div>
