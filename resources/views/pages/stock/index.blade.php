@extends('adminlte::page')

@section('title', 'Manage Stock')

@section('content_header')
<h1>Manage Stock</h1>
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
                <button type="button" class="btn btn-primary mt-3 ml-3"
                        onclick="$('#InputModal').modal('show');resetForm()">+ Add Stock
                </button>
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
                        <th>Spoilt</th>
                        <th>Date Added</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="InputModal" aria-labelledby="InputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="InputModalLabel">Record Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ url('admin/stock/save') }}" id="formStock">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="product_id" name="product_id">
                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" id="id" name="id">
                        <label for="order_id" class="col-sm-3 col-form-label">Order *</label>
                        <div class="col-sm-9">
                            <select class="form-control select2" id="order_id" name="order_id" style="width:80%">
                                <option value=''>--Select--</option>
                                @foreach($order as $row)
                                <option value="{{ $row->id }}">{{ "Batch ".$row->batch." - ".$row->product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="order_id" class="col-sm-3 col-form-label">Sold Out *</label>
                        <div class="col-sm-9">
                            <select class="form-control select2" id="soldout" name="soldout" style="width:80%">
                                <option value=''>--Select--</option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="units" class="col-sm-3 col-form-label">Batch No. *</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" autocomplete="off" id="batch" name="batch"
                                   placeholder="Batch Number e.g. 1" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="units" class="col-sm-3 col-form-label">Units *</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" autocomplete="off" id="units" name="units"
                                   placeholder="Number of units e.g. 1000" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cost" class="col-sm-3 col-form-label">Product Cost *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="pcost"
                                       name="pcost" placeholder="Purchase cost for the batch" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cost" class="col-sm-3 col-form-label">Clearance Cost *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="ccost"
                                       name="ccost" placeholder="Actual clearance cost" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cost" class="col-sm-3 col-form-label">Transport Cost *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="tcost"
                                       name="tcost" placeholder="Actual transport cost" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="spoilt" class="col-sm-3 col-form-label">Spoilt *</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" autocomplete="off" id="spoilt" name="spoilt"
                                   placeholder="Number of spoilt units e.g. 5" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="saveStock()" data-dismiss="modal" id="submitStock"
                            class="btn btn-primary">Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- #DetailsModal -->
<div class="modal fade" id="DetailsModal" aria-labelledby="InputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="InputModalLabel"></h5>
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

        $('#order_id').on("change", function (e) {
            //console.log($(this).val());
            loadOrderDetails($(this).val());
        });

    });

    function loadList() {
        const page_url = '{{ url('admin/stock/get-data')}}';

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
                {data: 'spoilt', name: 'spoilt'},
                {data: 'date_added', name: 'date_added'},
                { "data": null,"sortable": false,
                    render: function (data, type, row, meta) {
                        var result = '<a class="btn btn-success btn-sm" \
                                    data-id = '+row.id+' \
                                    data-batch = \''+row.batch+'\' \
                                    data-units = '+row.units+' \
                                    data-pcost = \''+row.pcost+'\' \
                                    data-ccost = \''+row.ccost+'\' \
                                    data-tcost = \''+row.tcost+'\' \
                                    data-spoilt = '+row.spoilt+' \
                                    data-order_id = '+row.order_id+' \
                                    data-product_id = '+row.product_id+' \
                                    data-soldout = '+row.soldout+' \
                                onclick="editStock(this)" data-toggle="modal" data-target="#InputModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
                        result += '<a class="btn btn-info btn-sm" \
                                    data-id = '+row.id+' \
                                    data-batch = \''+row.batch+'\' \
                                    data-units = '+row.units+' \
                                    data-pcost = \''+row.pcost+'\' \
                                    data-ccost = \''+row.ccost+'\' \
                                    data-tcost = \''+row.tcost+'\' \
                                    data-spoilt = '+row.spoilt+' \
                                    data-order_id = '+row.order_id+' \
                                    data-product_id = '+row.product_id+' \
                                    data-product_name = \''+row.product_name+'\' \
                                    data-actual_sales = \''+row.actual_sales+'\' \
                                    data-expected_sales = \''+row.expected_sales+'\' \
                                    data-actual_profit = \''+row.actual_profit+'\' \
                                    data-expected_profit = \''+row.expected_profit+'\' \
                                    data-system_profit = \''+row.system_profit+'\' \
                                    data-soldout = '+row.soldout+' \
                                onclick="viewStockDetails(this)" data-toggle="modal" data-target="#DetailsModal"><i class="fa fa-eye"></i></a>';
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
        $('#spoilt').val($(e).data('spoilt'));
        $('#pcost').val($(e).data('pcost'));
        $('#ccost').val($(e).data('ccost'));
        $('#tcost').val($(e).data('tcost'));
        //$('#order_id').val($(e).data('id'));
        $('#order_id').val($(e).data('order_id')).trigger('change');
        $('#soldout').val($(e).data('soldout')).trigger('change');

        $('.alert').hide();
    }

    function saveStock() {
        let id = document.getElementById('id').value;
        let order_id = $('#order_id').val();
        let soldout = $('#soldout').val();
        let batch = document.getElementById('batch').value;
        let units = document.getElementById('units').value;
        let pcost = document.getElementById('pcost').value;
        let ccost = document.getElementById('ccost').value;
        let tcost = document.getElementById('tcost').value;
        let spoilt = document.getElementById('spoilt').value;
        let product_id = document.getElementById('product_id').value;

        var url = "{{ url('admin/stock/save') }}";

        if (units === '') {
            Swal.fire("Error!", "Name of units required", "error");
        } else if (batch === '') {
            Swal.fire("Error!", "Batch id is required", "error");
        } else if (pcost === null) {
            Swal.fire("Error!", "Product cost is required", "error");
        } else if (ccost === null) {
            Swal.fire("Error!", "Clearance cost is required", "error");
        } else if (tcost === null) {
            Swal.fire("Error!", "Transport cost is required", "error");
        } else {
            // swalLoading();
            document.getElementById("submitStock").disabled = true;
            var form_data = new FormData();
            form_data.append('id', id);
            form_data.append('order_id', order_id);
            form_data.append('soldout', soldout);
            form_data.append('batch', batch);
            form_data.append('units', units);
            form_data.append('pcost', pcost);
            form_data.append('ccost', ccost);
            form_data.append('tcost', tcost);
            form_data.append('spoilt', spoilt);
            form_data.append('product_id', product_id);

            $.ajax({
                type: "POST",
                url: url,
                beforeSend: function (xhr) {
                    var token = $('meta[name="csrf_token"]').attr('content');

                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    }
                },
                data: form_data,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (result) {
                    document.getElementById("submitStock").disabled = false;

                    $('#InputModal').modal('hide');
                    Swal.fire("Success!", result.message, "success");
                    loadList();
                }, error: function (xhr, status, error) {
                    Swal.fire("Error!", JSON.stringify(xhr.responseJSON.errors), "error");
                    document.getElementById("submitStock").disabled = false;
                },

            });
        }
    }

    function viewStockDetails(e) {
        $('#id').val($(e).data('id'));
        $('#batch').val($(e).data('batch'));
        $('#units').val($(e).data('units'));
        $('#pcost').val($(e).data('pcost'));
        $('#ccost').val($(e).data('ccost'));
        $('#tcost').val($(e).data('tcost'));
        $('#spoilt').val($(e).data('spoilt'));
        $('#order_id').val($(e).data('order_id'));
        $('#product_id').val($(e).data('product_id'));
        $('#soldout').val($(e).data('soldout'));

        $('.alert').hide();

        //console.log($(e).data('product_name'));
        $('#InputModalLabel').html($(e).data('product_name'));
        $('#expected_sales').html($(e).data('expected_sales'));
        $('#actual_sales').html($(e).data('actual_sales'));
        $('#expected_profit').html($(e).data('expected_profit'));
        $('#actual_profit').html($(e).data('actual_profit'));
        $('#system_profit').html($(e).data('system_profit'));
        //document.querySelector('#InputModalLabel').innerHTML = $(e).data('product_name').split(' ').join('-');

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
        $('#spoilt').val(0);
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
