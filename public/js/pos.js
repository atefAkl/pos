// ملف جافاسكريبت رئيسي لنظام نقاط البيع
// كل الأحداث والمنطق سيتم بناؤها هنا تدريجياً

// دالة debounce لتأخير تنفيذ البحث حتى انتهاء المستخدم من الكتابة
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// طول الباركود المتوقع (يمكنك تعديله حسب نظامك)
const BARCODE_LENGTH = 13;

// ربط البحث مع الديبونس
const productSearch = document.getElementById("product-search");
const categoryFilter = document.getElementById("category-filter");

// البحث عند الضغط على Enter فقط (ماسك الباركود)
if (productSearch) {
    productSearch.addEventListener("keydown", function (e) {
        if (
            e.key === "Enter" &&
            productSearch.value.length === BARCODE_LENGTH
        ) {
            loadProducts(productSearch.value);
        }
    });
}

// يمكنك إبقاء كود الديبونس إذا أردت دعم البحث اليدوي أيضًا
// const debouncedSearch = debounce(function () {
//     if (
//         productSearch.value.length === BARCODE_LENGTH ||
//         productSearch.value.length === 0
//     ) {
//         loadProducts(productSearch.value);
//     }
// }, 300);
// if (productSearch) {
//     productSearch.addEventListener("input", debouncedSearch);
// }

// إذا كان لديك فلتر تصنيفات
if (categoryFilter) {
    categoryFilter.addEventListener("change", function () {
        loadProducts(productSearch.value);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // تعريف المتغيرات الأساسية
    let cart = [];
    const productsGrid = document.getElementById("products-grid");
    const cartTableBody = document.getElementById("cart-table-body");
    const checkoutBtn = document.getElementById("checkout-btn");
    const productSearch = document.getElementById("product-search");
    const categoryFilter = document.getElementById("category-filter");
    const paidAmount = document.getElementById("paid-amount");
    const invoiceTotal = document.getElementById("invoice-total");
    const remainingAmount = document.getElementById("remaining-amount");
    const invoiceNotes = document.getElementById("invoice-notes");
    const paymentMethod = document.getElementById("payment-method");
    const clearCartBtn = document.getElementById("clear-cart");

    // تحميل المنتجات (دالة مبدئية)
    function loadProducts(search = "") {
        // تفريغ الشبكة أولاً
        productsGrid.innerHTML = "";

        // جلب المنتجات من السيرفر
        fetch(`/pos/products?search=${encodeURIComponent(search)}`)
            .then((response) => response.json())
            .then((products) => {
                products.forEach((product) => {
                    const template =
                        document.getElementById("product-template");
                    const clone = template.content.cloneNode(true);
                    const card = clone.querySelector(".product-card");
                    card.dataset.id = product.id;
                    card.querySelector(".product-name").textContent =
                        product.name;
                    card.querySelector(".product-code").textContent =
                        product.code || "بدون كود";
                    card.querySelector(
                        ".product-price"
                    ).textContent = `${product.price} SAR`;
                    card.querySelector(
                        ".product-quantity"
                    ).textContent = `متوفر: ${product.quantity}`;
                    card.addEventListener("click", function () {
                        addToCart(product);
                    });
                    productsGrid.appendChild(clone);
                });
            })
            .catch((error) => {
                productsGrid.innerHTML =
                    '<div class="alert alert-danger">حدث خطأ أثناء تحميل المنتجات.</div>';
                console.error("خطأ في جلب المنتجات:", error);
            });
        // نهاية دالة loadProducts
    }

    // إضافة منتج للسلة (مبدئي)
    function addToCart(product) {
        // ...
    }

    // تحديث عرض السلة (مبدئي)
    function updateCartDisplay() {
        // ...
    }

    // إفراغ السلة
    if (clearCartBtn) {
        clearCartBtn.addEventListener("click", function () {
            cart = [];
            updateCartDisplay();
        });
    }

    // البحث عن المنتجات
    if (productSearch && categoryFilter) {
        productSearch.addEventListener("input", function () {
            loadProducts(productSearch.value, categoryFilter.value);
        });
        categoryFilter.addEventListener("change", function () {
            loadProducts(productSearch.value, categoryFilter.value);
        });
    }

    // تحديث المبلغ المتبقي عند تغيير المدفوع
    if (paidAmount) {
        paidAmount.addEventListener("input", function () {
            // ...
        });
    }

    // زر إتمام البيع
    if (checkoutBtn) {
        checkoutBtn.addEventListener("click", function () {
            // ...
        });
    }

    // تحميل المنتجات عند بدء التشغيل
    loadProducts();
});
