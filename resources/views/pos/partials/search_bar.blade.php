<!-- شريط البحث وإدخال الباركود -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-2 mb-3">
            <!-- بحث عن المنتجات أو إدخال باركود -->
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" id="product-search" class="form-control" placeholder="بحث عن منتج أو امسح الباركود..." autocomplete="off" autofocus>
                    <button class="btn btn-outline-primary" type="button" id="scan-barcode">
                        <i class="fas fa-barcode"></i>
                    </button>
                </div>
            </div>
            <!-- تصفية حسب الفئة -->
            <div class="col-md-4">
                <select id="category-filter" class="form-select">
                    <option value="">كل الفئات</option>
                    <!-- يمكن ملء الفئات ديناميكياً -->
                </select>
            </div>
        </div>
    </div>
</div>
