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
            <form id="productForm" action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
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
                        @include('products.partials.basic_info', ['product' => $product, 'categories' => $categories])
                    </div>
                    
                    <!-- تبويب التسعير والمخزون -->
                    <div class="tab-pane fade" id="pricing" role="tabpanel">
                        @include('products.partials.pricing_inventory', ['product' => $product])
                    </div>
                    
                    <!-- تبويب التفاصيل الإضافية -->
                    <div class="tab-pane fade" id="details" role="tabpanel">
                        @include('products.partials.additional_details', ['product' => $product])
                    </div>
                    
                    <!-- تبويب الوسائط -->
                    <div class="tab-pane fade" id="media" role="tabpanel">
                        @include('products.partials.media', ['product' => $product])
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ التغييرات
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
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
    })
})

// استعادة التبويب النشط عند إعادة تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    var activeTab = localStorage.getItem('activeProductTab');
    if (activeTab) {
        var tab = new bootstrap.Tab(document.querySelector(`[data-bs-target="${activeTab}"]`));
        tab.show();
    }
});

// حفظ النموذج باستخدام AJAX
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-HTTP-Method-Override': 'PUT'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success('تم تحديث المنتج بنجاح');
        } else {
            toastr.error('حدث خطأ أثناء تحديث المنتج');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('حدث خطأ غير متوقع');
    });
});
</script>
@endpush
