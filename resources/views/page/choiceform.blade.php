@extends('master')
@section('title', 'Procurement System')

@section('info')
    <style>
        body {
            background-color: #f8f9fa;
        }

        /* การ์ด */
        .custom-card {
            width: 30rem;
            height: auto;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid black;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* เอฟเฟกต์ hover สำหรับการ์ด */
        .custom-card:hover {
            transform: scale(1.04);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        /* ฟิลด์กรอกข้อมูล */
        .custom-input {
            font-size: 1.5rem;
            text-align: center;
            width: 100%;
            padding: 0.5rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        /* ปุ่ม */
        .custom-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #092174;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .custom-button:hover {
            background-color: #0d47a1;
        }
    </style>

    <div class="container-fluid  d-flex justify-content-center align-items-center">
        <div class="card custom-card">
            <form id="budgetForm">
                <h2 class="text-center mb-4">กรุณากรอกงบประมาณ</h2>
                <input type="text" id="budgetInput" class="custom-input mb-3" placeholder="งบประมาณ" required>
                <button type="submit" class="custom-button">ยืนยัน</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('budgetInput').addEventListener('input', function(e) {
            let value = e.target.value.replace(/,/g, ''); // ลบเครื่องหมายจุลภาคเดิมก่อน
            if (!isNaN(value) && value !== '') {
                e.target.value = Number(value).toLocaleString(); // จัดรูปแบบใหม่โดยใส่จุลภาค
            }
        });

        document.getElementById('budgetForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // รับค่างบประมาณจากช่องกรอก โดยลบจุลภาคออกก่อน
            const budget = parseFloat(document.getElementById('budgetInput').value.replace(/,/g, ''));

            // ตรวจสอบค่างบประมาณและพาไปยังหน้าที่ต้องการ
            if (budget > 100000) {
                window.location.href = "{{ route('page.createk') }}";
            } else {
                window.location.href = "{{ route('page.create') }}";
            }
        });
    </script>
@endsection
