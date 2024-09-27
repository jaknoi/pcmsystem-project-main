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
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('budget.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="budget_amount" class="form-label">จำนวนงบประมาณ</label>
                            <input type="text" class="form-control" id="budget_amount" name="budget_amount" required>
                            <small class="text-danger">โปรดตรวจสอบก่อนกดบันทึก**</small>
                        </div>
                        
                        
                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                            <a href="{{ url('/page') }}" role="button" class="btn btn-danger">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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

        // เมื่อทำการ submit ฟอร์มจะต้องนำ , ออกจากค่า input ก่อนส่งไปยัง server
        document.querySelector('form').addEventListener('submit', function () {
            budgetInput.value = budgetInput.value.replace(/,/g, '');
        });
    });
</script>
@endsection
