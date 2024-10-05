<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>รายงานประจำเดือน</title>
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
            margin-top: 0;
        }

        h2 {
            text-align: center;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        /* ปรับขนาดคอลัมน์ให้เข้ากับกระดาษแนวนอน */
        .col-order {
            width: 5%;
        }

        .col-reason {
            width: 35%;
        }

        .col-type {
            width: 20%;
        }

        .col-seller {
            width: 25%;
        }

        .col-total {
            width: 15%;
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>สรุปผลการจัดซื้อจัดจ้างรายเดือน {{ $thaiMonth }} {{ $year }}</h2>
    <table>
        <thead>
            <tr>
                <th class="col-order">ลำดับ</th>
                <th class="col-reason">เหตุผล</th>
                <th class="col-type">ประเภท</th>
                <th class="col-seller">ผู้ขาย</th>
                <th class="col-total">วงเงินรวม</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $index => $info)
                <tr>
                    <td class="col-order">{{ $index + 1 }}</td>
                    <td class="col-reason">{{ $info->reason_description }}</td>
                    <td class="col-type">{{ $info->methode_name }}</td>
                    <td class="col-seller">
                        @foreach($info->sellers as $seller)
                            {{ $seller->seller_name }}<br>
                        @endforeach
                    </td>
                    <td class="col-total">{{ number_format($info->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>ยอดรวมทั้งหมด:</strong></td>
                <td class="col-total">{{ number_format($totalPrice, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
