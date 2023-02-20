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
                    Cash and Credit Sales Distribution
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
                    <tbody>
                    <tr>
                        <th>Total Sales</th>
                        <td>{{"KES. ".number_format($ctods[0]->total_sales)}}</td>
                    </tr>
                    <tr>
                        <th>Total Cost</th>
                        <td>{{"KES. ".number_format($ctodc[0]->total_cost)}}</td>
                    </tr>
                    <tr>
                        <th>Total Expenses</th>
                        <td>{{"KES. ".number_format($ctode[0]->total_expenses)}}</td>
                    </tr>
                    <tr>
                        <th>Total Profit</th>
                        <td>{{"KES. ".number_format($profit_tod)}}</td>
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
                    <tbody>
                    <tr>
                        <th>Total Sales</th>
                        <td>{{"KES. ".number_format($cmons[0]->total_sales)}}</td>
                    </tr>
                    <tr>
                        <th>Total Cost</th>
                        <td>{{"KES. ".number_format($cmonc[0]->total_cost)}}</td>
                    </tr>
                    <tr>
                        <th>Total Expenses</th>
                        <td>{{"KES. ".number_format($cmone[0]->total_expenses)}}</td>
                    </tr>
                    <tr>
                        <th>Total Profit</th>
                        <td>{{"KES. ".number_format($profit_mon)}}</td>
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
        loadSummarySalesCreditChart();
        loadSummarySalesWeekly();
        var mont = {!! json_encode($mont) !!};
        loadNetProfitTrendGraph(mont);
        loadAlmostSoldOutStock();
        //console.log(mont[0]['month']+" "+mont[0]['year']);
        //console.log({{$credit}});
    });

    function loadNetProfitTrendGraph(mont){
        var salesGraphChartCanvas = $('#line-chart').get(0).getContext('2d');
        var salesGraphChartData = {
            labels: [mont[0]['month']+" "+mont[0]['year'], mont[1]['month']+" "+mont[1]['year'], mont[2]['month']+" "+mont[2]['year'], mont[3]['month']+" "+mont[3]['year'], mont[4]['month']+" "+mont[4]['year'], mont[5]['month']+" "+mont[5]['year'], mont[6]['month']+" "+mont[6]['year'], mont[7]['month']+" "+mont[7]['year']],
            datasets: [
                {
                    label: 'Net Profit',
                    fill: false,
                    borderWidth: 2,
                    lineTension: 0,
                    spanGaps: true,
                    borderColor: '#00a65a',
                    pointRadius: 3,
                    pointHoverRadius: 7,
                    pointColor: '#efefef',
                    pointBackgroundColor: '#00a65a',
                    data: [mont[0]['profit'], mont[1]['profit'], mont[2]['profit'], mont[3]['profit'], mont[4]['profit'], mont[5]['profit'], mont[6]['profit'], mont[7]['profit']]
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
                        stepSize: 20000,
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

    function loadSummarySalesWeekly(){
        //-------------
        //- BAR CHART -
        //-------------
        var areaChartData = {
            labels  : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            datasets: [
              {
                label               : 'Sales',
                backgroundColor     : 'rgba(60,141,188,0.9)',
                borderColor         : 'rgba(60,141,188,0.8)',
                pointRadius         : false,
                pointColor          : '#3b8bba',
                pointStrokeColor    : 'rgba(60,141,188,1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data                : [{{$mon}}, {{$tue}}, {{$wed}}, {{$thu}}, {{$fri}}, {{$sat}}, {{$sun}}]
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
            aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
            pageLength: 20,
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
