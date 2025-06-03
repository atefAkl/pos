<div>
    <div class="container-fluid">
        <div class="row">
            {{-- Products Section --}}
            <div class="col-md-7 col-lg-8 order-md-first">
                <h4 class="mb-3">المنتجات</h4>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="بحث بالمنتج بالاسم أو الباركود..." wire:model.live.debounce.300ms="searchProduct">
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3" style="max-height: 70vh; overflow-y: auto;">
                    @forelse ($productsToDisplay as $product)
                    <div class="col">
                        <div class="card shadow-sm h-100" style="cursor: pointer;">
                            {{-- <img src="{{ $product->image_url ?? asset('placeholder.jpg') }}" class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;"> --}}
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">{{ $product->name }}</h6>
                                <p class="card-text fs-5 fw-bold text-success">{{ $product->retail_price }} {{ __('SAR') }}</p>
                                <div class="mt-auto">
                                    <button class="btn btn-sm btn-primary w-100" wire:click="addItemToCart({{ $product->id }})">إضافة للسلة</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <p>لا توجد منتجات متاحة حالياً.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Cart and Customer Section --}}
            <div class="col-md-5 col-lg-4">
                {{-- Success Message --}}
                @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">الفاتورة</span>
                    <span class="badge bg-primary rounded-pill">{{ count($cartItems) }}</span> {{-- Cart item count --}}
                </h4>
                <div class="card p-2 mb-3">
                    <h5 class="mb-3">تفاصيل الفاتورة</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-2">
                            <thead class="table-light">
                                <tr>
                                    <th>الصنف</th>
                                    <th>الكمية</th>
                                    <th>سعر الوحدة</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cartItems as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>{{ number_format($item['price'], 2) }}</td>
                                    <td>{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد أصناف في السلة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <ul class="list-group mb-2">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>الإجمالي بدون ضريبة</span>
                            <strong>{{ number_format($cartSubtotal, 2) }} {{ __('SAR') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>ضريبة القيمة المضافة</span>
                            <strong>{{ number_format($cartVat, 2) }} {{ __('SAR') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span>الإجمالي مع الضريبة</span>
                            <strong>{{ number_format($cartTotal, 2) }} {{ __('SAR') }}</strong>
                        </li>
                    </ul>
                </div>

                <div class="card p-2 mb-3">
                    <div class="input-group mb-3">
                        <label for="customer" class="input-group-text">العميل</label>
                        <input type="text" class="form-control mb-2" placeholder="بحث عن عميل بالاسم أو الهاتف..." wire:model.debounce.300ms="searchCustomer">
                        <select class="form-select" id="customer" wire:model="selectedCustomerId">
                            @foreach ($customersToDisplay as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 input-group">
                        <label for="paymentMethod" class="input-group-text">طريقة الدفع</label>
                        <select class="form-select" id="paymentMethod" wire:model="paymentMethod">
                            <option value="cash">نقداً</option>
                            <option value="card">بطاقة</option>
                            <option value="mixed">مدفوع جزئي (نقداً + بطاقة)</option>
                        </select>
                    </div>
                    <div class="mb-3 input-group">
                        <label for="notes" class="input-group-text">ملاحظات</label>
                        <textarea class="form-control" id="notes" rows="2" wire:model="notes"></textarea>
                    </div>
                </div>

                <div class="card p-2">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>الإجمالي بدون ضريبة ({{ __('SAR') }})</span>
                            <strong>{{ number_format($cartSubtotal, 2) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>ضريبة القيمة المضافة ({{ __('SAR') }})</span>
                            <strong>{{ number_format($cartVat, 2) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>الإجمالي مع الضريبة ({{ __('SAR') }})</span>
                            <strong>{{ number_format($cartTotal, 2) }}</strong>
                        </li>
                    </ul>
                    <button type="button" class="btn btn-success btn-lg w-100 mt-2" wire:click.prevent="processSale" wire:loading.attr="disabled" wire:target="processSale">
                        <span wire:loading.remove wire:target="processSale">إتمام البيع</span>
                        <span wire:loading wire:target="processSale">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            جاري المعالجة...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>