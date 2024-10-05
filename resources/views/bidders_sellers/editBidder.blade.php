@extends('master')

@section('title', 'แก้ไขข้อมูลเจ้าหน้าที่')

@section('info')
<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <h1 class="mb-4 text-center text-primary">แก้ไขข้อมูลเจ้าหน้าที่</h1>

            <!-- ฟอร์มแก้ไขข้อมูลเจ้าหน้าที่ -->
            <form action="{{ route('bidders_sellers.updateBidder', ['id' => $bidder->id]) }}" method="POST" class="p-3">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="bidder_name" class="form-label">ชื่อเจ้าหน้าที่</label>
                    <input type="text" class="form-control border-primary" id="bidder_name" name="bidder_name" value="{{ $bidder->bidder_name }}" required>
                </div>

                <div class="mb-3">
                    <label for="bidder_position" class="form-label">ตำแหน่ง</label>
                    <input type="text" class="form-control border-primary" id="bidder_position" name="bidder_position" value="{{ $bidder->bidder_position }}" required>
                </div>

                <button type="submit" class="btn btn-success w-100 hover-btn">บันทึกการเปลี่ยนแปลง</button>
            </form>
             <!-- ปุ่มย้อนกลับ -->
    <div class="mt-3">
        <a href="{{ route('bidders_sellers.index') }}" class="btn btn-danger hover-btn">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
    </div>
        </div>
    </div>

   
</div>

<!-- SweetAlert2 CSS และ JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- เพิ่มแจ้งเตือนด้วย SweetAlert2 -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    });
</script>

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
