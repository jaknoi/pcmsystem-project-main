@extends('master')
@section('title', 'Procurement System')

@section('info')
    <div class="card">
        <div class="card-body">
            <h1 class="mb-4">รายการจัดซื้อจัดจ้าง</h1>

            <!-- Search Form -->
            <form action="{{ url('/page/listpdf') }}" method="GET" class="mb-3 d-flex justify-content-end">
                <div class="input-group" style="max-width: 300px;">
                    <input type="text" name="search" class="form-control" placeholder="ค้นหา..." value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>ประเภท</th>
                            <th>เหตุผล</th>
                            <th>คณะ</th>
                            <th>วันที่สร้างไฟล์</th>
                            <th>ระยะเวลาแล้วเสร็จ</th>
                            <th>การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($info->where('status', 'Complete') as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->methode_name }}</td>
                                <td>{{ $item->reason_description }}</td>
                                <td>{{ $item->office_name }}</td>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                <td>{{ $item->devilvery_time }}</td>
                                <td>
                                    <a href="{{ url('preview-pdf/' . $item->id) }}" role="button" class="btn btn-outline-danger" target="_blank">
                                        <i class="fas fa-file-pdf"></i> ดูตัวอย่าง PDF
                                    </a>
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $info->links('pagination::bootstrap-4') }}
            </div>

            <!-- SweetAlert2 CSS และ JS -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

            <!-- แจ้งเตือน SweetAlert2 -->
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

        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ url('/page') }}" role="button" class="btn btn-danger">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
    </div>

    <!-- FontAwesome CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

@endsection
