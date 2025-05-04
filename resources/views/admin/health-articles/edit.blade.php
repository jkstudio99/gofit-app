@extends('layouts.admin')

@section('title', 'แก้ไขบทความสุขภาพ - GoFit')

@section('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<style>
    .thumbnail-preview-container {
        width: 100%;
        height: 200px;
        border: 2px dashed #ddd;
        border-radius: 5px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        transition: all 0.3s;
    }
    .thumbnail-preview-container:hover {
        border-color: #adb5bd;
    }
    .thumbnail-preview {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .thumbnail-placeholder {
        color: #adb5bd;
        text-align: center;
    }
    .thumbnail-upload-btn {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: all 0.2s;
    }
    .thumbnail-upload-btn:hover {
        transform: scale(1.1);
    }
    label.required:after {
        content: " *";
        color: red;
    }
    .form-label {
        font-weight: 500;
    }
    .select2-container--default .select2-selection--multiple {
        border-color: #ced4da;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #2DC679;
        border-color: #27B36D;
        color: white;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
    }
    .seo-preview {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 15px;
        margin-top: 10px;
    }
    .seo-preview-title {
        color: #1a0dab;
        font-size: 18px;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .seo-preview-url {
        color: #006621;
        font-size: 14px;
        margin-bottom: 5px;
    }
    .seo-preview-description {
        color: #545454;
        font-size: 14px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .char-counter {
        font-size: 12px;
        color: #6c757d;
        text-align: right;
        margin-top: 4px;
    }
    .article-settings-card {
        transition: all 0.3s;
    }
    .article-settings-card .card-header {
        cursor: pointer;
    }
    .article-settings-card .card-header:hover {
        background-color: #f8f9fa;
    }
    .note-editor .note-editable {
        min-height: 300px;
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
        <div>
            <a href="{{ route('admin.health-articles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการบทความ
            </a>
        </div>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">แดชบอร์ด</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.health-articles.index') }}">บทความสุขภาพ</a></li>
        <li class="breadcrumb-item active">แก้ไขบทความ: {{ $article->title }}</li>
    </ol>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>เกิดข้อผิดพลาด</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form id="article-form" action="{{ route('admin.health-articles.update', $article->article_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-edit me-1"></i>
                        รายละเอียดบทความ
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label required">ชื่อบทความ</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $article->title) }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label required">Slug (URL)</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted">/articles/</span>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $article->slug) }}" required>
                                <button type="button" class="btn btn-outline-secondary" id="generate-slug">
                                    <i class="fas fa-sync-alt me-1"></i> สร้างอัตโนมัติ
                                </button>
                            </div>
                            <div class="form-text">URL ที่ใช้เข้าถึงบทความนี้ ควรเป็นภาษาอังกฤษ ตัวพิมพ์เล็ก และใช้เครื่องหมายยัติภังค์ (-) แทนช่องว่าง</div>
                            @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="excerpt" class="form-label required">บทคัดย่อ</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3" maxlength="255" required>{{ old('excerpt', $article->excerpt) }}</textarea>
                            <div class="char-counter"><span id="excerpt-char-count">0</span>/255 ตัวอักษร</div>
                            <div class="form-text">สรุปเนื้อหาสั้นๆ ที่จะแสดงในหน้ารายการบทความ</div>
                            @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label required">เนื้อหาบทความ</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" required>{{ old('content', $article->content) }}</textarea>
                            @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header" data-bs-toggle="collapse" data-bs-target="#seoCcollapse" aria-expanded="true" aria-controls="seoCcollapse">
                        <i class="fas fa-search me-1"></i>
                        ข้อมูล SEO (การค้นหาและการแสดงผล)
                        <i class="fas fa-chevron-down float-end mt-1"></i>
                    </div>
                    <div class="collapse show" id="seoCcollapse">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $article->meta_title) }}">
                                <div class="form-text">หากไม่ระบุ จะใช้ชื่อบทความแทน</div>
                            </div>

                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="160">{{ old('meta_description', $article->meta_description) }}</textarea>
                                <div class="char-counter"><span id="meta-desc-char-count">0</span>/160 ตัวอักษร</div>
                                <div class="form-text">คำอธิบายที่จะแสดงในผลการค้นหา หากไม่ระบุ จะใช้บทคัดย่อแทน</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ตัวอย่างผลการค้นหา</label>
                                <div class="seo-preview">
                                    <div class="seo-preview-title" id="seo-preview-title">{{ $article->meta_title ?: $article->title }} - GoFit</div>
                                    <div class="seo-preview-url" id="seo-preview-url">gofit.app/articles/{{ $article->slug }}</div>
                                    <div class="seo-preview-description" id="seo-preview-description">{{ $article->meta_description ?: $article->excerpt }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-image me-1"></i>
                        รูปภาพปกบทความ
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="thumbnail-preview-container mb-3" id="thumbnail-preview-container">
                                @if($article->thumbnail)
                                    <img id="thumbnail-preview" class="thumbnail-preview" src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}">
                                @else
                                    <div class="thumbnail-placeholder" id="thumbnail-placeholder">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p class="mb-0">คลิกเพื่อเลือกรูปภาพปก</p>
                                    </div>
                                    <img id="thumbnail-preview" class="thumbnail-preview d-none" src="#" alt="รูปภาพปกบทความ">
                                @endif
                                <div class="thumbnail-upload-btn">
                                    <i class="fas fa-upload"></i>
                                </div>
                            </div>
                            <input type="file" class="d-none @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail" accept="image/*">
                            <div class="form-text">แนะนำขนาด 1200 x 630 pixels (อัตราส่วน 1.91:1)</div>
                            @error('thumbnail')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4 article-settings-card">
                    <div class="card-header">
                        <i class="fas fa-cog me-1"></i>
                        ตั้งค่าบทความ
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="category_id" class="form-label required">หมวดหมู่</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">เลือกหมวดหมู่</option>
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
                            <label for="tags" class="form-label">แท็ก</label>
                            <select class="form-select @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple>
                                @foreach($tags as $tag)
                                @php
                                    $selectedTagIds = [];
                                    try {
                                        $selectedTagIds = $article->tags->pluck('tag_id')->toArray();
                                    } catch (\Exception $e) {
                                        // Silently fail if tags relationship encounters an error
                                    }
                                @endphp
                                <option value="{{ $tag->tag_id }}" {{ in_array($tag->tag_id, old('tags', $selectedTagIds)) ? 'selected' : '' }}>
                                    {{ $tag->tag_name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text">เลือกแท็กที่เกี่ยวข้องกับบทความนี้</div>
                            @error('tags')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label required">สถานะ</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="draft" {{ old('status', $article->status ?? ($article->is_published ? 'published' : 'draft')) == 'draft' ? 'selected' : '' }}>ฉบับร่าง</option>
                                <option value="published" {{ old('status', $article->status ?? ($article->is_published ? 'published' : 'draft')) == 'published' ? 'selected' : '' }}>เผยแพร่</option>
                            </select>
                            <div class="form-text">ฉบับร่างจะไม่แสดงในหน้าเว็บไซต์</div>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
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
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-1"></i> บันทึกบทความ
                    </button>
                    <a href="{{ route('admin.health-articles.index') }}" class="btn btn-outline-secondary">
                        ยกเลิก
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<!-- jQuery (required for Select2 and Summernote) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for category dropdown - must come before setting value
        $('#category_id').select2({
            placeholder: 'เลือกหมวดหมู่',
            allowClear: false
        });

        // Initialize Select2 for tags - must come before setting values
        $('#tags').select2({
            placeholder: 'เลือกแท็ก (สามารถเลือกได้หลายแท็ก)',
            allowClear: true,
            tags: true,
            createTag: function(params) {
                return {
                    id: params.term,
                    text: params.term,
                    newTag: true
                };
            }
        });

        // Make sure selected values are properly set
        // This forces Select2 to recognize the pre-selected options
        var selectedTags = [];
        try {
            selectedTags = {{ json_encode(old('tags', $selectedTagIds ?? [])) }};
        } catch (e) {
            console.error("Error loading selected tags:", e);
        }
        $('#tags').val(selectedTags).trigger('change');

        var selectedCategory = {{ old('category_id', $article->category_id ?? 'null') }};
        if (selectedCategory) {
            $('#category_id').val(selectedCategory).trigger('change');
        }

        // Initialize Summernote with existing content
        $('#content').summernote({
            placeholder: 'เขียนเนื้อหาบทความที่นี่...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    for (let i = 0; i < files.length; i++) {
                        uploadImage(files[i]);
                    }
                }
            }
        });

        // Function to upload image
        function uploadImage(file) {
            let formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("admin.health-articles.upload-image") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#content').summernote('insertImage', data.url);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error(textStatus + ": " + errorThrown);
                    alert('Error uploading image: ' + errorThrown);
                }
            });
        }

        // Thumbnail Preview
        $('#thumbnail-preview-container').click(function() {
            $('#thumbnail').click();
        });

        $('#thumbnail').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#thumbnail-preview').attr('src', e.target.result).removeClass('d-none');
                    $('#thumbnail-placeholder').addClass('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                $('#thumbnail-preview').addClass('d-none');
                $('#thumbnail-placeholder').removeClass('d-none');
            }
        });

        // Character counter for excerpt
        $('#excerpt').on('input', function() {
            let count = $(this).val().length;
            $('#excerpt-char-count').text(count);
            if (count > 255) {
                $('#excerpt-char-count').addClass('text-danger');
            } else {
                $('#excerpt-char-count').removeClass('text-danger');
            }
        });

        // Character counter for meta description
        $('#meta_description').on('input', function() {
            let count = $(this).val().length;
            $('#meta-desc-char-count').text(count);
            if (count > 160) {
                $('#meta-desc-char-count').addClass('text-danger');
            } else {
                $('#meta-desc-char-count').removeClass('text-danger');
            }
        });

        // Generate slug from title
        $('#generate-slug').click(function() {
            const title = $('#title').val();
            if (title) {
                const slug = generateSlug(title);
                $('#slug').val(slug);
                updateSeoPreview();
            }
        });

        // Basic Thai to English transliteration map
        const thaiToEnglish = {
            'ก': 'k', 'ข': 'kh', 'ค': 'kh', 'ฆ': 'kh', 'ง': 'ng',
            'จ': 'ch', 'ฉ': 'ch', 'ช': 'ch', 'ซ': 's', 'ฌ': 'ch',
            'ญ': 'y', 'ฎ': 'd', 'ฏ': 't', 'ฐ': 'th', 'ฑ': 'th',
            'ฒ': 'th', 'ณ': 'n', 'ด': 'd', 'ต': 't', 'ถ': 'th',
            'ท': 'th', 'ธ': 'th', 'น': 'n', 'บ': 'b', 'ป': 'p',
            'ผ': 'ph', 'ฝ': 'f', 'พ': 'ph', 'ฟ': 'f', 'ภ': 'ph',
            'ม': 'm', 'ย': 'y', 'ร': 'r', 'ล': 'l', 'ว': 'w',
            'ศ': 's', 'ษ': 's', 'ส': 's', 'ห': 'h', 'ฬ': 'l',
            'อ': 'o', 'ฮ': 'h', 'ะ': 'a', 'ั': 'a', 'า': 'a',
            'ำ': 'am', 'ิ': 'i', 'ี': 'i', 'ึ': 'ue', 'ื': 'ue',
            'ุ': 'u', 'ู': 'u', 'เ': 'e', 'แ': 'ae', 'โ': 'o',
            'ใ': 'ai', 'ไ': 'ai', '่': '', '้': '', '๊': '',
            '๋': '', '็': '', '์': '', 'ํ': ''
        };

        function generateSlug(text) {
            // Transliterate Thai characters
            let slugText = '';
            for (let i = 0; i < text.length; i++) {
                const char = text[i];
                slugText += thaiToEnglish[char] || char;
            }

            // Convert to lowercase, replace spaces with hyphens, remove special chars
            return slugText
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
                .trim(); // Trim leading/trailing spaces
        }

        // Update SEO preview
        function updateSeoPreview() {
            const title = $('#meta_title').val() || $('#title').val() || '{{ $article->title }}';
            const slug = $('#slug').val() || '{{ $article->slug }}';
            const description = $('#meta_description').val() || $('#excerpt').val() || '{{ $article->excerpt }}';

            $('#seo-preview-title').text(title + ' - GoFit');
            $('#seo-preview-url').text('gofit.app/articles/' + slug);
            $('#seo-preview-description').text(description);
        }

        // Update SEO preview on input changes
        $('#title, #meta_title, #slug, #excerpt, #meta_description').on('input', updateSeoPreview);

        // Initialize with existing values
        $('#excerpt').trigger('input');
        $('#meta_description').trigger('input');
        updateSeoPreview();
    });
</script>
@endsection
