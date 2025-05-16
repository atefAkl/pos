@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل العميل</h1>
        <div>
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> عودة للعملاء
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">معلومات العميل</h5>
                    <hr>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">الاسم:</dt>
                        <dd class="col-sm-8">{{ $customer->name }}</dd>

                        <dt class="col-sm-4">رقم الهاتف:</dt>
                        <dd class="col-sm-8">{{ $customer->phone }}</dd>

                        <dt class="col-sm-4">البريد:</dt>
                        <dd class="col-sm-8">{{ $customer->email ?: '-' }}</dd>

                        <dt class="col-sm-4">النوع:</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ $customer->type === 'wholesale' ? 'bg-primary' : 'bg-info' }}">
                                {{ $customer->type === 'wholesale' ? 'جملة' : 'تجزئة' }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">الرصيد:</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ $customer->balance > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ number_format($customer->balance, 2) }} ج.م
                            </span>
                        </dd>

                        <dt class="col-sm-4">العنوان:</dt>
                        <dd class="col-sm-8">{{ $customer->address ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">فواتير العميل</h5>
                    <hr>
                    
                    @if($customer->invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>التاريخ</th>
                                        <th>إجمالي الفاتورة</th>
                                        <th>المدفوع</th>
                                        <th>المتبقي</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->number }}</td>
                                        <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                        <td>{{ number_format($invoice->total, 2) }} ج.م</td>
                                        <td>{{ number_format($invoice->paid, 2) }} ج.م</td>
                                        <td>{{ number_format($invoice->remaining, 2) }} ج.م</td>
                                        <td>
                                            <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : ($invoice->status === 'partial' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $invoice->status === 'paid' ? 'مدفوع' : ($invoice->status === 'partial' ? 'مدفوع جزئياً' : 'غير مدفوع') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-file-invoice-dollar fa-3x mb-3"></i>
                            <p>لا توجد فواتير لهذا العميل</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
