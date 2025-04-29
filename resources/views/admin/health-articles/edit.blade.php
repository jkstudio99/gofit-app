@extends('layouts.admin')

@section('title', 'แก้ไขบทความสุขภาพ - GoFit')

@section('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">

<style>
    .thumbnail-preview {
        height: 200px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .thumbnail-preview img {
        max-height: 100%;
        max-width: 100%;
        border-radius: 0.375rem;
        object-fit: contain;
    }
    .form-label {
        font-weight: 500;
    }
    .required-label::after {
        content: " *";
        color: #dc3545;
    }
    .note-editor {
        border-radius: 0.375rem;
    }
    .note-editor .note-toolbar {
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }
    .note-statusbar {
        border-bottom-left-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
    .seo-preview {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background-color: #fff;
    }
    .seo-preview-title {
        color: #1a0dab;
        font-size: 1.2rem;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .seo-preview-url {
        color: #006621;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    .seo-preview-description {
        color: #545454;
        font-size: 0.875rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .article-info-item {
        margin-bottom: 0.75rem;
    }
    .article-info-item i {
        width: 20px;
        margin-right: 0.5rem;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">แก้ไขบทความสุขภาพ</h1>
        <a href="{{ route('admin.health-articles.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> กลับไปรายการบทความ
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.health-articles.index') }}">บทความสุขภาพ</a></li>
        <li class="breadcrumb-item active">แก้ไขบทความ: {{ $article->title }}</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            แก้ไขข้อมูลบทความ
        </div>
        <div class="card-body">
            <form action="{{ route('admin.health-articles.update', $article->article_id) }}" method="POST" enctype="multipart/form-data" id="article-form">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label required-label">หัวข้อบทความ</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title', $article->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label required-label">Slug URL</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted">/articles/</span>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                    id="slug" name="slug" value="{{ old('slug', $article->slug) }}" required>
                                <button type="button" class="btn btn-outline-secondary" id="generate-slug">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <div class="form-text">URL ที่ใช้เข้าถึงบทความนี้ ควรเป็นภาษาอังกฤษ ตัวเลข และเครื่องหมาย - เท่านั้น</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="excerpt" class="form-label required-label">สรุปย่อบทความ</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror"
                                id="excerpt" name="excerpt" rows="3" maxlength="255" required>{{ old('excerpt', $article->excerpt) }}</textarea>
                            <div class="form-text">
                                <span id="excerpt-count">0</span>/255 ตัวอักษร (จะแสดงในหน้ารายการบทความและผลการค้นหา)
                            </div>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label required-label">เนื้อหาบทความ</label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                id="content" name="content" required>{{ old('content', $article->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="fas fa-image me-1"></i> ภาพปกบทความ
                            </div>
                            <div class="card-body">
                                <div class="thumbnail-preview {{ !$article->thumbnail ? 'empty' : '' }}" id="thumbnail-preview">
                                    @if($article->thumbnail)
                                        <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}">
                                    @else
                                        <div class="text-center">
                                            <i class="fas fa-image fa-3x mb-2"></i><br>
                                            ยังไม่ได้เลือกภาพ
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="thumbnail" class="form-label">อัพโหลดภาพใหม่</label>
                                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                        id="thumbnail" name="thumbnail" accept="image/*">
                                    <div class="form-text">ขนาดที่แนะนำ: 1200 x 630 พิกเซล (อัตราส่วน 1.91:1)</div>
                                    @error('thumbnail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="fas fa-cogs me-1"></i> การตั้งค่าบทความ
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label required-label">หมวดหมู่</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror"
                                        id="category_id" name="category_id" required>
                                        <option value="">-- เลือกหมวดหมู่ --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}" {{ old('category_id', $article->category_id) == $category->category_id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label required-label">สถานะ</label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                        <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>ฉบับร่าง</option>
                                        <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>เผยแพร่</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tags" class="form-label">แท็ก</label>
                                    <select class="form-select @error('tags') is-invalid @enderror"
                                        id="tags" name="tags[]" multiple>
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->tag_id }}" {{ in_array($tag->tag_id, old('tags', $article->tags->pluck('tag_id')->toArray())) ? 'selected' : '' }}>
                                                {{ $tag->tag_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">เลือกแท็กที่เกี่ยวข้องกับบทความนี้ (ไม่บังคับ)</div>
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="fas fa-info-circle me-1"></i> ข้อมูลบทความ
                            </div>
                            <div class="card-body">
                                <div class="article-info-item">
                                    <i class="fas fa-user text-muted"></i>
                                    ผู้สร้าง: {{ $article->author->username ?? 'ไม่ระบุ' }}
                                </div>
                                <div class="article-info-item">
                                    <i class="fas fa-calendar-alt text-muted"></i>
                                    สร้างเมื่อ: {{ $article->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="article-info-item">
                                    <i class="fas fa-edit text-muted"></i>
                                    แก้ไขล่าสุด: {{ $article->updated_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="article-info-item">
                                    <i class="fas fa-eye text-muted"></i>
                                    จำนวนผู้เข้าชม: {{ number_format($article->view_count) }} ครั้ง
                                </div>
                                <div class="article-info-item">
                                    <i class="fas fa-heart text-muted"></i>
                                    จำนวนการกดถูกใจ: {{ number_format($article->like_count) }} ครั้ง
                                </div>
                                <div class="article-info-item">
                                    <i class="fas fa-comment text-muted"></i>
                                    จำนวนความคิดเห็น: {{ number_format($article->comments_count) }} ความคิดเห็น
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="fas fa-search me-1"></i> การปรากฏบนผลค้นหา (SEO)
                            </div>
                            <div class="card-body">
                                <div class="seo-preview mb-3">
                                    <div class="seo-preview-title" id="seo-title">{{ $article->title }}</div>
                                    <div class="seo-preview-url" id="seo-url">{{ config('app.url') }}/articles/{{ $article->slug }}</div>
                                    <div class="seo-preview-description" id="seo-description">{{ $article->excerpt }}</div>
                                </div>

                                <div class="form-text text-center mb-3">
                                    ตัวอย่างการแสดงผลบนหน้าค้นหา
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back()">
                        ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-1"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('#category_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'เลือกหมวดหมู่'
        });

        $('#tags').select2({
            theme: 'bootstrap-5',
            placeholder: 'เลือกแท็กที่เกี่ยวข้อง',
            tags: true
        });

        // Initialize Summernote
        $('#content').summernote({
            height: 400,
            placeholder: 'เขียนเนื้อหาบทความของคุณที่นี่...',
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'table', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    uploadImage(files[0], this);
                }
            }
        });

        // Handle thumbnail preview
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailPreview = document.getElementById('thumbnail-preview');

        thumbnailInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    thumbnailPreview.innerHTML = `<img src="${e.target.result}" alt="Thumbnail Preview">`;
                    thumbnailPreview.classList.remove('empty');
                }

                reader.readAsDataURL(this.files[0]);
            }
        });

        // Handle excerpt character count
        const excerptInput = document.getElementById('excerpt');
        const excerptCount = document.getElementById('excerpt-count');

        excerptInput.addEventListener('input', function() {
            excerptCount.textContent = this.value.length;
        });

        // Trigger initial count
        excerptCount.textContent = excerptInput.value.length;

        // Generate slug from title
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        const generateSlugBtn = document.getElementById('generate-slug');

        generateSlugBtn.addEventListener('click', function() {
            if (titleInput.value) {
                slugInput.value = generateSlug(titleInput.value);
                updateSeoPreview();
            }
        });

        titleInput.addEventListener('blur', function() {
            if (titleInput.value && !slugInput.value) {
                slugInput.value = generateSlug(titleInput.value);
                updateSeoPreview();
            }
        });

        // Update SEO preview
        const seoTitle = document.getElementById('seo-title');
        const seoUrl = document.getElementById('seo-url');
        const seoDescription = document.getElementById('seo-description');

        titleInput.addEventListener('input', updateSeoPreview);
        slugInput.addEventListener('input', updateSeoPreview);
        excerptInput.addEventListener('input', updateSeoPreview);

        function updateSeoPreview() {
            seoTitle.textContent = titleInput.value || '{{ $article->title }}';
            seoUrl.textContent = `{{ config('app.url') }}/articles/${slugInput.value || '{{ $article->slug }}'}`;
            seoDescription.textContent = excerptInput.value || '{{ $article->excerpt }}';
        }

        // Function to upload image for Summernote
        function uploadImage(file, editor) {
            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('admin.upload.image') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.location) {
                    $(editor).summernote('insertImage', data.location);
                } else {
                    alert('Failed to upload image.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while uploading the image.');
            });
        }

        // Function to generate slug from title
        function generateSlug(text) {
            // Basic Thai to English transliteration map (simplified)
            const thaiMap = {
                'ก':'k', 'ข':'kh', 'ค':'kh', 'ฆ':'kh', 'ง':'ng',
                'จ':'ch', 'ฉ':'ch', 'ช':'ch', 'ซ':'s', 'ฌ':'ch',
                'ญ':'y', 'ฎ':'d', 'ฏ':'t', 'ฐ':'th', 'ฑ':'th',
                'ฒ':'th', 'ณ':'n', 'ด':'d', 'ต':'t', 'ถ':'th',
                'ท':'th', 'ธ':'th', 'น':'n', 'บ':'b', 'ป':'p',
                'ผ':'ph', 'ฝ':'f', 'พ':'ph', 'ฟ':'f', 'ภ':'ph',
                'ม':'m', 'ย':'y', 'ร':'r', 'ล':'l', 'ว':'w',
                'ศ':'s', 'ษ':'s', 'ส':'s', 'ห':'h', 'ฬ':'l',
                'อ':'a', 'ฮ':'h', 'ะ':'a', 'ั':'a', 'า':'a',
                'ำ':'am', 'ิ':'i', 'ี':'i', 'ึ':'ue', 'ื':'ue',
                'ุ':'u', 'ู':'u', 'เ':'e', 'แ':'ae', 'โ':'o',
                'ใ':'ai', 'ไ':'ai', '่':'', '้':'', '๊':'',
                '๋':'', '์':''
            };

            // Replace Thai characters with English equivalents
            let result = '';
            for (let i = 0; i < text.length; i++) {
                result += thaiMap[text[i]] || text[i];
            }

            // Convert to lowercase, remove special characters, replace spaces with hyphens
            return result.toLowerCase()
                .replace(/[^\w\s-]/g, '')  // Remove special characters
                .replace(/\s+/g, '-')      // Replace spaces with hyphens
                .replace(/-+/g, '-')       // Replace multiple hyphens with single hyphen
                .trim();                   // Trim leading/trailing spaces
        }
    });
</script>
@endsection
