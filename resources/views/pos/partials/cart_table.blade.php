<!-- جدول السلة (Cart Table) -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> السلة</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th style="width: 90px;">الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cart-table-body">
                    <!-- ستتم تعبئة السلة ديناميكياً بالجافاسكريبت -->
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-2">
            <button class="btn btn-danger btn-sm" id="clear-cart"><i class="fas fa-trash"></i> إفراغ السلة</button>
            <!-- يمكن إضافة زر لإتمام البيع هنا أو في ملخص الفاتورة -->
        </div>
    </div>
</div>
