@if($categories->isNotEmpty())
<style>
    .btn-group button, 
    .btn-group a, 
    .btn-group .btn {
        padding: 0;
        height: 30px;
        max-width: 40px;
        margin: 0;
        font-size: 18px !important;
    }
    .btn-group a i,
    .btn-group button i,
    .btn-group .btn i {
        font-size: 18px !important;
    }
    
    .search-highlight {
        background-color: #fff3cd;
        padding: 0 2px;
        border-radius: 3px;
    }
</style>
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
                            <span class="me-2 toggle-children" data-category-id="{{ $category->id }}">
                                @if($category->children->isNotEmpty())
                                    <i class="fas fa-chevron-down"></i>
                                @else
                                    <i class="fas fa-chevron-right text-muted"></i>
                                @endif
                            </span>
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
                            <span class="badge bg-secondary ms-2">{{ $category->products_count }} منتج</span>
                            
                            @if($hasSearch && $category->is_active === false)
                                <span class="badge bg-warning text-dark ms-2">غير نشط</span>
                            @endif
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn text-primary" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn text-danger delete-category" data-id="{{ $category->id }}" data-name="{{ $category->name }}" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                            @if($category->level < 3)
                            <a href="{{ route('categories.create', ['parent_id' => $category->id]) }}" class="btn text-success" title="إضافة فرعي">
                                <i class="fas fa-plus"></i>
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
