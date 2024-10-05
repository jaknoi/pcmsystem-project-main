@extends('master')
@section('title', 'Procurement System')

@section('info')
    <style>
        body {
            /* You can add any body styles here if needed */
        }
        .btn-img {
            width: 80%;
            height: 0;
            padding-bottom: 80%;
            position: relative;
            overflow: hidden;
            border: 1px;
            margin: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            transition: transform 0.3s ease; /* Zoom effect */
        }
        .btn-img:hover {
            transform: scale(1.1); /* Zoom in effect on hover */
        }
        .btn-img img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .no-hover {
            pointer-events: none; /* Disable hover effect */
        }
        @media (max-width: 576px) {
            .btn-img {
                padding-bottom: 100%;
            }
        }
        .alert {
            margin: 1rem;
        }
    </style>
    <div class="content">
        <div class="container py-4">
            <div class="row">
                <div class="col-6 col-md-3 mb-3">
                    <a href="{{ route('page.choiceform') }}" class="btn-img">
                        <img src="{{ asset('images/createpcm.png') }}" alt="Create PCM">
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <a href="{{ route('bidders_sellers.index') }}" class="btn-img">
                        <img src="{{ asset('images/addbs.png') }}" alt="ADD BS">
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <a href="{{ route('page.listpdf') }}" class="btn-img">
                        <img src="{{ asset('images/downloadpdf.png') }}" alt="Download PDF">
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <a class="btn-img no-hover">
                        <img src="{{ asset('images/img2.png') }}" alt="Image 4">
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-md-3 mb-3">
                    <a class="btn-img no-hover">
                        <img src="{{ asset('images/img3.png') }}" alt="Image 5">
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <a href="{{ route('page.list') }}" class="btn-img">
                        <img src="{{ asset('images/listpcm.png') }}" alt="List PCM">
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <a class="btn-img no-hover">
                        <img src="{{ asset('images/img4.png') }}" alt="Image 7">
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <a href="{{ url('/page/history') }}" class="btn-img">
                        <img src="{{ asset('images/logfile.png') }}" alt="Log File">
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-md-3 mb-3">
                    <a href="{{ url('/summary') }}" class="btn-img">
                        <img src="{{ asset('images/sm.png') }}" alt="Image 9">
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <a class="btn-img no-hover">
                        <img src="{{ asset('images/img5.png') }}" alt="Image 10">
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <a href="{{ url('/dashboard') }}" class="btn-img">
                        <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard">
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btn-img">
                            <img src="{{ asset('images/logout.png') }}" alt="Logout">
                        </button>
                    </form>
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

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

@endsection
