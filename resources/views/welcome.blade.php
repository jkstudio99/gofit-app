<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="GoFit - เว็บแอปพลิเคชันออกกำลังกายอันดับ 1 ด้วยระบบเกมมิฟิเคชัน สะสมแต้ม รับเหรียญตรา และแลกของรางวัลมากมาย เริ่มต้นใช้งานฟรีวันนี้">
        <meta name="keywords" content="ออกกำลังกาย, เกมมิฟิเคชัน, สุขภาพ, วิ่ง, รางวัล, เหรียญตรา, เว็บแอปพลิเคชัน, fitness, gamification, ติดตามการออกกำลังกาย, เว็บไซต์ออกกำลังกาย">
        <meta name="author" content="GoFit Team">
        <meta name="robots" content="index, follow">
        <meta name="theme-color" content="#2DC679">
        <link rel="canonical" href="{{ url('/') }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:title" content="GoFit - ออกกำลังกายสนุกด้วยระบบเกมมิฟิเคชัน">
        <meta property="og:description" content="เว็บแอปพลิเคชันที่จะทำให้การออกกำลังกายของคุณสนุกมากขึ้นด้วยระบบเกมมิฟิเคชัน สะสมแต้ม รับเหรียญตรา และแลกของรางวัลมากมาย">
        <meta property="og:image" content="{{ asset('images/gofit-share.jpg') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">


        <title>GoFit - ออกกำลังกายสนุก สะสมแต้ม รับรางวัล</title>

        <!-- Preconnect to required origins -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">

        <!-- Preload critical assets -->
        <link rel="preload" href="{{ asset('images/gofit-logo-text-black.svg') }}" as="image" type="image/svg+xml">
        <link rel="preload" href="{{ asset('images/cover2.png') }}" as="image" fetchpriority="high">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">

        <!-- Styles -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])

        <!-- Structured Data -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "GoFit",
            "applicationCategory": "HealthApplication",
            "operatingSystem": "Web",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "THB"
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.8",
                "ratingCount": "10000"
            },
            "description": "เว็บแอปพลิเคชันออกกำลังกายด้วยระบบเกมมิฟิเคชัน สะสมแต้ม รับเหรียญตรา และแลกของรางวัลมากมาย"
        }
        </script>

        <style>
        :root {
            --animation-duration-slow: 1.2s;
            --animation-duration-medium: 0.8s;
            --animation-duration-fast: 0.5s;
            --animation-delay-base: 0.1s;
            --animation-offset: 40px;
            --animation-easing: cubic-bezier(0.25, 0.1, 0.25, 1);
        }

        body {
            font-family: 'Noto Sans Thai', sans-serif;
            color: var(--color-text-primary);
            background-color: var(--color-background);
            overflow-x: hidden;
        }

        /* Contact links without underline */
        .col-lg-4.col-md-4 a {
            text-decoration: none;
            color: inherit;
        }

        .col-lg-4.col-md-4 a:hover {
            color: var(--color-primary);
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
            animation: morphShape 20s infinite alternate ease-in-out;
        }

        @keyframes morphShape {
            0% {
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            }
            50% {
                border-radius: 60% 40% 30% 70% / 50% 60% 40% 50%;
            }
            100% {
                border-radius: 40% 60% 50% 50% / 40% 40% 60% 60%;
            }
        }

        .navbar-gofit {
            background-color: white;
            margin: 0.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s var(--animation-easing);
        }

        .navbar-gofit.scrolled {
            padding: 5px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .nav-link {
            color: var(--color-text-primary);
            font-weight: var(--font-weight-medium);
            padding: 0.75rem 1rem;
            transition: all var(--transition-fast);
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--color-primary);
            transition: all 0.3s var(--animation-easing);
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .nav-link:hover {
            color: var(--color-primary);
        }

        .navbar-brand img {
            height: 2.5rem;
            transition: all 0.3s var(--animation-easing);
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
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
            transition: transform 0.3s var(--animation-easing), background-color 0.3s var(--animation-easing);
        }

        .feature-card {
            padding: 2rem;
            border-radius: var(--radius-lg);
            background-color: white;
            box-shadow: var(--shadow-md);
            height: 100%;
            transition: all 0.4s var(--animation-easing);
            border: none;
            transform: translateY(0);
            will-change: transform, box-shadow;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            background-color: var(--color-primary-light);
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
            transition: width 0.5s var(--animation-easing);
        }

        .section-title.text-center:hover:after {
            width: 8rem;
        }

        .section-title.text-center:after {
            left: 50%;
            margin-left: -2.5rem;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='rgba(255,255,255,0.05)' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.15;
        }

        .avatar {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 1rem;
            transition: transform 0.3s var(--animation-easing);
        }

        .testimonial-card {
            padding: 2rem;
            border-radius: var(--radius-lg);
            background-color: white;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .testimonial-card:hover .avatar {
            transform: scale(1.1);
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
            position: relative;
            padding-left: 0;
        }

        .footer-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 2px;
            background-color: var(--color-primary);
            transition: all 0.3s var(--animation-easing);
            opacity: 0;
        }

        .footer-link:hover {
            color: var(--color-primary);
            padding-left: 10px;
        }

        .footer-link:hover::before {
            width: 5px;
            opacity: 1;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
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
            transition: all 0.3s var(--animation-easing);
            will-change: transform, background-color;
        }

        .social-icon:hover {
            background-color: var(--color-primary);
            color: white;
            transform: translateY(-3px);
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
            transition: transform 0.3s ease;
        }

        .badge-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
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
            will-change: transform, opacity;
        }

        .col-lg-6.text-center {
            height: 100%;
            position: relative;
            z-index: 1;
        }

        /* Animation classes */
        /* Ensure content displays even without JavaScript */
        .fade-in, .fade-in-left, .fade-in-right {
            opacity: 1;
            transform: translateY(0);
            transition: opacity var(--animation-duration-medium) var(--animation-easing),
                        transform var(--animation-duration-medium) var(--animation-easing);
        }

        .slide-in-right {
            transform: translateX(0);
            opacity: 1;
            animation: slideInRight var(--animation-duration-medium) var(--animation-easing);
        }

        @keyframes slideInRight {
            0% {
                transform: translateX(var(--animation-offset));
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Only apply fade animations when JS is available */
        html.js .fade-in:not(.active) {
            opacity: 0;
            transform: translateY(var(--animation-offset));
        }

        .fade-in.active {
            opacity: 1;
            transform: translateY(0);
        }

        html.js .fade-in-left:not(.active) {
            opacity: 0;
            transform: translateX(calc(-1 * var(--animation-offset)));
        }

        .fade-in-left.active {
            opacity: 1;
            transform: translateX(0);
        }

        html.js .fade-in-right:not(.active) {
            opacity: 0;
            transform: translateX(var(--animation-offset));
        }

        .fade-in-right.active {
            opacity: 1;
            transform: translateX(0);
        }

        .stagger-item {
            opacity: 1;
            transform: translateY(0);
            transition-delay: calc(var(--animation-delay-base) * var(--item-index, 0));
        }

        html.js .stagger-item:not(.active) {
            opacity: 0;
            transform: translateY(var(--animation-offset));
        }

        .stats-counter {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        html.js .stats-counter:not(.animated) {
            opacity: 0;
            transform: translateY(20px);
        }

        .stats-counter.animated {
            opacity: 1;
            transform: translateY(0);
        }

        /* Timeline Styles */
        .progress-timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 40px 0;
        }

        .progress-timeline:before {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 0;
            right: 0;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
        }

        .timeline-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            width: 25%;
            opacity: 1;
            transform: translateY(0);
            transition: all 0.5s var(--animation-easing);
        }

        html.js .timeline-item:not(.active) {
            opacity: 0;
            transform: translateY(20px);
        }

        .timeline-item.active {
            opacity: 1;
            transform: translateY(0);
        }

        .timeline-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: var(--color-primary);
            border: 4px solid var(--color-primary-light);
            box-shadow: 0 0 15px rgba(45, 198, 121, 0.3);
            transition: all 0.3s ease;
        }

        html.js .timeline-item:not(.active) .timeline-icon {
            color: #aaa;
            background: white;
            border-color: #e0e0e0;
            box-shadow: none;
        }

        .timeline-content {
            text-align: center;
            opacity: 1;
            transition: all 0.3s ease;
        }

        html.js .timeline-item:not(.active) .timeline-content {
            opacity: 0.7;
        }

        .timeline-content h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .timeline-content p {
            font-size: 0.9rem;
            color: var(--color-text-secondary);
            margin: 0;
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
            .badge-item {
                margin-bottom: 1rem;
            }
            .progress-timeline {
                flex-direction: column;
                padding: 0 20px;
            }
            .progress-timeline:before {
                top: 0;
                bottom: 0;
                left: 29px;
                width: 4px;
                height: 100%;
                transform: none;
            }
            .timeline-item {
                flex-direction: row;
                width: 100%;
                margin-bottom: 30px;
            }
            .timeline-icon {
                margin-bottom: 0;
                margin-right: 20px;
            }
            .timeline-content {
                text-align: left;
            }
        }
        </style>
    </head>
<body>
    <!-- No JavaScript warning -->
    <noscript>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; text-align: center; font-weight: bold;">
            เว็บไซต์นี้ทำงานได้ดีที่สุดเมื่อเปิดใช้งาน JavaScript ซึ่งตอนนี้ถูกปิดใช้งานอยู่ คุณสามารถเข้าใช้งานได้แต่บางฟีเจอร์อาจไม่ทำงาน
        </div>
    </noscript>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-gofit sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/gofit-logo-text-black.svg') }}" alt="GoFit Logo" width="120" height="40">
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
                <div class="col-lg-6 mb-5 mb-lg-0 fade-in-left" data-animation-delay="0">
                    <span class="badge bg-primary-light text-primary mb-3 rounded-pill px-3 py-2">เริ่มออกกำลังกายวันนี้</span>
                    <h1 class="display-4 fw-bold mb-4">ออกกำลังกายได้สนุก<br>ด้วย <span style="color: var(--color-primary);">GOFIT</span></h1>
                    <p class="lead mb-4">เว็บแอปพลิเคชันที่จะทำให้การออกกำลังกายของคุณสนุกมากขึ้นด้วยระบบเกมมิฟิเคชัน สะสมแต้ม รับเหรียญตรา และแลกของรางวัลมากมาย</p>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a href="{{ route('register') }}" class="btn btn-gofit btn-lg me-3 mb-3">เริ่มต้นใช้งานฟรี</a>
                        <a href="#how-it-works" class="btn btn-gofit-outline btn-lg mb-3">ดูวิธีการใช้งาน</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center position-relative">
                    <img src="{{ asset('images/cover2.png') }}" alt="GoFit เว็บแอปพลิเคชันออกกำลังกาย" class="mockup-device slide-in-right" width="600" height="450">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features">
        <div class="container py-5">
            <div class="text-center mb-5 fade-in" data-animation-delay="0">
                <span class="badge bg-primary-light text-primary mb-3 rounded-pill px-3 py-2">ฟีเจอร์เด่น</span>
                <h2 class="section-title text-center">ฟีเจอร์ที่คุณจะได้รับ</h2>
                <p class="lead text-muted col-md-8 mx-auto">เพลิดเพลินกับฟีเจอร์ที่ออกแบบมาให้การออกกำลังกายของคุณสนุกและมีประสิทธิภาพมากขึ้นผ่านเว็บแอปพลิเคชัน GoFit</p>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card fade-in stagger-item" style="--item-index: 0;">
                        <div class="feature-icon">
                            <i class="fas fa-medal" aria-hidden="true"></i>
                        </div>
                        <h3>เหรียญตราท้าทาย</h3>
                        <p>ระบบเหรียญตราเพื่อกระตุ้นการออกกำลังกายอย่างต่อเนื่อง ช่วยให้การออกกำลังกายของคุณสนุกและมีเป้าหมายมากขึ้น</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card fade-in stagger-item" style="--item-index: 1;">
                        <div class="feature-icon">
                            <i class="fas fa-route" aria-hidden="true"></i>
                        </div>
                        <h3>ติดตามการออกกำลังกาย</h3>
                        <p>บันทึกข้อมูลการออกกำลังกายของคุณ ไม่ว่าจะเป็นประเภทกิจกรรม ระยะเวลา และข้อมูลสำคัญอื่นๆ เพื่อดูความก้าวหน้า</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card fade-in stagger-item" style="--item-index: 2;">
                        <div class="feature-icon">
                            <i class="fas fa-gift" aria-hidden="true"></i>
                        </div>
                        <h3>รางวัลจริง</h3>
                        <p>ระบบสะสมคะแนนและแลกรับรางวัลเพื่อสร้างแรงจูงใจในการออกกำลังกายอย่างต่อเนื่อง</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class=" py-3 bg-light" id="how-it-works">
        <div class="container py-2">
            <div class="text-center mb-5 fade-in">
                <span class="badge bg-primary-light text-primary mb-3 rounded-pill px-3 py-2">วิธีใช้งาน</span>
                <h2 class="section-title text-center">วิธีใช้งาน GoFit</h2>
                <p class="lead text-muted col-md-8 mx-auto">เริ่มต้นการออกกำลังกายอย่างมีประสิทธิภาพด้วยเว็บแอปพลิเคชัน GoFit ในไม่กี่ขั้นตอน</p>
            </div>
            <div class="row align-items-center mb-4">
                <div class="col-lg-5 order-lg-2 mb-3 mb-lg-0 fade-in-right">
                    <img src="{{ asset('images/regis.png') }}" alt="ลงทะเบียนใช้งาน GoFit" class="img-fluid rounded-lg" width="450" height="300">
                </div>
                <div class="col-lg-7 order-lg-1 fade-in-left">
                    <div class="d-flex mb-3">
                        <div class="me-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; background-color: var(--color-primary);">1</div>
                        </div>
                        <div>
                            <h3>ลงทะเบียนและสร้างโปรไฟล์</h3>
                            <p>เริ่มต้นด้วยการลงทะเบียนง่ายๆ เพียงไม่กี่ขั้นตอน แล้วสร้างโปรไฟล์ส่วนตัวของคุณเพื่อเก็บข้อมูลการออกกำลังกาย</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center mb-4">
                <div class="col-lg-5 mb-3 mb-lg-0 fade-in-left">
                    <img src="{{ asset('images/run.png') }}" alt="บันทึกการวิ่ง GoFit" class="img-fluid rounded-lg" width="450" height="300">
                </div>
                <div class="col-lg-7 fade-in-right">
                    <div class="d-flex mb-3">
                        <div class="me-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; background-color: var(--color-primary);">2</div>
                        </div>
                        <div>
                            <h3>บันทึกกิจกรรมการออกกำลังกาย</h3>
                            <p>บันทึกกิจกรรมการออกกำลังกายของคุณ ระบบจะติดตามระยะทาง ระยะเวลา และแคลอรี่ที่เผาผลาญ ช่วยให้คุณจัดการการออกกำลังกายได้อย่างมีประสิทธิภาพ</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-5 order-lg-2 mb-3 mb-lg-0 fade-in-right">
                    <img src="{{ asset('images/badge.png') }}" alt="เหรียญตราและรางวัล GoFit" class="img-fluid rounded-lg" width="450" height="300">
                </div>
                <div class="col-lg-7 order-lg-1 fade-in-left">
                    <div class="d-flex mb-3">
                        <div class="me-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem; background-color: var(--color-primary);">3</div>
                        </div>
                            <div>
                            <h3>รับเหรียญตราและแลกรางวัล</h3>
                            <p>รับเหรียญตราเมื่อทำภารกิจสำเร็จและสะสมคะแนนเพื่อแลกของรางวัลมากมาย ยิ่งออกกำลังกายมาก ยิ่งได้รับสิทธิประโยชน์มากขึ้น</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-3 bg-white">
        <div class="container py-2">
            <div class="text-center mb-5 fade-in">
                <span class="badge bg-primary-light text-primary mb-3 rounded-pill px-3 py-2">ข้อมูลสถิติ</span>
                <h2 class="section-title text-center">เป็นส่วนหนึ่งกับชุมชนผู้รักสุขภาพ</h2>
                <p class="lead text-muted col-md-8 mx-auto">ร่วมเป็นส่วนหนึ่งกับชุมชนคนรักสุขภาพที่มากกว่า 10,000 คนที่ได้เปลี่ยนแปลงชีวิตด้วย GoFit</p>
            </div>
            <div class="row text-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="stats-counter" data-count="200">
                        <div class="stats-number"><span class="counter-value">200</span></div>
                        <div class="stats-label">คน</div>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="stats-counter" data-count="10000">
                        <div class="stats-number"><span class="counter-value">10,000</span></div>
                        <div class="stats-label">กิโลเมตร</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-counter" data-count="100">
                        <div class="stats-number"><span class="counter-value">100</span></div>
                        <div class="stats-label">เหรียญ</div>
                    </div>
                </div>
            </div>

            <!-- Progress Timeline -->
            <div class="row mt-5 pt-4">
                <div class="col-12">
                    <div class="progress-timeline">
                        <div class="timeline-item" data-step="1">
                            <div class="timeline-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>สมัครสมาชิก</h4>
                                <p>เริ่มต้นใช้งาน GoFit ได้ฟรี</p>
                            </div>
                        </div>
                        <div class="timeline-item" data-step="2">
                            <div class="timeline-icon">
                                <i class="fas fa-running"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>ออกกำลังกาย</h4>
                                <p>บันทึกการวิ่งและกิจกรรม</p>
                            </div>
                        </div>
                        <div class="timeline-item" data-step="3">
                            <div class="timeline-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>รับเหรียญตรา</h4>
                                <p>รับเหรียญตราเมื่อทำภารกิจสำเร็จ</p>
                            </div>
                        </div>
                        <div class="timeline-item" data-step="4">
                            <div class="timeline-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>แลกรางวัล</h4>
                                <p>ใช้คะแนนแลกรับรางวัลมากมาย</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    {{-- คอมเมนต์ส่วนรีวิวจากผู้ใช้งานจริงไว้ก่อน เนื่องจากยังไม่พร้อมใช้งาน
    <section id="testimonials">
        <div class="container">
            <h2 class="section-title text-center">รีวิวจากผู้ใช้งานจริง</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">
                            "GoFit ทำให้ผมกลับมาวิ่งอีกครั้งหลังจากหยุดมานาน ระบบเหรียญตราทำให้รู้สึกสนุกและมีแรงจูงใจมากขึ้น ตอนนี้วิ่งเป็นกิจวัตรไปแล้ว"
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar" style="background-color: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #333; font-weight: bold; font-size: 1.5rem;">
                                <span>ต</span>
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
                            "ฉันชอบการติดตามความก้าวหน้าและการได้รับเหรียญตรา มันทำให้การออกกำลังกายสนุกขึ้นเยอะเลย! แทบรอไม่ไหวที่จะได้เหรียญใหม่"
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar" style="background-color: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #333; font-weight: bold; font-size: 1.5rem;">
                                <span>พ</span>
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
                            "แอปที่ดีมาก ระบบติดตาม GPS แม่นยำ และการแลกรางวัลก็คุ้มค่ามาก ใช้มา 3 เดือนแล้วไม่เคยเบื่อเลย สุดยอดจริงๆ"
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar" style="background-color: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #333; font-weight: bold; font-size: 1.5rem;">
                                <span>ป</span>
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
                            "ชอบที่มีการแจ้งเตือนอัจฉริยะ ทำให้ฉันไม่พลาดการออกกำลังกายตามแผน แนะนำเลย! จาก 3 วันต่อสัปดาห์ ตอนนี้วิ่ง 5 วันแล้ว"
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar" style="background-color: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #333; font-weight: bold; font-size: 1.5rem;">
                                <span>ม</span>
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
    --}}

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center fade-in">
            <h2 class="display-5 fw-bold mb-4">พร้อมเริ่มต้นการออกกำลังกายแบบสนุกแล้วหรือยัง?</h2>
            <p class="lead mb-5">สมัครสมาชิกวันนี้ เริ่มต้นใช้งานเว็บแอปพลิเคชัน GoFit และเก็บเหรียญตราแรกของคุณได้ทันที!</p>
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
                    <p class="mb-4">GoFit คือเว็บแอปพลิเคชันที่จะเปลี่ยนการออกกำลังกายของคุณให้สนุกและมีแรงจูงใจมากขึ้นด้วยระบบเกมมิฟิเคชัน</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="footer-title">เมนูหลัก</h5>
                    <a href="#" class="footer-link">หน้าแรก</a>
                    <a href="#features" class="footer-link">ฟีเจอร์</a>
                    <a href="#how-it-works" class="footer-link">วิธีการใช้งาน</a>
                    <!-- <a href="#testimonials" class="footer-link">รีวิวจากผู้ใช้</a> -->
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="footer-title">บัญชีผู้ใช้</h5>
                    <a href="{{ route('login') }}" class="footer-link">เข้าสู่ระบบ</a>
                    <a href="{{ route('register') }}" class="footer-link">สมัครสมาชิก</a>
                    <a href="{{ route('password.request') }}" class="footer-link">ลืมรหัสผ่าน</a>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">ติดต่อเรา</h5>
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> <a href="https://maps.google.com/?q=123 ถนนพระราม 9 แขวงห้วยขวาง กรุงเทพฯ 10310" target="_blank">123 ถนนพระราม 9 แขวงห้วยขวาง กรุงเทพฯ 10310</a></p>
                    <p class="mb-2"><i class="fas fa-phone me-2"></i> <a href="tel:0993831496">099-383-1496</a></p>
                    <p class="mb-2"><i class="fas fa-envelope me-2"></i> <a href="mailto:contact@gofitrunnow.com">contact@gofitrunnow.com</a></p>
                </div>
            </div>
            <div class="text-center copyright">
                <p>&copy; {{ date('Y') }} GoFit Web Application. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @section('scripts')
    <script>
        // Add 'js' class to HTML element to indicate JavaScript is available
        document.documentElement.classList.add('js');

        // Fallback to ensure animations trigger even if IntersectionObserver fails
        window.onload = function() {
            setTimeout(function() {
                document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .timeline-item').forEach(function(el) {
                    if (!el.classList.contains('active')) {
                        el.classList.add('active');
                    }
                });

                document.querySelectorAll('.stats-counter').forEach(function(counter) {
                    if (!counter.classList.contains('animated')) {
                        counter.classList.add('animated');
                        const countTo = parseInt(counter.getAttribute('data-count'));
                        const counterValue = counter.querySelector('.counter-value');
                        counterValue.textContent = countTo.toLocaleString();
                    }
                });
            }, 2000); // Wait 2 seconds after load as final fallback
        };

        // Apply active class to all animation elements by default if JavaScript fails
        window.addEventListener('DOMContentLoaded', function() {
            // Add active class to all elements with animation classes
            setTimeout(function() {
                const allAnimationElements = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .timeline-item, .stats-counter');
                allAnimationElements.forEach(function(element) {
                    if (!element.classList.contains('active') && !element.classList.contains('animated')) {
                        if (element.classList.contains('stats-counter')) {
                            element.classList.add('animated');
                        } else {
                            element.classList.add('active');
                        }
                    }
                });
            }, 1000); // Fallback timeout
        });

        // Intersection Observer for scroll animations
        document.addEventListener('DOMContentLoaded', function() {
            // Handle fade-in animations
            const fadeElements = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right');
            const timelineItems = document.querySelectorAll('.timeline-item');
            const statsCounters = document.querySelectorAll('.stats-counter');

            // Set default state for browsers without Intersection Observer
            function setDefaultStates() {
                fadeElements.forEach(element => element.classList.add('active'));
                timelineItems.forEach(item => item.classList.add('active'));
                statsCounters.forEach(counter => {
                    counter.classList.add('animated');
                    const countTo = parseInt(counter.getAttribute('data-count'));
                    const counterValue = counter.querySelector('.counter-value');
                    counterValue.textContent = countTo.toLocaleString();
                });
            }

            // Check if IntersectionObserver is supported
            if (!('IntersectionObserver' in window)) {
                console.log('IntersectionObserver not supported');
                setDefaultStates();
                return;
            }

            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            // Observer for regular fade animations
            const fadeObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Add active class to trigger animation
                        entry.target.classList.add('active');

                        // Unobserve after animation
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Add observers to all fade elements
            fadeElements.forEach(element => {
                fadeObserver.observe(element);
            });

            // Observer for timeline items
            const timelineObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Add active class to trigger animation
                        entry.target.classList.add('active');

                        // Unobserve after animation
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Add observers to all timeline items
            timelineItems.forEach(item => {
                timelineObserver.observe(item);
            });

            // Counter animation
            const counterObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counter = entry.target;
                        counter.classList.add('animated');

                        const countTo = parseInt(counter.getAttribute('data-count'));
                        const counterValue = counter.querySelector('.counter-value');

                        // Set initial value immediately
                        counterValue.textContent = '0';

                        // Use requestAnimationFrame for better performance
                        let count = 0;
                        const duration = 2000; // Duration in milliseconds
                        const startTime = performance.now();

                        function updateCount(currentTime) {
                            const elapsedTime = currentTime - startTime;
                            const progress = Math.min(elapsedTime / duration, 1);
                            const easeOutProgress = 1 - Math.pow(1 - progress, 3); // Cubic ease out

                            count = Math.floor(countTo * easeOutProgress);
                            counterValue.textContent = count.toLocaleString();

                            if (progress < 1) {
                                requestAnimationFrame(updateCount);
                            } else {
                                counterValue.textContent = countTo.toLocaleString();
                            }
                        }

                        requestAnimationFrame(updateCount);

                        // Unobserve after animation
                        observer.unobserve(counter);
                    }
                });
            }, observerOptions);

            statsCounters.forEach(counter => {
                // Initialize counter values so they're not empty
                const counterValue = counter.querySelector('.counter-value');
                const countTo = parseInt(counter.getAttribute('data-count'));

                if (!counterValue.textContent || counterValue.textContent === '0') {
                    counterValue.textContent = countTo.toLocaleString();
                }

                counterObserver.observe(counter);
            });

            // Navbar scroll effect
            const navbar = document.querySelector('.navbar-gofit');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });

        // Load the latest health articles
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('health-articles-container')) {
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
                                        <div class="card shadow-sm h-100 fade-in">
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

                            // Apply animations to newly added elements
                            const fadeElements = articlesContainer.querySelectorAll('.fade-in');
                            fadeElements.forEach((el, index) => {
                                el.style.setProperty('--item-index', index);
                                el.classList.add('stagger-item');
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
                        if (articlesContainer) {
                            articlesContainer.innerHTML = `
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                                    <h4>ไม่สามารถโหลดบทความได้</h4>
                                    <p class="text-muted">โปรดลองอีกครั้งในภายหลัง</p>
                                </div>
                            `;
                        }
                    });
            }
        });

        // Cookie Consent Banner
        document.addEventListener('DOMContentLoaded', function() {
            // Check if user already accepted cookies
            if (!localStorage.getItem('cookie_consent')) {
                setTimeout(function() {
                    const cookieBanner = document.getElementById('cookie-banner');
                    if (cookieBanner) {
                        cookieBanner.style.display = 'block';
                    }
                }, 1000);
            }

            // Handle accept all cookies
            const acceptAllBtn = document.getElementById('accept-all-cookies');
            if (acceptAllBtn) {
                acceptAllBtn.addEventListener('click', function() {
                    acceptCookies('all');
                });
            }

            // Handle accept necessary cookies only
            const acceptNecessaryBtn = document.getElementById('accept-necessary-cookies');
            if (acceptNecessaryBtn) {
                acceptNecessaryBtn.addEventListener('click', function() {
                    acceptCookies('necessary');
                });
            }

            // Handle cookie settings
            const cookieSettingsBtn = document.getElementById('cookie-settings');
            if (cookieSettingsBtn) {
                cookieSettingsBtn.addEventListener('click', function() {
                    document.getElementById('cookie-banner').style.display = 'none';
                    document.getElementById('cookie-settings-modal').style.display = 'block';
                });
            }

            // Handle close cookie settings
            const closeCookieSettingsBtn = document.getElementById('close-cookie-settings');
            if (closeCookieSettingsBtn) {
                closeCookieSettingsBtn.addEventListener('click', function() {
                    document.getElementById('cookie-settings-modal').style.display = 'none';
                    document.getElementById('cookie-banner').style.display = 'block';
                });
            }

            // Handle save cookie settings
            const saveCookieSettingsBtn = document.getElementById('save-cookie-settings');
            if (saveCookieSettingsBtn) {
                saveCookieSettingsBtn.addEventListener('click', function() {
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
            }
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
            const cookieBanner = document.getElementById('cookie-banner');
            if (cookieBanner) {
                cookieBanner.style.display = 'none';
            }

            // Show confirmation message
            showCookieConfirmation();

            // Apply cookie settings
            applyCookieSettings(preferences);
        }

        function showCookieConfirmation() {
            const confirmation = document.getElementById('cookie-confirmation');
            if (confirmation) {
                confirmation.style.display = 'block';

                setTimeout(function() {
                    confirmation.style.opacity = '0';
                    setTimeout(function() {
                        confirmation.style.display = 'none';
                        confirmation.style.opacity = '1';
                    }, 500);
                }, 3000);
            }
        }

        function applyCookieSettings(preferences) {
            // Apply necessary cookies - always enabled

            // Apply functional cookies if enabled
            if (preferences.functional) {
                // Example: enable theme preferences, language settings
            }

            // Apply analytics cookies if enabled
            if (preferences.analytics) {
                // Initialize analytics with respectful tracking
                if (typeof gtag === 'function') {
                    // Google Analytics consent
                    gtag('consent', 'update', {
                        'analytics_storage': 'granted'
                    });
                }
            } else {
                // Deny analytics tracking
                if (typeof gtag === 'function') {
                    gtag('consent', 'update', {
                        'analytics_storage': 'denied'
                    });
                }
            }

            // Apply marketing cookies if enabled
            if (preferences.marketing) {
                // Example: initialize ad personalization
                if (typeof gtag === 'function') {
                    // Google Ads consent
                    gtag('consent', 'update', {
                        'ad_storage': 'granted',
                        'ad_user_data': 'granted',
                        'ad_personalization': 'granted'
                    });
                }
            } else {
                // Deny marketing tracking
                if (typeof gtag === 'function') {
                    gtag('consent', 'update', {
                        'ad_storage': 'denied',
                        'ad_user_data': 'denied',
                        'ad_personalization': 'denied'
                    });
                }
            }
        }

        // Performance optimizations
        document.addEventListener('DOMContentLoaded', function() {
            // Lazy loading images
            if ('loading' in HTMLImageElement.prototype) {
                // Native lazy loading supported
                const lazyImages = document.querySelectorAll('img:not([loading])');
                lazyImages.forEach(img => {
                    if (!img.hasAttribute('fetchpriority')) {
                        img.setAttribute('loading', 'lazy');
                    }
                });
            } else {
                // Fallback for browsers without native lazy loading
                const lazyloadScript = document.createElement('script');
                lazyloadScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
                lazyloadScript.async = true;
                document.body.appendChild(lazyloadScript);

                const lazyImages = document.querySelectorAll('img:not([loading])');
                lazyImages.forEach(img => {
                    img.classList.add('lazyload');
                    img.setAttribute('data-src', img.src);
                    img.src = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
                });
            }
        });
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
                        <p>ช่วยให้เราสามารถเข้าใจวิธีที่ผู้ใช้โต้ตอบกับเว็บไซต์ของเรา</p>
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


