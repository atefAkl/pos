@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            @if(isset($parent))
                إضافة فئة فرعية لـ: {{ $parent->name }}
            @else
                إضافة فئة رئيسية جديدة
            @endif
        </h1>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> عودة للفئات
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('categories.store') }}" method="POST" id="categoryForm">
                @csrf
                <input type="hidden" name="level" id="level" value="{{ $level ?? 1 }}">
                @if(isset($parent))
                    <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                @endif
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">اسم الفئة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                            id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($level > 1)
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label">الفئة الرئيسية</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" 
                                id="parent_id" name="parent_id" {{ $level == 1 ? 'disabled' : '' }}>
                                <option value="">-- اختر الفئة الرئيسية --</option>
                                @foreach($parentCategories as $parentCat)
                                    <option value="{{ $parentCat->id }}" 
                                        {{ (old('parent_id') == $parentCat->id || (isset($parent) && $parent->id == $parentCat->id)) ? 'selected' : '' }}>
                                        {{ $parentCat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    @if($level < 3)
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                يمكنك إضافة فئات فرعية لهذه الفئة بعد حفظها.
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6 mb-3">
                        <label for="is_active" class="form-label">الحالة</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" 
                                id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ الفئة
                        </button>
                        
                        @if(isset($parent) && $parent->parent_id)
                            <a href="{{ route('categories.create', ['parent_id' => $parent->parent_id]) }}" class="btn btn-secondary">
                                <i class="fas fa-level-up-alt"></i> مستوى أعلى
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle parent category change
    const parentSelect = document.getElementById('parent_id');
    if (parentSelect) {
        parentSelect.addEventListener('change', function() {
            const parentId = this.value;
            if (parentId) {
                // You can add AJAX call here to load sub-categories if needed
                console.log('Parent category changed to:', parentId);
            }
        });
    }
    
    // Form validation
    const form = document.getElementById('categoryForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            if (!name) {
                e.preventDefault();
                alert('الرجاء إدخال اسم الفئة');
                return false;
            }
            return true;
        });
    }
});
</script>
@endpush

@endsection
