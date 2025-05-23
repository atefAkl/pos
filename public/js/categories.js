document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('categorySearchForm');
    const searchInput = document.getElementById('search');
    const statusSelect = document.getElementById('is_active');
    const categoryTreeContainer = document.getElementById('categoryTreeContainer');
    let searchTimeout;

    // دالة للبحث عن الفئات
    function searchCategories() {
        const searchParams = new URLSearchParams();
        
        if (searchInput.value) {
            searchParams.append('search', searchInput.value);
        }
        
        if (statusSelect.value !== '') {
            searchParams.append('is_active', statusSelect.value);
        }
        
        // إضافة parent_id إذا كان موجوداً
        const parentId = new URLSearchParams(window.location.search).get('parent_id');
        if (parentId) {
            searchParams.append('parent_id', parentId);
        }
        
        // إضافة معلمة ajax للإشارة إلى أن الطلب قادم من AJAX
        searchParams.append('ajax', '1');
        
        // إظهار مؤشر التحميل
        categoryTreeContainer.innerHTML = '<div class="text-center p-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">جاري التحميل...</span></div></div>';
        
        // إرسال طلب AJAX
        fetch(`/categories/search?${searchParams.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            }
        })
        .then(response => response.text())
        .then(html => {
            categoryTreeContainer.innerHTML = html;
            initializeCategoryTree();
        })
        .catch(error => {
            console.error('Error:', error);
            categoryTreeContainer.innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء تحميل الفئات. يرجى المحاولة مرة أخرى.</div>';
        });
    }
    
    // تهيئة شجرة الفئات (إعادة ربط الأحداث)
    function initializeCategoryTree() {
        // إعادة ربط أحداث التبديل بين الفئات
        document.querySelectorAll('.toggle-children').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category-id');
                const childrenDiv = document.getElementById(`children-${categoryId}`);
                const icon = this.querySelector('i');
                
                if (childrenDiv) {
                    if (childrenDiv.style.display === 'none' || !childrenDiv.style.display) {
                        childrenDiv.style.display = 'block';
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-down');
                    } else {
                        childrenDiv.style.display = 'none';
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-right');
                    }
                }
            });
        });
    }
    
    // البحث عند إرسال النموذج
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            searchCategories();
        });
    }
    
    // البحث أثناء الكتابة مع تأخير
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchCategories, 500);
        });
    }
    
    // البحث عند تغيير حالة النشاط
    if (statusSelect) {
        statusSelect.addEventListener('change', searchCategories);
    }
    
    // تهيئة شجرة الفئات عند التحميل
    initializeCategoryTree();
});
