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
                    Sales by Date
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
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
                <div class="row jumbotron">
                    <div class="col-md-9">
                        <form method="POST" action="{{ url('admin/report/sales/filter') }}" id="formSaleFilter">
                            <div class="form-group row">
                                <label for="start_date_picker" class="col-sm-2 col-form-label">Select Start Date *</label>
                                <div class="col-sm-4">
                                    <x-adminlte-date-range id="start_date_picker" name="drSizeMd" igroup-size="md" :config="$config">
                                        <x-slot name="appendSlot">
                                            <div class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-date-range>
                                </div>
                                <label for="end_date_picker" class="col-sm-2 col-form-label">Select End Date *</label>
                                <div class="col-sm-4">
                                    <x-adminlte-date-range id="end_date_picker" name="drSizeMd" igroup-size="md" :config="$config">
                                        <x-slot name="appendSlot">
                                            <div class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-date-range>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary" onclick="getSaleByDate()" id="btn-filter-date">Generate Report</button>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table id="product" class="table table-bordered table-striped">
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
  loadList();

});

function loadList() {
    const page_url = '{{ url('report/sales/today') }}';

    $.fn.dataTable.ext.errMode = 'ignore';
    var table = $('#product').DataTable({
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
        ],
        oLanguage: {
            sLengthMenu: "_MENU_",
            sSearch: ""
        },
        aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
        order: [[1, "desc"]],
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
            aaData.forEach(function(x) {
                total_price += (x['total_price']);
                paid += (x['paid']);
                balance += (x['balance']);
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
        }
    });

}

function getSaleByDate()
{
    let start_date = document.getElementById('start_date_picker').value;
    let end_date = document.getElementById('end_date_picker').value;
     if(start_date == ''){
        Swal.fire("Error!", "Start date needs to be selected", "error");
    } else if (end_date == '') {
        Swal.fire("Error!", "End date needs to be selected", "error");
    } else {
        var page_url = "{{ url('report/sales/salesByDate') }}"+ '/' + start_date+ '/' + end_date;
        $.fn.dataTable.ext.errMode = 'ignore';
        var table = $('#product').DataTable({
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
                aaData.forEach(function(x) {
                    total_price += (x['total_price']);
                    paid += (x['paid']);
                    balance += (x['balance']);
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
            }
        });
    }

}
</script>
@stop
