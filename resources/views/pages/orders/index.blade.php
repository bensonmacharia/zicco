@extends('adminlte::page')

@section('title', 'Manage Order')

@section('content_header')
<h1>Manage Order</h1>
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
                        onclick="$('#InputModal').modal('show'); resetForm()">+ Add Order
                </button>
            </div>
            <div class="table-responsive mt-4">
                <table id="orders" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Units</th>
                        <th>Product Cost</th>
                        <th>Clearance Cost</th>
                        <th>Transport Cost</th>
                        <th>Total Cost</th>
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
                <h5 class="modal-title" id="InputModalLabel">Create Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ url('admin/order/save') }}" id="formOrder">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" id="id" name="id">
                        <label for="product_id" class="col-sm-3 col-form-label">Product *</label>
                        <div class="col-sm-9">
                            <select class="form-control select2" id="product_id" name="product_id" style="width:50%">
                                <option value=''>--Select--</option>
                                @foreach($product as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="payment" class="col-sm-3 col-form-label">Payment Option *</label>
                        <div class="col-sm-9">
                            <select class="form-control select2" id="payment" name="payment" style="width:50%">
                                <option value=''>--Select--</option>
                                <option value="0">Cash</option>
                                <option value="1">Transfer</option>
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
                        <label for="pcost" class="col-sm-3 col-form-label">Product Cost *</label>
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
                        <label for="ccost" class="col-sm-3 col-form-label">Clearance Cost *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="ccost"
                                       name="ccost" placeholder="Estimated clearance cost" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tcost" class="col-sm-3 col-form-label">Transport Cost *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="tcost"
                                       name="tcost" placeholder="Estimated transport cost" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="esale" class="col-sm-3 col-form-label">Estimated Sales *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="esale"
                                       name="esale" placeholder="Estimated total sales" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="asale" class="col-sm-3 col-form-label">Actual Sales </label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="asale"
                                       name="asale" placeholder="Actual total sales" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="saveOrder()" data-dismiss="modal" id="submitOrder"
                            class="btn btn-primary">Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="PartnerModal" aria-labelledby="PartnerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="InputModalLabel">Update Payment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ url('admin/order/payment') }}" id="formPayment">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" id="order_id" name="order_id">
                        <input type="hidden" id="payment_type_id" name="payment_type_id">
                        <label for="product_cost" class="col-sm-3 col-form-label">Product Cost *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="product_cost"
                                       name="product_cost" placeholder="Product cost" disabled>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    @foreach($partner as $rw)
                    <div class="form-row">
                        <div class="col-md-3">
                            <label for="partner-{{ $rw->id }}" class="col-form-label">Partner {{ $rw->id }}*</label>
                        </div>
                        <div class="col-md-5">
                            <select class="form-control select2" id="partner-{{ $rw->id }}" name="partner-{{ $rw->id }}" style="width:80%">
                                <option value=''>--Select--</option>
                                <option value="{{ $rw->id }}" selected>{{ $rw->name }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="amount-{{ $rw->id }}"
                                       name="amount-{{ $rw->id }}" placeholder="Amount" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="d-none" id="transferDiv">
                        <div class="m-3">
                            <table id="add_table" class="table" data-toggle="table" data-mobile-responsive="true">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Amount</th>
                                        <th>
                                            <button class="btn btn-outline-success" id="add_row" class="add" onclick="addRow()"> + Add
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control select2" id="item" name="item"
                                                        style="width:100%">
                                                    <option value=''>--Select Product--</option>
                                                    @foreach($stock as $row)
                                                    <option value="{{ $row->id }}">{{ $row->batch." - ".$row->product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">KES</span>
                                                    <input type="text" autocomplete="off"
                                                           class="form-control numeral-mask" id="tamount"
                                                           name="tamount" placeholder="Amount" required>
                                                    <span class="input-group-text">.00</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-danger delete_row"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="savePaymentDetails()" data-dismiss="modal" id="submitPaymentDetails"
                            class="btn btn-primary">Save
                    </button>
                </div>
            </form>
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

    });

    function loadList() {
        const page_url = '{{ url('admin/order/get-data')}}';

        $.fn.dataTable.ext.errMode = 'ignore';
        var table = $('#orders').DataTable({
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
                {data: 'ppcost', name: 'ppcost'},
                {data: 'cccost', name: 'cccost'},
                {data: 'ttcost', name: 'ttcost'},
                {data: 'scost', name: 'scost'},
                { "data": null,"sortable": false,
                    render: function (data, type, row, meta) {
                        var result = '<a class="btn btn-success btn-sm" \
                                    data-id = '+row.id+' \
                                    data-batch = \''+row.batch+'\' \
                                    data-units = '+row.units+' \
                                    data-pcost = \''+row.pcost+'\' \
                                    data-ccost = \''+row.ccost+'\' \
                                    data-tcost = \''+row.tcost+'\' \
                                    data-esale = \''+row.esale+'\' \
                                    data-asale = \''+row.asale+'\' \
                                    data-payment = '+row.payment+' \
                                    data-product_id = '+row.product_id+' \
                                onclick="editOrder(this)" data-toggle="modal" data-target="#InputModal"><i class="fa fa-edit"></i></a>&nbsp;';
                        result += '<a class="btn btn-info btn-sm"  \
                                    data-id = '+row.id+' \
                                    data-batch = \''+row.batch+'\' \
                                    data-pcost = \''+row.pcost+'\' \
                                    data-ccost = \''+row.ccost+'\' \
                                    data-tcost = \''+row.tcost+'\' \
                                    data-scost = \''+row.scost+'\' \
                                    data-payment = '+row.payment+' \
                                    data-product_id = '+row.product_id+' \
                            onclick="editPayment(this)" data-toggle="modal" data-target="#PartnerModal"><i class="fa fa-check-circle"></i></a>';
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

    function editOrder(e) {
        $('#id').val($(e).data('id'));
        $('#batch').val($(e).data('batch'));
        $('#units').val($(e).data('units'));
        $('#pcost').val($(e).data('pcost'));
        $('#ccost').val($(e).data('ccost'));
        $('#tcost').val($(e).data('tcost'));
        $('#esale').val($(e).data('esale'));
        $('#asale').val($(e).data('asale'));
        $('#product_id').val($(e).data('product_id')).trigger('change');
        $('#payment').val($(e).data('payment')).trigger('change');

        $('.alert').hide();
    }

    function editPayment(e) {
        const cont_url = "{{ url('admin/order/get-contributions')}}"+"/"+$(e).data('id');
        $('#formPayment').trigger("reset");
        $('#amount-1').val('');
        $('#amount-2').val('');
        $('#amount-3').val('');
        $.ajax({
            url: cont_url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                //console.log(res);
                $('#amount-1').val(res[0].amounts.split('-')[0]);
                $('#amount-2').val(res[0].amounts.split('-')[1]);
                $('#amount-3').val(res[0].amounts.split('-')[2]);
            }
        });
        var cost = $(e).data('pcost') + $(e).data('ccost') + $(e).data('tcost') ;
        $('#order_id').val($(e).data('id'));
        $('#payment_type_id').val($(e).data('payment'));
        $('#product_cost').val($(e).data('scost'));

        var pyt = document.getElementById('payment_type_id').value;
        //console.log(pyt);
        if (pyt === '1' ){
            $("#transferDiv").removeClass('d-none');
            loadTransfers($(e).data('id'));
        } else {
            $("#transferDiv").addClass('d-none');
        }

        $('.alert').hide();
    }

    function loadTransfers(order_id){
        const trans_url = "{{ url('admin/order/get-transfers')}}"+"/"+order_id;
        $.ajax({
            url: trans_url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                //console.log(res);
                if(Array.isArray(res) && res.length) {
                    let prodArray = res[0].products.split('-');
                    let amntArray = res[0].amounts.split('-');
                    if (Array.isArray(prodArray) || amntArray.length) {
                        $('#item').val(prodArray[0]).trigger('change');
                        $('#tamount').val(amntArray[0]);
                        if (prodArray.length > 1) {
                            let count = prodArray.length - 1
                            addRowEdit(count, prodArray, amntArray);
                        }
                    }
                }
                //console.log(prodArray.length);
            }
        });
    }

    function saveOrder() {
        let id = document.getElementById('id').value;
        let product_id = $('#product_id').val();
        let payment = $('#payment').val();
        let batch = document.getElementById('batch').value;
        let units = document.getElementById('units').value;
        let pcost = document.getElementById('pcost').value;
        let ccost = document.getElementById('ccost').value;
        let tcost = document.getElementById('tcost').value;
        let esale = document.getElementById('esale').value;
        let asale = document.getElementById('asale').value;

        var url = "{{ url('admin/order/save') }}";

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
        } else if (esale === null) {
            Swal.fire("Error!", "Estimated sales required", "error");
        } else {
            // swalLoading();
            document.getElementById("submitOrder").disabled = true;
            var form_data = new FormData();
            form_data.append('id', id);
            form_data.append('product_id', product_id);
            form_data.append('payment', payment);
            form_data.append('batch', batch);
            form_data.append('units', units);
            form_data.append('pcost', pcost);
            form_data.append('ccost', ccost);
            form_data.append('tcost', tcost);
            form_data.append('esale', esale);
            form_data.append('asale', asale);

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
                    document.getElementById("submitOrder").disabled = false;

                    $('#InputModal').modal('hide');
                    Swal.fire("Success!", result.message, "success");
                    loadList();
                }, error: function (xhr, status, error) {
                    Swal.fire("Error!", JSON.stringify(xhr.responseJSON.errors), "error");
                    document.getElementById("submitOrder").disabled = false;
                },

            });
        }
    }

    function savePaymentDetails(){
        var myData = $('#formPayment').serialize();
        var csrf = $('meta[name="csrf_token"]').attr('content');
        var url = "{{ url('admin/order/payment') }}";
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrf,
            },
            data: {
                "_token": csrf,
                data : myData
            },
            success: function(result) {
                document.getElementById("submitOrder").disabled = false;

                $('#InputModal').modal('hide');
                Swal.fire("Success!", result.message, "success");
                $('#formPayment').trigger("reset");
                loadList();
            },
            error: function(xhr, status, error){
                $('#formPayment').trigger("reset");
                Swal.fire("Error!", JSON.stringify(xhr.responseJSON.errors), "error");
                document.getElementById("submitOrder").disabled = false;
            },
        });
    }

    function resetForm() {
        $('#id').val('');
        $('#batch').val('');
        $('#units').val('');
        $('#pcost').val('');
        $('#ccost').val('');
        $('#tcost').val('');
        $('#esale').val('');
        $('#asale').val('');
        $('#input').val('');
        $('#product_id').val('').trigger('change');
        $('#payment').val('').trigger('change');
        $('.alert').hide();
        $('#formOrder').trigger("reset");
    }

    var clicks = 0;
    function addRow(){
        clicks += 1;
        //console.log(clicks);
        //Add row
        row = '';
        row += '<tr><td><div class="form-group"><select class="form-control select2" id="item_id" name="item_id" style="width:100%"><option value="">--Select Product--</option>@foreach($stock as $row)<option value="{{ $row->id }}">{{ $row->batch." - ".$row->product->name }}</option>@endforeach </select></div></td><td><div class="form-group"><div class="input-group mb-3"><span class="input-group-text">KES</span><input type="text" autocomplete="off" class="form-control numeral-mask" id="tamount_id" name="tamount_id" placeholder="Amount" required><span class="input-group-text">.00</span></div></div></td>';
        row += '<td><button class="btn btn-outline-danger delete_row"><i class="fa fa-trash"></i></button></td></tr>';
        $('#item_id').attr('name', 'item_id_'+clicks);
        $('#item_id').attr('id', 'item_id_'+clicks);
        $('#tamount_id').attr('name', 'tamount_id_'+clicks);
        $('#tamount_id').attr('id', 'tamount_id_'+clicks);
        $("#add_table").append(row);
    }

    function addRowEdit(count, prodArray, amntArray){
        //console.log(count);
        //$("#add_table").closest('tr').remove();
        //$("#add_table tr").remove();
        var x = document.getElementById("add_table").rows.length;
        //console.log(x);
        if(x <= prodArray.length){
            for (let i = 1; i <= count; i++){
                //console.log(i+" - "+prodArray[i]);
                //Add row
                row = '';
                row += '<tr><td><div class="form-group"><select class="form-control select2" id="item_id" name="item_id" style="width:100%"><option value="">--Select Product--</option>@foreach($stock as $row)<option value="{{ $row->id }}">{{ $row->batch." - ".$row->product->name }}</option>@endforeach </select></div></td><td><div class="form-group"><div class="input-group mb-3"><span class="input-group-text">KES</span><input type="text" autocomplete="off" class="form-control numeral-mask" id="tamount_id" name="tamount_id" placeholder="Amount" required><span class="input-group-text">.00</span></div></div></td>';
                row += '<td><button class="btn btn-outline-danger delete_row"><i class="fa fa-trash"></i></button></td></tr>';
                $('#item_id').attr('name', 'item_id_'+i);
                $('#item_id').attr('id', 'item_id_'+i);
                $('#tamount_id').attr('name', 'tamount_id_'+i);
                $('#tamount_id').attr('id', 'tamount_id_'+i);
                $("#add_table").append(row);
                $('#item_id_'+i).val(prodArray[i]).trigger('change');
                $('#tamount_id_'+i).val(amntArray[i]);
            }
        }
        $('#item_id').val(prodArray[1]).trigger('change');
        $('#tamount_id').val(amntArray[1]);
        //console.log(document.getElementById("item_id_"+2));
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

    $("#add_table").on('click', '.delete_row', function (e) {
        e.preventDefault();
        $(this).closest('tr').remove();
    });
</script>
@stop
