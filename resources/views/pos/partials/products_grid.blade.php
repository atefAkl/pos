<!-- شبكة عرض المنتجات (Products Grid) -->
<div class="row g-3" id="products-grid">
    <!-- سيتم تحميل المنتجات هنا ديناميكياً بواسطة جافاسكريبت -->
</div>

<template id="product-template">
    <div class="col-md-4 col-lg-3">
        <div class="card h-100 product-card" data-id="">
            <div class="card-body text-center">
                <h6 class="product-name mb-1">اسم المنتج</h6>
                <div class="product-code text-muted small mb-2">كود</div>
                <div class="product-price fw-bold mb-2">0 SAR</div>
                <div class="product-quantity text-success small mb-2">متوفر: 0</div>
            </div>
        </div>
    </div>
</template>