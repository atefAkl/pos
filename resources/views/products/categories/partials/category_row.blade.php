@php
    $indent = $level * 20; // 20px for each level
    $hasChildren = $category->children->isNotEmpty();
    $rowClass = '';
    $nameClass = 'ps-2';
    
    if ($level === 1) {
        $rowClass = 'child-row';
        $nameClass = 'ps-4';
    } elseif ($level >= 2) {
        $rowClass = 'grandchild-row';
        $nameClass = 'ps-5';
    }
    
    $statusClass = $category->is_active ? 'success' : 'danger';
    $statusText = $category->is_active ? 'نشط' : 'غير نشط';
    $levelBadgeClass = [
        1 => 'bg-primary',
        2 => 'bg-info',
        3 => 'bg-secondary'
    ][$category->level] ?? 'bg-secondary';
@endphp

<tr class="category-row {{ $rowClass }} {{ $loop->even ? 'table-active' : '' }}" 
    data-id="{{ $category->id }}" 
    data-parent-id="{{ $category->parent_id }}"
    data-level="{{ $category->level }}">
    
    <td class="text-center">
        @if($hasChildren)
            <button type="button" class="toggle-children" data-bs-toggle="tooltip" title="عرض/إخفاء الفئات الفرعية">
                <i class="fas fa-plus"></i>
            </button>
        @endif
    </td>
    
    <td class="category-name {{ $nameClass }}">
        <div class="d-flex align-items-center">
            @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" 
                     class="rounded me-2" width="32" height="32" style="object-fit: cover;">
            @else
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded me-2" 
                     style="width: 32px; height: 32px;">
                    <i class="fas fa-folder text-warning"></i>
                </div>
            @endif
            
            <div>
                <div class="fw-medium">{{ $category->name }}</div>
                @if($category->code)
                    <small class="text-muted">{{ $category->code }}</small>
                @endif
            </div>
        </div>
    </td>
    
    <td class="text-center">
        <span class="badge {{ $levelBadgeClass }}">
            {{ $category->level === 1 ? 'رئيسي' : 'فرعي' }} ({{ $category->level }})
        </span>
    </td>
    
    <td>
        @if($category->description)
            <div class="text-truncate" style="max-width: 250px;" 
                 data-bs-toggle="tooltip" title="{{ $category->description }}">
                {{ $category->description }}
            </div>
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    
    <td class="text-center">
        <span class="badge bg-primary rounded-pill">
            {{ $category->products_count }}
        </span>
    </td>
    
    <td class="text-center">
        <span class="badge bg-{{ $statusClass }}">
            {{ $statusText }}
        </span>
    </td>
    
    <td class="text-center">
        <div class="btn-group btn-group-sm" role="group">
            
            @if($category->level < 3)
                <a href="{{ route('categories.create', ['parent_id' => $category->id]) }}" 
                   class="btn btn-sm p-0"
                   data-bs-toggle="tooltip" title="إضافة فرعي">
                    <i class="fas fa-plus"></i>
                </a>
            @endif
            
            <a href="{{ route('categories.edit', $category->id) }}" 
               class="btn btn-sm p-0"
               data-bs-toggle="tooltip" title="تعديل">
                <i class="fas fa-edit"></i>
            </a>
           
            @if(!$category->products_count > 0 || !$hasChildren)
            <button type="button" 
                    class="btn btn-sm p-0"
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteCategoryModal"
                    data-category-id="{{ $category->id }}"
                    
                    data-bs-toggle="tooltip" 
                    title="{{ $category->products_count > 0 ? 'لا يمكن حذف فئة تحتوي على منتجات' : ($hasChildren ? 'لا يمكن حذف فئة تحتوي على فئات فرعية' : 'حذف') }}">
                <i class="fas fa-trash"></i>
            </button>
            @endif
        </div>
    </td>
</tr>

@if($hasChildren)
    @foreach($category->children as $child)
        @include('products.categories.partials.category_row', [
            'category' => $child, 
            'level' => $level + 1
        ])
    @endforeach
@endif
