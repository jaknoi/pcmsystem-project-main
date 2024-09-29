@extends('master')

@section('info')
<div class="container">
    <h2>ข้อมูลรายเดือน</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>เหตุผล</th>
                <th>ประเภท</th>
                <th>ผู้ขาย</th>
                <th>วงเงินรวม</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $info) <!-- ใช้ $monthlyData ตรงๆ -->
                <tr>
                    <td>{{ $info->id }}</td> <!-- เพิ่ม ID -->
                    <td>{{ $info->reason_description }}</td>
                    <td>{{ $info->methode_name }}</td>
                    <td>
                        @foreach($info->sellers as $seller)
                            {{ $seller->seller_name }}<br>
                        @endforeach
                    </td>
                    <td>{{ number_format($info->total_price, 2) }}</td> <!-- แสดงยอดรวม -->
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>ข้อมูลรายไตรมาส</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th> <!-- เพิ่ม ID -->
                <th>เลขประจำตัวผู้เสียภาษี</th>
                <th>ผู้ขาย</th>
                <th>เหตุผล</th>
                <th>วงเงินรวม</th>
                <th>วันที่</th>
                <th>เอกสารอ้างอิง</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quarterlyData as $info) <!-- ใช้ $quarterlyData ตรงๆ -->
                <tr>
                    <td>{{ $info->id }}</td> <!-- เพิ่ม ID -->
                    <td>@foreach($info->sellers as $seller)
                            {{ $seller->taxpayer_number }}<br>
                        @endforeach</td>
                    <td>
                        @foreach($info->sellers as $seller)
                            {{ $seller->seller_name }}<br>
                        @endforeach
                    </td>
                    <td>{{ $info->reason_description }}</td>
                    <td>{{ number_format($info->total_price, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($info->date)->format('d/m/Y') }}</td> <!-- จัดรูปแบบวันที่ -->
                    <td>@foreach($info->sellers as $seller)
                            {{ $seller->reference_documents }}<br>
                        @endforeach</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        <a href="{{ url('/page') }}" role="button" class="btn btn-danger">Back</a>
    </div>
</div>
@endsection
