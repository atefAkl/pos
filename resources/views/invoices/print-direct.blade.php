<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $invoice->invoice_number }}</title>
    <style>
        @media print {
            body {
                font-family: xbriyaz, sans-serif;
                margin: 0;
                padding: 0;
                font-size: 12px;
            }
            .print-header {
                text-align: center;
                margin-bottom: 10px;
            }
            .print-header h1 {
                margin: 0;
                font-size: 18px;
            }
            .print-header p {
                margin: 2px 0;
                font-size: 12px;
            }
            .info-section {
                margin-bottom: 10px;
                font-size: 12px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            th, td {
                padding: 4px;
                text-align: right;
                border: 1px solid #ddd;
                font-size: 12px;
            }
            .totals {
                width: 100%;
                margin-top: 10px;
            }
            .totals td:first-child {
                font-weight: bold;
            }
            .footer {
                margin-top: 20px;
                text-align: center;
                font-size: 10px;
            }
            .qr-code {
                text-align: center;
                margin: 10px 0;
            }
            @page {
                margin: 10mm;
                size: 80mm 297mm;
            }
        }
    </style>
</head>
<body>
    <div class="print-header">
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
    <div style="margin-top: 10px;">
        <strong>ملاحظات:</strong>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="qr-code" style="text-align: center; margin: 15px 0;">
        <div style="display: inline-block; text-align: center;">
            @php
                $zatcaQr = \App\Services\ZatcaQrCode::generate($invoice);
            @endphp
            <img src="data:image/svg+xml;base64,{{ base64_encode($zatcaQr['qr_code']) }}" alt="QR Code" style="width: 150px; height: 150px;">
            <div style="margin-top: 5px; font-size: 10px;">
                {{ config('app.name') }} - الفاتورة #{{ $invoice->invoice_number }}
            </div>
            <!--
            <div style="font-size: 8px; margin-top: 5px; word-break: break-all; direction: ltr;">
                {{ $zatcaQr['base64'] }}
            </div>
            -->
        </div>
    </div>

    <div class="footer">
        <p>شكراً لتعاملكم معنا</p>
        <p>{{ config('app.name') }} - {{ date('Y') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
