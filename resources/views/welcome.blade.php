<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="GoFit - แอปพลิเคชันส่งเสริมการออกกำลังกายโดยใช้หลักการเกมมิฟิเคชัน">
    <title>GoFit - ออกกำลังกายสนุกสไตล์เกม</title>

        <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

        <style>
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            color: var(--color-text-primary);
            background-color: var(--color-background);
        }

        .hero {
            background: linear-gradient(135deg, #F8FDFB 0%, #E0F7EC 100%);
            padding-top: 4rem;
            padding-bottom: 0rem;
            position: relative;
            overflow: hidden;
        }

        .hero-shape {
            position: absolute;
            bottom: -5%;
            right: -5%;
            width: 40%;
            height: 60%;
            background-color: rgba(45, 198, 121, 0.1);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            z-index: 0;
        }

        .navbar-gofit {
            background-color: white;
            margin: 0.5rem 0;
        }

        .nav-link {
            color: var(--color-text-primary);
            font-weight: var(--font-weight-medium);
            padding: 0.75rem 1rem;
            transition: all var(--transition-fast);
        }

        .nav-link:hover {
            color: var(--color-primary);
        }

        .navbar-brand img {
            height: 2.5rem;
        }

        .feature-icon {
            width: 4rem;
            height: 4rem;
            border-radius: var(--radius-lg);
            background-color: var(--color-primary-lighter);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: var(--color-primary-darker);
            font-size: 1.5rem;
        }

        .feature-card {
            padding: 2rem;
            border-radius: var(--radius-lg);
            background-color: white;
            box-shadow: var(--shadow-md);
            height: 100%;
            transition: all var(--transition-normal);
            border: none;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .section-title {
            color: var(--color-text-primary);
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 3rem;
        }

        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 5rem;
            height: 4px;
            background-color: var(--color-primary);
            border-radius: 2px;
        }

        .section-title.text-center:after {
            left: 50%;
            margin-left: -2.5rem;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            padding: 5rem 0;
        }

        .avatar {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 1rem;
        }

        .testimonial-card {
            padding: 2rem;
            border-radius: var(--radius-lg);
            background-color: white;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            border: none;
        }

        .testimonial-quote {
            font-size: var(--font-size-lg);
            font-weight: var(--font-weight-medium);
            font-style: italic;
            margin-bottom: 1.5rem;
            color: var(--color-text-secondary);
        }

        .stats-number {
            font-size: 3rem;
            font-weight: var(--font-weight-bold);
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: var(--color-text-secondary);
            font-weight: var(--font-weight-medium);
        }

        footer {
            background-color: var(--color-background-alt);
            padding: 4rem 0 2rem;
        }

        .footer-title {
            font-weight: var(--font-weight-bold);
            margin-bottom: 1.5rem;
            color: var(--color-text-primary);

        }

        .footer-link {
            color: var(--color-text-secondary);
            display: block;
            padding: 0.5rem 0;
            transition: all var(--transition-fast);
            text-decoration: none;
        }

        .footer-link:hover {
            color: var(--color-primary);
            text-decoration: none;
        }

        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: var(--color-primary-lighter);
            color: var(--color-primary-darker);
            margin-right: 0.75rem;
            transition: all var(--transition-fast);
        }

        .social-icon:hover {
            background-color: var(--color-primary);
            color: white;
        }

        .copyright {
            padding-top: 2rem;
            border-top: 1px solid rgba(0,0,0,0.1);
            margin-top: 3rem;
        }

        .badges-section {
            position: relative;
            overflow: hidden;
        }

        .badge-circle {
            position: absolute;
            border-radius: 50%;
            background-color: var(--color-primary-lighter);
            opacity: 0.2;
        }

        .badge-circle-1 {
            width: 20rem;
            height: 20rem;
            top: -10rem;
            left: -10rem;
        }

        .badge-circle-2 {
            width: 15rem;
            height: 15rem;
            bottom: -5rem;
            right: -5rem;
        }

        .badge-item {
            background-color: white;
            border-radius: var(--radius-full);
            box-shadow: var(--shadow-md);
            padding: 1rem 2rem;
            margin: 0.5rem;
            font-weight: var(--font-weight-medium);
            display: inline-block;
        }

        .badge-icon {
            color: var(--color-primary);
            margin-right: 0.5rem;
        }

        .mockup-device {
            max-width: 100%;
            border-radius: 0;
            box-shadow: none;
            position: relative;
            z-index: 1;
            height: 100%;
            object-fit: cover;
        }

        .col-lg-6.text-center {
            height: 100%;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 768px) {
            .hero {
                padding-top: 4rem;
                padding-bottom: 0rem;
            }
            .section-title {
                margin-bottom: 2rem;
            }
            .feature-card {
                margin-bottom: 1.5rem;
            }
        }
        </style>
    </head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-gofit">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="GoFit Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">ฟีเจอร์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">วิธีการใช้งาน</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">รีวิวจากผู้ใช้</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-gofit ms-2">ลงทะเบียน</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-shape"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="display-4 fw-bold mb-4">ออกกำลังกายได้สนุก<br>ด้วย <span style="color: var(--color-primary);">GOFIT</span></h1>
                    <p class="lead mb-5">แอปพลิเคชันที่จะทำให้การออกกำลังกายของคุณสนุกมากขึ้นด้วยระบบเกมมิฟิเคชัน สะสมแต้ม รับเหรียญตรา และแลกของรางวัลมากมาย</p>
                    <div class="d-flex flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-gofit btn-lg me-3 mb-3">เริ่มต้นใช้งานฟรี</a>
                        <a href="#how-it-works" class="btn btn-gofit-outline btn-lg mb-3">ดูวิธีการใช้งาน</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ asset('images/cover-1.png') }}" alt="GoFit App" class="mockup-device">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features">
        <div class="container py-5">
            <h2 class="section-title text-center">ฟีเจอร์เด่นของเรา</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h3>เหรียญตราท้าทาย</h3>
                        <p>สะสมเหรียญตราจากความสำเร็จในการออกกำลังกายแต่ละครั้ง ยิ่งออกกำลังกายมาก ยิ่งได้รับเหรียญมากขึ้น</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <h3>ติดตามการวิ่งแบบ GPS</h3>
                        <p>บันทึกเส้นทางการวิ่งด้วย GPS อย่างแม่นยำ พร้อมแสดงผลแคลอรี่ ระยะทาง และความเร็วเฉลี่ย</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h3>รางวัลจริง</h3>
                        <p>แลกเหรียญตราที่สะสมได้กับรางวัลจริงมากมาย เช่น ส่วนลด คูปอง หรือของรางวัลพิเศษ</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>ติดตามความก้าวหน้า</h3>
                        <p>ดูสถิติและความก้าวหน้าในการออกกำลังกายของคุณผ่านแดชบอร์ดที่ใช้งานง่าย</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>ท้าทายเพื่อน</h3>
                        <p>สร้างการท้าทายและแข่งขันกับเพื่อนเพื่อเพิ่มแรงจูงใจในการออกกำลังกาย</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>แจ้งเตือนอัจฉริยะ</h3>
                        <p>ระบบแจ้งเตือนที่ชาญฉลาดเพื่อช่วยกระตุ้นให้คุณออกกำลังกายอย่างสม่ำเสมอ</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Badges Section -->
    <section class="py-5 badges-section bg-light">
        <div class="badge-circle badge-circle-1"></div>
        <div class="badge-circle badge-circle-2"></div>
        <div class="container py-5">
            <h2 class="section-title text-center">เหรียญตราที่รอคุณอยู่</h2>
            <div class="text-center">
                <div class="badge-item mb-3">
                    <i class="fas fa-running badge-icon"></i> นักวิ่งมือใหม่
                </div>
                <div class="badge-item mb-3">
                    <i class="fas fa-fire badge-icon"></i> เผาผลาญ 1,000 แคลอรี่
                </div>
                <div class="badge-item mb-3">
                    <i class="fas fa-road badge-icon"></i> วิ่ง 10 กิโลเมตร
                </div>
                <div class="badge-item mb-3">
                    <i class="fas fa-bolt badge-icon"></i> ความเร็วสายฟ้า
                </div>
                <div class="badge-item mb-3">
                    <i class="fas fa-calendar-check badge-icon"></i> ออกกำลังกาย 7 วันติดต่อกัน
                </div>
                <div class="badge-item mb-3">
                    <i class="fas fa-mountain badge-icon"></i> นักพิชิตยอดเขา
                </div>
                <div class="badge-item mb-3">
                    <i class="fas fa-trophy badge-icon"></i> แชมป์ประจำเดือน
                </div>
                <div class="badge-item mb-3">
                    <i class="fas fa-star badge-icon"></i> ดาวรุ่งวงการวิ่ง
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class="py-5" id="how-it-works">
        <div class="container py-5">
            <h2 class="section-title text-center">วิธีใช้งาน GoFit</h2>
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    <img src="https://source.unsplash.com/random/600x400/?mobile,app" alt="ลงทะเบียน" class="img-fluid rounded-lg shadow-lg">
                </div>
                <div class="col-lg-6 order-lg-1">
                    <div class="d-flex mb-4">
                        <div class="me-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; background-color: var(--color-primary);">1</div>
                        </div>
                        <div>
                            <h3>ลงทะเบียนและสร้างโปรไฟล์</h3>
                            <p>เริ่มต้นด้วยการลงทะเบียนง่ายๆ เพียงไม่กี่ขั้นตอน แล้วสร้างโปรไฟล์ส่วนตัวของคุณ</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://source.unsplash.com/random/600x400/?running,tracking" alt="เริ่มวิ่ง" class="img-fluid rounded-lg shadow-lg">
                </div>
                <div class="col-lg-6">
                    <div class="d-flex mb-4">
                        <div class="me-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; background-color: var(--color-primary);">2</div>
                        </div>
                        <div>
                            <h3>เริ่มวิ่งและบันทึกกิจกรรม</h3>
                            <p>กดปุ่ม "เริ่มวิ่ง" เพื่อบันทึกกิจกรรมการวิ่งของคุณ ระบบจะติดตามเส้นทาง ระยะทาง และแคลอรี่แบบเรียลไทม์</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    <img src="https://source.unsplash.com/random/600x400/?rewards,medal" alt="รับเหรียญตรา" class="img-fluid rounded-lg shadow-lg">
                </div>
                <div class="col-lg-6 order-lg-1">
                    <div class="d-flex mb-4">
                        <div class="me-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; background-color: var(--color-primary);">3</div>
                        </div>
                            <div>
                            <h3>รับเหรียญตราและแลกรางวัล</h3>
                            <p>รับเหรียญตราเมื่อทำภารกิจสำเร็จและสะสมเพื่อแลกของรางวัลมากมาย</p>
                        </div>
                    </div>
                </div>
            </div>
                                </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-light">
        <div class="container py-5">
            <div class="row text-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="stats-number">5,000+</div>
                    <div class="stats-label">ผู้ใช้งานที่พึงพอใจ</div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="stats-number">10,000+</div>
                    <div class="stats-label">กิโลเมตรที่วิ่งสะสม</div>
                </div>
                <div class="col-md-4">
                    <div class="stats-number">2,500+</div>
                    <div class="stats-label">เหรียญตราที่มอบให้ผู้ใช้</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5" id="testimonials">
        <div class="container py-5">
            <h2 class="section-title text-center">รีวิวจากผู้ใช้งานจริง</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">
                            "GoFit ทำให้ผมกลับมาวิ่งอีกครั้งหลังจากหยุดมานาน ระบบเหรียญตราทำให้รู้สึกสนุกและมีแรงจูงใจมากขึ้น"
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <img src="https://i.pravatar.cc/150?img=11" alt="Avatar" class="img-fluid">
                            </div>
                            <div>
                                <h5 class="mb-0">ต้น สมชาย</h5>
                                <p class="text-muted mb-0">พนักงานออฟฟิศ</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">
                            "ฉันชอบการติดตามความก้าวหน้าและการได้รับเหรียญตรา มันทำให้การออกกำลังกายสนุกขึ้นเยอะเลย!"
                                </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <img src="https://i.pravatar.cc/150?img=5" alt="Avatar" class="img-fluid">
                            </div>
                            <div>
                                <h5 class="mb-0">แพร วรรณา</h5>
                                <p class="text-muted mb-0">นักศึกษา</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">
                            "แอปที่ดีมาก ระบบติดตาม GPS แม่นยำ และการแลกรางวัลก็คุ้มค่ามาก ใช้มา 3 เดือนแล้วไม่เคยเบื่อเลย"
                                </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <img src="https://i.pravatar.cc/150?img=3" alt="Avatar" class="img-fluid">
                            </div>
                            <div>
                                <h5 class="mb-0">ปอนด์ กิตติพงศ์</h5>
                                <p class="text-muted mb-0">นักกีฬาสมัครเล่น</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">
                            "ชอบที่มีการแจ้งเตือนอัจฉริยะ ทำให้ฉันไม่พลาดการออกกำลังกายตามแผน แนะนำเลย!"
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <img src="https://i.pravatar.cc/150?img=9" alt="Avatar" class="img-fluid">
                            </div>
                            <div>
                                <h5 class="mb-0">มีน มินตรา</h5>
                                <p class="text-muted mb-0">ครูสอนโยคะ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


 <!-- CTA Section -->
 <section class="cta-section">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">พร้อมเริ่มต้นการออกกำลังกายแบบสนุกแล้วหรือยัง?</h2>
        <p class="lead mb-5">สมัครสมาชิกวันนี้ และเริ่มต้นเก็บเหรียญตราแรกของคุณ!</p>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">สมัครสมาชิกฟรี</a>
    </div>
</section>


    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="GoFit Logo" style="height: 2.5rem;" class="mb-4">
                    </a>
                    <p class="mb-4">GoFit คือแอปพลิเคชันที่จะเปลี่ยนการออกกำลังกายของคุณให้สนุกและมีแรงจูงใจมากขึ้นด้วยระบบเกมมิฟิเคชัน</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="footer-title">เมนูหลัก</h5>
                    <a href="#" class="footer-link">หน้าแรก</a>
                    <a href="#features" class="footer-link">ฟีเจอร์</a>
                    <a href="#how-it-works" class="footer-link">วิธีการใช้งาน</a>
                    <a href="#testimonials" class="footer-link">รีวิวจากผู้ใช้</a>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="footer-title">บัญชีผู้ใช้</h5>
                    <a href="{{ route('login') }}" class="footer-link">เข้าสู่ระบบ</a>
                    <a href="{{ route('register') }}" class="footer-link">สมัครสมาชิก</a>
                    <a href="#" class="footer-link">ลืมรหัสผ่าน</a>
                    <a href="#" class="footer-link">โปรไฟล์</a>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">ติดต่อเรา</h5>
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 ถนนพระราม 9 แขวงห้วยขวาง กรุงเทพฯ 10310</p>
                    <p class="mb-2"><i class="fas fa-phone me-2"></i> 02-123-4567</p>
                    <p class="mb-2"><i class="fas fa-envelope me-2"></i> contact@gofit-app.com</p>
                </div>
            </div>
            <div class="text-center copyright">
                <p>&copy; {{ date('Y') }} KMP Digital Group. All rights reserved.</p>
            </div>
        </div>
    </footer>


    @section('scripts')
    <script>
        // Load the latest health articles
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("public.latest-health-articles") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const articlesContainer = document.getElementById('health-articles-container');

                        // Clear placeholder cards
                        articlesContainer.innerHTML = '';

                        // Add each article
                        data.articles.forEach(article => {
                            const publishedDate = new Date(article.published_at);
                            const formattedDate = publishedDate.toLocaleDateString('th-TH', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });

                            const thumbnailUrl = article.thumbnail
                                ? `{{ asset('storage') }}/${article.thumbnail}`
                                : '{{ asset("images/article-placeholder.jpg") }}';

                            const articleCard = `
                                <div class="col-md-4 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <img src="${thumbnailUrl}" class="card-img-top" alt="${article.title}" style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <span class="badge bg-primary mb-2">${article.category.category_name}</span>
                                            <h5 class="card-title">${article.title}</h5>
                                            <p class="card-text text-muted small">${formattedDate} | <i class="fas fa-eye me-1"></i> ${article.view_count} ครั้ง</p>
                                            <a href="{{ url('health-articles') }}/${article.article_id}" class="btn btn-sm btn-outline-primary">อ่านบทความ</a>
                                        </div>
                                    </div>
                                </div>
                            `;

                            articlesContainer.innerHTML += articleCard;
                        });

                        // If no articles found
                        if (data.articles.length === 0) {
                            articlesContainer.innerHTML = `
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                    <h4>ยังไม่มีบทความในขณะนี้</h4>
                                    <p class="text-muted">โปรดกลับมาตรวจสอบในภายหลัง</p>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading articles:', error);
                    const articlesContainer = document.getElementById('health-articles-container');
                    articlesContainer.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                            <h4>ไม่สามารถโหลดบทความได้</h4>
                            <p class="text-muted">โปรดลองอีกครั้งในภายหลัง</p>
                        </div>
                    `;
                });
        });

        // Cookie Consent Banner
        document.addEventListener('DOMContentLoaded', function() {
            // Check if user already accepted cookies
            if (!localStorage.getItem('cookie_consent')) {
                setTimeout(function() {
                    document.getElementById('cookie-banner').style.display = 'block';
                }, 1000);
            }

            // Handle accept all cookies
            document.getElementById('accept-all-cookies').addEventListener('click', function() {
                acceptCookies('all');
            });

            // Handle accept necessary cookies only
            document.getElementById('accept-necessary-cookies').addEventListener('click', function() {
                acceptCookies('necessary');
            });

            // Handle cookie settings
            document.getElementById('cookie-settings').addEventListener('click', function() {
                document.getElementById('cookie-banner').style.display = 'none';
                document.getElementById('cookie-settings-modal').style.display = 'block';
            });

            // Handle close cookie settings
            document.getElementById('close-cookie-settings').addEventListener('click', function() {
                document.getElementById('cookie-settings-modal').style.display = 'none';
                document.getElementById('cookie-banner').style.display = 'block';
            });

            // Handle save cookie settings
            document.getElementById('save-cookie-settings').addEventListener('click', function() {
                const preferences = {
                    necessary: true, // Always true
                    functional: document.getElementById('functional-cookies').checked,
                    analytics: document.getElementById('analytics-cookies').checked,
                    marketing: document.getElementById('marketing-cookies').checked
                };

                localStorage.setItem('cookie_consent', 'custom');
                localStorage.setItem('cookie_preferences', JSON.stringify(preferences));

                document.getElementById('cookie-settings-modal').style.display = 'none';
                showCookieConfirmation();

                // Apply cookie settings
                applyCookieSettings(preferences);
            });
        });

        function acceptCookies(type) {
            // Save consent to localStorage
            localStorage.setItem('cookie_consent', type);

            const preferences = {
                necessary: true,
                functional: type === 'all',
                analytics: type === 'all',
                marketing: type === 'all'
            };

            localStorage.setItem('cookie_preferences', JSON.stringify(preferences));

            // Hide banner
            document.getElementById('cookie-banner').style.display = 'none';

            // Show confirmation message
            showCookieConfirmation();

            // Apply cookie settings
            applyCookieSettings(preferences);
        }

        function showCookieConfirmation() {
            const confirmation = document.getElementById('cookie-confirmation');
            confirmation.style.display = 'block';

            setTimeout(function() {
                confirmation.style.opacity = '0';
                setTimeout(function() {
                    confirmation.style.display = 'none';
                    confirmation.style.opacity = '1';
                }, 500);
            }, 3000);
        }

        function applyCookieSettings(preferences) {
            // Apply necessary cookies - always enabled

            // Apply functional cookies if enabled
            if (preferences.functional) {
                // Example: enable theme preferences, language settings
                // setupFunctionalCookies();
            }

            // Apply analytics cookies if enabled
            if (preferences.analytics) {
                // Example: initialize Google Analytics
                // setupAnalyticsCookies();
            }

            // Apply marketing cookies if enabled
            if (preferences.marketing) {
                // Example: initialize Facebook Pixel
                // setupMarketingCookies();
            }
        }
    </script>
    @endsection

    <!-- Cookie Consent Banner -->
    <div id="cookie-banner" class="cookie-banner" style="display: none;">
        <div class="cookie-content">
            <h4>เราใช้คุกกี้เพื่อมอบประสบการณ์ที่ดีที่สุดให้คุณ</h4>
            <p>GoFit ใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ เพื่อวิเคราะห์การใช้งานเว็บไซต์ และเพื่อช่วยในกิจกรรมทางการตลาดของเรา <a href="{{ url('/privacy-policy') }}">นโยบายความเป็นส่วนตัว</a></p>
            <div class="cookie-buttons">
                <button id="accept-necessary-cookies" class="btn btn-gofit-outline">ยอมรับเฉพาะที่จำเป็น</button>
                <button id="cookie-settings" class="btn btn-gofit-outline">ตั้งค่าคุกกี้</button>
                <button id="accept-all-cookies" class="btn btn-gofit">ยอมรับทั้งหมด</button>
            </div>
        </div>
    </div>

    <!-- Cookie Settings Modal -->
    <div id="cookie-settings-modal" class="cookie-modal" style="display: none;">
        <div class="cookie-modal-content">
            <div class="cookie-modal-header">
                <h4>ตั้งค่าความเป็นส่วนตัวของคุกกี้</h4>
                <button id="close-cookie-settings" class="close-button">&times;</button>
            </div>
            <div class="cookie-modal-body">
                <div class="cookie-option">
                    <div>
                        <h5>คุกกี้ที่จำเป็น</h5>
                        <p>คุกกี้เหล่านี้จำเป็นสำหรับการทำงานของเว็บไซต์และไม่สามารถปิดได้</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked disabled>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="cookie-option">
                    <div>
                        <h5>คุกกี้ฟังก์ชันการใช้งาน</h5>
                        <p>ช่วยให้เว็บไซต์จดจำตัวเลือกของคุณ เช่น ธีม ภาษา และการตั้งค่าอื่นๆ</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="functional-cookies">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="cookie-option">
                    <div>
                        <h5>คุกกี้การวิเคราะห์</h5>
                        <p>ช่วยให้เราเข้าใจวิธีที่ผู้ใช้โต้ตอบกับเว็บไซต์ของเรา</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="analytics-cookies">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="cookie-option">
                    <div>
                        <h5>คุกกี้การตลาด</h5>
                        <p>ช่วยให้เราสามารถแสดงโฆษณาที่เกี่ยวข้องกับความสนใจของคุณ</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="marketing-cookies">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="cookie-modal-footer">
                <button id="save-cookie-settings" class="btn btn-gofit">บันทึกการตั้งค่า</button>
            </div>
        </div>
    </div>

    <!-- Cookie Confirmation Message -->
    <div id="cookie-confirmation" class="cookie-confirmation" style="display: none;">
        <p><i class="fas fa-check-circle"></i> บันทึกการตั้งค่าคุกกี้แล้ว</p>
    </div>

    <style>
        /* Cookie Banner Styles */
        .cookie-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 1rem;
            border-top: 3px solid var(--color-primary);
        }

        .cookie-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        .cookie-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        /* Cookie Modal Styles */
        .cookie-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cookie-modal-content {
            background-color: white;
            border-radius: var(--radius-lg);
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .cookie-modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cookie-modal-body {
            padding: 1.5rem;
        }

        .cookie-modal-footer {
            padding: 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            text-align: right;
        }

        .close-button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--color-text-secondary);
        }

        .cookie-option {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .cookie-option:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .cookie-option h5 {
            margin-bottom: 0.5rem;
        }

        .cookie-option p {
            color: var(--color-text-secondary);
            margin-bottom: 0;
        }

        /* Switch Toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            flex-shrink: 0;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: var(--color-primary);
        }

        input:disabled + .slider {
            opacity: 0.5;
            cursor: not-allowed;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 24px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        /* Cookie Confirmation */
        .cookie-confirmation {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--color-primary);
            color: white;
            padding: 10px 20px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            z-index: 1000;
            transition: opacity 0.5s;
        }

        .cookie-confirmation p {
            margin: 0;
        }

        .cookie-confirmation i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .cookie-buttons {
                flex-direction: column;
            }

            .cookie-buttons button {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</body>
</html>


