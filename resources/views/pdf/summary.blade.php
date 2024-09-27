<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Summary Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>สรุปรายการจัดซื้อจัดจ้าง</h2>
        <p>เดือน: {{ $month }}, ปี: {{ $year }}</p>
        <table class="table">
            <thead>
                <tr>
                    <th>ประเภท</th>
                    <th>เหตุผล</th>
                    <th>คณะ</th>
                    <th>วันที่สร้างไฟล์</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>{{ $summary->methode_name }}</td>
                <td>{{ $summary->reason_description }}</td>
                <td>{{ $summary->office_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($summary->created_at)->format('d/m/Y H:i:s') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
