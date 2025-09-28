@extends('adminlte::page')

@section('title', 'Zicco TECH')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Per Week Sales Trend
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    {{ date('F') }} Top Products
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-1"></i>
                    Products Almost Soldout
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="aso" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Remaining Units</th>
                        <th>Total Units</th>
                        <th>Total Sold</th>
                        <th>Total Spoilt</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i>
                    Today's Summary
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm text-nowrap">
                    <tr>
                        <th></th>
                        <th>Shop A</th>
                        <th>Shop B</th>
                        <th>Shop C</th>
                    </tr>
                    <tbody>
                        <tr>
                            <th>Total Sales</th>
                            <td>{{ "KES. " . number_format($todayStats[1]['sales']) }}</td>
                            <td>{{ "KES. " . number_format($todayStats[2]['sales']) }}</td>
                            <td>{{ "KES. " . number_format($todayStats[3]['sales']) }}</td>
                        </tr>
                        <tr>
                            <th>Total Cost</th>
                            <td>{{ "KES. " . number_format($todayStats[1]['cost']) }}</td>
                            <td>{{ "KES. " . number_format($todayStats[2]['cost']) }}</td>
                            <td>{{ "KES. " . number_format($todayStats[3]['cost']) }}</td>
                        </tr>
                        <tr>
                            <th>Total Expenses</th>
                            <td>{{ "KES. " . number_format($todayStats[1]['expenses']) }}</td>
                            <td>{{ "KES. " . number_format($todayStats[2]['expenses']) }}</td>
                            <td>{{ "KES. " . number_format($todayStats[3]['expenses']) }}</td>
                        </tr>
                        <tr>
                            <th>Total Profit</th>
                            <td>{{ "KES. " . number_format($todayStats[1]['profit']) }}</td>
                            <td>{{ "KES. " . number_format($todayStats[2]['profit']) }}</td>
                            <td>{{ "KES. " . number_format($todayStats[3]['profit']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar mr-1"></i>
                    {{ date('F') }} Summary
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm text-nowrap">
                    <tr>
                        <th></th>
                        <th>Shop A</th>
                        <th>Shop B</th>
                        <th>Shop C</th>
                    </tr>
                    <tbody>
                    <tr>
                        <th>Total Sales</th>
                        <td>{{ number_format($monthlyStats[1]['sales']) }}</td>
                        <td>{{ number_format($monthlyStats[2]['sales']) }}</td>
                        <td>{{ number_format($monthlyStats[3]['sales']) }}</td>
                    </tr>
                    <tr>
                        <th>Total Cost</th>
                        <td>{{ number_format($monthlyStats[1]['cost']) }}</td>
                        <td>{{ number_format($monthlyStats[2]['cost']) }}</td>
                        <td>{{ number_format($monthlyStats[3]['cost']) }}</td>
                    </tr>
                    <tr>
                        <th>Total Expenses</th>
                        <td>{{ number_format($monthlyStats[1]['expenses']) }}</td>
                        <td>{{ number_format($monthlyStats[2]['expenses']) }}</td>
                        <td>{{ number_format($monthlyStats[3]['expenses']) }}</td>
                    </tr>
                    <tr>
                        <th>Total Profit</th>
                        <td>{{ number_format($monthlyStats[1]['profit']) }}</td>
                        <td>{{ number_format($monthlyStats[2]['profit']) }}</td>
                        <td>{{ number_format($monthlyStats[3]['profit']) }}</td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Monthly Net Profit Trend
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas class="chart" id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <!-- <link rel="stylesheet" href="/css/admin_custom.css"> -->
@stop

@section('js')
<script type="text/javascript">
    $(document).ready(function(){
        //loadSummarySalesCreditChart();
        loadTopProductsChart();
        loadSummarySalesWeekly();
        var mont1 = {!! json_encode($montpA) !!};
        var mont2 = {!! json_encode($montpB) !!};
        var mont3 = {!! json_encode($montpC) !!};
        // Build labels dynamically from Shop A (since months are same for all shops)
        var labels = mont1.map(function(item) {
            return item.month + " " + item.year;
        });
        loadNetProfitTrendGraph(labels, mont1, mont2, mont3);
        loadAlmostSoldOutStock();
    });

    function loadNetProfitTrendGraph(labels, mont1, mont2, mont3){
        var salesGraphChartCanvas = $('#line-chart').get(0).getContext('2d');
        var salesGraphChartData = {
            labels: labels,
            datasets: [
                {
                    label: 'Shop A',
                    fill: false,
                    borderWidth: 2,
                    lineTension: 0,
                    spanGaps: true,
                    borderColor: '#3b8bba',
                    pointRadius: 3,
                    pointHoverRadius: 7,
                    pointColor: '#efefef',
                    pointBackgroundColor: '#3b8bba',
                    data: mont1.map(item => item.profit)
                },
                {
                    label: 'Shop B',
                    fill: false,
                    borderWidth: 2,
                    lineTension: 0,
                    spanGaps: true,
                    borderColor: '#b2beb5',
                    pointRadius: 3,
                    pointHoverRadius: 7,
                    pointColor: '#efefef',
                    pointBackgroundColor: '#b2beb5',
                    data: mont2.map(item => item.profit)
                }
                ,
                {
                    label: 'Shop C',
                    fill: false,
                    borderWidth: 2,
                    lineTension: 0,
                    spanGaps: true,
                    borderColor: '#00D100',
                    pointRadius: 3,
                    pointHoverRadius: 7,
                    pointColor: '#efefef',
                    pointBackgroundColor: '#00D100',
                    data: mont3.map(item => item.profit)
                }
            ]
        }

        var salesGraphChartOptions = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    ticks: {
                        fontColor: '#3b8bba'
                    },
                    gridLines: {
                        display: false,
                        color: '#3b8bba',
                        drawBorder: false
                    }
                }],
                yAxes: [{
                    ticks: {
                        stepSize: 50000,
                        fontColor: '#3b8bba'
                    },
                    gridLines: {
                        display: false,
                        color: '#3b8bba',
                        drawBorder: false
                    }
                }]
            }
        }

        var salesGraphChart = new Chart(salesGraphChartCanvas, { // lgtm[js/unused-local-variable]
            type: 'line',
            data: salesGraphChartData,
            options: salesGraphChartOptions
        })
    }

    function loadSummarySalesCreditChart(){
        //-------------
        //- DONUT CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
        var donutData        = {
          labels: [
              'Cash',
              'Credit',
          ],
          datasets: [
            {
              data: [{{$cash}},{{$credit}}],
              backgroundColor : ['#00a65a', '#f56954',],
            }
          ]
        }
        var donutOptions     = {
          maintainAspectRatio : false,
          responsive : true,
        }
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        new Chart(donutChartCanvas, {
          type: 'doughnut',
          data: donutData,
          options: donutOptions
        })
    }

    function loadTopProductsChart(){
        var donutChartCanvas = $('#donutChart').get(0).getContext('2d')

        var donutData = {
            labels: [
                @foreach($topProducts as $product)
                    "{{ $product->product_name }}",
                @endforeach
            ],
            datasets: [
                {
                    data: [
                        @foreach($topProducts as $product)
                            {{ (int) $product->profit }},
                        @endforeach
                    ],
                    backgroundColor : [
                        '#00a65a',
                        '#f56954',
                        '#f39c12',
                        '#00c0ef',
                        '#3c8dbc'
                    ],
                }
            ]
        }

        var donutOptions = {
            maintainAspectRatio : false,
            responsive : true,
        }

        new Chart(donutChartCanvas, {
            type: 'doughnut',
            data: donutData,
            options: donutOptions
        })
    }

    function loadSummarySalesWeekly(){
        //-------------
        //- BAR CHART -
        //-------------
        var areaChartData = {
            labels  : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            datasets: [
              {
                label               : 'Shop A',
                backgroundColor     : 'rgba(60,141,188,0.9)',
                borderColor         : '#fff',
                pointRadius         : false,
                pointColor          : '#3b8bba',
                pointStrokeColor    : 'rgba(60,141,188,1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data                : [
                    {{$weekly_sales[1][2]}}, // Monday
                    {{$weekly_sales[1][3]}}, // Tuesday
                    {{$weekly_sales[1][4]}}, // Wednesday
                    {{$weekly_sales[1][5]}}, // Thursday
                    {{$weekly_sales[1][6]}}, // Friday
                    {{$weekly_sales[1][7]}}, // Saturday
                    {{$weekly_sales[1][1]}}  // Sunday
                ]
              },
              {
                label               : 'Shop B',
                backgroundColor     : 'rgba(210, 214, 222, 1)',
                borderColor         : '#fff',
                pointRadius         : false,
                pointColor          : 'rgba(210, 214, 222, 1)',
                pointStrokeColor    : '#c1c7d1',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(220,220,220,1)',
                data                : [
                    {{$weekly_sales[2][2]}}, // Monday
                    {{$weekly_sales[2][3]}}, // Tuesday
                    {{$weekly_sales[2][4]}}, // Wednesday
                    {{$weekly_sales[2][5]}}, // Thursday
                    {{$weekly_sales[2][6]}}, // Friday
                    {{$weekly_sales[2][7]}}, // Saturday
                    {{$weekly_sales[2][1]}}  // Sunday
                ]
              },
              {
                label               : 'Shop C',
                backgroundColor     : 'rgba(0, 128, 0, 0.6)',
                borderColor         : '#fff',
                pointRadius         : false,
                pointColor          : 'rgba(0, 128, 0, 0.6)',
                pointStrokeColor    : '#006400',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(0, 128, 0, 1)',
                data                : [
                    {{$weekly_sales[3][2]}}, // Monday
                    {{$weekly_sales[3][3]}}, // Tuesday
                    {{$weekly_sales[3][4]}}, // Wednesday
                    {{$weekly_sales[3][5]}}, // Thursday
                    {{$weekly_sales[3][6]}}, // Friday
                    {{$weekly_sales[3][7]}}, // Saturday
                    {{$weekly_sales[3][1]}}  // Sunday
                ]
              },
            ]
        }
        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        var barChartData = $.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        barChartData.datasets[0] = temp0

        var barChartOptions = {
          responsive              : true,
          maintainAspectRatio     : false,
          datasetFill             : false
        }

        new Chart(barChartCanvas, {
          type: 'bar',
          data: barChartData,
          options: barChartOptions
        })
    }

    function loadAlmostSoldOutStock(){
        const page_url = '{{ url('stock/load_stock_almost_soldout') }}';
        $.fn.dataTable.ext.errMode = 'ignore';
        var table = $('#aso').DataTable({
            "autoWidth": false,
            "responsive": true,
            processing: true,
            serverSide: true,
            "bDestroy": true,
            ajax: {
                url: page_url,
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
                {data: 'product_name', name: 'product_name'},
                {data: 'total_balance', name: 'total_balance'},
                {data: 'total_units', name: 'total_units'},
                {data: 'total_sold', name: 'total_sold'},
                {data: 'total_spoilt', name: 'total_spoilt'},
            ],
            oLanguage: {
                sLengthMenu: "_MENU_",
                sSearch: ""
            },
            aLengthMenu: [[5, 10, 15, 20], [4, 10, 15, 20]],
            pageLength: 5,
            buttons: [
            ],
            initComplete: function (settings, json) {
                $(".dt-buttons .btn").removeClass("btn-secondary")
            },
            drawCallback: function (settings) {
                console.log(settings.json);
            }
        });
    }

</script>
@stop
