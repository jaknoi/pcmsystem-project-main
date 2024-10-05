<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Blog System')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: 'Prompt', sans-serif;
            overflow-x: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
            position: relative;
            min-height: 100vh;
            z-index: 1;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/bg1.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.5;
            z-index: -1;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .navbar-custom {
            background-color: #092174;
            padding: 1rem 2rem;
        }

        .navbar-custom .navbar-brand {
            color: #ffffff;
            display: flex;
            align-items: center;
            font-family: 'Prompt', sans-serif;
        }

        .navbar-custom .navbar-brand img {
            height: 100px;
            margin-right: 1rem;
        }

        .logo-divider {
            border-left: 2px solid #ffffff;
            height: 100px;
            margin-right: 1rem;
        }

        .logo-text {
            color: #ffffff;
            text-align: left;
            font-family: 'Prompt', sans-serif;
        }

        .main-title {
            font-size: 1.7rem;
            font-weight: bold;
        }

        .sub-title {
            font-size: 1.2rem;
        }

        .navbar-custom .navbar-nav .nav-link {
            font-size: 1rem;
            color: #ffffff;
        }

        .navbar-custom .navbar-nav .nav-link.active {
            color: #ffffff;
        }

        .user-info,
        .budget-info {
            color: #ffffff;
            text-align: right;
            font-family: 'Prompt', sans-serif;
        }

        .user-info {
            font-size: 1.5rem;
        }

        .budget-info {
            font-size: 1.3rem;
        }

        .btn-budget {
            background-color: #dc3545;
            border: none;
            font-size: 1.1rem;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-budget:hover {
            background-color: #c82333;
            transform: scale(1.05);
            color: #ffffff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-budget:active {
            background-color: #bd2130;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transform: scale(0.98);
        }

        .footer-main {
            background-color: #5EC7E2;
            color: #ffffff;
            padding: 2rem 0;
        }

        .footer-dark {
            background-color: #092174;
            color: #ffffff;
            padding: 1rem 0;
        }

        .footer-main h5 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .footer-main p {
            margin: 0.5rem 0;
            font-size: 1rem;
        }

        .footer-main .social-icons a {
            text-decoration: none;
            color: #ffffff;
            font-size: 1.5rem;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .footer-main .social-icons a:hover {
            color: #dc3545;
        }

        .footer-dark p {
            margin: 0;
            font-size: 1rem;
        }

        .map-frame {
            border: 0;
            border-radius: 15px;
            overflow: hidden;
        }

        /* Media queries for smaller screens */
        @media (max-width: 768px) {
            .navbar-custom .navbar-brand img {
                height: 70px; /* Reduce logo size */
                margin-right: 0.5rem;
            }

            .logo-divider {
                height: 70px; /* Reduce divider height */
                margin-right: 0.5rem;
            }

            .main-title {
                font-size: 1.3rem; /* Smaller title */
            }

            .sub-title {
                font-size: 1rem; /* Smaller subtitle */
            }

            .navbar-custom .navbar-brand {
                flex-wrap: wrap;
                text-align: center;
            }

            /* Center the content in mobile view */
            .navbar-custom .navbar-nav {
                text-align: center;
                display: flex;
                flex-direction: column;
            }

            .user-info, .budget-info {
                text-align: center;
                font-size: 1.1rem; /* Reduce font size on mobile */
            }

            .btn-budget {
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/page') }}">
            <img src="{{ asset('images/tsu.png') }}" alt="Logo">
            <div class="logo-divider"></div>
            <div class="logo-text">
                <span class="main-title">ระบบจัดทำเอกสารจัดซื้อจัดจ้างอัตโนมัติ</span><br>
                <span class="sub-title">คณะวิทยาศาสตร์และนวัตกรรมดิจิทัล</span>
            </div>
        </a>
        
        <!-- Hamburger Menu Icon -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item d-flex flex-column align-items-end">
                    @auth
                        <span class="navbar-text user-info">
                            สวัสดีคุณ {{ Auth::user()->name }}
                        </span>
                    @else
                        <span class="navbar-text user-info"></span>
                    @endauth

                    @auth
                        <span class="navbar-text budget-info">
                            งบประมาณคงเหลือปัจจุบัน: 
                            {{ $budget ? number_format($budget->remaining_amount, 2) : '0.00' }} บาท
                        </span>
                        <a href="{{ route('budget.add') }}" class="btn btn-danger btn-budget">เพิ่มงบประมาณ</a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container content">
    @yield('info')
</div>

<footer class="footer-main">
    <div class="container text-light">
        <div class="row justify-content-between align-items-start">
            <div class="col-md-4 text-start mb-4">
                <h5 class="fw-bold">เกี่ยวกับเรา:</h5>
                <p>ระบบจัดทำเอกสารจัดซื้อจัดจ้างอัตโนมัติ</p>
                <p>อีเมล: <a href="mailto:642021143@tsu.ac.th" class="text-light fw-bold">642021143@tsu.ac.th</a></p>
                <p>อีเมล: <a href="mailto:642021148@tsu.ac.th" class="text-light fw-bold">642021148@tsu.ac.th</a></p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <h5 class="fw-bold">ติดตามเรา:</h5>
                <div class="social-icons">
                    <a href="https://www.facebook.com/scidi.tsu" target="_blank" class="me-2">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    <a href="https://www.instagram.com/scidi_tsu/" target="_blank" class="me-2">
                        <i class="fab fa-instagram-square"></i>
                    </a>
                    <a href="https://x.com/sci_tsu" target="_blank" class="me-2">
                        <i class="fab fa-twitter-square"></i>
                    </a>
                    <a href="https://www.youtube.com/c/ScienceTsu" target="_blank">
                        <i class="fab fa-youtube-square"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <h5 class="fw-bold">แผนที่:</h5>
                <iframe class="map-frame" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.8119375937313!2d99.9435492!3d7.8097232000000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x305287a57f46a313%3A0xf0223bd0b5f3070!2sThaksin%20University%2C%20Phatthalung%20Campus!5e0!3m2!1sen!2sth!4v1727665715128!5m2!1sen!2sth"
                    width="400" height="300" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</footer>

<footer class="footer-dark">
    <div class="container text-center">
        <p class="mb-0">Copyright © 2024 www.tsu.ac.th All rights reserved</p>
    </div>
</footer>

<!-- Bootstrap JavaScript (ensure it's included at the bottom) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</body>

</html>
