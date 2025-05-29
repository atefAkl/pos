<div class="row">
    <!-- نظام إدارة الملفات الجديد -->
    <div class="col-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-file-upload"></i> رفع ملف جديد</h6>
            </div>
            <div class="card-body">
                <form id="fileUploadForm" method="POST" action="{{ route('product.files.upload') }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="related_id" value="{{ $product->id }}">
                    <input type="hidden" name="related_type" value="product">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="file" class="form-label required">اختر ملفاً</label>
                                <input type="file" class="form-control" id="file" name="file" required>
                            </div>
                            @if ($errors->has('file'))
                            <div class="text-danger mt-2">
                                {{ $errors->first('file') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="display_name" class="form-label">اسم العرض</label>
                                <input type="text" class="form-control" id="display_name" name="display_name" placeholder="اسم الملف للعرض (اختياري)">
                            </div>
                            @if ($errors->has('display_name'))
                            <div class="text-danger mt-2">
                                {{ $errors->first('display_name') }}
                            </div>
                            @endif
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
                            @if ($errors->has('category'))
                            <div class="text-danger mt-2">
                                {{ $errors->first('category') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="alt_text" class="form-label">النص البديل</label>
                                <input type="text" class="form-control" id="alt_text" name="alt_text" placeholder="وصف الصورة للقارئات الشاشة (اختياري)">
                            </div>
                            @if ($errors->has('alt_text'))
                            <div class="text-danger mt-2">
                                {{ $errors->first('alt_text') }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>

                    @if ($errors->has('is_active'))
                    <div class="text-danger mt-2">
                        {{ $errors->first('is_active') }}
                    </div>
                    @endif

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
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-barcode"></i> الباركود</h6>
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
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-folder-open"></i> ملفات المنتج</h6>
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
                        @forelse($product->productFiles()->with('file')->orderBy('the_order')->get() as $productFile)
                            <div class="col-md-3 col-sm-4 col-6 mb-3" id="file-{{ $productFile->id }}">
                                <div class="card h-100">
                                    @if($productFile->file && $productFile->file->isImage())
                                        <img src="{{ asset('storage/' . $productFile->file->path) }}" alt="{{ $productFile->file->alt_text ?? $product->name }}" class="card-img-top" style="height: 120px; object-fit: contain;">
                                    @else
                                        <div class="text-center py-4">
                                            <i class="{{ $productFile->file->icon ?? 'fas fa-file' }} fa-3x text-secondary mb-2"></i>
                                            <div class="small mt-2">{{ $productFile->file->display_name ?? $productFile->file->original_name }}</div>
                                        </div>
                                    @endif
                                    <div class="card-body p-2">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <span class="badge bg-secondary">{{ $productFile->category ? __("app.file_categories.$productFile->category") : 'غير مصنف' }}</span>
                                            <div class="btn-group btn-group-sm w-100">
                                                <button type="button" class="btn btn-outline-danger" onclick="deleteFile({{ $productFile->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
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