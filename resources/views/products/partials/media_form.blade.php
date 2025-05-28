<div class="row">
    <!-- نظام إدارة الملفات الجديد -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-file-upload"></i> رفع ملف جديد</h5>
            </div>
            <div class="card-body">
                <form id="fileUploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="related_id" value="{{ $product->id }}">
                    <input type="hidden" name="related_type" value="product">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="file" class="form-label required">اختر ملفاً</label>
                                <input type="file" class="form-control" id="file" name="file" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="display_name" class="form-label">اسم العرض</label>
                                <input type="text" class="form-control" id="display_name" name="display_name" placeholder="اسم الملف للعرض (اختياري)">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label required">الفئة</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="product_image">صورة المنتج</option>
                                    <option value="gallery_image">صورة معرض</option>
                                    <option value="barcode">باركود</option>
                                    <option value="document">مستند</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="alt_text" class="form-label">النص البديل</label>
                                <input type="text" class="form-control" id="alt_text" name="alt_text" placeholder="وصف الصورة للقارئات الشاشة (اختياري)">
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary" id="uploadFileBtn">
                            <i class="fas fa-upload"></i> رفع الملف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- قسم الباركود -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-barcode"></i> الباركود</h5>
            </div>
            <div class="card-body">
                @if($product->barcode)
                <div class="text-center mb-3">
                    <img src="{{ $product->barcode_image_url }}" alt="باركود المنتج" class="img-fluid">
                    <p class="mt-2">{{ $product->barcode }}</p>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('products.print-barcode', $product) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fas fa-print"></i> طباعة الباركود
                    </a>
                </div>
                @else
                <div class="alert alert-warning">
                    لم يتم تعيين باركود لهذا المنتج. يرجى تحديث المعلومات الأساسية للمنتج.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- مستعرض ملفات المنتج -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-folder-open"></i> ملفات المنتج</h5>
            </div>
            <div class="card-body">
                <!-- أزرار التصفية -->
                <div class="mb-3">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" data-filter="all">الكل</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-filter="product_image">صور المنتج</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-filter="gallery_image">معرض الصور</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-filter="barcode">باركود</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-filter="document">مستندات</button>
                    </div>
                </div>

                <!-- مستعرض الملفات -->
                <div id="filesContainer">
                    <div class="row" id="filesList">
                        @forelse($product->images as $image)
                        <div class="col-md-3 col-sm-4 col-6 mb-3" id="image-{{ $image->id }}">
                            <div class="card h-100">
                                <img src="{{ asset('uploads/' . $image->path) }}" alt="{{ $image->alt ?? $product->name }}" class="card-img-top"
                                    style="height: 120px; object-fit: contain;">
                                <div class="card-body p-2">
                                    <div class="btn-group btn-group-sm w-100">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editImageModal{{ $image->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" data_id="{{ $image->id }}" onclick="deleteImage(this.id)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <!-- سيتم تحميل الملفات هنا عبر JavaScript -->
                        <div class="col-12 text-center py-5" id="noFilesMessage">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p>لا توجد ملفات مرتبطة بالمنتج حالياً</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>




@push('scripts')
<script>
    // معاينة الصورة قبل الرفع
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
            preview.src = "{{ $product->image ? asset('uploads/' . $product->image) : asset('img/no-image.png') }}";
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
                            document.getElementById('galleryPreview').innerHTML = '<div class="col-12 text-center text-muted"><p>لا توجد صور في المعرض</p></div>';
                        }
                    } else {
                        alert(data.message || 'حدث خطأ أثناء حذف الصورة');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ غير متوقع أثناء حذف الصورة');
                });
        }
    }
</script>
@endpush