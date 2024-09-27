@extends('master')

@section('title', 'Summary')

@section('info')
<div class="container">
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5>ค้นหารายการจัดซื้อจัดจ้าง</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('summary') }}">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="year" class="form-label">ปี พ.ศ.</label>
                        <select class="form-select" id="year" name="year">
                            @for ($i = 2560; $i <= now()->year + 543; $i++)
                                <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary mt-4">ค้นหา</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card สำหรับตารางข้อมูล -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>รายการจัดซื้อจัดจ้าง</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>สรุปรายการจัดซื้อจัดจ้าง</th>
                        <th>ดาวน์โหลดไฟล์ PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($monthlySummaries as $summary)
                        <tr>
                            <td>{{ $summary['title'] }}</td>
                            <td>
                                <a href="#" class="btn btn-outline-danger">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                <a href="{{ url('/page') }}" role="button" class="btn btn-danger">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
