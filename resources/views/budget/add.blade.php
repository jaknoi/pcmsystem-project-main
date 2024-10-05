@extends('master')

@section('title', 'Add budget')

@section('info')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white">
                    <h2 class="mb-0">เพิ่มงบประมาณ</h2>
                </div>
                <div class="card-body">
                    <form id="budgetForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="budget_amount" class="form-label">จำนวนงบประมาณ</label>
                            <input type="text" class="form-control" id="budget_amount" name="budget_amount" required>
                            <small class="text-danger">โปรดตรวจสอบก่อนกดบันทึก**</small>
                        </div>
                        
                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                            <a href="{{ url('/page') }}" role="button" class="btn btn-danger">กลับ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 CSS และ JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- SweetAlert2 JS อยู่ในส่วนนี้ -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ตรวจสอบว่ามี session 'success' หรือไม่
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('success') }}',
            });
        @endif

        const budgetInput = document.getElementById('budget_amount');

        // ฟังก์ชันจัดรูปแบบตัวเลขให้มี , คั่นหลักพัน
        function formatCurrency(value) {
            return value.replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        // Event listener สำหรับเปลี่ยนแปลงค่าที่กรอกในฟิลด์ budget_amount
        budgetInput.addEventListener('input', function (e) {
            let inputValue = e.target.value;
            e.target.value = formatCurrency(inputValue);
        });

        // เมื่อทำการ submit ฟอร์ม
        document.getElementById('budgetForm').addEventListener('submit', function (e) {
            e.preventDefault(); // ป้องกันการ submit ปกติ
            
            const budgetValue = budgetInput.value.replace(/,/g, ''); // เอาจุดคั่นออกเพื่อให้ได้ค่าที่แท้จริง
            
            // แสดง SweetAlert2 ยืนยัน
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: `คุณต้องการบันทึกงบประมาณจำนวน ${formatCurrency(budgetValue)} บาท หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // เมื่อผู้ใช้กดยืนยันให้บันทึกข้อมูล
                    budgetInput.value = budgetInput.value.replace(/,/g, ''); // นำ , ออก
                    this.submit(); // ส่งฟอร์ม
                }
            });
        });
    });
</script>
@endsection
