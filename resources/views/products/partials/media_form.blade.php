<form id="mediaForm" action="{{ route('products.update-media', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label for="image" class="form-label">صورة المنتج الرئيسية</label>
        <input required class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" 
               accept="image/*" onchange="previewImage(this, 'imagePreview')">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-images"></i> حفظ الوسائط
        </button>
    </div>
</form>


<form id="mediaForm" action="{{ route('products.update-media', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
        
    
    <div class="mb-3 input-group">
        <label for="gallery" class="input-group">معرض الصور (يمكن اختيار أكثر من صورة)</label>
        <input class="form-control @error('gallery.*') is-invalid @enderror" type="file" 
               id="gallery" name="gallery[]" multiple accept="image/*">
        <button type="submit" class="input-group-text btn-primary">
            <i class="fas fa-images"></i> حفظ الوسائط
        </button>
        @error('gallery.*')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</form>
<div class="row pb-3" id="galleryPreview">
    @if($product->image)
        <div class="col col-auto mb-2" style="height: 100px;">
            <img id="imagePreview" src="{{ asset('uploads/' . $product->image) }}" 
                    alt="صورة المنتج" class="img-thumbnail" style="height: 100px;">
            <form method="POST" action="{{ route('product-images.destroy', $product->image) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الصورة؟');">
                @csrf
                @method('DELETE')
                <button id="productImageDeleteBtn" type="submit" class="btn btn-danger btn-sm mt-1 w-100">
                    <i class="fas fa-trash"></i> حذف
                </button>
            </form>
            </div>
        @else
            <div class="col col-auto mb-2">
                <img id="imagePreview" src="{{ asset('img/no-image.png') }}" 
                        alt="لا توجد صورة" class="img-thumbnail" style="height: 100px; display: none;">
            </div>
        @endif
        
        <script>
            const imageWidth = document.getElementById('imagePreview').offsetWidth;
            const productImageDeleteBtn = document.getElementById('productImageDeleteBtn')
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('imgwdth').textContent = imageWidth;
                productImageDeleteBtn.style.width = imageWidth + 'px';
            });
        </script>
        
        
        @if($product->images?->count() > 0)
            
            @foreach($product->images as $image)
                <div class="col col-auto mb-2" id="image-{{ $image->id }}" style="height: 100px;">
                    <img src="{{ asset('uploads/' . $image->path) }}" class="img-thumbnail" style="height: 100px;">
                    <form method="POST" action="{{ route('product-images.destroy', $image) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الصورة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm mt-1 w-100">
                            <i class="fas fa-trash"></i> &nbsp; حذف
                        </button>
                    </form>
                </div>
            @endforeach
        @endif
    </div>

@push('scripts')
<script>
// معاينة الصورة قبل الرفع
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.src = "{{ $product->image ? asset('uploads/products/gallery/' . $product->image) : asset('img/no-image.png') }}";
    }
}
// I'm here

</script>
@endpush
