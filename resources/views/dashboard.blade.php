@extends('master')

@section('title', 'Procurement System')

@section('info')
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .container-flex {
            flex: 1;
        }

        .card {
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 20px;
        }

        .header {
            background-color: #003366;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .chart {
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
        }

        .chart-title {
            color: black;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .footer {
            background-color: #e9f5ff;
            padding: 15px;
            text-align: center;
            width: 100%;
            position: relative;
            bottom: 0;
        }

        .btn-back {
            background-color: #ff4d4d;
            border-color: #ff4d4d;
        }

        .btn-back:hover {
            background-color: #ff3333;
            border-color: #ff3333;
        }
    </style>

    <div class="container-flex">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card text-white bg-danger mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">จัดซื้อ</h5>
                                    <p class="card-text">{{ array_sum($purchaseData) }} รายการ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">จัดจ้าง</h5>
                                    <p class="card-text">{{ array_sum($hiringData) }} รายการ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">วัสดุ</h5>
                                    <p class="card-text">{{ array_sum($materialData) }} รายการ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">ครุภัณฑ์</h5>
                                    <p class="card-text">{{ array_sum($equipmentData) }} รายการ</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card chart">
                                <h5 class="chart-title">รายการจัดซื้อจัดจ้างภายในปี {{ now()->year + 543 }}</h5> <!-- ใช้ปีปัจจุบัน -->
                                <canvas id="purchaseChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card chart">
                                <h5 class="chart-title">รายการวัสดุและครุภัณฑ์ ภายในปี {{ now()->year + 543 }}</h5> <!-- ใช้ปีปัจจุบัน -->
                                <canvas id="materialChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <a href="{{ url('/page') }}" role="button" class="btn btn-danger">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx1 = document.getElementById('purchaseChart').getContext('2d');
        var purchaseChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                        label: 'จัดซื้อ',
                        data: @json($purchaseData),
                        borderColor: 'red',
                        fill: false
                    },
                    {
                        label: 'จัดจ้าง',
                        data: @json($hiringData),
                        borderColor: 'blue',
                        fill: false
                    }
                ]
            }
        });

        var ctx2 = document.getElementById('materialChart').getContext('2d');
        var materialChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'วัสดุ',
                    data: @json(array_values($materialData)), // แสดงข้อมูลวัสดุที่ดึงมาจากฐานข้อมูล
                    borderColor: 'green',
                    fill: false
                },
                {
                    label: 'ครุภัณฑ์',
                    data: @json(array_values($equipmentData)), // แสดงข้อมูลครุภัณฑ์ที่ดึงมาจากฐานข้อมูล
                    borderColor: 'yellow',
                    fill: false
                    }
                ]
            }
        });
    </script>

@endsection
