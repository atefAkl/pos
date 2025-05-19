@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تفاصيل الفاتورة #{{ $invoice->invoice_number }}</h5>
                    <div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-secondary" target="_blank">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            <a href="{{ route('invoices.print-direct', $invoice) }}" class="btn btn-info" target="_blank">
                                <i class="fas fa-print"></i> طباعة مباشرة
                            </a>
                        </div>
                        @if($invoice->total > $invoice->paid_amount)
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-money-bill-wave"></i> إضافة دفعة
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="mb-3">معلومات العميل</h6>
                            <div>
                                <strong>الاسم:</strong> {{ $invoice->customer->name ?? 'عميل نقدي' }}
                            </div>
                            @if($invoice->customer)
                            <div>
                                <strong>الهاتف:</strong> {{ $invoice->customer->phone }}
                            </div>
                            <div>
                                <strong>العنوان:</strong> {{ $invoice->customer->address }}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">معلومات الفاتورة</h6>
                            <div>
                                <strong>التاريخ:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}
                            </div>
                            <div>
                                <strong>الحالة:</strong>
                                @if($invoice->status === 'paid')
                                    <span class="badge bg-success">مدفوعة</span>
                                @elseif($invoice->status === 'partially_paid')
                                    <span class="badge bg-warning">مدفوعة جزئياً</span>
                                @else
                                    <span class="badge bg-danger">غير مدفوعة</span>
                                @endif
                            </div>
                            <div>
                                <strong>المستخدم:</strong> {{ $invoice->createdBy->name }}
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-start">الإجمالي قبل الضريبة</td>
                                    <td>{{ number_format($invoice->subtotal, 2) }}</td>
                                </tr>
                                @if($invoice->tax > 0)
                                <tr>
                                    <td colspan="4" class="text-start">الضريبة</td>
                                    <td>{{ number_format($invoice->tax, 2) }}</td>
                                </tr>
                                @endif
                                @if($invoice->discount > 0)
                                <tr>
                                    <td colspan="4" class="text-start">الخصم</td>
                                    <td>{{ number_format($invoice->discount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="text-start">الإجمالي النهائي</td>
                                    <td>{{ number_format($invoice->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-start">المدفوع</td>
                                    <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-start">المتبقي</td>
                                    <td>{{ number_format($invoice->total - $invoice->paid_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($invoice->payments->isNotEmpty())
                    <div class="mt-4">
                        <h6 class="mb-3">سجل المدفوعات</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>التاريخ</th>
                                        <th>المبلغ</th>
                                        <th>طريقة الدفع</th>
                                        <th>المستخدم</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            @if($payment->payment_method === 'cash')
                                                <span class="badge bg-success">نقداً</span>
                                            @else
                                                <span class="badge bg-info">بطاقة</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->createdBy->name }}</td>
                                        <td>{{ $payment->notes }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($invoice->notes)
                    <div class="mt-4">
                        <h6 class="mb-2">ملاحظات</h6>
                        <p class="mb-0">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($invoice->total > $invoice->paid_amount)
<!-- نموذج إضافة دفعة -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('invoices.payment', $invoice) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة دفعة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المبلغ المتبقي</label>
                        <input type="text" class="form-control" value="{{ number_format($invoice->total - $invoice->paid_amount, 2) }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المبلغ المدفوع</label>
                        <input type="number" name="amount" class="form-control" step="0.01" max="{{ $invoice->total - $invoice->paid_amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">طريقة الدفع</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">نقداً</option>
                            <option value="card">بطاقة</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
