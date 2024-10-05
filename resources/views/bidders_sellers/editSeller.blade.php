@extends('master')

@section('title', 'แก้ไขข้อมูลผู้ขาย')

@section('info')
<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <h1 class="mb-4 text-center text-primary">แก้ไขข้อมูลผู้ขาย</h1>

            <!-- แสดงข้อความสำเร็จด้วย SweetAlert -->
            @if(session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: '{{ session('success') }}',
                        timer: 3000,
                        showConfirmButton: false
                    });
                </script>
            @endif

            <form action="{{ route('bidders_sellers.updateSeller', ['id' => $seller->id]) }}" method="POST" class="p-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="info_id" value="{{ $seller->info_id }}">

                <div class="mb-3">
                    <label for="seller_name" class="form-label">ชื่อผู้ขาย</label>
                    <input type="text" class="form-control border-primary" id="seller_name" name="seller_name" value="{{ $seller->seller_name }}" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">ที่อยู่</label>
                    <input type="text" class="form-control border-primary" id="address" name="address" value="{{ $seller->address }}" required>
                </div>

                <div class="mb-3">
                    <label for="taxpayer_number" class="form-label">หมายเลขผู้เสียภาษี</label>
                    <input type="text" class="form-control border-primary" id="taxpayer_number" name="taxpayer_number" value="{{ $seller->taxpayer_number }}" required>
                </div>

                <button type="submit" class="btn btn-success w-100 hover-btn">บันทึกการเปลี่ยนแปลง</button>
            </form>
            <div class="mt-3">
        <a href="{{ route('bidders_sellers.index') }}" class="btn btn-danger hover-btn">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
    </div>
        </div>
    </div>

    <!-- ปุ่มย้อนกลับ -->
    
</div>

<!-- SweetAlert2 CSS และ JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- ปรับแต่ง CSS -->
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .btn {
        transition: transform 0.3s;
    }

    .btn:hover {
        transform: scale(1.01); /* ซูมเข้าขณะ hover */
    }

    

    
</style>
@endsection
