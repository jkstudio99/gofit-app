@extends('layouts.app')

@section('title', 'ยืนยันการลบบัญชี')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-danger text-white border-0">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ยืนยันการลบบัญชี
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-warning mb-4">
                        <h6 class="alert-heading fw-bold"><i class="fas fa-exclamation-circle me-2"></i>คำเตือน!</h6>
                        <p class="mb-0">การลบบัญชีเป็นการกระทำที่ไม่สามารถย้อนกลับได้ และจะส่งผลดังนี้:</p>
                        <ul class="mt-2 mb-0">
                            <li>ข้อมูลส่วนตัวของคุณทั้งหมดจะถูกลบออกจากระบบ</li>
                            <li>ประวัติกิจกรรมการวิ่งทั้งหมดจะหายไป</li>
                            <li>ข้อมูลเป้าหมายและความสำเร็จต่างๆ จะถูกลบ</li>
                            <li>คุณจะไม่สามารถเข้าถึงข้อมูลที่เคยมีในระบบได้อีก</li>
                            <li>การลงทะเบียนเข้าร่วมกิจกรรมต่างๆ จะถูกยกเลิก</li>
                        </ul>
                    </div>

                    <h6 class="mb-3 fw-bold">โปรดยืนยันการลบบัญชีของคุณ</h6>

                    <form method="POST" action="{{ route('profile.delete') }}">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label for="password" class="form-label">กรุณากรอกรหัสผ่านปัจจุบันเพื่อยืนยัน</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm" required>
                            <label class="form-check-label" for="confirm">
                                ฉันเข้าใจว่าการกระทำนี้ไม่สามารถยกเลิกได้ และข้อมูลทั้งหมดของฉันจะหายไปอย่างถาวร
                            </label>
                        </div>

                        <div class="d-flex flex-column flex-sm-row">
                            <a href="{{ route('profile.edit') }}" class="btn btn-secondary mb-2 mb-sm-0 me-sm-2">
                                <i class="fas fa-arrow-left me-1"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-1"></i> ลบบัญชีของฉัน
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Card styling */
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
    }

    /* Light border color */
    .border {
        border-color: rgba(0,0,0,0.08) !important;
    }

    /* Card header styling */
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.08);
        padding: 1rem 1.25rem;
    }

    /* Button styling - ensure icons and hover text are white */
    .btn-danger, .btn-danger:hover, .btn-danger:focus {
        color: #fff !important;
    }

    .btn-danger i, .btn-danger:hover i {
        color: #fff !important;
    }

    .btn-secondary, .btn-secondary:hover, .btn-secondary:focus {
        color: #fff !important;
    }

    .btn-secondary i, .btn-secondary:hover i {
        color: #fff !important;
    }

    .btn-outline-danger:hover {
        color: #fff !important;
    }

    .btn-outline-danger:hover i {
        color: #fff !important;
    }

    /* Alert responsiveness */
    @media (max-width: 767.98px) {
        .alert ul {
            padding-left: 1.5rem;
        }

        .alert ul li {
            margin-bottom: 0.25rem;
        }

        .form-check-label {
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function(e) {
            if (!confirm('คุณแน่ใจหรือไม่ที่จะลบบัญชีของคุณ? การกระทำนี้ไม่สามารถยกเลิกได้')) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endsection
