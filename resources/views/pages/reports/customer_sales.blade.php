@extends('adminlte::page')

@section('title', 'Sales Report')

@section('content_header')
    <h1>Sales Report</h1>
@stop

@section('content')
@php
$config = [
    "singleDatePicker" => true,
    "showDropdowns" => true,
    "startDate" => "js:moment()",
    "minYear" => 2020,
    "maxYear" => "js:parseInt(moment().format('YYYY'),10)",
    "timePicker" => false,
    "cancelButtonClasses" => "btn-danger",
    "locale" => ["format" => "YYYY-MM-DD"],
];
@endphp
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar mr-1"></i>
                    Sales by Customer
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row jumbotron">
                    <div class="col-md-9">
                        <form method="POST" action="{{ url('admin/report/sales/filter') }}" id="formSaleFilter">
                            <div class="form-group row">
                                <label for="customer_id" class="col-sm-6 col-form-label">Select Customer *</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2" id="customer_id" name="customer_id" style="width:100%">
                                        <option value=''>--Select--</option>
                                        @foreach($customer as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary" onclick="getSaleByCustomer()" id="btn-filter-customer">Generate Report</button>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table id="sales_customer" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Units</th>
                                <th>Price</th>
                                <th>Total Price</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Profit</th>
                                <th>Receipt</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                    </table>
                </div>
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
$(".select2").select2();

$(document).ready(function(){
  $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
  });
  $("#product").append('<tfoot><tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr></tfoot>');
  $("#sales_customer").append('<tfoot><tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr></tfoot>');
  getSaleByCustomerDefault();

});

function getSaleByCustomerDefault()
{
    let customer_id = 1;
    if(customer_id == ''){
        Swal.fire("Error!", "Customer needs to be selected", "error");
    } else {
        var page_url = "{{ url('report/sales/salesByCustomer') }}"+ '/' + customer_id;
        $.fn.dataTable.ext.errMode = 'ignore';
        var table = $('#sales_customer').DataTable({
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
                {data: 'profit', name: 'profit'},
                {data: 'rcpt_no', name: 'rcpt_no'},
                {data: 'date_added', name: 'date_added'},
            ],
            responsive: true,
            oLanguage: {
                sLengthMenu: "_MENU_",
                sSearch: ""
            },
            aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
            order: [[1, "asc"]],
            pageLength: 10,
            buttons: [
            ],
            initComplete: function (settings, json) {
                $(".dt-buttons .btn").removeClass("btn-secondary")
            },
            drawCallback: function (settings) {
                console.log(settings.json);
            },
            fnFooterCallback: function(nRow, aaData, iStart, iEnd, aiDisplay) {
                var api = this.api();
                var total_price = 0;
                var paid = 0;
                var balance = 0;
                var profit = 0;
                aaData.forEach(function(x) {
                    total_price += (x['total_price']);
                    paid += (x['paid']);
                    balance += (x['balance']);
                    profit += (x['profit']);
                });
                $(api.column(5).footer()).html(
                    total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
                $(api.column(6).footer()).html(
                    paid.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
                $(api.column(7).footer()).html(
                    balance.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
                $(api.column(8).footer()).html(
                    profit.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
            }
        });
    }
}

function getSaleByCustomer()
{
    let customer_id = $('#customer_id').val();
    if(customer_id == ''){
        Swal.fire("Error!", "Customer needs to be selected", "error");
    } else {
        var page_url = "{{ url('report/sales/salesByCustomer') }}"+ '/' + customer_id;
        $.fn.dataTable.ext.errMode = 'ignore';
        var table = $('#sales_customer').DataTable({
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
                {data: 'profit', name: 'profit'},
                {data: 'rcpt_no', name: 'rcpt_no'},
                {data: 'date_added', name: 'date_added'},
            ],
            responsive: true,
            oLanguage: {
                sLengthMenu: "_MENU_",
                sSearch: ""
            },
            aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
            order: [[1, "asc"]],
            pageLength: 10,
            buttons: [
            ],
            initComplete: function (settings, json) {
                $(".dt-buttons .btn").removeClass("btn-secondary")
            },
            drawCallback: function (settings) {
                console.log(settings.json);
            },
            fnFooterCallback: function(nRow, aaData, iStart, iEnd, aiDisplay) {
                var api = this.api();
                var total_price = 0;
                var paid = 0;
                var balance = 0;
                var profit = 0;
                aaData.forEach(function(x) {
                    total_price += (x['total_price']);
                    paid += (x['paid']);
                    balance += (x['balance']);
                    profit += (x['profit']);
                });
                // I need a footer in my table before doing this, what is the smartest way to add the footer?
                $(api.column(5).footer()).html(
                    total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
                $(api.column(6).footer()).html(
                    paid.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
                $(api.column(7).footer()).html(
                    balance.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
                $(api.column(8).footer()).html(
                    profit.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
            }
        });
    }
}
</script>
@stop
