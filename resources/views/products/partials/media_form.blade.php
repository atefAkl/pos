<form action="{{ route('products.update-media', $product) }}" method="POST" enctype="multipart/form-data" class="mb-4">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- القسم الأيمن: الصورة الرئيسية ومعرض الصور -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-image"></i> الصورة الرئيسية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img id="mainImagePreview" src="{{ $product->image ? asset('uploads/' . $product->image) : asset('img/no-image.png') }}" 
                                alt="{{ $product->name }}" class="img-thumbnail mb-2" style="max-height: 150px;">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="image" class="form-label">تحديث الصورة الرئيسية</label>
                                <input class="form-control @error('image') is-invalid @enderror" type="file" 
                                    id="image" name="image" accept="image/*" onchange="previewImage(this, 'mainImagePreview')">
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="image_alt" class="form-label">النص البديل للصورة</label>
                                <input type="text" class="form-control" id="image_alt" name="image_alt" 
                                    value="{{ old('image_alt', $product->image_alt) }}" placeholder="وصف الصورة للقارئات الشاشة">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- معرض الصور -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-images"></i> معرض الصور</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="gallery" class="form-label">إضافة صور للمعرض (يمكن اختيار أكثر من صورة)</label>
                        <input class="form-control @error('gallery.*') is-invalid @enderror" type="file" 
                            id="gallery" name="gallery[]" multiple accept="image/*">
                        @error('gallery.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row" id="galleryPreview">
                        @forelse($product->images as $image)
                        <div class="col-md-3 col-sm-4 col-6 mb-3" id="image-{{ $image->id }}">
                            <div class="card h-100">
                                <img src="{{ asset('uploads/' . $image->path) }}" alt="{{ $image->alt ?? $product->name }}" 
                                    class="card-img-top" style="height: 120px; object-fit: contain;">
                                <div class="card-body p-2">
                                    <div class="btn-group btn-group-sm w-100">
                                        <button type="button" class="btn btn-outline-primary" 
                                            data-bs-toggle="modal" data-bs-target="#editImageModal{{ $image->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteImage({{ $image->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal لتعديل الصورة -->
                            <div class="modal fade" id="editImageModal{{ $image->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">تعديل بيانات الصورة</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('product-images.update', $image->id) }}" method="POST" class="image-edit-form">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="alt{{ $image->id }}" class="form-label">النص البديل</label>
                                                    <input type="text" class="form-control" id="alt{{ $image->id }}" 
                                                        name="alt" value="{{ $image->alt }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="name{{ $image->id }}" class="form-label">اسم الصورة</label>
                                                    <input type="text" class="form-control" id="name{{ $image->id }}" 
                                                        name="name" value="{{ $image->name }}">
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                                </div>
                                            </form>
                                            <hr>
                                            <form action="{{ route('product-images.replace', $image->id) }}" method="POST" 
                                                enctype="multipart/form-data" class="image-replace-form mt-3">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="new_image{{ $image->id }}" class="form-label">استبدال الصورة</label>
                                                    <input type="file" class="form-control" id="new_image{{ $image->id }}" 
                                                        name="new_image" accept="image/*" required>
                                                </div>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-warning">استبدال الصورة</button>
                                                </div>
                                            </form>
                                        </div>
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
            </div>
        </div>
        
        <!-- القسم الأيسر: الخيارات والإعدادات -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> إعدادات الصور</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="watermark" class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="watermark" name="watermark" 
                                {{ old('watermark', $product->watermark) ? 'checked' : '' }}>
                            إضافة علامة مائية للصور
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="featured_image" class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="featured_image" name="featured_image" 
                                {{ old('featured_image', $product->featured_image) ? 'checked' : '' }}>
                            عرض في الصفحة الرئيسية
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="image_position" class="form-label">موضع الصورة في صفحة المنتج</label>
                        <select class="form-select" id="image_position" name="image_position">
                            <option value="right" {{ old('image_position', $product->image_position) == 'right' ? 'selected' : '' }}>يمين</option>
                            <option value="left" {{ old('image_position', $product->image_position) == 'left' ? 'selected' : '' }}>يسار</option>
                            <option value="top" {{ old('image_position', $product->image_position) == 'top' ? 'selected' : '' }}>أعلى</option>
                            <option value="bottom" {{ old('image_position', $product->image_position) == 'bottom' ? 'selected' : '' }}>أسفل</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-qrcode"></i> الباركود</h5>
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
            
            <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
                <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> معاينة المنتج
                </a>
            </div>
        </div>
    </div>
</form>

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