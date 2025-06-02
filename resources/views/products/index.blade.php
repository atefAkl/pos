@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">المنتجات والخدمات</h1>
        <div>
            <a href="{{ route('products.import') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-file-import"></i> استيراد
            </a>
            <a href="{{ route('products.export') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-file-export"></i> تصدير
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة جديد
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <!-- Search and Filters -->
            <form action="{{ route('products.index') }}" method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-2">
                        <input type="text" name="search" class="form-control"
                            placeholder="بحث بالاسم أو الكود..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-select">
                            <option value="">كل الفئات</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">منتج أو خدمة</option>
                            <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>منتجات</option>
                            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>خدمات</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">كل الحالات</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="stock" class="form-select">
                            <option value="">كل حالات المخزون</option>
                            <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>منخفض</option>
                            <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>منتهي</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100" title="بحث">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary w-100" title="إعادة تعيين">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="products-table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>

                            <th>الاسم</th>
                            <th>الكود / الباركود</th>
                            <th>السعر</th>
                            <th>المخزون</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr class="{{ $product->quantity <= $product->alert_quantity ? 'table-warning' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $product->name }}</div>
                                @if($product->sku)
                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                                @endif
                                <span class="fas fa-{{ $product->is_service ? 'tools' : 'box' }} text-{{ $product->is_service ? 'info' : 'primary' }}">
                                </span>
                                <span class="progress-bar bg-primary text-light" role="progressbar">{{ $product->category->name }}</span>
                            </td>
                            <td>
                                @if($product->code)
                                <div><strong>الكود:</strong> {{ $product->code }}</div>
                                @endif
                                @if($product->barcode)
                                <div><strong>الباركود:</strong> {{ $product->barcode }}</div>
                                @endif
                            </td>
                            <td>
                                <div><strong>التكلفة:</strong> {{ number_format($product->price, 2) }} ر.س</div>
                                <div><strong>البيع:</strong> {{ number_format($product->retail_price, 2) }} ر.س</div>
                                @if($product->wholesale_price && $product->wholesale_quantity > 1)
                                <div class="text-success">
                                    <small>الجملة: {{ number_format($product->wholesale_price, 2) }} ر.س ({{ $product->wholesale_quantity }}+)</small>
                                </div>
                                @endif
                            </td>
                            <td>
                                @if(!$product->is_service)
                                <div class="progress" style="height: 20px;">
                                    @php
                                    $percentage = $product->quantity > 0 ? min(100, ($product->quantity / ($product->reorder_level > 0 ? $product->reorder_level * 2 : 10)) * 100) : 0;
                                    $color = $product->quantity == 0 ? 'danger' : ($product->quantity <= $product->alert_quantity ? 'warning' : 'success');
                                        @endphp
                                        <div class="progress-bar bg-{{ $color }}" role="progressbar"
                                            style="width: {{ $percentage }}%"
                                            aria-valuenow="{{ $percentage }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                            {{ $product->quantity }} {{ $product->unit ?: 'قطعة' }}
                                        </div>
                                </div>
                                @if($product->alert_quantity > 0)
                                <small class="text-muted">حد التنبيه: {{ $product->alert_quantity }}</small>
                                @endif
                                @else
                                <span class="text-muted">غير قابل للتطبيق</span>
                                @endif
                            </td>
                            <td>
                                <div class="form-check form-switch d-inline-block" title="{{ $product->active ? 'نشط' : 'غير نشط' }}">
                                    <form action="{{ route('products.toggle-status', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="active" value="{{ $product->active ? 0 : 1 }}">
                                        <button type="submit" class="btn btn-link p-0 border-0 bg-transparent">
                                            <input class="form-check-input" type="checkbox"
                                                {{ $product->active ? 'checked' : '' }}
                                                onchange="this.form.submit()"
                                                style="cursor: pointer;">
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('هل أنت متأكد من حذف {{ $product->is_service ? 'هذه الخدمة' : 'هذا المنتج' }}؟')">
                                        @csrf
                                        @method('DELETE')
                                        <a href="{{ route('products.show', $product) }}" class="btn" data-bs-toggle="tooltip" data-bs-title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="btn" data-bs-toggle="tooltip" data-bs-title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="submit" class="btn" data-bs-toggle="tooltip" data-bs-title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">لا توجد {{ request('type') === 'service' ? 'خدمات' : 'منتجات' }} متاحة</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        عرض {{ $products->firstItem() }} - {{ $products->lastItem() }} من أصل {{ $products->total() }} عنصر
                    </div>
                    <div>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .table th,
    .table td {
        vertical-align: middle;
        text-align: center;
    }

    .table th {
        background-color: #f8f9fc;
        font-weight: 600;
    }

    .progress {
        min-width: 100px;
    }

    .img-thumbnail {
        padding: 0.15rem;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }

    .btn-group-sm>.btn,
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#products-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json"
            },
            "responsive": true,
            "order": [
                [0, 'asc']
            ],
            "pageLength": 25,
            "dom": `
                <'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>
                <'row'<'col-sm-12'tr>>
                <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>
            `,
            "columnDefs": [{
                    "orderable": false,
                    "targets": [1, 9]
                } // Disable sorting on image and actions columns
            ]
        });

        // Toggle product/stock alerts
        $('.toggle-stock-alert').on('click', function(e) {
            e.preventDefault();
            const button = $(this);
            const url = button.attr('href');

            $.ajax({
                url: url,
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PATCH'
                },
                success: function(response) {
                    button.find('i').toggleClass('fa-bell fa-bell-slash');
                    button.toggleClass('btn-warning btn-secondary');

                    // Update tooltip
                    const newTitle = button.attr('title') === 'تعطيل التنبيه' ? 'تفعيل التنبيه' : 'تعطيل التنبيه';
                    button.attr('title', newTitle).tooltip('dispose').tooltip();
                },
                error: function(xhr) {
                    alert('حدث خطأ أثناء تحديث حالة التنبيه');
                }
            });
        });

        // Quick edit price
        $('.editable-price').on('dblclick', function() {
            const $this = $(this);
            const value = $this.data('value');
            const field = $this.data('field');
            const productId = $this.data('product');

            $this.html(`
                <div class="input-group input-group-sm">
                    <input type="number" step="0.01" class="form-control" value="${value}" id="edit-${field}">
                    <button class="btn btn-outline-primary btn-save-edit" type="button">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-cancel-edit" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `);

            $(`#edit-${field}`).focus();

            // Save on Enter key
            $(`#edit-${field}`).on('keypress', function(e) {
                if (e.which === 13) {
                    saveEdit($(this).val(), field, productId, $this);
                    return false;
                }
            });

            // Save button click
            $this.find('.btn-save-edit').on('click', function() {
                const newValue = $(`#edit-${field}`).val();
                saveEdit(newValue, field, productId, $this);
            });

            // Cancel button click
            $this.find('.btn-cancel-edit').on('click', function() {
                $this.html(formatValue(value, field));
            });
        });

        function saveEdit(value, field, productId, $element) {
            if (!value) return;

            $.ajax({
                url: `/admin/products/${productId}/update-field`,
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    field: field,
                    value: value
                },
                success: function(response) {
                    $element.html(formatValue(value, field));
                    showToast('success', 'تم التحديث بنجاح');
                },
                error: function() {
                    showToast('error', 'حدث خطأ أثناء التحديث');
                }
            });
        }

        function formatValue(value, field) {
            if (field === 'price' || field === 'retail_price' || field === 'wholesale_price') {
                return parseFloat(value).toFixed(2) + ' ر.س';
            }
            return value;
        }

        function showToast(type, message) {
            const toast = `
                <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

            $('.toast-container').append(toast);
            $('.toast').toast({
                autohide: true,
                delay: 3000
            });
            $('.toast').toast('show');

            // Remove toast after it's hidden
            $('.toast').on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }
    });
</script>
@endpush