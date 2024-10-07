@extends('master')
@section('title', 'Procurement System')

@section('info')
<div class="container mt-4">

    @if(session('error'))
    <script>
    Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: '{{ session('
        error ') }}',
    });
    </script>
    @endif

    <div class="card modern-card">
        <div class="card-body">
            <h2 class="text-center mb-4">สรุปรายการจัดซื้อจัดจ้าง</h2>
            <form action="{{ route('filter.summary') }}" method="GET" class="mb-3">
                <div class="d-flex align-items-center justify-content-center">
                    <label for="year" class="me-2 mb-0">เลือกปีงบประมาณ:</label>
                    <select name="year" id="year" class="form-select form-select-sm me-2" style="width: auto;">
                        @for ($i = 2020; $i <= date('Y') + 1; $i++) <!-- เพิ่ม date('Y') + 1 เพื่อให้เลือกปีถัดไปได้ -->
                            <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                    </select>
                    <button type="submit" class="btn btn-success btn-sm">ค้นหา</button>
                </div>
            </form>


            <div class="mt-4">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>สรุปรายการจัดซื้อจัดจ้าง</th>
                            <th>ดาวน์โหลด PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- แสดงรายเดือน -->
                        @if($groupedMonthlyData->isNotEmpty())
                        @foreach($groupedMonthlyData as $month => $data)
                        <tr>
                            <td>สรุปผลการจัดซื้อจัดจ้างรายเดือน
                                {{ \Carbon\Carbon::parse($month)->locale('th')->translatedFormat('F Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('generate.monthly.pdf', ['month' => \Carbon\Carbon::parse($month)->month, 'year' => \Carbon\Carbon::parse($month)->year]) }}"
                                    class="btn btn-pdf">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="2" class="text-center">ไม่มีข้อมูลรายเดือนในปี {{ $currentYear }}</td>
                        </tr>
                        @endif

                        
                        <!-- แสดงรายไตรมาส -->
                        @if(isset($groupedQuarterlyData) && $groupedQuarterlyData->isNotEmpty())
                        @foreach($groupedQuarterlyData as $quarter => $data)
                        <tr>
                            <td>
                                @php
                                preg_match('/Q(\d) (\d{4})/', $quarter, $matches);
                                $quarterNumber = $matches[1];
                                $year = $matches[2];
                                if ($quarterNumber == 1) {
                                $startMonth = 10; // October
                                $endMonth = 12; // December
                                } elseif ($quarterNumber == 2) {
                                $startMonth = 1; // January
                                $endMonth = 3; // March
                                } elseif ($quarterNumber == 3) {
                                $startMonth = 4; // April
                                $endMonth = 6; // June
                                } else {
                                $startMonth = 7; // July
                                $endMonth = 9; // September
                                }
                                @endphp
                                ประกาศผลผู้ชนะการจัดซื้อจัดจ้าง ประจำไตรมาสที่ {{ $quarterNumber }} ตั้งแต่เดือน
                                {{ \Carbon\Carbon::create()->month($startMonth)->locale('th')->translatedFormat('F') }}
                                ถึงเดือน
                                {{ \Carbon\Carbon::create()->month($endMonth)->locale('th')->translatedFormat('F') }} ปี
                                {{ $year }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('generate.quarterly.pdf', ['year' => $year, 'quarter' => $quarterNumber]) }}"
                                    class="btn btn-pdf">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="2" class="text-center">ไม่มีข้อมูลรายไตรมาสในปี {{ $currentYear }}</td>
                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ url('/page') }}" role="button" class="btn btn-danger">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
    </div>
</div>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Font Awesome CSS for PDF icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
/* Modern Card Styles */
.modern-card {
    background-color: #fff;
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease-in-out;
    width: 100%;
    /* คงขนาดการ์ดให้เต็มตามหน้าจอ */
}

.modern-card:hover {
    transform: scale(1.02);
}

/* Button Styles */
.btn {
    transition: background-color 0.3s, transform 0.2s;
}

.btn:hover {
    transform: scale(1.05);
}

/* Table hover effect */
.table-hover tbody tr:hover {
    background-color: #f9f9f9;
    /* Light gray background on hover */
}

/* PDF button styles */
.btn-pdf {
    background-color: white;
    border: 2px solid red;
    border-radius: 50%;
    padding: 10px;
    color: red;
    display: inline-block;
    text-align: center;
    width: 50px;
    height: 50px;
}

.btn-pdf i {
    font-size: 1.5rem;
}

.btn-pdf:hover {
    background-color: red;
    color: white;
    border-color: red;
}

/* Text and layout improvements */
h2 {
    font-size: 1.75rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
}

label {
    font-weight: bold;
}

/* Footer alignment */
.card-footer {
    background-color: #f8f9fa;
    border-top: none;
    padding: 10px;
}

/* General Styles */
.container {
    max-width: 1000px;
    /* คงความกว้างของ container */
    margin: 0 auto;
}
</style>
@endsection