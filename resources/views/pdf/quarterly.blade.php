<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>รายงานประจำไตรมาส</title>
    <style>
        @font-face {
            font-family: 'THSarabunIT๙';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path('fonts/THSarabunIT๙.ttf') }}') format('truetype');
        }

        body {
            font-family: 'THSarabunIT๙', sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        h2 {
            text-align: center;
        }

        /* กำหนดความกว้างของแต่ละคอลัมน์ */
        .col-order {
            width: 5%; /* คอลัมน์ลำดับ */
        }

        .col-taxid {
            width: 15%; /* คอลัมน์เลขประจำตัวผู้เสียภาษี */
        }

        .col-seller {
            width: 20%; /* คอลัมน์ชื่อผู้ขาย */
        }

        .col-reason {
            width: 20%; /* คอลัมน์เหตุผล */
        }

        .col-type {
            width: 5%; /* คอลัมน์ประเภท */
        }

        .col-total {
            width: 10%; /* คอลัมน์จำนวนเงินรวม */
            text-align: right; /* จัดข้อความให้ชิดขวา */
        }

        .col-date {
            width: 10%; /* คอลัมน์วันที่ */
        }

        .col-ref {
            width: 15%; /* คอลัมน์เอกสารอ้างอิง */
        }
    </style>
</head>
<body>
    <h2>รายงานประจำไตรมาส: {{ $quarter }} ตั้งแต่เดือน {{ $monthRange }} ปี {{ $year }}</h2>
    <table>
        <thead>
            <tr>
                <th class="col-order">ลำดับ</th>
                <th class="col-taxid">เลขประจำตัวผู้เสียภาษี</th>
                <th class="col-seller">ชื่อผู้ขาย</th>
                <th class="col-reason">เหตุผล</th>
                <th class="col-type">ประเภท</th>
                <th class="col-total">จำนวนเงินรวม</th>
                <th class="col-date">วันที่</th>
                <th class="col-ref">เอกสารอ้างอิง</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quarterlyData as $index => $info)
                <tr>
                    <td class="col-order">{{ $index + 1 }}</td>
                    <td class="col-taxid">
                        @foreach($info->sellers as $seller)
                            {{ $seller->taxpayer_number }}<br>
                        @endforeach
                    </td>
                    <td class="col-seller">
                        @foreach($info->sellers as $seller)
                            {{ $seller->seller_name }}<br>
                        @endforeach
                    </td>
                    <td class="col-reason">{{ $info->reason_description }}</td>
                    <td class="col-type">{{ $info->methode_name }}</td>
                    <td class="col-total">{{ number_format($info->total_price, 2) }}</td>
                    <td class="col-date">{{ \Carbon\Carbon::parse($info->date)->format('d/m/Y') }}</td>
                    <td class="col-ref">
                        @foreach($info->sellers as $seller)
                            {{ $seller->reference_documents }}<br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align: right;"><strong>ยอดรวมทั้งหมด:</strong></td>
                <td class="col-total">{{ number_format($quarterlyData->sum('total_price'), 2) }}</td>
                <td></td> <!-- ช่องว่างสำหรับเอกสารอ้างอิง -->
            </tr>
        </tfoot>
    </table>
</body>
</html>
