@extends('layouts.admin')

@section('title', 'เพิ่มรางวัลใหม่')

@section('content_header')
    <h1>เพิ่มรางวัลใหม่</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">กรอกข้อมูลรางวัล</h3>
        </div>

        <form action="{{ route('admin.rewards.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="name">ชื่อรางวัล <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label for="description">คำอธิบาย <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="points_required">คะแนนที่ใช้แลก <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('points_required') is-invalid @enderror" id="points_required" name="points_required" min="1" value="{{ old('points_required', 10) }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-coins text-warning"></i></span>
                                </div>
                            </div>
                            @error('points_required')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="quantity">จำนวนสินค้า <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" min="0" value="{{ old('quantity', 1) }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-cubes"></i></span>
                                </div>
                            </div>
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="image_path">รูปภาพรางวัล</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('image_path') is-invalid @enderror" id="image_path" name="image_path" accept="image/*">
                            <label class="custom-file-label" for="image_path">เลือกไฟล์รูปภาพ</label>
                        </div>
                    </div>
                    <small class="text-muted">* แนะนำขนาดรูปภาพ 500x500 พิกเซล (สูงสุด 2MB)</small>
                    @error('image_path')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="mt-2 d-none" id="image-preview-container">
                        <img id="image-preview" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                </div>

                <div class="form-group mt-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_enabled" name="is_enabled" checked>
                        <label class="custom-control-label" for="is_enabled">เปิดใช้งาน</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">บันทึก</button>
                <a href="{{ route('admin.rewards') }}" class="btn btn-default">ยกเลิก</a>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        .card-footer {
            background-color: #f8f9fa;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            // Show image preview
            $('#image_path').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-preview').attr('src', e.target.result);
                        $('#image-preview-container').removeClass('d-none');
                    }
                    reader.readAsDataURL(file);

                    // Display filename
                    $(this).next('.custom-file-label').html(file.name);
                } else {
                    $('#image-preview-container').addClass('d-none');
                    $(this).next('.custom-file-label').html('เลือกไฟล์รูปภาพ');
                }
            });
        });
    </script>
@stop
