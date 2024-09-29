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

        /* การ์ด */
        .custom-card {
            width: 50rem;
            height: 10rem;
            overflow: hidden;
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

        /* รูปภาพภายในการ์ด */
        .custom-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
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
                </a>
                </div>
            </div>
        </div>
    </div>
@endsection
