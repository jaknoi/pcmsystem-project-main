@extends('master')

@section('title', 'Procurement System')

@section('info')
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .container-flex {
            flex: 1;
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            overflow: hidden;
            background-color: #ffffff;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.15);
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
        }

        .card-body {
            padding: 30px;
            text-align: center;
        }

        .header {
            background-color: #003366;
            color: white;
            padding: 20px;
            text-align: center;
            font-weight: 700;
            font-size: 24px;
        }

        .chart {
            padding: 20px;
            background-color: #fff;
            border: none;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s;
        }

        .chart:hover {
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
        }

        .chart-title {
            color: #333;
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
            font-size: 16px;
            font-weight: 500;
        }

        .btn-back:hover {
            background-color: #ff3333;
            border-color: #ff3333;
        }

        .btn-danger {
            font-size: 16px;
        }

        /* Chart Styling */
        .card-chart {
            background-color: #f8f9fa;
            border-radius: 15px;
        }

        /* Dashboard cards styling */
        .dashboard-card {
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        /* Specific card colors */
        .bg-danger { background-color: #e74c3c !important; }
        .bg-primary { background-color: #3498db !important; }
        .bg-success { background-color: #2ecc71 !important; }
        .bg-warning { background-color: #f39c12 !important; }

        /* Chart text styling */
        .chart-title {
            color: #555;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
        }

    </style>

    <div class="container-flex">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <div class="row text-center">
                        <!-- Cards for procurement statistics -->
                        <div class="col-md-3 mb-3">
                            <div class="card dashboard-card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">จัดซื้อ</h5>
                                    <p class="card-text">{{ array_sum($purchaseData) }} รายการ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card dashboard-card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">จัดจ้าง</h5>
                                    <p class="card-text">{{ array_sum($hiringData) }} รายการ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card dashboard-card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">วัสดุ</h5>
                                    <p class="card-text">{{ array_sum($materialData) }} รายการ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card dashboard-card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">ครุภัณฑ์</h5>
                                    <p class="card-text">{{ array_sum($equipmentData) }} รายการ</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Procurement Chart -->
                        <div class="col-md-6">
                            <div class="card-chart">
                                <h5 class="chart-title">รายการจัดซื้อจัดจ้างภายในปี {{ now()->year + 543 }}</h5>
                                <canvas id="purchaseChart"></canvas>
                            </div>
                        </div>
                        <!-- Materials and Equipment Chart -->
                        <div class="col-md-6">
                            <div class="card-chart">
                                <h5 class="chart-title">รายการวัสดุและครุภัณฑ์ ภายในปี {{ now()->year + 543 }}</h5>
                                <canvas id="materialChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
            <a href="{{ url('/page') }}" role="button" class="btn btn-danger">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
        </div>
        
    </div>
<!-- FontAwesome CSS CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Chart for procurement data
    var ctx1 = document.getElementById('purchaseChart').getContext('2d');
    var purchaseChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: @json($months), // เรียงเดือนตามปกติจากมกราคมถึงธันวาคม
            datasets: [
                {
                    label: 'จัดซื้อ',
                    data: @json($purchaseData),
                    borderColor: 'rgba(231, 76, 60, 1)',
                    backgroundColor: 'rgba(231, 76, 60, 0.7)',
                    fill: true
                },
                {
                    label: 'จัดจ้าง',
                    data: @json($hiringData),
                    borderColor: 'rgba(52, 152, 219, 1)',
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    fill: true
                }
            ]
        }
    });

    // Chart for materials and equipment data
    var ctx2 = document.getElementById('materialChart').getContext('2d');
    var materialChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: @json($months), // เรียงเดือนตามปกติจากมกราคมถึงธันวาคม
            datasets: [
                {
                    label: 'วัสดุ',
                    data: @json(array_values($materialData)),
                    borderColor: 'rgba(46, 204, 113, 1)',
                    backgroundColor: 'rgba(46, 204, 113, 0.7)',
                    fill: true
                },
                {
                    label: 'ครุภัณฑ์',
                    data: @json(array_values($equipmentData)),
                    borderColor: 'rgba(241, 196, 15, 1)',
                    backgroundColor: 'rgba(241, 196, 15, 0.7)',
                    fill: true
                }
            ]
        }
    });
</script>

@endsection
