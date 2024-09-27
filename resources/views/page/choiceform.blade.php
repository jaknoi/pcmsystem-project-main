@extends('master')
@section('title', 'Procurement System')

@section('info')
    <style>
        body {
            transform: scale(0.8);
            /* ลดขนาดลง 20% */
            transform-origin: top left;
            /* จุดศูนย์กลางการย่อขนาดอยู่ที่มุมซ้ายบน */
            width: 125%;
            /* ปรับขนาดให้พอดีกับการย่อขนาด */
            height: 125%;
            /* ปรับขนาดให้พอดีกับการย่อขนาด */
        }

        .custom-card {
            width: 50rem;
            /* กำหนดความกว้างของการ์ด */
            height: 10rem;
            /* กำหนดความสูงของการ์ด */
            overflow: hidden;
            /* ซ่อนส่วนที่เกินออกไปจากกรอบการ์ด */
            display: flex;
            /* ใช้ Flexbox เพื่อให้รูปภาพเต็มกรอบ */
            justify-content: center;
            /* จัดให้อยู่กลางการ์ด */
            align-items: center;
            /* จัดให้อยู่กลางการ์ด */
            border: 1px solid black;
            /* เพิ่มขอบสีดำ */
        }

        .custom-card-img {
            width: 100%;
            /* ทำให้รูปภาพเต็มความกว้างของการ์ด */
            height: 100%;
            /* ทำให้รูปภาพเต็มความสูงของการ์ด */
            object-fit: cover;
            /* ทำให้รูปภาพเต็มกรอบโดยไม่ทำให้เบี้ยว */
        }
    </style>

    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="d-flex flex-column align-items-center">
            <!-- Card 1 -->
            <div class="mb-5"> <!-- เพิ่มระยะห่างระหว่างการ์ด -->
                <div class="card custom-card">
                    <a href="{{ route('page.create') }}" class="btn-img">
                        <img src="{{ asset('images/choiceform1.png') }}" class="card-img-top custom-card-img" alt="Image 1">
                    </a>
                </div>
            </div>

            <!-- Card 2 -->
            <div>
                <div class="card custom-card">
                    <a href="{{ route('page.createk') }}" class="btn-img">
                    <img src="{{ asset('images/choiceform2.png') }}" class="card-img-top custom-card-img" alt="Image 2">
                </div>
            </div>
        </div>
    </div>
@endsection
