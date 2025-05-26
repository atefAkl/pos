@extends('layouts.dashboard')

@section('content')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to handle button click and API call
    function setupGenerateButton(buttonId, inputId, routeName, loadingText, iconClass) {
        const button = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        
        if (button && input) {
            button.addEventListener('click', function() {
                // Show loading state
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
                
                // Call the API to generate the code
                fetch(routeName, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const fieldName = Object.keys(data).find(key => key !== 'success');
                        input.value = data[fieldName];
                        // Trigger change event in case there are any listeners
                        input.dispatchEvent(new Event('change'));
                    } else {
                        console.error(`Failed to generate ${inputId}`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = `<i class="${iconClass}"></i> توليد`;
                });
            });
        }
    }
    
    // Setup all generate buttons
    setupGenerateButton(
        'generateBarcode', 
        'barcode', 
        '{{ route("products.generate-barcode") }}',
        'جاري توليد الباركود...',
        'fas fa-sync-alt'
    );
    
    setupGenerateButton(
        'generateProductCode', 
        'code', 
        '{{ route("products.generate-product-code") }}',
        'جاري توليد الكود...',
        'fas fa-barcode'
    );
    
    setupGenerateButton(
        'generateSKU', 
        'sku', 
        '{{ route("products.generate-sku") }}',
        'جاري توليد SKU...',
        'fas fa-hashtag'
    );
});
</script>
@endpush

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-box-open me-2"></i>إضافة منتج / خدمة جديدة
        </h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> عودة للقائمة
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-3">
                    <!-- Basic Information -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-1">
                            <i class="fas fa-info-circle me-2"></i> المعلومات الأساسية
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_service" name="is_service" value="1" {{ old('is_service') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_service">
                                <i class="fas fa-concierge-bell me-1"></i>هذه خدمة وليست منتج
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                <i class="fas fa-check-circle me-1"></i>نشط
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label small text-muted mb-1">الاسم <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="category_id" class="form-label small text-muted mb-1">التصنيف <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">اختر التصنيف</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', $selectedCategoryId) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->full_path }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="code" class="form-label small text-muted mb-1">كود المنتج</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                id="code" name="code" value="{{ old('code', $newProductCode ?? '') }}" required>
                            <button class="btn btn-outline-secondary" type="button" id="generateProductCode">
                                <i class="fas fa-barcode"></i> توليد
                            </button>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="barcode" class="form-label small text-muted mb-1">الباركود</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                            <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                id="barcode" name="barcode" value="{{ old('barcode', $newBarcode) }}" required>
                            <button class="btn btn-outline-secondary" type="button" id="generateBarcode">
                                <i class="fas fa-sync-alt"></i> توليد
                            </button>
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">سيتم توليد باركود تلقائيًا بزيادة 10 عن آخر باركود</small>
                    </div>

                    <div class="col-md-4">
                        <label for="sku" class="form-label small text-muted mb-1">SKU</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                id="sku" name="sku" value="{{ old('sku') }}" required>
                            <button class="btn btn-outline-secondary" type="button" id="generateSKU">
                                <i class="fas fa-hashtag"></i> توليد
                            </button>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-1 mt-2">
                            <i class="fas fa-tags me-2"></i> الأسعار والضرائب
                        </h5>
                    </div>

                    <div class="col-md-4">
                        <label for="price" class="form-label small text-muted mb-1">سعر التكلفة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                id="price" name="price" value="{{ old('price', 0) }}" required>
                            <span class="input-group-text">ر.س</span>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="retail_price" class="form-label small text-muted mb-1">سعر البيع</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <input type="number" step="0.01" class="form-control @error('retail_price') is-invalid @enderror" 
                                id="retail_price" name="retail_price" value="{{ old('retail_price') }}">
                            <span class="input-group-text">ر.س</span>
                            @error('retail_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="tax_rate" class="form-label small text-muted mb-1">نسبة الضريبة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                            <input type="number" step="0.01" class="form-control @error('tax_rate') is-invalid @enderror" 
                                id="tax_rate" name="tax_rate" value="{{ old('tax_rate', 15) }}" required>
                            <span class="input-group-text">%</span>
                            @error('tax_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="wholesale_price" class="form-label small text-muted mb-1">سعر الجملة</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                            <input type="number" step="0.01" class="form-control @error('wholesale_price') is-invalid @enderror" 
                                id="wholesale_price" name="wholesale_price" value="{{ old('wholesale_price') }}">
                            <span class="input-group-text">ر.س</span>
                            @error('wholesale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="wholesale_quantity" class="form-label small text-muted mb-1">الحد الأدنى لكمية الجملة</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-boxes"></i></span>
                            <input type="number" class="form-control @error('wholesale_quantity') is-invalid @enderror" 
                                id="wholesale_quantity" name="wholesale_quantity" value="{{ old('wholesale_quantity', 1) }}" min="1">
                            @error('wholesale_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3 service-field" style="display: none;">
                        <label for="service_duration" class="form-label small text-muted mb-1">مدة الخدمة (دقيقة)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="number" class="form-control @error('service_duration') is-invalid @enderror" 
                                id="service_duration" name="service_duration" value="{{ old('service_duration', 60) }}" min="1">
                            @error('service_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 mb-1 mt-4 product-fields">
                        <h5 class="mb-3 border-bottom pb-2">
                            <i class="fas fa-boxes"></i> معلومات المخزون
                        </h5>
                    </div>

                    <div class="col-md-4 product-fields">
                        <label for="quantity" class="form-label small text-muted mb-1">الكمية المتوفرة</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-boxes"></i></span>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3 product-fields">
                        <label for="alert_quantity" class="form-label small text-muted mb-1">حد التنبيه</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-bell"></i></span>
                            <input type="number" class="form-control @error('alert_quantity') is-invalid @enderror" 
                                id="alert_quantity" name="alert_quantity" value="{{ old('alert_quantity', 5) }}" min="0">
                            @error('alert_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3 product-fields">
                        <label for="reorder_level" class="form-label small text-muted mb-1">حد إعادة الطلب</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-boxes"></i></span>
                            <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" 
                                id="reorder_level" name="reorder_level" value="{{ old('reorder_level', 10) }}" min="0">
                            @error('reorder_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3 product-fields">
                        <label for="unit" class="form-label small text-muted mb-1">الوحدة</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-ruler"></i></span>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                id="unit" name="unit" value="{{ old('unit', 'قطعة') }}">
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3 product-fields">
                        <label for="weight" class="form-label small text-muted mb-1">الوزن (كجم)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-weight"></i></span>
                            <input type="number" step="0.001" class="form-control @error('weight') is-invalid @enderror" 
                                id="weight" name="weight" value="{{ old('weight') }}" min="0">
                            <span class="input-group-text">كجم</span>
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3 product-fields">
                        <label for="dimensions" class="form-label small text-muted mb-1">الأبعاد (سم)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-ruler"></i></span>
                            <input type="text" class="form-control @error('dimensions') is-invalid @enderror" 
                                id="dimensions" name="dimensions" value="{{ old('dimensions') }}" placeholder="الطول × العرض × الارتفاع">
                            @error('dimensions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 mb-1 mt-4">
                        <h5 class="mb-3 border-bottom pb-2">
                            <i class="fas fa-image"></i> الصورة
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label for="image" class="form-label small text-muted mb-1">صورة المنتج</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text text-muted">الحد الأقصى لحجم الملف 2 ميجابايت. الصيغ المسموح بها: jpg, jpeg, png, gif</div>
                    </div>

                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-4 mt-2">
                            <i class="fas fa-info-circle me-2"></i> معلومات إضافية
                        </h5>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> حفظ المنتج
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        // Toggle service/fields based on service checkbox
        function toggleServiceFields() {
            if ($('#is_service').is(':checked')) {
                $('.product-fields').hide();
                $('.service-field').show();
                $('label[for="quantity"], #quantity, label[for="alert_quantity"], #alert_quantity').closest('.form-group').hide();
            } else {
                $('.product-fields').show();
                $('.service-field').hide();
                $('label[for="quantity"], #quantity, label[for="alert_quantity"], #alert_quantity').closest('.form-group').show();
            }
        }

        // Initial toggle
        toggleServiceFields();

        // Toggle on change
        $('#is_service').change(function() {
            toggleServiceFields();
            // Update button text
            const buttonText = $(this).is(':checked') ? 'حفظ الخدمة' : 'حفظ المنتج';
            $('button[type="submit"]').html('<i class="fas fa-save"></i> ' + buttonText);
        });

        // Auto-calculate retail price if empty
        $('#price').on('blur', function() {
            if (!$('#retail_price').val()) {
                const price = parseFloat($(this).val()) || 0;
                const retailPrice = price * 1.2; // Add 20% margin by default
                $('#retail_price').val(retailPrice.toFixed(2));
            }
        });
    });
</script>
@endsection
