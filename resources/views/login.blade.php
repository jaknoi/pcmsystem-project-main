<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>เข้าสู่ระบบ</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: 'Prompt', sans-serif;
            overflow-x: hidden;
            background-color: #f5f7fa;
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
            background-image: url('images/bg1.jpg');
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

        .login-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .login-container:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .login-container h3 {
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #092174;
        }

        .text-muted {
            font-size: 1.1rem;
            color: #6c757d !important;
        }

        .btn-primary {
            background-color: #092174;
            border: none;
            padding: 10px;
            font-size: 1.2rem;
            border-radius: 30px;
            transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #081b5f;
            transform: translateY(-3px);
        }

        input.form-control {
            border: 2px solid #dee2e6;
            padding: 10px;
            border-radius: 10px;
            transition: border-color 0.3s ease-in-out;
        }

        input.form-control:focus {
            border-color: #092174;
            box-shadow: none;
        }

        .form-label {
            font-size: 1rem;
            font-weight: 600;
            color: #092174;
        }

        .form-check-input {
            margin-top: 0.35rem;
            margin-left: 0;
        }

        .form-check-label {
            margin-left: 1.25rem;
        }

        .logo-divider {
            border-left: 2px solid #ffffff;
            height: 60px;
            margin-right: 1rem;
        }

       
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; background-size: cover; background-position: center;">
        <div class="login-container bg-white p-5 rounded shadow-lg" style="width: 100%;">

            <!-- Logo and Title (similar to Navbar) -->
            <div class="text-center mb-4">
                <img src="{{ asset('images/tsu.png') }}" alt="Logo" style="height: 100px;">
                <h3 class="mt-2">ระบบจัดทำเอกสารจัดซื้อจัดจ้างอัตโนมัติ</h3>
                <p class="text-muted">คณะวิทยาศาสตร์และนวัตกรรมดิจิทัล</p>
            </div>

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">อีเมล</label>
        <input type="email" class="form-control" placeholder="Email" name="email" required>
        @if ($errors->has('email'))
            <span class="text-danger">{{ $errors->first('email') }}</span>
        @endif
    </div>
    
    <div class="mb-3">
        <label for="password" class="form-label">รหัสผ่าน</label>
        <input type="password" placeholder="Password" id="password" class="form-control" name="password" required>
        @if ($errors->has('password'))
            <span class="text-danger">{{ $errors->first('password') }}</span>
        @endif
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">จดจำฉัน</label>
    </div>

    <button type="submit" class="btn btn-primary w-100">ล็อกอินเข้าสู่ระบบ</button>
</form>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- ลิงก์ไปยัง Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<!-- SweetAlert2 CSS และ JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
    
    document.addEventListener('DOMContentLoaded', function() {
    // ตรวจสอบว่ามี session 'login_success' หรือไม่
    @if(session('login_success'))
        Swal.fire({
            icon: 'success',
            title: 'ล็อกอินสำเร็จ!',
            text: '{{ session('login_success') }}',
            timer: 3000, // เวลาแสดงผล
            showConfirmButton: false // ไม่แสดงปุ่มยืนยัน
        });
    @endif

    // ตรวจสอบ session สำหรับการล็อกเอาต์
    @if(session('logout'))
        Swal.fire({
            icon: 'success',
            title: 'ล็อกเอาต์สำเร็จ!',
            text: 'คุณได้ออกจากระบบเรียบร้อยแล้ว!',
            timer: 3000, // เวลาแสดงผล
            showConfirmButton: false // ไม่แสดงปุ่มยืนยัน
        });
    @endif
});



</script>
</body>

</html>
