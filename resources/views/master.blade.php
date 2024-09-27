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
        }

        .user-info {
            font-size: 1.5rem;
        }

        .budget-info {
            font-size: 1.3rem;
        }

        footer {
            background-color: #5EC7E2;
            color: #000000;
            padding: 1rem 0;
            text-align: center;
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
                                งบประมาณคงเหลือปัจจุบัน: {{ number_format($budget->remaining_amount, 2) }} บาท
                            </span>
                            <!-- ปุ่มเปิด modal -->
                            <a href="{{ route('budget.add') }}" class="btn btn-danger">เพิ่มงบประมาณ</a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container content">
        @yield('info')
    </div>

    <!-- Modal สำหรับเพิ่มงบประมาณ -->
    @auth
        <div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBudgetModalLabel">เพิ่มงบประมาณ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('budget.add') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="budget_amount" class="form-label">จำนวนงบประมาณ</label>
                                <input type="number" class="form-control" id="budget_amount" name="budget_amount" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endauth

    <footer>
        <p>&copy; 2024 www.tsu.ac.th All rights reserved</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-7C1ie8Ed3KjzBBTYjcFad4KN1FwZhG9LFf4+N41yG+HoTC5lZ92R9+FXl5Do8hPM" crossorigin="anonymous">
    </script>
    <!-- สคริปต์แสดง popup -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('addBudgetModal'));
            myModal.show();  // แสดงโมดัลเมื่อหน้าโหลด
        });
    </script>
    
</body>

</html>
