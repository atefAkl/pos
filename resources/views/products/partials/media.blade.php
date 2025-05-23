<div class="row">
    <!-- الصورة الرئيسية -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0">الصورة الرئيسية</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3" id="imagePreviewContainer">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="صورة المنتج" 
                             class="img-fluid rounded mb-2" style="max-height: 200px;">
                    @else
                        <div class="bg-light p-5 text-muted rounded mb-3">
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <p class="mb-0">لا توجد صورة</p>
                        </div>
                    @endif
                </div>
                <div class="d-grid">
                    <input type="file" class="d-none" id="image" name="image" accept="image/*">
                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('image').click()">
                        <i class="fas fa-upload me-1"></i> رفع صورة
                    </button>
                    @if($product->image)
                        <button type="button" class="btn btn-outline-danger mt-2" id="removeImage">
                            <i class="fas fa-trash-alt me-1"></i> حذف الصورة
                        </button>
                    @endif
                </div>
                <small class="text-muted d-block mt-2">الحجم الأمثل: 800×800 بكسل</small>
                @error('image')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- معرض الصور -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">معرض الصور</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addGalleryImage">
                    <i class="fas fa-plus"></i> إضافة صورة
                </button>
            </div>
            <div class="card-body">
                <div class="row" id="galleryPreview">
                    @forelse($product->getMedia('gallery') as $media)
                        <div class="col-6 col-md-4 mb-3 gallery-item" data-id="{{ $media->id }}">
                            <div class="position-relative">
                                <img src="{{ $media->getUrl('thumb') }}" class="img-fluid rounded border" alt="صورة المعرض">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 start-0 m-1 remove-gallery-image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-4">
                            <i class="fas fa-images fa-3x mb-2"></i>
                            <p class="mb-0">لا توجد صور في المعرض</p>
                        </div>
                    @endforelse
                </div>
                <input type="file" class="d-none" id="gallery" name="gallery[]" multiple accept="image/*">
            </div>
        </div>
    </div>

    <!-- المستندات المرفقة -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">المستندات المرفقة</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addDocument">
                    <i class="fas fa-plus"></i> إضافة مستند
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="documentsTable">
                        <thead>
                            <tr>
                                <th>اسم الملف</th>
                                <th>النوع</th>
                                <th>الحجم</th>
                                <th>تاريخ الرفع</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->getMedia('documents') as $document)
                                <tr data-id="{{ $document->id }}">
                                    <td>
                                        <a href="{{ $document->getUrl() }}" target="_blank">
                                            <i class="fas {{ getFileIcon($document->mime_type) }} me-2"></i>
                                            {{ $document->name }}
                                        </a>
                                    </td>
                                    <td>{{ strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION)) }}</td>
                                    <td>{{ formatFileSize($document->size) }}</td>
                                    <td>{{ $document->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ $document->getUrl() }}" class="btn btn-sm btn-outline-primary" download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-document">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">لا توجد مستندات مرفقة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <input type="file" class="d-none" id="documents" name="documents[]" multiple>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// معاينة الصورة الرئيسية
const imageInput = document.getElementById('image');
const imagePreview = document.getElementById('imagePreviewContainer');

imageInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.innerHTML = `
                <img src="${e.target.result}" alt="معاينة الصورة" class="img-fluid rounded mb-2" style="max-height: 200px;">
            `;
            
            // إظهار زر الحذف إذا لم يكن موجوداً
            if (!document.getElementById('removeImage')) {
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-outline-danger mt-2';
                removeBtn.id = 'removeImage';
                removeBtn.innerHTML = '<i class="fas fa-trash-alt me-1"></i> حذف الصورة';
                removeBtn.addEventListener('click', function() {
                    imagePreview.innerHTML = `
                        <div class="bg-light p-5 text-muted rounded mb-3">
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <p class="mb-0">لا توجد صورة</p>
                        </div>
                    `;
                    imageInput.value = '';
                    // إضافة زر إزالة الصورة
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'remove_image';
                    hiddenInput.value = '1';
                    document.getElementById('productForm').appendChild(hiddenInput);
                });
                imagePreview.parentNode.insertBefore(removeBtn, imagePreview.nextSibling);
            }
        };
        reader.readAsDataURL(file);
    }
});

// إزالة الصورة الرئيسية
if (document.getElementById('removeImage')) {
    document.getElementById('removeImage').addEventListener('click', function() {
        const removeImageInput = document.createElement('input');
        removeImageInput.type = 'hidden';
        removeImageInput.name = 'remove_image';
        removeImageInput.value = '1';
        document.getElementById('productForm').appendChild(removeImageInput);
        
        imagePreview.innerHTML = `
            <div class="bg-light p-5 text-muted rounded mb-3">
                <i class="fas fa-image fa-3x mb-2"></i>
                <p class="mb-0">لا توجد صورة</p>
            </div>
        `;
        this.remove();
    });
}

// إضافة صور للمعرض
document.getElementById('addGalleryImage').addEventListener('click', function() {
    document.getElementById('gallery').click();
});

document.getElementById('gallery').addEventListener('change', function(e) {
    const files = e.target.files;
    const galleryPreview = document.getElementById('galleryPreview');
    
    // إزالة رسالة "لا توجد صور" إذا كانت موجودة
    const emptyMessage = galleryPreview.querySelector('.text-muted');
    if (emptyMessage) {
        emptyMessage.remove();
    }
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 mb-3 gallery-item';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded border" alt="صورة المعرض">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 start-0 m-1 remove-gallery-image">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                galleryPreview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    }
});

// حذف صورة من المعرض
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-gallery-image')) {
        const galleryItem = e.target.closest('.gallery-item');
        galleryItem.remove();
        
        // إظهار رسالة "لا توجد صور" إذا لم يعد هناك صور
        const galleryPreview = document.getElementById('galleryPreview');
        if (galleryPreview.querySelectorAll('.gallery-item').length === 0) {
            galleryPreview.innerHTML = `
                <div class="col-12 text-center text-muted py-4">
                    <i class="fas fa-images fa-3x mb-2"></i>
                    <p class="mb-0">لا توجد صور في المعرض</p>
                </div>
            `;
        }
    }
    
    // حذف مستند
    if (e.target.closest('.remove-document')) {
        const row = e.target.closest('tr');
        row.remove();
        
        // إظهار رسالة "لا توجد مستندات" إذا لم يعد هناك مستندات
        const tbody = document.querySelector('#documentsTable tbody');
        if (tbody.querySelectorAll('tr').length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">لا توجد مستندات مرفقة</td>
                </tr>
            `;
        }
    }
});

// إضافة مستندات
document.getElementById('addDocument').addEventListener('click', function() {
    document.getElementById('documents').click();
});

document.getElementById('documents').addEventListener('change', function(e) {
    const files = e.target.files;
    const tbody = document.querySelector('#documentsTable tbody');
    
    // إزالة رسالة "لا توجد مستندات" إذا كانت موجودة
    const emptyRow = tbody.querySelector('tr:only-child td[colspan="5"]');
    if (emptyRow) {
        tbody.innerHTML = '';
    }
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const row = document.createElement('tr');
        
        // إنشاء معرف فريد للمستند الجديد
        const docId = 'new_doc_' + Date.now() + i;
        
        row.innerHTML = `
            <td>
                <i class="fas ${getFileIcon(file.type)} me-2"></i>
                ${file.name}
            </td>
            <td>${file.name.split('.').pop().toUpperCase()}</td>
            <td>${formatFileSize(file.size)}</td>
            <td>${new Date().toISOString().split('T')[0]}</td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-primary" disabled>
                    <i class="fas fa-download"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger remove-document">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    }
});

// دالة مساعدة للحصول على أيقونة الملف
function getFileIcon(mimeType) {
    const mimeTypes = {
        'application/pdf': 'fa-file-pdf text-danger',
        'application/msword': 'fa-file-word text-primary',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'fa-file-word text-primary',
        'application/vnd.ms-excel': 'fa-file-excel text-success',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'fa-file-excel text-success',
        'application/vnd.ms-powerpoint': 'fa-file-powerpoint text-warning',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'fa-file-powerpoint text-warning',
        'text/plain': 'fa-file-alt',
        'text/csv': 'fa-file-csv text-info',
        'application/zip': 'fa-file-archive text-muted',
        'application/x-rar-compressed': 'fa-file-archive text-muted',
        'application/x-7z-compressed': 'fa-file-archive text-muted'
    };
    
    return mimeTypes[mimeType] || 'fa-file';
}

// دالة مساعدة لتنسيق حجم الملف
function formatFileSize(bytes) {
    if (bytes === 0) return '0 بايت';
    
    const k = 1024;
    const sizes = ['بايت', 'كيلوبايت', 'ميجابايت', 'جيجابايت', 'تيرابايت'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush
