@extends('layouts.auth')

@section('content')
<div class="auth-page">
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-logo">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="GoFit Logo" style="height: 2.5rem;">
                </a>
            </div>

            <h1 class="auth-title">{{ __('เข้าสู่ระบบ') }}</h1>
            <p class="auth-subtitle">ยินดีต้อนรับกลับ! เข้าสู่ระบบเพื่อดูความก้าวหน้า</p>

            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                        @csrf

                <div class="mb-4">
                    <label for="username" class="form-label fw-medium">{{ __('ชื่อผู้ใช้') }}</label>
                    <div class="input-icon-group">
                        <span class="input-icon"><i class="bx bx-user"></i></span>
                        <input id="username" type="text" class="form-control form-control-lg @error('username') is-invalid @enderror @error('email') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus placeholder="ชื่อผู้ใช้ของคุณ">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                                @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-medium">{{ __('รหัสผ่าน') }}</label>
                    <div class="input-icon-group">
                        <span class="input-icon"><i class="bx bx-lock"></i></span>
                        <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="รหัสผ่านของคุณ">
                        <button type="button" class="toggle-password" toggle="#password">
                            <i class="bx bx-show"></i>
                        </button>
                                @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                {{ __('จดจำฉันไว้') }}
                                    </label>
                        </div>
                                @if (Route::has('password.request'))
                            <a class="text-decoration-none" style="color: var(--color-primary);" href="{{ route('password.request') }}">
                                {{ __('ลืมรหัสผ่าน?') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-gofit w-100 py-3 fw-medium">
                        <i class="bx bx-log-in-circle me-2"></i>{{ __('เข้าสู่ระบบ') }}
                    </button>
                </div>

                <div class="text-center">
                    <p class="mb-0">{{ __('ยังไม่มีบัญชี?') }} <a href="{{ route('register') }}" style="color: var(--color-primary);" class="text-decoration-none fw-medium">{{ __('ลงทะเบียนเลย') }}</a></p>
                </div>
                    </form>
        </div>
    </div>

    <div class="auth-image-side">
        <div class="auth-image-shape shape-1"></div>
        <div class="auth-image-shape shape-2"></div>
        <div class="auth-image-content">
            <img src="{{ asset('images/login-cover-right.png') }}" alt="GoFit Dashboard" class="img-fluid">
        </div>
    </div>
</div>

<!-- Modal สำหรับเงื่อนไขการใช้งาน -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">เงื่อนไขการใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">อัปเดตล่าสุด: {{ date('d/m/Y') }}</p>

                <div class="terms-content">
                    <section class="mb-4">
                        <h2 class="h5 mb-3">1. การยอมรับเงื่อนไข</h2>
                        <p>การใช้งานแอปพลิเคชัน GoFit ("บริการ") ที่ดำเนินการโดย GoFit ("บริษัท", "เรา", "ของเรา") ถือว่าคุณได้ยอมรับและปฏิบัติตามเงื่อนไขและข้อตกลงเหล่านี้ หากคุณไม่เห็นด้วยกับส่วนใดของเงื่อนไขเหล่านี้ คุณไม่สามารถใช้บริการของเราได้</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">2. การใช้บริการ</h2>
                        <p>GoFit เป็นแอปพลิเคชันส่งเสริมการออกกำลังกายและสุขภาพที่ใช้หลักการเกมมิฟิเคชัน โดยมีวัตถุประสงค์เพื่อช่วยให้ผู้ใช้มีสุขภาพที่ดีขึ้น</p>
                        <p>คุณยอมรับที่จะไม่ใช้บริการของเราในทางที่ผิดกฎหมาย หรือห้ามโดยเงื่อนไขเหล่านี้ หรือใช้บริการในลักษณะที่อาจเป็นอันตรายต่อบริษัท ผู้ให้บริการบุคคลที่สาม หรือผู้ใช้คนอื่น ๆ</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">3. บัญชีผู้ใช้</h2>
                        <p>เมื่อคุณสร้างบัญชีกับเรา คุณต้องให้ข้อมูลที่ถูกต้อง ครบถ้วน และเป็นปัจจุบันตลอดเวลา การไม่ปฏิบัติตามข้อกำหนดนี้ถือเป็นการละเมิดเงื่อนไข ซึ่งอาจส่งผลให้บัญชีของคุณถูกยกเลิกทันที</p>
                        <p>คุณมีหน้าที่รับผิดชอบในการรักษาความลับของบัญชีและรหัสผ่านของคุณ รวมถึงกิจกรรมทั้งหมดที่เกิดขึ้นภายใต้บัญชีของคุณ</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">4. ข้อมูลสุขภาพและการออกกำลังกาย</h2>
                        <p>ข้อมูลสุขภาพและการออกกำลังกายที่มีอยู่ในแอปพลิเคชันมีไว้เพื่อวัตถุประสงค์ในการให้ข้อมูลทั่วไปเท่านั้น ไม่ได้มีเจตนาให้เป็นคำแนะนำทางการแพทย์</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">5. การเปลี่ยนแปลงเงื่อนไข</h2>
                        <p>เราขอสงวนสิทธิ์ในการเปลี่ยนแปลงหรือแทนที่เงื่อนไขเหล่านี้ได้ตลอดเวลา การเปลี่ยนแปลงที่สำคัญจะแจ้งให้คุณทราบผ่านทางอีเมลหรือการแจ้งเตือนบนแอปพลิเคชันของเรา</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">6. ข้อจำกัดความรับผิดชอบ</h2>
                        <p>บริการของเรามีให้บริการตามสภาพที่เป็นอยู่ โดยไม่มีการรับประกันใด ๆ ทั้งสิ้น ไม่ว่าจะโดยชัดแจ้งหรือโดยนัย</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">7. ติดต่อเรา</h2>
                        <p>หากคุณมีคำถามใด ๆ เกี่ยวกับเงื่อนไขเหล่านี้ โปรดติดต่อเราที่: <a href="mailto:support@gofit.app">support@gofit.app</a></p>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-gofit" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับนโยบายความเป็นส่วนตัว -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">นโยบายความเป็นส่วนตัว</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">อัปเดตล่าสุด: {{ date('d/m/Y') }}</p>

                <div class="privacy-content">
                    <section class="mb-4">
                        <h2 class="h5 mb-3">1. บทนำ</h2>
                        <p>นโยบายความเป็นส่วนตัวนี้อธิบายวิธีที่ GoFit ("บริษัท", "เรา", "ของเรา") รวบรวม ใช้ และเปิดเผยข้อมูลของคุณเมื่อคุณใช้แอปพลิเคชันและบริการของเรา</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">2. ข้อมูลที่เราเก็บรวบรวม</h2>
                        <p>เราอาจเก็บรวบรวมข้อมูลประเภทต่าง ๆ ต่อไปนี้:</p>
                        <ul>
                            <li><strong>ข้อมูลส่วนบุคคล</strong>: ชื่อ, อีเมล, หมายเลขโทรศัพท์, ที่อยู่, วันเกิด</li>
                            <li><strong>ข้อมูลบัญชี</strong>: ชื่อผู้ใช้, รหัสผ่าน (ในรูปแบบที่เข้ารหัส)</li>
                            <li><strong>ข้อมูลสุขภาพและการออกกำลังกาย</strong>: น้ำหนัก, ส่วนสูง, กิจกรรมการออกกำลังกาย</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">3. วิธีที่เราใช้ข้อมูลของคุณ</h2>
                        <p>เราใช้ข้อมูลที่เก็บรวบรวมเพื่อ:</p>
                        <ul>
                            <li>จัดหาและบำรุงรักษาบริการของเรา</li>
                            <li>ปรับแต่งประสบการณ์การใช้งานของคุณ</li>
                            <li>พัฒนาและปรับปรุงฟีเจอร์และบริการใหม่</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">4. การแบ่งปันข้อมูลของคุณ</h2>
                        <p>เราจะไม่ขาย, ให้เช่า, หรือแลกเปลี่ยนข้อมูลส่วนบุคคลของคุณกับบุคคลที่สามโดยไม่ได้รับความยินยอมจากคุณ</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">5. ความปลอดภัยของข้อมูล</h2>
                        <p>เราใช้มาตรการความปลอดภัยทางเทคนิคและองค์กรที่เหมาะสมเพื่อปกป้องข้อมูลส่วนบุคคลของคุณ</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">6. สิทธิความเป็นส่วนตัวของคุณ</h2>
                        <p>คุณมีสิทธิ์ดังต่อไปนี้เกี่ยวกับข้อมูลส่วนบุคคลของคุณ:</p>
                        <ul>
                            <li>สิทธิ์ในการเข้าถึงและรับสำเนาข้อมูลส่วนบุคคลของคุณ</li>
                            <li>สิทธิ์ในการแก้ไขหรืออัปเดตข้อมูลส่วนบุคคลของคุณที่ไม่ถูกต้อง</li>
                            <li>สิทธิ์ในการลบข้อมูลส่วนบุคคลของคุณ</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">7. การเปลี่ยนแปลงนโยบายความเป็นส่วนตัวนี้</h2>
                        <p>เราอาจปรับปรุงนโยบายความเป็นส่วนตัวนี้เป็นครั้งคราว เราจะแจ้งให้คุณทราบเกี่ยวกับการเปลี่ยนแปลงที่สำคัญใด ๆ</p>
                    </section>

                    <section class="mb-4">
                        <h2 class="h5 mb-3">8. ติดต่อเรา</h2>
                        <p>หากคุณมีคำถามใด ๆ เกี่ยวกับนโยบายความเป็นส่วนตัวนี้ โปรดติดต่อเราที่: <a href="mailto:privacy@gofit.app">privacy@gofit.app</a></p>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-gofit" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
@endsection
