@if($categories->isNotEmpty())
    <div class="category-tree">
        <ul class="list-group">
            @foreach($categories as $category)
                @php
                    $hasSearch = request()->hasAny(['search', 'is_active']);
                    $hasMatchingChildren = $hasSearch && $category->children->isNotEmpty();
                @endphp
                
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if($category->children->isNotEmpty())
                                <span class="me-2 toggle-children" data-category-id="{{ $category->id }}">
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            @else
                                <span class="me-2" style="width: 20px;"></span>
                            @endif
                            <div class="d-flex flex-column">
                                <span class="fw-bold">
                                    @if(request()->filled('search') && str_contains(strtolower($category->name), strtolower(request('search'))))
                                        {!! str_ireplace(
                                            request('search'), 
                                            '<span class="search-highlight">'.request('search').'</span>', 
                                            e($category->name)
                                        ) !!}
                                    @else
                                        {{ $category->name }}
                                    @endif
                                </span>
                                @if($category->description)
                                    <small class="text-muted">{{ $category->description }}</small>
                                @endif
                            </div>
                            <div class="ms-3">
                                <span class="badge bg-secondary">{{ $category->products_count }} منتج</span>
                                
                                @if($hasSearch && $category->is_active === false)
                                    <span class="badge bg-warning text-dark ms-1">غير نشط</span>
                                @endif
                            </div>

                            <span class="badge ms-1 bg-secondary">المستوى {{ $category->level }} </span>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('categories.edit', $category->id) }}" 
                               class="btn text-primary" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="<i class='fas fa-edit me-1'></i> تعديل الفئة">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" 
                                    class="btn text-danger delete-category" 
                                    data-id="{{ $category->id }}" 
                                    data-name="{{ $category->name }}"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="<i class='fas fa-trash me-1'></i> حذف الفئة">
                                <i class="fas fa-trash"></i>
                            </button>
                            @if($category->level < 3)
                            <a href="{{ route('categories.create', ['parent_id' => $category->id]) }}" 
                               class="btn text-success" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top"
                               title="<i class='fas fa-plus me-1'></i> إضافة فئة فرعية">
                                <i class="fas fa-plus"></i>
                            </a>
                            @else
                            <a href="{{ route('products.create', ['category_id' => $category->id]) }}" 
                               class="btn text-success" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top"
                               title="<i class='fas fa-cart-plus me-1'></i> إضافة منتجات">
                                <i class="fas fa-cart-plus"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    
                    @if($hasMatchingChildren)
                        <div class="children ms-4 mt-2" id="children-{{ $category->id }}" style="display: block;">
                            @include('products.categories.partials.category_tree', ['categories' => $category->children])
                        </div>
                    @elseif($category->children->isNotEmpty())
                        <div class="children ms-4 mt-2" id="children-{{ $category->id }}" style="display: none;">
                            @include('products.categories.partials.category_tree', ['categories' => $category->children])
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div class="alert alert-info">
        @if(request()->hasAny(['search', 'is_active']))
            لا توجد نتائج مطابقة لبحثك.
        @else
            لا توجد فئات مضافة حتى الآن.
        @endif
    </div>
@endif
