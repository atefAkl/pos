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
                <ul class="list-group mb-3" style="max-height: 35vh; overflow-y: auto;">
                    @forelse ($cartItems as $itemId => $item)
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0">{{ $item['name'] }}</h6>
                            <small class="text-muted">الكمية: {{ $item['quantity'] }}</small>
                        </div>
                        <span class="text-muted me-2">{{ number_format($item['price'] * $item['quantity'], 2) }} {{ __('SAR') }}</span>
                        <div>
                            <button class="btn btn-sm btn-outline-success py-0 px-1" wire:click="increaseQuantity('{{ $itemId }}')"><i class="fas fa-plus"></i></button>
                            <button class="btn btn-sm btn-outline-warning py-0 px-1" wire:click="decreaseQuantity('{{ $itemId }}')"><i class="fas fa-minus"></i></button>
                            <button class="btn btn-sm btn-outline-danger py-0 px-1" wire:click="removeItem('{{ $itemId }}')"><i class="fas fa-trash"></i></button>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0">لا توجد أصناف في السلة</h6>
                            <small class="text-muted">قم بإضافة منتجات</small>
                        </div>
                    </li>
                    @endforelse
                </ul>

                <div class="card p-2 mb-3">
                    <div class="mb-3">
                        <label for="customer" class="form-label">العميل</label>
                        <select class="form-select" id="customer" wire:model="selectedCustomerId">
                            <option value="">اختر العميل...</option>
                            @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control" id="notes" rows="2" wire:model="notes"></textarea>
                    </div>
                </div>

                <div class="card p-2">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>الإجمالي ({{ __('SAR') }})</span>
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