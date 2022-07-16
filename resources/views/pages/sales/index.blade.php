@extends('adminlte::page')

@section('title', 'Sales')

@section('content_header')
    <h1>Manage Sales</h1>
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
                <button type="button" class="btn btn-primary mt-3 ml-3" onclick="$('#InputModal').modal('show');resetForm()">+ New Sale</button>
            </div>
            <div class="table-responsive mt-4">
                <table id="product" class="table table-bordered table-striped">
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
                            <th>Receipt</th>
                            <th>Invoice</th>
                            <th>Date</th>
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
                <h5 class="modal-title" id="InputModalLabel">Add/Edit Sale</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ url('admin/sale/save') }}" id="formSale">
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
                        <label for="units" class="col-sm-3 col-form-label">Units *</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" autocomplete="off" id="units" name="units" placeholder="Number of units" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="price" class="col-sm-3 col-form-label">Price *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="price" name="price" placeholder="Price charged per unit" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="customer_id" class="col-sm-3 col-form-label">Customer *</label>
                        <div class="col-sm-9">
                            <select class="form-control select2" id="customer_id" name="customer_id" style="width:50%">
                                <option value=''>--Select--</option>
                                @foreach($customer as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="amnt_paid" class="col-sm-3 col-form-label">Amount Paid *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="amnt_paid" name="amnt_paid" placeholder="Amount paid" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rcpt_no" class="col-sm-3 col-form-label">Receipt No.</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" autocomplete="off" id="rcpt_no" name="rcpt_no" placeholder="Receipt No. (optional)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inv_no" class="col-sm-3 col-form-label">Invoice No.</label>
                        <div class="col-sm-8">
                            <input type="numeral" class="form-control" autocomplete="off" id="inv_no" name="inv_no" placeholder="Invoice No.(optional)" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="saveSale()" data-dismiss="modal" id="submitSale" class="btn btn-primary">Save</button>
                </div>
            </form>
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
  loadList();

});

function loadList() {
    const page_url = '{{ url('admin/sale/get-data') }}';

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
            { "data": null,"sortable": false,
                render: function (data, type, row, meta) {
                    var result = '<a class="btn btn-success btn-sm" \
                                    data-id = '+row.id+' \
                                    data-customer_id = '+row.customer_id+' \
                                    data-product_id = '+row.product_id+' \
                                    data-units = '+row.units+' \
                                    data-price = '+row.price+' \
                                    data-amnt_paid = '+row.amnt_paid+' \
                                    data-rcpt_no = \''+row.rcpt_no+'\' \
                                    data-inv_no = \''+row.inv_no+'\' \
                                onclick="editSale(this)" data-toggle="modal" data-target="#InputModal"><i class="fa fa-edit"></i> edit</a>&nbsp;';
                    result += '<a class="btn btn-warning btn-sm" onclick="destroy('+row.id+')"><i class="fa fa-trash"></i> delete</a>';
                        return result;
                }
            }
        ],
        responsive: true,
        oLanguage: {
            sLengthMenu: "_MENU_",
            sSearch: ""
        },
        aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
        pageLength: 10,
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

function editSale(e) {
        $('#id').val($(e).data('id'));
        $('#product_id').val($(e).data('product_id')).trigger('change');
        $('#units').val($(e).data('units'));
        $('#price').val($(e).data('price'));
        $('#customer_id').val($(e).data('customer_id')).trigger('change');
        $('#amnt_paid').val($(e).data('amnt_paid'));
        $('#rcpt_no').val($(e).data('rcpt_no'));
        $('#inv_no').val($(e).data('inv_no'));

        $('.alert').hide();
    }

  function saveSale()
  {
    let id = document.getElementById('id').value;
    let product_id = $('#product_id').val();
    let units = document.getElementById('units').value;
    let price = document.getElementById('price').value;
    let customer_id = $('#customer_id').val();
    let amnt_paid = document.getElementById('amnt_paid').value;
    let rcpt_no = document.getElementById('rcpt_no').value;
    let inv_no = document.getElementById('inv_no').value;

    var url = "{{ url('admin/sale/save') }}";

    if(units == ''){
          Swal.fire("Error!", "Units are required", "error");
       }else{
        // swalLoading();
           document.getElementById("submitSale").disabled = true;
            var form_data = new FormData();
                form_data.append('id', id);
                form_data.append('product_id', product_id);
                form_data.append('units', units);
                form_data.append('price', price);
                form_data.append('customer_id', customer_id);
                form_data.append('amnt_paid', amnt_paid);
                form_data.append('rcpt_no', rcpt_no);
                form_data.append('inv_no', inv_no);

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
                cache : false,
                processData : false,
                success: function(result){
                    document.getElementById("submitSale").disabled = false;

                    $('#InputModal').modal('hide');
                    Swal.fire("Success!", result.message, "success");
                    loadList();
                } ,error: function(xhr, status, error) {
                    Swal.fire("Error!", JSON.stringify(xhr.responseJSON.errors), "error");
                    document.getElementById("submitSale").disabled = false;
                },

            });
       }
  }

    function destroy(id){
        var url = "{{url('admin/sale/destroy')}}"+"/"+id;
        Swal.fire({
            title: `Are you sure?`,
            text: ` This item will be permanantly deleted!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.value) {
                        $.ajax({
                    type: "POST",
                    url: url,
                    beforeSend: function (xhr) {
                        var token = $('meta[name="csrf_token"]').attr('content');

                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    },
                    dataType: "json",
                    contentType: false,
                    cache : false,
                    processData : false,
                    success: function(result){
                        Swal.fire("Success!", result.message, "success");
                        loadList();
                    } ,error: function(xhr, status, error) {
                        console.log(xhr.responseJSON.message);
                    },

                });
                }else{

                    }
            })
    }

function resetForm(){
    $('#id').val('');
    $('#product_id').val('').trigger('change');
    $('#units').val('');
    $('#price').val('');
    $('#customer_id').val('').trigger('change');
    $('#amnt_paid').val('');
    $('#rcpt_no').val('');
    $('#inv_no').val('');
    $('.alert').hide();
    $('#formSale').trigger("reset");
}
</script>
@stop
