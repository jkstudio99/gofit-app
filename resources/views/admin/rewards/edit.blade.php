@extends('layouts.admin')

@section('title', 'แก้ไขรางวัล')

@section('content_header')
    <h1>แก้ไขรางวัล</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">แก้ไขข้อมูลรางวัล: {{ $reward->name }}</h3>
        </div>

        <form action="{{ route('admin.rewards.update', $reward->reward_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $reward->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label for="description">คำอธิบาย <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description', $reward->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="points_required">คะแนนที่ใช้แลก <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('points_required') is-invalid @enderror" id="points_required" name="points_required" min="1" value="{{ old('points_required', $reward->points_required) }}" required>
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
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" min="0" value="{{ old('quantity', $reward->quantity) }}" required>
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
                            <label class="custom-file-label" for="image_path">{{ $reward->image_path ? 'เปลี่ยนรูปภาพ' : 'เลือกไฟล์รูปภาพ' }}</label>
                        </div>
                    </div>
                    <small class="text-muted">* แนะนำขนาดรูปภาพ 500x500 พิกเซล (สูงสุด 2MB)</small>
                    @error('image_path')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="mt-2 {{ $reward->image_path ? '' : 'd-none' }}" id="image-preview-container">
                        @if($reward->image_path)
                            <img src="{{ asset('storage/' . $reward->image_path) }}" id="image-preview" class="img-thumbnail" style="max-height: 200px;">
                        @else
                            <img id="image-preview" class="img-thumbnail" style="max-height: 200px;">
                        @endif
                    </div>

                    @if($reward->image_path)
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                        <label class="form-check-label text-danger" for="remove_image">
                            ลบรูปภาพนี้
                        </label>
                    </div>
                    @endif
                </div>

                <div class="form-group mt-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_enabled" name="is_enabled" {{ $reward->is_enabled ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_enabled">เปิดใช้งาน</label>
                    </div>
                </div>

                <div class="card bg-light mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">ข้อมูลเพิ่มเติม</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>วันที่สร้าง:</strong> {{ $reward->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>จำนวนการแลก:</strong> {{ $reward->redeems->count() ?? 0 }} ครั้ง</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>แก้ไขล่าสุด:</strong> {{ $reward->updated_at->format('d/m/Y H:i') }}</p>
                                <p><strong>สถานะ:</strong>
                                    @if($reward->is_enabled)
                                        <span class="badge bg-success">เปิดใช้งาน</span>
                                    @else
                                        <span class="badge bg-secondary">ปิดใช้งาน</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
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

                    // Uncheck remove image if a new one is selected
                    $('#remove_image').prop('checked', false);
                }
            });

            // Hide image preview when remove checkbox is checked
            $('#remove_image').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#image-preview-container').addClass('d-none');
                } else {
                    $('#image-preview-container').removeClass('d-none');
                }
            });
        });
    </script>
@stop
