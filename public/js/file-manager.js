/**
 * نظام إدارة الملفات للمنتجات
 */

/**
 * إظهار رسالة تنبيه
 */
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    // إخفاء التنبيه بعد 5 ثواني
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

/**
 * إنشاء حاوية للتنبيهات
 */
function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alertContainer';
    container.className = 'position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1050';
    document.body.appendChild(container);
    return container;
}

/**
 * الحصول على اسم الفئة باللغة العربية
 */
function getCategoryName(category) {
    const categories = {
        'product_image': 'صورة المنتج',
        'gallery_image': 'معرض الصور',
        'barcode': 'باركود',
        'document': 'مستند',
        'other': 'أخرى'
    };
    
    return categories[category] || category;
}

/**
 * الحصول على أيقونة الفئة
 */
function getCategoryIcon(category) {
    const icons = {
        'product_image': 'fas fa-image',
        'gallery_image': 'fas fa-images',
        'barcode': 'fas fa-barcode',
        'document': 'fas fa-file-alt',
        'other': 'fas fa-file'
    };
    
    return icons[category] || 'fas fa-file';
}

/**
 * تنسيق حجم الملف بصيغة مقروءة
 */
function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

/**
 * الحصول على أيقونة الملف حسب نوعه
 */
function getFileIcon(mimeType, extension) {
    extension = extension.toLowerCase();
    
    // أيقونات حسب نوع MIME
    if (mimeType.startsWith('image/')) {
        return 'fas fa-file-image';
    } else if (mimeType.startsWith('video/')) {
        return 'fas fa-file-video';
    } else if (mimeType.startsWith('audio/')) {
        return 'fas fa-file-audio';
    } else if (mimeType.startsWith('text/')) {
        return 'fas fa-file-alt';
    } else if (mimeType === 'application/pdf') {
        return 'fas fa-file-pdf';
    }
    
    // أيقونات حسب الامتداد
    const iconsByExtension = {
        'pdf': 'fas fa-file-pdf',
        'doc': 'fas fa-file-word',
        'docx': 'fas fa-file-word',
        'xls': 'fas fa-file-excel',
        'xlsx': 'fas fa-file-excel',
        'ppt': 'fas fa-file-powerpoint',
        'pptx': 'fas fa-file-powerpoint',
        'zip': 'fas fa-file-archive',
        'rar': 'fas fa-file-archive',
        '7z': 'fas fa-file-archive',
        'txt': 'fas fa-file-alt',
        'html': 'fas fa-file-code',
        'css': 'fas fa-file-code',
        'js': 'fas fa-file-code',
        'json': 'fas fa-file-code',
        'xml': 'fas fa-file-code',
        'csv': 'fas fa-file-csv',
    };
    
    return iconsByExtension[extension] || 'fas fa-file';
}

/**
 * تحميل ملفات المنتج من الخادم
 */
function loadProductFiles(productId) {
    // عرض رسالة التحميل
    const filesList = document.getElementById('filesList');
    filesList.innerHTML = '<div class="col-12 text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i><p>جاري تحميل الملفات...</p></div>';
    
    // إرسال طلب للحصول على الملفات
    fetch(`/product-files/${productId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث قائمة الملفات
            updateFilesList(data.files);
            
            // تفعيل زر التصفية الافتراضي (الكل)
            document.querySelector('[data-filter="all"]').click();
        } else {
            // إظهار رسالة خطأ
            filesList.innerHTML = `<div class="col-12 text-center py-5"><i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i><p>${data.message || 'حدث خطأ أثناء تحميل الملفات'}</p></div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        filesList.innerHTML = '<div class="col-12 text-center py-5"><i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i><p>حدث خطأ أثناء تحميل الملفات</p></div>';
    });
}

// عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // التحقق من وجود نموذج رفع الملفات
    const fileUploadForm = document.getElementById('fileUploadForm');
    if (!fileUploadForm) return;
    
    // تحميل الملفات الحالية للمنتج
    const productId = document.querySelector('input[name="related_id"]').value;
    loadProductFiles(productId);
    
    // معالجة رفع الملفات
    fileUploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const uploadBtn = document.getElementById('uploadFileBtn');
        
        // تغيير حالة الزر أثناء الرفع
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الرفع...';
        
        // إرسال الطلب
        fetch('/product-files/upload', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // إعادة تفعيل الزر
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> رفع الملف';
            
            if (data.success) {
                // إعادة تحميل الملفات
                loadProductFiles(productId);
                
                // إظهار رسالة نجاح
                showAlert('success', data.message || 'تم رفع الملف بنجاح');
                
                // إعادة تعيين النموذج
                fileUploadForm.reset();
                document.getElementById('filePreview').style.display = 'none';
            } else {
                // إظهار رسالة خطأ
                showAlert('danger', data.message || 'حدث خطأ أثناء رفع الملف');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> رفع الملف';
            showAlert('danger', 'حدث خطأ أثناء رفع الملف');
        });
    });
    
    // معالجة أزرار تصفية الملفات
    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // إزالة الفئة النشطة من جميع الأزرار
            filterButtons.forEach(btn => btn.classList.remove('btn-primary', 'active'));
            filterButtons.forEach(btn => btn.classList.add('btn-outline-primary'));
            
            // إضافة الفئة النشطة للزر المحدد
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary', 'active');
            
            // تصفية الملفات
            filterFiles(filter);
        });
    });
});
        .then(response => response.json())
        .then(data => {
            // إعادة تفعيل الزر
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> رفع الملف';
            
            if (data.success) {
                // إعادة تحميل الملفات
                loadProductFiles(productId);
                
                // إظهار رسالة نجاح
                showAlert('success', data.message);
                
                // إعادة تعيين النموذج
                fileUploadForm.reset();
                document.getElementById('filePreview').style.display = 'none';
            } else {
                // إظهار رسالة خطأ
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> رفع الملف';
            showAlert('danger', 'حدث خطأ أثناء رفع الملف');
        });
    });
    
    // معالجة أزرار تصفية الملفات
    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // إزالة الفئة النشطة من جميع الأزرار
            filterButtons.forEach(btn => btn.classList.remove('btn-primary', 'active'));
            filterButtons.forEach(btn => btn.classList.add('btn-outline-primary'));
            
            // إضافة الفئة النشطة للزر المحدد
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary', 'active');
            
            // تصفية الملفات
            filterFiles(filter);
        });
    });
});

/**
 * تحميل ملفات المنتج من الخادم
 */
function loadProductFiles(productId) {
    // عرض رسالة التحميل
    const filesList = document.getElementById('filesList');
    filesList.innerHTML = '<div class="col-12 text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i><p>جاري تحميل الملفات...</p></div>';
    
    // إرسال طلب للحصول على الملفات
    fetch(`/product-files/${productId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث قائمة الملفات
            updateFilesList(data.files);
            
            // تفعيل زر التصفية الافتراضي (الكل)
            document.querySelector('[data-filter="all"]').click();
        } else {
            // إظهار رسالة خطأ
            filesList.innerHTML = `<div class="col-12 text-center py-5"><i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i><p>${data.message || 'حدث خطأ أثناء تحميل الملفات'}</p></div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        filesList.innerHTML = '<div class="col-12 text-center py-5"><i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i><p>حدث خطأ أثناء تحميل الملفات</p></div>';
    });
}

/**
 * تحديث قائمة الملفات في واجهة المستخدم
 */
function updateFilesList(files) {
    const filesList = document.getElementById('filesList');
    const noFilesMessage = document.getElementById('noFilesMessage');
    
    // التحقق من وجود ملفات
    if (!files || files.length === 0) {
        filesList.innerHTML = '';
        if (noFilesMessage) {
            noFilesMessage.style.display = 'block';
        } else {
            filesList.innerHTML = '<div class="col-12 text-center py-5" id="noFilesMessage"><i class="fas fa-folder-open fa-3x text-muted mb-3"></i><p>لا توجد ملفات مرتبطة بالمنتج حالياً</p></div>';
        }
        return;
    }
    
    // إخفاء رسالة عدم وجود ملفات
    if (noFilesMessage) {
        noFilesMessage.style.display = 'none';
    }
    
    // إنشاء بطاقات الملفات
    filesList.innerHTML = '';
    files.forEach(productFile => {
        const file = productFile.file;
        
        // إضافة URL للملف
        file.url = `/storage/${file.path}`;
        
        // إضافة حجم الملف بصيغة مقروءة
        file.formatted_size = formatFileSize(file.size);
        
        // إضافة أيقونة الملف حسب نوعه
        file.icon = getFileIcon(file.mime_type, file.extension);
        
        // إنشاء بطاقة الملف
        const fileCard = createFileCard(file, productFile);
        filesList.appendChild(fileCard);
    });
}

/**
 * تصفية الملفات حسب الفئة
 */
function filterFiles(filter) {
    const fileCards = document.querySelectorAll('#filesList > div');
    
    fileCards.forEach(card => {
        const category = card.getAttribute('data-category');
        
        if (filter === 'all' || category === filter) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
    
    // عرض رسالة إذا لم يتم العثور على ملفات
    const visibleCards = Array.from(fileCards).filter(card => card.style.display !== 'none');
    const noFilesMessage = document.getElementById('noFilesMessage');
    
    if (visibleCards.length === 0) {
        if (!noFilesMessage) {
            const message = document.createElement('div');
            message.className = 'col-12 text-center py-5';
            message.id = 'noFilesMessage';
            message.innerHTML = `<i class="fas fa-folder-open fa-3x text-muted mb-3"></i><p>لا توجد ملفات في فئة ${getCategoryName(filter)}</p>`;
            document.getElementById('filesList').appendChild(message);
        } else {
            noFilesMessage.style.display = 'block';
            noFilesMessage.querySelector('p').textContent = `لا توجد ملفات في فئة ${getCategoryName(filter)}`;
        }
    } else if (noFilesMessage) {
        noFilesMessage.style.display = 'none';
    }
}

/**
 * تنسيق حجم الملف بصيغة مقروءة
 */
function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

/**
 * الحصول على أيقونة الملف حسب نوعه
 */
function getFileIcon(mimeType, extension) {
    extension = extension.toLowerCase();
    
    // أيقونات حسب نوع MIME
    if (mimeType.startsWith('image/')) {
        return 'fas fa-file-image';
    } else if (mimeType.startsWith('video/')) {
        return 'fas fa-file-video';
    } else if (mimeType.startsWith('audio/')) {
        return 'fas fa-file-audio';
    } else if (mimeType.startsWith('text/')) {
        return 'fas fa-file-alt';
    } else if (mimeType === 'application/pdf') {
        return 'fas fa-file-pdf';
    }
    
    // أيقونات حسب الامتداد
    const iconsByExtension = {
        'pdf': 'fas fa-file-pdf',
        'doc': 'fas fa-file-word',
        'docx': 'fas fa-file-word',
        'xls': 'fas fa-file-excel',
        'xlsx': 'fas fa-file-excel',
        'ppt': 'fas fa-file-powerpoint',
        'pptx': 'fas fa-file-powerpoint',
        'zip': 'fas fa-file-archive',
        'rar': 'fas fa-file-archive',
        '7z': 'fas fa-file-archive',
        'txt': 'fas fa-file-alt',
        'html': 'fas fa-file-code',
        'css': 'fas fa-file-code',
        'js': 'fas fa-file-code',
        'json': 'fas fa-file-code',
        'xml': 'fas fa-file-code',
        'csv': 'fas fa-file-csv',
    };
    
    return iconsByExtension[extension] || 'fas fa-file';
}
                // إظهار رسالة نجاح
                showAlert('success', 'تم رفع الملف بنجاح');
                
                // إعادة تعيين النموذج
                fileUploadForm.reset();
            } else {
                showAlert('error', data.message || 'حدث خطأ أثناء رفع الملف');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'حدث خطأ أثناء رفع الملف');
        })
        .finally(() => {
            // إعادة تفعيل الزر
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> رفع الملف';
        });
    });
    
    // إضافة معالجات أحداث لأزرار التصفية
    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // إزالة الفئة النشطة من جميع الأزرار
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // إضافة الفئة النشطة للزر المحدد
            this.classList.add('active');
            
            // تصفية الملفات حسب الفئة
            const filter = this.getAttribute('data-filter');
            filterFiles(filter);
        });
    });
});

/**
 * تحميل ملفات المنتج
 */
function loadProductFiles(productId) {
    fetch(`/api/products/${productId}/files`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // عرض جميع الملفات في مستعرض الملفات الموحد
                renderFiles(data.files);
            }
        })
        .catch(error => {
            console.error('Error loading product files:', error);
        });
}

/**
 * عرض جميع الملفات في مستعرض الملفات
 */
function renderFiles(files) {
    const container = document.getElementById('filesList');
    const noFilesMessage = document.getElementById('noFilesMessage');
    
    if (!container) return;
    
    if (files.length === 0) {
        if (noFilesMessage) noFilesMessage.style.display = 'block';
        container.innerHTML = '';
        return;
    }
    
    if (noFilesMessage) noFilesMessage.style.display = 'none';
    container.innerHTML = '';
    
    // إضافة سمة data-category لكل ملف لتسهيل التصفية
    files.forEach(fileData => {
        const file = fileData.file;
        const fileCard = createFileCard(file, fileData);
        fileCard.setAttribute('data-category', fileData.category);
        container.appendChild(fileCard);
    });
}

/**
 * تصفية الملفات حسب الفئة
 */
function filterFiles(category) {
    const allFileCards = document.querySelectorAll('#filesList > div');
    
    allFileCards.forEach(card => {
        if (category === 'all' || card.getAttribute('data-category') === category) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    // إظهار رسالة عدم وجود ملفات إذا لم يكن هناك ملفات مطابقة
    const visibleCards = document.querySelectorAll('#filesList > div[style="display: block"]');
    const noFilesMessage = document.getElementById('noFilesMessage');
    
    if (visibleCards.length === 0 && noFilesMessage) {
        noFilesMessage.style.display = 'block';
        if (category !== 'all') {
            noFilesMessage.querySelector('p').textContent = `لا توجد ملفات من فئة "${getCategoryName(category)}" حالياً`;
        } else {
            noFilesMessage.querySelector('p').textContent = 'لا توجد ملفات مرتبطة بالمنتج حالياً';
        }
    } else if (noFilesMessage) {
        noFilesMessage.style.display = 'none';
    }
}

/**
 * الحصول على اسم الفئة بالعربية
 */
function getCategoryName(category) {
    const categories = {
        'product_image': 'صورة المنتج',
        'gallery_image': 'معرض الصور',
        'barcode': 'باركود',
        'document': 'مستند',
        'other': 'أخرى'
    };
    
    return categories[category] || category;
}

/**
 * إنشاء بطاقة عرض للملف
 */
function createFileCard(file, productFile) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4 mb-3';
    
    let cardContent = '';
    
    // نوع العرض حسب نوع الملف
    if (file.mime_type.startsWith('image/')) {
        cardContent = `
            <div class="card h-100 ${productFile.is_active ? 'border-success' : ''}">
                <div class="card-header d-flex justify-content-between align-items-center ${productFile.is_active ? 'bg-success text-white' : ''}">
                    <span><i class="${productFile.category_icon}"></i> ${file.display_name || file.name}</span>
                    <span class="badge bg-secondary">${getCategoryName(productFile.category)}</span>
                </div>
                <div class="card-img-top text-center p-2">
                    <img src="${file.url}" class="img-fluid" style="max-height: 150px;" alt="${file.alt_text}">
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input file-active-toggle" type="checkbox" 
                                data-file-id="${file.id}" 
                                data-product-file-id="${productFile.id}" 
                                ${productFile.is_active ? 'checked' : ''}>
                            <label class="form-check-label small">نشط</label>
                        </div>
                        <small class="text-muted">${file.formatted_size}</small>
                    </div>
                    <div class="btn-group w-100">
                        <a href="${file.url}" class="btn btn-sm btn-outline-info" target="_blank">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        <button class="btn btn-sm btn-outline-primary replace-file-btn" data-file-id="${file.id}">
                            <i class="fas fa-sync-alt"></i> استبدال
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-file-btn" data-file-id="${file.id}">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </div>
                </div>
            </div>
        `;
    } else {
        cardContent = `
            <div class="card h-100 ${productFile.is_active ? 'border-success' : ''}">
                <div class="card-header d-flex justify-content-between align-items-center ${productFile.is_active ? 'bg-success text-white' : ''}">
                    <span><i class="${productFile.category_icon}"></i> ${file.display_name || file.name}</span>
                    <span class="badge bg-secondary">${getCategoryName(productFile.category)}</span>
                </div>
                <div class="card-body text-center">
                    <i class="${file.icon} fa-3x mb-2"></i>
                    <p class="mb-1">${file.name}</p>
                    <p class="text-muted small">${file.formatted_size}</p>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input file-active-toggle" type="checkbox" 
                                data-file-id="${file.id}" 
                                data-product-file-id="${productFile.id}" 
                                ${productFile.is_active ? 'checked' : ''}>
                            <label class="form-check-label small">نشط</label>
                        </div>
                    </div>
                    <div class="btn-group w-100">
                        <a href="${file.url}" class="btn btn-sm btn-outline-info" target="_blank">
                            <i class="fas fa-download"></i> تنزيل
                        </a>
                        <button class="btn btn-sm btn-outline-primary replace-file-btn" data-file-id="${file.id}">
                            <i class="fas fa-sync-alt"></i> استبدال
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-file-btn" data-file-id="${file.id}">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    col.innerHTML = cardContent;
    
    // إضافة معالجات الأحداث
    addEventListeners(col);
    
    return col;
}

/**
 * إضافة معالجات الأحداث للبطاقة
 */
function addEventListeners(cardElement) {
    // تبديل حالة النشاط
    const toggleSwitch = cardElement.querySelector('.file-active-toggle');
    if (toggleSwitch) {
        toggleSwitch.addEventListener('change', function() {
            const fileId = this.dataset.fileId;
            const productFileId = this.dataset.productFileId;
            const isActive = this.checked;
            
            updateFileStatus(fileId, productFileId, isActive);
        });
    }
    
    // حذف الملف
    const deleteBtn = cardElement.querySelector('.delete-file-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const fileId = this.dataset.fileId;
            
            if (confirm('هل أنت متأكد من حذف هذا الملف؟')) {
                deleteFile(fileId);
            }
        });
    }
    
    // استبدال الملف
    const replaceBtn = cardElement.querySelector('.replace-file-btn');
    if (replaceBtn) {
        replaceBtn.addEventListener('click', function() {
            const fileId = this.dataset.fileId;
            showReplaceFileModal(fileId);
        });
    }
}

/**
 * تحديث حالة الملف (نشط/غير نشط)
 */
function updateFileStatus(fileId, productFileId, isActive) {
    fetch(`/files/${fileId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            is_active: isActive,
            product_file_id: productFileId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // إعادة تحميل ملفات المنتج
            const productId = document.querySelector('input[name="related_id"]').value;
            loadProductFiles(productId);
            
            showAlert('success', 'تم تحديث حالة الملف بنجاح');
        } else {
            showAlert('error', data.message || 'حدث خطأ أثناء تحديث حالة الملف');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'حدث خطأ أثناء تحديث حالة الملف');
    });
}

/**
 * حذف ملف
 */
function deleteFile(fileId) {
    fetch(`/files/${fileId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // إعادة تحميل ملفات المنتج
            const productId = document.querySelector('input[name="related_id"]').value;
            loadProductFiles(productId);
            
            showAlert('success', 'تم حذف الملف بنجاح');
        } else {
            showAlert('error', data.message || 'حدث خطأ أثناء حذف الملف');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'حدث خطأ أثناء حذف الملف');
    });
}

/**
 * عرض نافذة استبدال الملف
 */
function showReplaceFileModal(fileId) {
    // إنشاء نموذج مؤقت
    const form = document.createElement('form');
    form.enctype = 'multipart/form-data';
    form.innerHTML = `
        <div class="mb-3">
            <label for="replacement-file" class="form-label">اختر ملفاً للاستبدال</label>
            <input type="file" class="form-control" id="replacement-file" name="file" required>
        </div>
        <div class="mb-3">
            <label for="replacement-display-name" class="form-label">اسم العرض (اختياري)</label>
            <input type="text" class="form-control" id="replacement-display-name" name="display_name">
        </div>
        <div class="mb-3">
            <label for="replacement-alt-text" class="form-label">النص البديل (اختياري)</label>
            <input type="text" class="form-control" id="replacement-alt-text" name="alt_text">
        </div>
    `;
    
    // إنشاء نافذة منبثقة
    Swal.fire({
        title: 'استبدال الملف',
        html: form,
        showCancelButton: true,
        confirmButtonText: 'استبدال',
        cancelButtonText: 'إلغاء',
        preConfirm: () => {
            const fileInput = document.getElementById('replacement-file');
            const displayName = document.getElementById('replacement-display-name').value;
            const altText = document.getElementById('replacement-alt-text').value;
            
            if (!fileInput.files[0]) {
                Swal.showValidationMessage('يرجى اختيار ملف');
                return false;
            }
            
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('display_name', displayName);
            formData.append('alt_text', altText);
            formData.append('_method', 'POST');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            return replaceFile(fileId, formData);
        }
    });
}

/**
 * استبدال ملف
 */
function replaceFile(fileId, formData) {
    return fetch(`/files/${fileId}/replace`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // إعادة تحميل ملفات المنتج
            const productId = document.querySelector('input[name="related_id"]').value;
            loadProductFiles(productId);
            
            showAlert('success', 'تم استبدال الملف بنجاح');
            return true;
        } else {
            showAlert('error', data.message || 'حدث خطأ أثناء استبدال الملف');
            return false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'حدث خطأ أثناء استبدال الملف');
        return false;
    });
}

/**
 * عرض رسالة تنبيه
 */
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="${icon}"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // إضافة التنبيه إلى الصفحة
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    // إزالة التنبيه بعد 5 ثوانٍ
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
