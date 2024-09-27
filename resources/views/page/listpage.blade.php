@extends('master')
@section('title', 'Procurement System')

@section('info')
    <div class="card">
        <div class="card-body">
            <h1>รายการจัดซื้อจัดจ้าง</h1>

            <!-- Search Form -->
            <form action="{{ url('/page/list') }}" method="GET" class="mb-3 d-flex justify-content-end">
                <div class="input-group" style="width: 300px;">
                    <input type="text" name="search" class="form-control" placeholder="ค้นหา..."
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>id</th>
                            <th>ประเภท</th>
                            <th>เหตุผล</th>
                            <th>คณะ</th>
                            <th>วันที่สร้างไฟล์</th>
                            <th>ระยะเวลาแล้วเสร็จ</th>
                            <th>การดำเนินการ</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($info as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->methode_name }}</td>
                                <td>{{ $item->reason_description }}</td>
                                <td>{{ $item->office_name }}</td>
                                <td>{{ $item->created_at->format('d/m/y') }}</td>
                                <td>{{ $item->devilvery_time }}</td>

                                <td>
                                    @if ($item->status != 'Complete')
                                        <a href="{{ route('page.confirm', $item->id) }}" role="button"
                                            class="btn btn-sm btn-danger">Convert</a>
                                        <a href="{{ url("page/{$item->id}/edit") }}" role="button"
                                            class="btn btn-sm btn-warning">Edit</a>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>Convert</button>
                                        <button class="btn btn-sm btn-secondary" disabled>Edit</button>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status == 'Complete')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                <!-- Pagination -->
                {{ $info->links('pagination::bootstrap-4') }}
            </div>
            <div class="mb-2">
                <a href="{{ url('/page') }}" role="button" class="btn btn-danger">Back</a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
@endsection
