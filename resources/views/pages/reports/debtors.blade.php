@extends('adminlte::page')

@section('title', 'Debtors')

@section('content_header')
    <h1>Debtors</h1>
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
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Units</th>
                            <th>Unit Price (KES.)</th>
                            <th>Total Price (KES.)</th>
                            <th>Paid (KES.)</th>
                            <th>Balance (KES.)</th>
                            <th>Receipt</th>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Age</th>
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
    loadDebtors();
});

function loadDebtors(){
    const page_url = '{{ url('report/sales/get-debtors') }}';
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
                {data: 'customer', name: 'customer'},
                {data: 'product', name: 'product'},
                {data: 'units', name: 'units'},
                {data: 'price', name: 'price'},
                {data: 'total_price', name: 'total_price'},
                {data: 'paid', name: 'paid'},
                {data: 'balance', name: 'balance'},
                {data: 'rcpt_no', name: 'rcpt_no'},
                {data: 'inv_no', name: 'inv_no'},
                {data: 'date_added', name: 'date_added'},
                {data: 'time_ago', name: 'time_ago'},
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
