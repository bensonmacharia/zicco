@extends('adminlte::page')

@section('title', 'Stock Summary')

@section('content_header')
    <h1>Stock Summary</h1>
@stop

@section('content')
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="table-responsive mt-4">
                <table id="stock" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Total Units</th>
                            <th>Sold Units</th>
                            <th>Remaining Units</th>
                            <th>Total Sales</th>
                            <th>Collected Amount</th>
                            <th>Credit Amount</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .modal {
        overflow-y:auto;
    }
</style>
@stop

@section('js')
<script type="text/javascript">

$(document).ready(function(){
    loadSummaryStock();
});

function loadSummaryStock(){
    const page_url = '{{ url('stock/load_stock_summary') }}';
    $.fn.dataTable.ext.errMode = 'ignore';
    var table = $('#stock').DataTable({
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
            {data: 'total_units', name: 'total_units'},
            {data: 'total_sold', name: 'total_sold'},
            {data: 'total_remaining', name: 'total_remaining'},
            {data: 'total_sales', name: 'total_sales'},
            {data: 'total_paid', name: 'total_paid'},
            {data: 'total_balance', name: 'total_balance'},
        ],
        responsive: true,
        oLanguage: {
            sLengthMenu: "_MENU_",
            sSearch: ""
        },
        aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
        order: [[1, "asc"]],
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
