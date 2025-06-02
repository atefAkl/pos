@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">الفواتير</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{__('sales.invoice_number')}}</th>
                                    <th>{{__('sales.customer')}}</th>
                                    <th>{{__('sales.total')}}</th>
                                    <th>{{__('sales.paid_amount')}}</th>
                                    <th>{{__('sales.remaining_amount')}}</th>
                                    <th>{{__('sales.status')}}</th>
                                    <th>{{__('sales.created_at')}}</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->customer->name ?? __('sales.customer_cash') }}</td>
                                    <td>{{ number_format($invoice->total, 2) }}</td>
                                    <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                                    <td>{{ number_format($invoice->total - $invoice->paid_amount, 2) }}</td>
                                    <td>
                                        @if($invoice->status === 'paid')
                                        <span class="badge bg-success">{{__('sales.fully_paid')}}</span>
                                        @elseif($invoice->status === 'partially_paid')
                                        <span class="badge bg-warning">{{__('sales.partially_paid')}}</span>
                                        @else
                                        <span class="badge bg-danger">{{__('sales.not_paid')}}</span>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-sm btn-secondary" title="طباعة" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @if($invoice->total > $invoice->paid_amount)
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $invoice->id }}" title="إضافة دفعة">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                            @endif
                                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- نموذج إضافة دفعة -->
                                        @if($invoice->total > $invoice->paid_amount)
                                        <div class="modal fade" id="paymentModal{{ $invoice->id }}" tabindex="-1">
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
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">لا توجد فواتير</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $invoices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection