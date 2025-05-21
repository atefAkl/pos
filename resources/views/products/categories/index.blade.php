@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            @if(request()->has('parent_id'))
                الفئات الفرعية
            @else
                الفئات الرئيسية
            @endif
        </h1>
        <div>
            @if(request()->has('parent_id'))
                <a href="{{ route('categories.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-level-up-alt"></i> العودة للفئات الرئيسية
                </a>
            @endif
            <a href="{{ route('categories.create', ['parent_id' => request('parent_id')]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ request()->has('parent_id') ? 'إضافة فئة فرعية' : 'إضافة فئة رئيسية' }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                @if(request()->has('parent_id'))
                    الفئات الفرعية
                @else
                    قائمة الفئات الرئيسية
                @endif
            </h6>
            <div class="d-flex">
                @if(request()->has('parent_id'))
                    @php $parent = \App\Models\Category::find(request('parent_id')) @endphp
                    @if($parent && $parent->parent_id)
                        <a href="{{ route('categories.index', ['parent_id' => $parent->parent_id]) }}" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-level-up-alt"></i> مستوى أعلى
                        </a>
                    @endif
                @endif
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#categoryFilters">
                    <i class="fas fa-filter"></i> تصفية
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="collapse mb-4" id="categoryFilters">
                <form action="{{ route('categories.index') }}" method="GET" class="row g-3">
                    <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">
                    <div class="col-md-4">
                        <label for="search" class="form-label">بحث</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو الوصف...">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">الحالة</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">الكل</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="level" class="form-label">المستوى</label>
                        <select class="form-select" id="level" name="level">
                            <option value="">الكل</option>
                            <option value="1" {{ request('level') === '1' ? 'selected' : '' }}>رئيسي (1)</option>
                            <option value="2" {{ request('level') === '2' ? 'selected' : '' }}>فرعي (2)</option>
                            <option value="3" {{ request('level') === '3' ? 'selected' : '' }}>فرعي (3)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('categories.index', ['parent_id' => request('parent_id')]) }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> إعادة تعيين
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="categoriesTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>اسم الفئة</th>
                            <th>المستوى</th>
                            <th>الوصف</th>
                            <th width="10%" class="text-center">عدد المنتجات</th>
                            <th width="10%" class="text-center">الحالة</th>
                            <th width="15%" class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            @include('products.categories.partials.category_row', ['category' => $category, 'level' => 0])
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">لا توجد فئات متاحة</p>
                                        <a href="{{ route('categories.create', ['parent_id' => request('parent_id')]) }}" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-plus"></i> إضافة فئة جديدة
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCategoryModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في حذف هذه الفئة؟</p>
                <p class="mb-0"><strong>ملاحظة:</strong> لا يمكن حذف الفئة إذا كانت تحتوي على منتجات أو فئات فرعية.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteCategoryForm" method="POST" action="{{ route('categories.destroy', $category->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize delete modal
    const deleteModal = document.getElementById('deleteCategoryModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const categoryId = button.getAttribute('data-category-id');
            const form = deleteModal.querySelector('#deleteCategoryForm');
            form.action = `{{ url('dashboard/categories') }}/${categoryId}`;
        });
    }

    // Toggle child rows
    document.querySelectorAll('.toggle-children').forEach(button => {
        button.addEventListener('click', function() {
            const parentRow = this.closest('tr');
            const parentId = parentRow.dataset.id;
            const childRows = document.querySelectorAll(`tr[data-parent-id="${parentId}"]`);
            
            childRows.forEach(row => {
                row.classList.toggle('d-none');
            });
            
            this.innerHTML = this.innerHTML.includes('fa-plus') ? 
                '<i class="fas fa-minus"></i>' : 
                '<i class="fas fa-plus"></i>';
        });
    });
});
</script>
@endpush

<style>
    .category-row td {
        vertical-align: middle;
    }
    .category-name {
        font-weight: 500;
    }
    .category-actions .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .child-row {
        background-color: rgba(0, 0, 0, 0.02);
    }
    .child-row .category-name {
        padding-left: 30px;
    }
    .grandchild-row .category-name {
        padding-left: 60px;
    }
    .toggle-children {
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 0 5px;
    }
    .toggle-children:focus {
        outline: none;
        box-shadow: none;
    }
</style>
@endsection

@push('styles')
<style>
    .btn-group {
        gap: 5px;
    }
</style>
@endpush
