@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- القسم الأيمن: المنتجات -->
        <div class="col-lg-8">
            @include('pos.partials.search_bar')
            @include('pos.partials.products_grid')
        </div>
        <!-- القسم الأيسر: الفاتورة -->
        <div class="col-lg-4">
            @include('pos.partials.cart_table')
            @include('pos.partials.invoice_summary')
            <option value="">اختر العميل</option>
            @foreach($customers as $customer)
            <option value="{{ $customer->id }}" data-balance="{{ $customer->balance }}">
                {{ $customer->name }} ({{ $customer->phone }})
            </option>
            @endforeach
            </select>
            <div class="mt-2" id="customer-info" style="display: none;">
                <small class="text-muted">الرصيد المستحق: <span id="customer-balance">0</span> SAR</small>
            </div>
        </div>
    </div>
</div>



<!-- قالب عنصر الفاتورة -->
<template id="invoice-item-template">
    <tr>
        <td class="item-name"></td>
        <td>
            <input type="number" class="form-control form-control-sm item-quantity" min="1" value="1" style="width: 70px">
        </td>
        <td class="item-price"></td>
        <td class="item-total"></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-item">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script src="{{ asset('js/pos.js') }}"></script>
@endpush

@endsection