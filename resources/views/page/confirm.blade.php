@extends('master')

@section('title', 'Confirm File Generation')

@section('info')
    <div class="card">
        <div class="card-body">
            <h1>Convert File Generation</h1>
            <form action="{{ route('page.confirmPdf', $info->id) }}" method="POST">
                @csrf
                <p>คุณแน่ใจหรือไม่ว่าต้องการสร้างไฟล์สำหรับรายละเอียดต่อไปนี้?</p>
                <ul>
                    <li><strong>ประเภท:</strong> {{ $info->methode_name }}</li>
                    <li><strong>เหตุผล:</strong> {{ $info->reason_description }}</li>
                    <li><strong>คณะ:</strong> {{ $info->office_name }}</li>
                    <li><strong>วันที่ส่ง:</strong> {{ $info->devilvery_time }}</li>
                </ul>

                <!-- แสดงข้อมูลจากตาราง Product -->
                @if($info->products->count())
                <strong>ผลิตภัณฑ์</strong>
                    <ul>
                        @foreach($info->products as $product)
                            <li>{{ $product->product_name }} - จำนวน: {{ $product->quantity }} {{ $product->unit }} - ราคา: {{ $product->product_price }}</li>
                        @endforeach
                    </ul>
                @endif

                <!-- แสดงข้อมูลจากตาราง Seller -->
                @if($info->sellers->count())
                <strong>ผู้ขาย</strong>
                    <ul>
                        @foreach($info->sellers as $seller)
                            <li>{{ $seller->seller_name }} - ที่อยู่: {{ $seller->address }} - หมายเลขผู้เสียภาษี: {{ $seller->taxpayer_number }}</li>
                        @endforeach
                    </ul>
                @endif

                <!-- แสดงข้อมูลจากตาราง CommitteeMember -->
                @if($info->committeemembers->count())
                <strong>คณะกรรมการ</strong>
                    <ul>
                        @foreach($info->committeemembers as $member)
                            <li>{{ $member->member_name }} - ตำแหน่ง: {{ $member->member_position }}</li>
                        @endforeach
                    </ul>
                @endif

                <!-- แสดงข้อมูลจากตาราง Bidder -->
                @if($info->bidders->count())
                <strong>ผู้เสนอราคา</strong>
                    <ul>
                        @foreach($info->bidders as $bidder)
                            <li>{{ $bidder->bidder_name }} - ตำแหน่ง: {{ $bidder->bidder_position }}</li>
                        @endforeach
                    </ul>
                @endif

                <!-- แสดงข้อมูลจากตาราง Inspector -->
                @if($info->inspectors->count())
                <strong>ผู้ตรวจสอบ</strong>
                    <ul>
                        @foreach($info->inspectors as $inspector)
                            <li>{{ $inspector->inspector_name }} - ตำแหน่ง: {{ $inspector->inspector_position }}</li>
                        @endforeach
                    </ul>
                @endif

                <!-- แสดงข้อมูลจากตาราง More -->
                @if($info->mores->count())
                <strong>ข้อมูลเพิ่มเติม</strong>
                    <ul>
                        @foreach($info->mores as $more)
                            <li><strong>ใบเสนอราคา:</strong> {{ $more->price_list }}</li>
                            <li><strong>เอกสารขออนุมัติ:</strong> {{ $more->request_documents }}</li>
                            <li><strong>แหล่งที่มาของราคากลาง 1:</strong> {{ $more->middle_price_first }}</li>
                            <li><strong>แหล่งที่มาของราคากลาง 2:</strong> {{ $more->middle_price_second }}</li>
                            <li><strong>แหล่งที่มาของราคากลาง 3:</strong> {{ $more->middle_price_third }}</li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">ยืนยัน</button>
                    <a href="{{ route('page.list') }}" class="btn btn-danger">ยกเลิก</a>
                </div>
            </form>
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

    <style>
       .btn {
            transition: transform 0.3s;
        }
        .btn:hover {
            transform: scale(1.1); /* ซูมเข้าขณะ hover */
        }
    </style>
@endsection
