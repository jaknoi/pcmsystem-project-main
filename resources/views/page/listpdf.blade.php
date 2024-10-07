@extends('master')
@section('title', 'Procurement System')

@section('info')
<div class="card">
    <div class="card-body">
        <h1>รายการจัดซื้อจัดจ้าง</h1> 

        <!-- Search Form -->
        <form action="{{ url('/page/listpdf') }}" method="GET" class="mb-3 d-flex justify-content-end">
            <div class="input-group" style="width: 300px;">
                <input type="text" name="search" class="form-control" placeholder="ค้นหา..."
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <!-- จัดกึ่งกลางตาราง -->
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>ประเภท</th>
                        <th>เหตุผล</th>
                        <th>วันที่สร้างไฟล์</th>
                        <th>ชื่อผู้ขาย</th>
                        <th>เอกสารอ้างอิง</th>
                        <th>การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($info->where('status', 'Complete') as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->methode_name }}</td>
                        <td>{{ $item->reason_description }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if ($item->sellers && $item->sellers->count() > 0)
                            @foreach ($item->sellers as $seller)
                            {{ $seller->seller_name }}<br>
                            @endforeach
                            @else
                            ไม่มีข้อมูลผู้ขาย
                            @endif
                        </td>
                        <td>
                            @if ($item->sellers && $item->sellers->count() > 0)
                            @foreach ($item->sellers as $seller)
                            {{ $seller->reference_documents }}<br>
                            @endforeach
                            @else
                            ไม่มีข้อมูลผู้ขาย
                            @endif
                        </td>
                        <td>
                            <a href="{{ url('preview-pdf/' . $item->id) }}" role="button" class="btn btn-outline-danger"
                                target="_blank">
                                <i class="fas fa-file-pdf"></i> ดูตัวอย่าง PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $info->links('pagination::bootstrap-4') }}
        </div>

        <!-- SweetAlert2 CSS และ JS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

        <!-- แจ้งเตือน SweetAlert2 -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
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

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
h1 {
    font-size: 2rem;
    color: #333;
}
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.table {
    border: 1px solid #dee2e6;
}

.table th,
.table td {
    vertical-align: middle;
}

.btn {
    transition: transform 0.3s;
}

.btn:hover {
    transform: scale(1.05);
}

</style>

@endsection