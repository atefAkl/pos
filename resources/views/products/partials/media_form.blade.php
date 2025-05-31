<style>
    /* تنسيقات عرض الملفات - تصميم Google Drive */
    .file-card {
        transition: all 0.2s ease;
        border-radius: 0;
        overflow: hidden;
        box-shadow: none;
        border: 1px solid #e0e0e0;
        margin-bottom: 15px;
        position: relative;
        height: 235px;
        /* Increased height */
    }

    .file-card:hover {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        border-color: #ccc;
    }

    .file-card.selected {
        background-color: #e8f0fe;
        border-color: #c6dafc;
    }

    .file-preview {
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 0;
    }

    .file-preview img {
        max-height: 100%;
        max-width: 100%;
        object-fit: cover;
        width: 100%;
        height: 100%;
    }

    .file-icon-container {
        height: 100%;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .file-extension {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 35px;
        background-color: rgba(0, 0, 0, 0.6);
        color: white;
        text-align: center;
        padding: 2px 5px;
        font-size: 10px;
        font-weight: bold;
    }

    .card-footer {
        padding: 8px 10px !important;
        background-color: white !important;
        border-top: none !important;
        height: 75px;
        /* Increased height */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        /* Changed for better spacing */
    }

    .card-footer .small {
        font-size: 13px;
        color: #333;
        margin-bottom: 3px;
        white-space: normal;
        /* Allow wrapping */
        word-break: break-all;
        /* Break long words if necessary */
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 2 lines */
        line-clamp: 2;
        /* Standard property */
        -webkit-box-orient: vertical;
        line-height: 1.3em;
        /* Adjust line height */
        max-height: 2.6em;
        /* Max height for 2 lines (2 * 1.3em) */
    }

    .file-meta {
        font-size: 11px;
        color: #777;
    }

    .file-actions {
        position: absolute;
        top: 5px;
        right: 5px;
        display: none;
    }

    .file-card:hover .file-actions {
        display: block;
    }

    .file-actions .btn {
        padding: 2px 5px;
        font-size: 12px;
        margin-left: 2px;
        background-color: rgba(255, 255, 255, 0.9);
        border-color: rgba(0, 0, 0, 0.1);
    }

    /* تنسيقات أزرار التبديل بين طرق العرض */
    #gridViewBtn.active,
    #listViewBtn.active {
        background-color: #0d6efd;
        color: white;
    }

    /* تنسيقات أيقونات الملفات */
    .file-type-icon {
        font-size: 40px;
    }

    .file-type-doc {
        color: #4285F4;
    }

    .file-type-sheet {
        color: #0F9D58;
    }

    .file-type-slide {
        color: #F4B400;
    }

    .file-type-pdf {
        color: #DB4437;
    }

    .file-type-image {
        color: #8e44ad;
    }

    .file-type-video {
        color: #e74c3c;
    }

    .file-type-audio {
        color: #3498db;
    }

    .file-type-archive {
        color: #7f8c8d;
    }

    .file-type-code {
        color: #2c3e50;
    }
</style>

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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-images"></i> مستعرض ملفات المنتج: <span class="text-primary">{{ $product->name ?? 'المنتج الحالي' }}</span></h6>
                <!-- أزرار تبديل طريقة العرض -->
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active" id="gridViewBtn" title="عرض كصور مصغرة">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="listViewBtn" title="عرض كقائمة">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- أزرار التصفية -->
                <div class="d-flex justify-content-between mb-3">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" data-filter="all">{{ __('file_categories.all') }}</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-filter="product_image">{{ __('file_categories.product_image') }}</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-filter="gallery_image">{{ __('file_categories.gallery_image') }}</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-filter="barcode">{{ __('file_categories.barcode') }}</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-filter="document">{{ __('file_categories.document') }}</button>
                    </div>
                </div>

                <!-- مستعرض الملفات -->
                <div id="filesContainer">
                    <div class="row" id="filesList">
                        @forelse($product->productFiles()->with('file')->orderBy('the_order')->get() as $productFile)
                        <div class="col-md-2 col-sm-3 col-4 mb-3 file-item" id="grid-file-{{ $productFile->id }}" data-category="{{ $productFile->category }}">
                            <div class="card file-card">
                                <!-- أزرار الإجراءات التي تظهر عند تمرير المؤشر -->
                                <div class="file-actions p-3">
                                    <div class="btn-group">
                                        <a href="{{ asset('storage/' . $productFile->file->path) }}" class="btn btn-sm btn-light" target="_blank" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" data-id="{{ $productFile->id }}" class="btn btn-sm btn-light" onclick="deleteFile(this.getAttribute('data-id'))" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <div class="form-check form-switch d-inline-block ms-1">
                                            <input class="form-check-input file-active-toggle" type="checkbox"
                                                data-file-id="{{ $productFile->file_id }}"
                                                data-product-file-id="{{ $productFile->id }}"
                                                {{ $productFile->is_active ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <!-- معاينة الملف -->
                                <div class="card-img-top file-preview">
                                    @if($productFile->file) {{-- Check if file record exists --}}
                                    @if($productFile->file->isImage())
                                    <img src="{{ $productFile->file->url }}" alt="{{ $productFile->file->alt_text ?? $productFile->file->display_name }}">
                                    @else
                                    <div class="file-icon-container d-flex align-items-center justify-content-center">
                                        <i class="{{ $productFile->file->icon }} file-type-icon {{ $productFile->file->icon_color_class }}"></i>
                                    </div>
                                    <div class="file-extension">{{ strtoupper($productFile->file->extension) }}</div>
                                    @endif
                                    @else
                                    {{-- Fallback for MISSING FILE RECORD --}}
                                    <div class="file-icon-container d-flex align-items-center justify-content-center flex-column text-center p-2">
                                        <i class="fas fa-unlink file-type-icon text-danger" style="font-size: 2rem;"></i>
                                        <small class="text-danger mt-1">File record missing</small>
                                        @if($productFile->display_name)
                                        <small class="text-muted mt-1" style="font-size: 0.75rem; word-break: break-all;">Expected: {{ Str::limit($productFile->display_name, 30) }}</small>
                                        @endif
                                    </div>
                                    @endif
                                </div>

                                <!-- معلومات الملف -->
                                <div class="card-footer py-2">
                                    <div class="small">{{ $productFile->file->display_name ?? $productFile->file->original_name ?? $productFile->display_name ?? 'N/A' }}</div>
                                    <div class="file-meta d-flex justify-content-between">
                                        <span>{{ $productFile->file->formatted_size ?? '-' }}</span>
                                        <span class="badge bg-{{ $productFile->category == 'product_image' ? 'primary' : ($productFile->category == 'gallery_image' ? 'info' : ($productFile->category == 'barcode' ? 'warning' : ($productFile->category == 'document' ? 'success' : 'secondary'))) }} badge-sm">{{ $productFile->category ? __('file_categories.'.$productFile->category) : 'غير مصنف' }}</span>
                                    </div>
                                    <div class="file-actions-footer mt-2 pt-2 border-top">
                                        <div class="btn-group btn-group-sm w-100" role="group" aria-label="File actions">
                                            <a href="{{ $productFile->file ? $productFile->file->url : '#' }}"
                                                class="btn btn-outline-secondary {{ $productFile->file ? '' : 'disabled' }}"
                                                target="_blank"
                                                title="عرض الملف">
                                                <i class="fas fa-eye"></i> {{-- عرض --}}
                                            </a>
                                            <button type="button"
                                                class="btn btn-outline-secondary"
                                                data-file-id="{{ $productFile->file_id ?? 0 }}"
                                                data-display-name="{{ $productFile->file->display_name ?? '' }}"
                                                data-alt-text="{{ $productFile->file->alt_text ?? '' }}"
                                                onclick="handleEditFileClick(this)"
                                                title="تعديل بيانات الملف"
                                                {{ $productFile->file ? '' : 'disabled' }}>
                                                <i class="fas fa-edit"></i> {{-- تعديل --}}
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-danger"
                                                data-file-id="{{ $productFile->file_id ?? 0 }}"
                                                onclick="deleteFile(this.getAttribute('data-file-id'))"
                                                title="حذف ارتباط الملف بالمنتج"
                                                {{ $productFile->file ? '' : 'disabled' }}>
                                                <i class="fas fa-trash"></i> {{-- حذف --}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5" id="noFilesGrid">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p>لا توجد ملفات مرتبطة بالمنتج تم رفعها حتى الآن</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- عرض القائمة -->
                <div id="listView" class="view-mode" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="40">#</th>
                                    <th width="50">النوع</th>
                                    <th>الاسم</th>
                                    <th>الفئة</th>
                                    <th>الحجم</th>
                                    <th>الحالة</th>
                                    <th width="120">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="filesList">
                                @forelse($product->productFiles()->with('file')->orderBy('the_order')->get() as $index => $productFile)
                                <tr class="file-item" id="list-file-{{ $productFile->id }}" data-category="{{ $productFile->category }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <i class="{{ $productFile->file->icon ?? 'fas fa-file' }} fa-lg"></i>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;">
                                            {{ $productFile->file->display_name ?? $productFile->file->original_name }}
                                        </div>
                                        <small class="text-muted">{{ $productFile->file->original_name }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $productFile->category ? __('file_categories.'.$productFile->category) : 'غير مصنف' }}</span>
                                    </td>
                                    <td>{{ $productFile->file->formatted_size ?? '-' }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input file-active-toggle" type="checkbox"
                                                data-file-id="{{ $productFile->file_id }}"
                                                data-product-file-id="{{ $productFile->id }}"
                                                {{ $productFile->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ asset('storage/' . $productFile->file->path) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" data-id="{{ $productFile->id }}" class="btn btn-sm btn-outline-danger" onclick="deleteFile(this.getAttribute('data-id'))">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4" id="noFilesList">
                                        <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                                        <p>لا توجد ملفات مرتبطة بالمنتج حالياً</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    // جعل حقل الباركود دائماً في حالة focus
    window.onload = function() {
        const barcodeInput = document.getElementById('barcode-search');
        if (barcodeInput) barcodeInput.focus();
    };
    const barcodeInput = document.getElementById('barcode-search');
    if (barcodeInput) {
        barcodeInput.addEventListener('blur', function() {
            setTimeout(() => this.focus(), 100);
        });
        // تنفيذ البحث تلقائياً عند الضغط Enter
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const barcode = this.value.trim();
                if (barcode) {
                    // إعادة تحميل الصفحة مع الباركود كـ query string
                    window.location.href = `?barcode=${encodeURIComponent(barcode)}`;
                }
            }
        });
    }
</script>
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

    // حذف ملف من المنتج
    function deleteFile(productFileId) {
        if (confirm('هل أنت متأكد من حذف هذا الملف؟')) {
            fetch(`/product-files/${productFileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // إزالة الملف من العرض
                        document.getElementById(`grid-file-${productFileId}`)?.remove();
                        document.getElementById(`list-file-${productFileId}`)?.remove();

                        // إظهار رسالة نجاح
                        alert('تم حذف الملف بنجاح');
                    } else {
                        alert(data.message || 'حدث خطأ أثناء حذف الملف');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء حذف الملف');
                });
        }
    }

    // تبديل حالة تنشيط الملف
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.file-active-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const productFileId = this.getAttribute('data-product-file-id');
                const isActive = this.checked;

                fetch('/product-files/toggle-active', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_file_id: productFileId,
                            is_active: isActive
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert(data.message || 'حدث خطأ أثناء تحديث حالة الملف');
                            // إعادة الحالة إلى ما كانت عليه
                            this.checked = !isActive;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('حدث خطأ أثناء تحديث حالة الملف');
                        // إعادة الحالة إلى ما كانت عليه
                        this.checked = !isActive;
                    });
            });
        });

        // تبديل طريقة العرض بين القائمة والصور المصغرة
        const gridViewBtn = document.getElementById('gridViewBtn');
        const listViewBtn = document.getElementById('listViewBtn');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');

        // زر عرض الشبكة
        gridViewBtn.addEventListener('click', function() {
            gridView.style.display = 'block';
            listView.style.display = 'none';

            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');

            // حفظ تفضيل العرض في localStorage
            localStorage.setItem('filesViewMode', 'grid');
        });

        // زر عرض القائمة
        listViewBtn.addEventListener('click', function() {
            gridView.style.display = 'none';
            listView.style.display = 'block';

            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');

            // حفظ تفضيل العرض في localStorage
            localStorage.setItem('filesViewMode', 'list');
        });

        // استعادة تفضيل العرض المحفوظ
        const savedViewMode = localStorage.getItem('filesViewMode');
        if (savedViewMode === 'list') {
            listViewBtn.click();
        } else {
            gridViewBtn.click();
        }

        // تصفية الملفات حسب الفئة
        const filterButtons = document.querySelectorAll('[data-filter]');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');

                // إزالة الفئة النشطة من جميع الأزرار
                filterButtons.forEach(btn => {
                    btn.classList.remove('btn-primary', 'active');
                    btn.classList.add('btn-outline-primary');
                });

                // إضافة الفئة النشطة للزر المحدد
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary', 'active');

                // تصفية الملفات في كلا العرضين
                const fileItems = document.querySelectorAll('.file-item');
                let visibleCount = 0;

                fileItems.forEach(item => {
                    const category = item.getAttribute('data-category');

                    if (filter === 'all' || category === filter) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // عرض رسالة إذا لم يتم العثور على ملفات
                const noFilesGrid = document.getElementById('noFilesGrid');
                const noFilesList = document.getElementById('noFilesList')?.parentNode;

                if (visibleCount === 0) {
                    if (noFilesGrid) {
                        noFilesGrid.style.display = 'block';
                        noFilesGrid.querySelector('p').textContent = `لا توجد ملفات في فئة ${getCategoryName(filter)}`;
                    }

                    if (noFilesList) {
                        noFilesList.style.display = 'table-row';
                        const messageCell = noFilesList.querySelector('td');
                        if (messageCell) {
                            messageCell.querySelector('p').textContent = `لا توجد ملفات في فئة ${getCategoryName(filter)}`;
                        }
                    }
                } else {
                    if (noFilesGrid) noFilesGrid.style.display = 'none';
                    if (noFilesList) noFilesList.style.display = 'none';
                }
            });
        });
    });

    // الحصول على اسم الفئة بالعربية
    function getCategoryName(category) {
        const categories = {
            'product_image': 'صورة المنتج',
            'gallery_image': 'معرض الصور',
            'barcode': 'باركود',
            'document': 'مستندات',
            'other': 'أخرى',
            'all': 'الكل'
        };

        return categories[category] || category;
    }
</script>

@endpush