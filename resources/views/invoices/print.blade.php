<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'XB Riyaz', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .invoice-header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section div {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f8f9fa;
        }
        .totals {
            width: 300px;
            margin-right: auto;
        }
        .totals td:first-child {
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>نظام المبيعات</h1>
        <p>فاتورة ضريبية مبسطة</p>
        <p>رقم الفاتورة: {{ $invoice->invoice_number }}</p>
        <p>التاريخ: {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
    </div>

    <div class="info-section">
        <div><strong>العميل:</strong> {{ $invoice->customer->name ?? 'عميل نقدي' }}</div>
        @if($invoice->customer)
        <div><strong>الهاتف:</strong> {{ $invoice->customer->phone }}</div>
        <div><strong>العنوان:</strong> {{ $invoice->customer->address }}</div>
        @endif
    </div>

    <table>
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
    </table>

    <table class="totals">
        <tr>
            <td>الإجمالي قبل الضريبة:</td>
            <td>{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->tax > 0)
        <tr>
            <td>الضريبة:</td>
            <td>{{ number_format($invoice->tax, 2) }}</td>
        </tr>
        @endif
        @if($invoice->discount > 0)
        <tr>
            <td>الخصم:</td>
            <td>{{ number_format($invoice->discount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td>الإجمالي النهائي:</td>
            <td>{{ number_format($invoice->total, 2) }}</td>
        </tr>
        <tr>
            <td>المدفوع:</td>
            <td>{{ number_format($invoice->paid_amount, 2) }}</td>
        </tr>
        <tr>
            <td>المتبقي:</td>
            <td>{{ number_format($invoice->total - $invoice->paid_amount, 2) }}</td>
        </tr>
    </table>

    @if($invoice->notes)
    <div style="margin-top: 20px;">
        <strong>ملاحظات:</strong>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>شكراً لتعاملكم معنا</p>
        <p>{{ config('app.name') }} - {{ date('Y') }}</p>
    </div>
</body>
</html>
