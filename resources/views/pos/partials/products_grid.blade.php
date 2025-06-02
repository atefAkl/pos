{{-- resources/views/pos/partials/products_grid.blade.php --}}
<div class="row g-2" id="products-grid">
    <template x-if="displayedProducts.length === 0 && searchTerm.length > 0">
        <div class="col-12">
            <p class="text-center text-muted mt-4 py-5">لا توجد منتجات تطابق بحثك.</p>
        </div>
    </template>
    <template x-if="displayedProducts.length === 0 && searchTerm.length === 0 && allProducts.length > 0">
         <div class="col-12">
            <p class="text-center text-muted mt-4 py-5">لا توجد منتجات لعرضها حاليًا (قد تكون المشكلة في الفلترة الأولية أو جميع المنتجات نفذت كميتها).</p>
        </div>
    </template>
     <template x-if="allProducts.length === 0">
         <div class="col-12">
            <p class="text-center text-muted mt-4 py-5">لا توجد أي منتجات مضافة في النظام.</p>
        </div>
    </template>

    <template x-for="product in displayedProducts" :key="product.id">
        <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
            <div class="card product-card h-100 shadow-sm"
                 @click="product.quantity > 0 ? addToCart(product) : showNotification('نفذت الكمية', `المنتج "${product.name}" غير متوفر.`, 'warning')"
                 :class="{ 'opacity-75 pe-none bg-light': product.quantity <= 0 }"
                 role="button"
                 tabindex="0"
            >
                {{-- يمكنك إضافة صورة المنتج هنا إذا أردت --}}
                {{-- <img :src="product.image_url || '/images/placeholder.png'" class="card-img-top p-2" alt="..." style="max-height: 120px; object-fit: contain;"> --}}
                <div class="card-body text-center d-flex flex-column p-2">
                    <h6 class="product-name mb-1 small fw-bold" x-text="product.name"></h6>
                    <div class="product-code text-muted extra-small mb-1" x-text="product.code || ''"></div>
                    <div class="product-price fw-bold mb-1" x-text="`${parseFloat(product.price).toFixed(2)} SAR`"></div>
                    <div class="mt-auto">
                        <small class="product-quantity extra-small"
                               :class="product.quantity > 0 ? 'text-success' : 'text-danger fw-bold'"
                               x-text="product.quantity > 0 ? `متوفر: ${product.quantity}` : 'نفذت الكمية'">
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
.extra-small {
    font-size: 0.75rem;
}
.opacity-75 {
    opacity: 0.75 !important;
}
.pe-none {
    pointer-events: none !important;
}
</style>