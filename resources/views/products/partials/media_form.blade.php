<div class="row">
    <div class="col col-md-8">
        <!-- قسم الصورة الرئيسية -->
        <div class="card mb-3">
            <form action="{{ route('products.update-main-image', $product) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-image"></i> &nbsp; الصورة الرئيسية</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img id="mainImagePreview" src="{{ $product->image ? asset('uploads/' . $product->image) : asset('img/no-image.png') }}" alt="{{ $product->name }}"
                                class="img-thumbnail mb-2" style="max-height: 150px;">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="image" class="form-label">تحديث الصورة الرئيسية</label>
                                <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*"
                                    onchange="previewImage(this, 'mainImagePreview')">
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="image_alt" class="form-label">النص البديل للصورة</label>
                                <input type="text" class="form-control" id="image_alt" name="image_alt" value="{{ old('image_alt', $product->image_alt) }}"
                                    placeholder="وصف الصورة للقارئات الشاشة">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الصورة الرئيسية</button>
                </div>
            </form>
        </div>
        <!-- نهاية قسم الصورة الرئيسية -->
    </div>
    <div class="col col-md-4">
        <!-- قسم الباركود -->
        <div class="card mb-3">
            <form action="{{ route('products.update-barcode', $product) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                @method('PUT')
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-qrcode"></i> &nbsp; الباركود</h6>
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
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> تحديث الباركود</button>
                    </div>
                </div>
            </form>
            <!-- نهاية قسم الباركود -->
        </div>
    </div>
    <div class="col col-md-4">
        <!-- قسم صورة إضافية (مثال: صورة خلفية) -->
        <div class="card mb-3">
            <form action="{{ route('products.update-extra-image', $product) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                @method('PUT')
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-image"></i> &nbsp; صورة إضافية</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="extra_image" class="form-label">رفع صورة إضافية</label>
                            <input class="form-control @error('extra_image') is-invalid @enderror" type="file" id="extra_image" name="extra_image" accept="image/*">
                            @error('extra_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($product->extra_image)
                        <div class="mb-3 text-center">
                            <img src="{{ asset('uploads/' . $product->extra_image) }}" alt="صورة إضافية" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        @endif
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الصورة الإضافية</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col col-md-8">
        <!-- قسم معرض الصور -->
        <div class="card mb-3">
            <form action="{{ route('products.update-gallery', $product) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-images"></i> &nbsp; معرض الصور</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="gallery" class="form-label">إضافة صور للمعرض (يمكن اختيار أكثر من صورة)</label>
                        <input class="form-control @error('gallery.*') is-invalid @enderror" type="file" id="gallery" name="gallery[]" multiple accept="image/*">
                        @error('gallery.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row" id="galleryPreview">
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
                                        <button type="button" class="btn btn-outline-danger" onclick="deleteImage({{ $image->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-muted">
                            <p>لا توجد صور في المعرض</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ المعرض</button>
                </div>
            </form>
        </div>
        <!-- نهاية قسم معرض الصور -->
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