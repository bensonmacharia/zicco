@extends('adminlte::page')

@section('title', 'In Shipment')

@section('content_header')
<h1>In Shipment</h1>
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
            <div class="row">
            </div>
            <div class="table-responsive mt-4">
                <table id="shipping" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Units</th>
                        <th>Product Cost</th>
                        <th>Clearance Cost</th>
                        <th>Transport Cost</th>
                        <th>Total Cost</th>
                        <th>Date Added</th>
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
        overflow-y: auto;
    }
</style>
@stop

@section('js')
<script type="text/javascript">
    $(".select2").select2();

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#shipping").append('<tfoot><tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr></tfoot>');
        loadList();

    });

    function loadList() {
        const page_url = '{{ url('admin/order/get-shipping')}}';

        $.fn.dataTable.ext.errMode = 'ignore';
        var table = $('#shipping').DataTable({
            "autoWidth": false,
            "responsive": true,
            processing: true,
            serverSide: true,
            "bDestroy": true,
            ajax: {
                url: page_url,
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product_and_batch', name: 'product_name'},
                {data: 'units', name: 'units'},
                {data: 'ppcost', name: 'ppcost'},
                {data: 'cccost', name: 'cccost'},
                {data: 'ttcost', name: 'ttcost'},
                {data: 'scost', name: 'scost'},
                {data: 'date_added', name: 'date_added'},
            ],
            oLanguage: {
                sLengthMenu: "_MENU_",
                sSearch: ""
            },
            aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
            pageLength: 20,
            buttons: [],
            initComplete: function (settings, json) {
                $(".dt-buttons .btn").removeClass("btn-secondary")
            },
            drawCallback: function (settings) {
                console.log(settings.json);
            },
            fnFooterCallback: function(nRow, aaData, iStart, iEnd, aiDisplay) {
                var api = this.api();
                var total_pcost = 0;
                var total_clearance = 0;
                var total_transport = 0;
                var total_cost = 0;
                aaData.forEach(function(x) {
                    total_pcost += (x['pcost']);
                    total_clearance += (x['ccost']);
                    total_transport += (x['tcost']);
                    total_cost += (x['pcost'] + x['ccost'] + x['tcost']);
                });
                // I need a footer in my table before doing this, what is the smartest way to add the footer?
                $(api.column(3).footer()).html(
                    total_pcost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
                $(api.column(4).footer()).html(
                    total_clearance.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
                $(api.column(5).footer()).html(
                    total_transport.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
                $(api.column(6).footer()).html(
                    total_cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
            }
        });

    }

</script>
<script>
    $('.numeral-mask').each(function (index, ele) {
        var cleaveCustom = new Cleave(ele, {
            numeral: true,
            numeralDecimalMark: ',',
            delimiter: '.'
        });
    });
</script>
@stop
