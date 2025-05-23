@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">طباعة الباركود</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="window.print()">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
                
                <div class="card-body text-center">
                    <h4 class="mb-4">{{ $product->name }}</h4>
                    
                    <div class="mb-3">
                        @php
                            $barcode = new Picqer\Barcode\BarcodeGeneratorHTML();
                            echo $barcode->getBarcode($product->barcode, $barcodeType);
                        @endphp
                        <div class="mt-2">{{ $product->barcode }}</div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barcodeType">نوع الباركود:</label>
                                <select class="form-select" id="barcodeType" onchange="updateBarcodeType(this.value)">
                                    <option value="C128" {{ $barcodeType === 'C128' ? 'selected' : '' }}>Code 128</option>
                                    <option value="C39" {{ $barcodeType === 'C39' ? 'selected' : '' }}>Code 39</option>
                                    <option value="EAN13" {{ $barcodeType === 'EAN13' ? 'selected' : '' }}>EAN-13</option>
                                    <option value="UPCA" {{ $barcodeType === 'UPCA' ? 'selected' : '' }}>UPC-A</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barcodeHeight">ارتفاع الباركود (بكسل):</label>
                                <input type="number" class="form-control" id="barcodeHeight" 
                                       value="{{ $barcodeHeight }}" min="20" max="200" 
                                       onchange="updateBarcodeHeight(this.value)">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barcodeWidth">سمك الباركود:</label>
                                <input type="number" class="form-control" id="barcodeWidth" 
                                       value="{{ $barcodeWidth }}" min="1" max="5" step="0.5"
                                       onchange="updateBarcodeWidth(this.value)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barcodeCount">عدد النسخ:</label>
                                <input type="number" class="form-control" id="barcodeCount" value="1" min="1" max="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary" onclick="printBarcodes()">
                            <i class="fas fa-print"></i> طباعة {{ $product->quantity }} نسخة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateBarcodeType(type) {
    const url = new URL(window.location.href);
    url.searchParams.set('type', type);
    window.location.href = url.toString();
}

function updateBarcodeHeight(height) {
    const url = new URL(window.location.href);
    url.searchParams.set('height', height);
    window.location.href = url.toString();
}

function updateBarcodeWidth(width) {
    const url = new URL(window.location.href);
    url.searchParams.set('width', width);
    window.location.href = url.toString();
}

function printBarcodes() {
    const count = parseInt(document.getElementById('barcodeCount').value) || 1;
    const printWindow = window.open('', '_blank');
    
    let content = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>طباعة الباركود - {{ $product->name }}</title>
            <link href="{{ asset('css/app.css') }}" rel="stylesheet">
            <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
            <style>
                @page { size: auto; margin: 5mm; }
                body { font-family: Arial, sans-serif; }
                .barcode-container { 
                    page-break-inside: avoid; 
                    margin-bottom: 10px;
                    text-align: center;
                    padding: 10px;
                }
                .barcode-label {
                    margin-top: 5px;
                    font-size: 12px;
                }
                .product-name {
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .product-price {
                    font-size: 14px;
                    color: #333;
                }
            </style>
        </head>
        <body>
            <div style="display: flex; flex-wrap: wrap; justify-content: center;">
    `;
    
    for (let i = 0; i < count; i++) {
        content += `
            <div class="barcode-container">
                <div class="product-name">{{ $product->name }}</div>
                <div class="barcode">
                    {!! (new Picqer\Barcode\BarcodeGeneratorHTML())->getBarcode($product->barcode, '{{ $barcodeType }}') !!}
                </div>
                <div class="barcode-label">{{ $product->barcode }}</div>
                <div class="product-price">السعر: {{ number_format($product->retail_price, 2) }} {{ config('settings.currency_symbol') }}</div>
            </div>
        `;
    }
    
    content += `
            </div>
            <script src="{{ asset('js/app.js') }}<\/script>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() {
                        window.close();
                    }, 1000);
                };
            <\/script>
        </body>
        </html>
    `;
    
    printWindow.document.open();
    printWindow.document.write(content);
    printWindow.document.close();
}
</script>
@endpush
@endsection
