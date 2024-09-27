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
                    <button type="submit" class="btn btn-primary">Search</button>
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
                                    <a href="{{ url('preview-pdf/' . $item->id) }}" role="button" class="btn btn-outline-danger" target="_blank">ดูตัวอย่าง PDF</a>
                                    <a href="{{ route('generate-word', $item->id) }}" role="button" class="btn btn-info">ดาวน์โหลด Word</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $info->links('pagination::bootstrap-4') }}
            </div>
            <div class="mt-3">
                <a href="{{ url('/page') }}" role="button" class="btn btn-danger">Back</a>
            </div>

            @if (session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
@endsection
