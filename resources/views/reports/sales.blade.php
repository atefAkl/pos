@extends('layouts.dashboard')

@section('title', __('Sales Report'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('Sales Report')</h3>
                </div>
                <div class="card-body">
                    @if(isset($sales) && $sales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Invoice ID')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Cashier')</th>
                                    <th>@lang('Total Amount')</th>
                                    <th>@lang('Notes')</th>
                                    <th>@lang('Items')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                <tr>
                                    <td>{{ $sale->id }}</td>
                                    <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $sale->customer->name ?? __('N/A') }}</td>
                                    <td>{{ $sale->user->name ?? __('N/A') }}</td>
                                    <td>{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>{{ $sale->notes }}</td>
                                    <td>
                                        @if($sale->items && $sale->items->count() > 0)
                                        <ul>
                                            @foreach($sale->items as $item)
                                            <li>{{ $item->product->name ?? __('Product N/A') }} ({{ $item->quantity }} x {{ number_format($item->price, 2) }})</li>
                                            @endforeach
                                        </ul>
                                        @else
                                        @lang('No items')
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $sales->links() }}
                    </div>
                    @else
                    <p>@lang('No sales found.')</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection