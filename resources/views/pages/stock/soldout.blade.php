@extends('adminlte::page')

@section('title', 'Sold Out Stock')

@section('content_header')
<h1>Sold Out Stock</h1>
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
                <table id="product" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Units</th>
                        <th>Total Cost</th>
                        <th>Total Sales</th>
                        <th>Profit</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="TransfersModal" aria-labelledby="TransfersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TransfersModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Sales</h5>
                <div class="table-responsive mt-4">
                    <table id="sales" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Expected Sales</th>
                            <th>Actual Sales</th>
                            <th>Expected Profit</th>
                            <th>Actual Profit</th>
                            <th>System Profit</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td id="expected_sales"></td>
                            <td id="actual_sales"></td>
                            <td id="expected_profit"></td>
                            <td id="actual_profit"></td>
                            <td id="system_profit"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <br/>
                <h5>Transfers</h5>
                <div class="table-responsive mt-4">
                    <table id="transfers" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Transferred to</th>
                            <th>Batch</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        loadList();

        $("#transfers").append('<tfoot><tr><th></th><th></th><th></th><th></th></tr></tfoot>');
    });

    function loadList() {
        const page_url = '{{ url('admin/stock/get-soldout')}}';

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
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product_name', name: 'product_name'},
                {data: 'batch', name: 'batch'},
                {data: 'units', name: 'units'},
                {data: 'total_cost', name: 'total_cost'},
                {data: 'actual_sales', name: 'actual_sales'},
                {data: 'actual_profit', name: 'actual_profit'},
                { "data": null,"sortable": false,
                    render: function (data, type, row, meta) {
                        var result = '<a class="btn btn-success btn-sm" \
                                    data-id = '+row.id+' \
                                    data-batch = \''+row.batch+'\' \
                                    data-units = '+row.units+' \
                                    data-pcost = \''+row.pcost+'\' \
                                    data-ccost = \''+row.ccost+'\' \
                                    data-tcost = \''+row.tcost+'\' \
                                    data-order_id = '+row.order_id+' \
                                    data-product_id = '+row.product_id+' \
                                    data-product_name = \''+row.product_name+'\' \
                                    data-actual_sales = \''+row.actual_sales+'\' \
                                    data-expected_sales = \''+row.expected_sales+'\' \
                                    data-actual_profit = \''+row.actual_profit+'\' \
                                    data-expected_profit = \''+row.expected_profit+'\' \
                                    data-system_profit = \''+row.system_profit+'\' \
                                    data-soldout = '+row.soldout+' \
                                onclick="viewStockDetails(this)" data-toggle="modal" data-target="#TransfersModal"><i class="fa fa-eye"></i> view</a>&nbsp;';
                        return result;
                    }
                }
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
            }
        });

    }

    function viewStockDetails(e) {
        $('#id').val($(e).data('id'));
        $('#batch').val($(e).data('batch'));
        $('#units').val($(e).data('units'));
        $('#pcost').val($(e).data('pcost'));
        $('#ccost').val($(e).data('ccost'));
        $('#tcost').val($(e).data('tcost'));
        $('#order_id').val($(e).data('order_id'));
        $('#product_id').val($(e).data('product_id'));
        $('#soldout').val($(e).data('soldout'));

        $('.alert').hide();

        //console.log($(e).data('product_name'));
        $('#TransfersModalLabel').html($(e).data('product_name')+" - Batch "+$(e).data('batch'));
        $('#expected_sales').html($(e).data('expected_sales'));
        $('#actual_sales').html($(e).data('actual_sales'));
        $('#expected_profit').html($(e).data('expected_profit'));
        $('#actual_profit').html($(e).data('actual_profit'));
        $('#system_profit').html($(e).data('system_profit'));
        //document.querySelector('#TransfersModalLabel').innerHTML = $(e).data('product_name').split(' ').join('-');

        $('#sales').DataTable({
            "autoWidth": false,
            "responsive": true,
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false
        });

        loadStockTransfers($(e).data('id'));

    }

    function loadStockTransfers(stock_id){
        const trn_url = "{{ url('admin/stock/get-stock-transfers')}}"+"/"+stock_id;
        //console.log(trn_url);
        $.fn.dataTable.ext.errMode = 'ignore';
        $('#transfers').DataTable().destroy();
        $('#transfers tbody').empty();
        var table = $('#transfers').DataTable({
            "autoWidth": false,
            "responsive": true,
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            ajax: {
                url: trn_url,
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product_name', name: 'product_name'},
                {data: 'batch', name: 'batch'},
                {data: 'amount', name: 'amount'}
            ],
            fnFooterCallback: function(nRow, aaData, iStart, iEnd, aiDisplay) {
                var api = this.api();
                var total_amount = 0;
                aaData.forEach(function(x) {
                    total_amount += (x['amount']);
                });
                $(api.column(3).footer()).html(
                    total_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
            }
        });
    }

    function resetForm() {
        $('#id').val('');
        $('#batch').val('');
        $('#units').val('');
        $('#pcost').val('');
        $('#ccost').val('');
        $('#tcost').val('');
        $('#input').val('');
        $('.alert').hide();
        $('#formStock').trigger("reset");
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

    $("#TransfersModal").on("hidden", function () {
        //location.reload();
    });
</script>
@stop
