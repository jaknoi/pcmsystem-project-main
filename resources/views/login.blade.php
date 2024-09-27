@extends('master')
@section('info')
<main class="login-form">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <h3 class="card-header text-center">เข้าสู่ระบบ</h3>
                    @if(\Session::has('message'))
                        <div class="alert alert-info">
                            {{ \Session::get('message') }}
                        </div>
                    @endif
                    <div class="card-body">
                        <form method="POST" action="{{ route('postlogin') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <input type="text" placeholder="อีเมล" id="email" class="form-control" name="email" autofocus>
                                @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="form-group mb-3 position-relative">
                                <input type="password" placeholder="รหัสผ่าน" id="password" class="form-control" name="password">
                                <span class="position-absolute end-0 top-0 mt-2 me-2" id="password-icon" style="display: none;">
                                    <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                                </span>
                                @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> จดจำฉัน
                                    </label>
                                </div>
                            </div>
                            <div class="d-grid mx-auto">
                                <button type="submit" class="btn btn-primary">ล็อกอินเข้าสู่ระบบ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- ลิงก์ไปยัง Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<script>
    const passwordField = document.getElementById("password");
    const togglePasswordIcon = document.getElementById("password-icon");
    const togglePassword = document.getElementById("togglePassword");

    // ตรวจสอบการพิมพ์ในฟิลด์รหัสผ่าน
    passwordField.addEventListener("input", function() {
        if (passwordField.value) {
            togglePasswordIcon.style.display = "block"; // แสดงไอคอน
        } else {
            togglePasswordIcon.style.display = "none"; // ซ่อนไอคอน
        }
    });

    // สลับแสดง/ซ่อนรหัสผ่าน
    togglePassword.addEventListener("click", function() {
        if (passwordField.type === "password") {
            passwordField.type = "text";
            togglePassword.classList.remove("fa-eye");
            togglePassword.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            togglePassword.classList.remove("fa-eye-slash");
            togglePassword.classList.add("fa-eye");
        }
    });
</script>
@endsection
