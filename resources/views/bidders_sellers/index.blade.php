@extends('master')
@section('title', 'Procurement System')

@section('info')
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h1 class="mb-4 text-center text-danger">เพิ่มข้อมูลผู้ขายและเจ้าหน้าที่</h1>

            <div class="row">
                <!-- ฟอร์มสำหรับผู้ขาย -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-dark hover-card">
                        <div class="card-header bg-dark text-white">ข้อมูลผู้ขาย</div>
                        <div class="card-body">
                            <form action="{{ route('bidders_sellers.storeSeller', ['id' => $info->id ?? null]) }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="info_id" value="{{ $info->id ?? '' }}">
                                <div class="mb-3">
                                    <label for="seller_name" class="form-label">ชื่อผู้ขาย</label>
                                    <input type="text" class="form-control" id="seller_name" name="seller_name"
                                        placeholder="กรอกชื่อผู้ขาย" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">ที่อยู่</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        placeholder="กรอกที่อยู่ผู้ขาย" required>
                                </div>
                                <div class="mb-3">
                                    <label for="taxpayer_number" class="form-label">หมายเลขผู้เสียภาษี</label>
                                    <input type="text" class="form-control" id="taxpayer_number" name="taxpayer_number"
                                        placeholder="กรอกหมายเลขผู้เสียภาษี" required>
                                </div>
                                <button type="submit"
                                    class="btn btn-primary w-100 hover-btn">บันทึกข้อมูลผู้ขาย</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ฟอร์มสำหรับเจ้าหน้าที่ -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-dark hover-card">
                        <div class="card-header bg-dark text-white">ข้อมูลเจ้าหน้าที่</div>
                        <div class="card-body">
                            <form action="{{ route('bidders_sellers.storeBidder', ['id' => $info->id ?? null]) }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="info_id" value="{{ $info->id ?? '' }}">
                                <div class="mb-3">
                                    <label for="bidder_name" class="form-label">ชื่อเจ้าหน้าที่</label>
                                    <input type="text" class="form-control" id="bidder_name" name="bidder_name"
                                        placeholder="กรอกชื่อเจ้าหน้าที่" required>
                                </div>
                                <div class="mb-3">
                                    <label for="bidder_position" class="form-label">ตำแหน่ง</label>
                                    <select class="form-select" id="bidder_position" name="bidder_position" required>
                                        <option value="" selected disabled>เลือกตำแหน่ง</option>
                                        <option value="หัวหน้าเจ้าหน้าที่">หัวหน้าเจ้าหน้าที่</option>
                                        <option value="เจ้าหน้าที่">เจ้าหน้าที่</option>
                                    </select>
                                </div>
                                <button type="submit"
                                    class="btn btn-primary w-100 hover-btn">บันทึกข้อมูลเจ้าหน้าที่</button>
                            </form>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <!-- รายการผู้ขายที่มีอยู่ -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-success hover-card">
                            <div class="card-header bg-success text-white">รายชื่อผู้ขายที่มีอยู่</div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($sellers->unique('seller_name') as $seller)
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center list-item-hover">
                                        <div>
                                            <strong>{{ $seller->seller_name }}</strong> - ที่อยู่ {{ $seller->address }}
                                            - เลขประจำตัวผู้เสียภาษี {{ $seller->taxpayer_number }}
                                        </div>

                                        <!-- ปุ่มแก้ไขและลบ -->
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('bidders_sellers.editSeller', ['id' => $seller->id]) }}"
                                                class="btn btn-sm btn-outline-warning hover-btn">แก้ไข</a>
                                            <form
                                                action="{{ route('bidders_sellers.deleteSeller', ['id' => $seller->id]) }}"
                                                method="POST" onsubmit="return confirmDelete(event)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger hover-btn">ลบ</button>
                                            </form>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- รายการเจ้าหน้าที่ที่มีอยู่ -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-success hover-card">
                            <div class="card-header bg-success text-white">รายชื่อเจ้าหน้าที่ที่มีอยู่</div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($bidders->unique('bidder_name') as $bidder)
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center list-item-hover">
                                        <div>
                                            <strong>{{ $bidder->bidder_name }}</strong> - ตำแหน่ง:
                                            {{ $bidder->bidder_position }}
                                        </div>

                                        <!-- ปุ่มแก้ไขและลบ -->
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('bidders_sellers.editBidder', ['id' => $bidder->id]) }}"
                                                class="btn btn-sm btn-outline-warning hover-btn">แก้ไข</a>
                                            <form
                                                action="{{ route('bidders_sellers.deleteBidder', ['id' => $bidder->id]) }}"
                                                method="POST" onsubmit="return confirmDelete(event)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger hover-btn">ลบ</button>
                                            </form>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ url('/page') }}" role="button" class="btn btn-danger hover-btn">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
            </div>
        </div>
    </div>
</div>
<!-- SweetAlert2 CSS และ JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ตรวจสอบว่ามี session 'success' หรือไม่
    @if(session('success'))

    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: '{{ session('
        success ') }}',
        timer: 3000,
        showConfirmButton: false
    });

    @endif
});

function confirmDelete(event) {
    event.preventDefault();
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณไม่สามารถย้อนกลับการลบนี้ได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.submit(); // ถ้ายืนยันจะทำการ submit
        }
    });
}
</script>

<style>
/* Hover effects */
.hover-card:hover {
    transform: translateY(-5px);
    transition: 0.3s;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.btn {
    transition: transform 0.3s;
}

.btn:hover {
    transform: scale(1.01);
    /* ซูมเข้าขณะ hover */
}

.list-item-hover:hover {
    background-color: #f8f9fa;
    transition: background-color 0.3s;
}
</style>

@endsection