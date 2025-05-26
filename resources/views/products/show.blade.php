@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل المنتج</h1>
        <div>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> عودة للمنتجات
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-striped">
                        <tr>
                            <th class="w-25">اسم المنتج</th>
                            <td>{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th>الفئة</th>
                            <td>{{ $product->category->name }}</td>
                        </tr>
                        <tr>
                            <th>كود المنتج</th>
                            <td>{{ $product->code ?: 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>الباركود</th>
                            <td>
                                @if($product->barcode)
                                    <div class="d-flex align-items-center">
                                        <span class="me-3">{{ $product->barcode }}</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#barcodeModal">
                                            <i class="fas fa-barcode"></i> عرض الباركود
                                        </button>
                                    </div>
                                @else
                                    غير محدد
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>السعر</th>
                            <td>{{ number_format($product->price, 2) }} ج.م</td>
                        </tr>
                        <tr>
                            <th>الكمية</th>
                            <td>
                                <span class="badge {{ $product->quantity <= $product->alert_quantity ? 'bg-danger' : 'bg-success' }}">
                                    {{ $product->quantity }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>حد التنبيه</th>
                            <td>{{ $product->alert_quantity }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                <span class="badge {{ $product->active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>الوصف</th>
                            <td>{{ $product->description ?: 'لا يوجد وصف' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">حركة المخزون</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center text-muted">
                                <i class="fas fa-chart-line fa-3x mb-3"></i>
                                <p>سيتم عرض حركة المخزون هنا</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة منبثقة لعرض الباركود -->
@if($product->barcode)
<div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barcodeModalLabel">باركود المنتج: {{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <img src="{{ $product->barcode_image }}" alt="باركود المنتج" class="img-fluid">
                    <p class="mt-2 text-center">{{ $product->barcode }}</p>
                </div>
                <div class="d-flex justify-content-center">
                    <a href="{{ route('products.print-barcode', $product) }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-print"></i> طباعة الباركود
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
