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

        $('#order_id').on("change", function (e) {
            //console.log($(this).val());
            loadOrderDetails($(this).val());
        });

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
                {data: 'total_sales', name: 'total_sales'},
                {data: 'profit', name: 'profit'},
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
                                    data-soldout = '+row.soldout+' \
                                onclick="viewStockDetails(this)" data-toggle="modal" data-target="#InputModal"><i class="fa fa-eye"></i> view</a>&nbsp;';
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

    function loadOrderDetails(order_id) {
        //console.log(order_id);
        //resetForm();
        const page_url = "{{ url('admin/order/get-order')}}"+"/"+order_id;
        $.ajax({
            url: page_url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#batch').val(res[0].batch);
                $('#units').val(res[0].units);
                $('#pcost').val(res[0].pcost);
                $('#ccost').val(res[0].ccost);
                $('#tcost').val(res[0].tcost);
                $('#product_id').val(res[0].product_id);
            }
        });
    }

    function editStock(e) {
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
</script>
@stop
