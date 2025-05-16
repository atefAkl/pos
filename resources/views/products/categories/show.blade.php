@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الفئة</h1>
        <div>
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> عودة للفئات
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">معلومات الفئة</h5>
                    <hr>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">اسم الفئة:</dt>
                        <dd class="col-sm-8">{{ $category->name }}</dd>

                        <dt class="col-sm-4">الحالة:</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ $category->active ? 'bg-success' : 'bg-danger' }}">
                                {{ $category->active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">الوصف:</dt>
                        <dd class="col-sm-8">{{ $category->description ?: 'لا يوجد وصف' }}</dd>

                        <dt class="col-sm-4">عدد المنتجات:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-info">{{ $category->products->count() }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">المنتجات في هذه الفئة</h5>
                    <hr>
                    
                    @if($category->products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>الكود</th>
                                        <th>اسم المنتج</th>
                                        <th>السعر</th>
                                        <th>الكمية</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->products as $product)
                                    <tr>
                                        <td>{{ $product->code ?: '-' }}</td>
                                        <td>
                                            <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($product->price, 2) }} ج.م</td>
                                        <td>
                                            <span class="badge {{ $product->quantity <= $product->alert_quantity ? 'bg-danger' : 'bg-success' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $product->active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-box fa-3x mb-3"></i>
                            <p>لا توجد منتجات في هذه الفئة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
