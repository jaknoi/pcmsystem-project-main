@extends('master')

@section('title', 'Usage History')

@section('info')
<div class="container mt-4">

   
        <div class="card mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4>ประวัติการใช้งาน</h4>
                <i class="fas fa-clock"></i> <!-- ไอคอนนาฬิกาที่มุมขวาบน -->
            </div>

            <div class="card-body">
                <form action="{{ url('/page/history') }}" method="GET" class="mb-3 d-flex justify-content-end">
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" name="search" class="form-control" placeholder="ค้นหา..." value="{{ request()->query('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ชื่อผู้ใช้งาน</th> <!-- แสดงชื่อผู้ใช้ -->
                                <th>กิจกรรม</th>
                                <th>รายละเอียด</th>
                                <th>เวลา</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $entry)
                                <tr>
                                    <td>{{ $entry->user ? $entry->user->name : 'Unknown User' }}</td> <!-- แสดงชื่อผู้ใช้ -->
                                    <td>{{ $entry->activity }}</td>
                                    <td>{{ $entry->details }}</td>
                                    <td>{{ $entry->created_at->format('d-m-Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- ลิงก์สำหรับการแบ่งหน้า -->
                <div class="d-flex justify-content-center">
                    {{ $history->links('pagination::bootstrap-4') }}
                </div>
                
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ url('/page') }}" role="button" class="btn btn-danger">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
            </div>
        </div>
    
</div>

<!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    .btn {
        transition: transform 0.3s, background-color 0.3s;
        display: flex;
        align-items: center;
    }
    .btn i {
        margin-right: 5px; /* ระยะห่างระหว่างไอคอนกับข้อความ */
    }
    .btn:hover {
        transform: scale(1.05); /* ซูมเข้าขณะ hover */
    }
    .card-header i {
        font-size: 24px; /* ขนาดไอคอนนาฬิกา */
    }
</style>
@endsection
