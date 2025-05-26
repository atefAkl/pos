@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل المنتج: {{ $product->name }}</h1>
        <div>
            <a href="{{ route('products.show', $product) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> معاينة
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">بيانات المنتج</h6>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#quickActionsModal">
                    <i class="fas fa-bolt"></i> إجراءات سريعة
                </button>
            </div>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                        <i class="fas fa-info-circle"></i> المعلومات الأساسية
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab">
                        <i class="fas fa-tags"></i> التسعير والمخزون
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                        <i class="fas fa-info-circle"></i> التفاصيل الإضافية
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab">
                        <i class="fas fa-images"></i> الوسائط
                    </button>
                </li>
            </ul>

            <div class="tab-content p-3 border border-top-0 rounded-bottom" id="productTabsContent">
                <!-- تبويب المعلومات الأساسية -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel">
                    @include('products.partials.basic_info_form', ['product' => $product, 'categories' => $categories])
                </div>
                
                <!-- تبويب التسعير والمخزون -->
                <div class="tab-pane fade" id="pricing" role="tabpanel">
                    @include('products.partials.pricing_form', ['product' => $product])
                </div>
                
                <!-- تبويب التفاصيل الإضافية -->
                <div class="tab-pane fade" id="details" role="tabpanel">
                    @include('products.partials.details_form', ['product' => $product])
                </div>
                
                <!-- تبويب الوسائط -->
                <div class="tab-pane fade" id="media" role="tabpanel">
                    @include('products.partials.media_form', ['product' => $product])
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة الإجراءات السريعة -->
<div class="modal fade" id="quickActionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">الإجراءات السريعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <a href="{{ route('products.offers.create', $product) }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-tag me-2"></i> إضافة عرض خاص
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#discountModal">
                        <i class="fas fa-percentage me-2"></i> تطبيق خصم
                    </a>
                    <a href="{{ route('products.print-barcode', $product) }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-barcode me-2"></i> طباعة الباركود
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#purchaseOrderModal">
                        <i class="fas fa-shopping-cart me-2"></i> طلب شراء
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة إضافة خصم -->
@include('products.modals.discount')

<!-- نافذة طلب شراء -->
@include('products.modals.purchase_order')

@endsection

@push('scripts')
<script>
// تفعيل التبويبات
var productTabs = document.querySelectorAll('#productTabs button')
productTabs.forEach(function(tab) {
    tab.addEventListener('click', function (event) {
        // حفظ التبويب النشط في التخزين المحلي
        localStorage.setItem('activeProductTab', event.target.getAttribute('data-bs-target'));
    });
});

// استعادة التبويب النشط عند إعادة تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    var activeTab = localStorage.getItem('activeProductTab');
    if (activeTab) {
        var tabElement = document.querySelector(`[data-bs-target="${activeTab}"]`);
        if (tabElement) {
            var tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }
    
    // إضافة مؤشر التحميل للنماذج
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
            }
        });
    });
});

// عرض معاينة الصورة
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.src = "{{ $product->image ? asset('storage/' . $product->image) : asset('img/no-image.png') }}";
    }
}

// حذف صورة من المعرض
function deleteImage(imageId) {
    if (confirm('هل أنت متأكد من حذف هذه الصورة؟')) {
        fetch(`/product-images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`image-${imageId}`).remove();
                if (document.querySelectorAll('#galleryPreview > div').length === 0) {
                    document.getElementById('galleryPreview').innerHTML = '<div class="col-12 text-center text-muted">لا توجد صور في المعرض</div>';
                }
            }
        });
    }
}
</script>
@endpush
