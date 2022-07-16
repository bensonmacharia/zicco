@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')
    <h1>Manage Customers</h1>
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
              <button type="button" class="btn btn-primary mt-3 ml-3" onclick="$('#InputModal').modal('show');resetForm()">+ Add</button>
            </div>
            <div class="table-responsive mt-4">
                <table id="product" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
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
        <h5 class="modal-title" id="InputModalLabel">Add/Edit Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{ url('admin/customer/save') }}" id="formCustomer">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="modal-body">
          <div class="form-group row">
            <input type="hidden" id="id" name="id">
            <label for="name" class="col-sm-3 col-form-label">Full Name *</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" autocomplete="off" id="name" name="name" placeholder="Full Name" required>
            </div>
          </div>
          <div class="form-group row">
              <label for="email" class="col-sm-3 col-form-label">Email</label>
              <div class="col-sm-8">
                  <input type="email" class="form-control" autocomplete="off" id="email" name="email" placeholder="Email (optional)">
              </div>
          </div>
          <div class="form-group row">
              <label for="phone" class="col-sm-3 col-form-label">Phone</label>
              <div class="col-sm-8">
                  <input type="numeral" class="form-control" autocomplete="off" id="phone" name="phone" placeholder="Phone No. (optional)" required>
              </div>
          </div>
          <div class="form-group row">
            <label for="status_id" class="col-sm-3 col-form-label">Status *</label>
            <div class="col-sm-9">
                <select class="form-control select2" id="status_id" name="status_id" style="width:50%">
                    <option value=''>--Select--</option>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" onclick="saveCustomer()" data-dismiss="modal" id="submitCustomer" class="btn btn-primary">Save</button>
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
    const page_url = '{{ url('admin/customer/get-data') }}';

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
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'cust_status', name: 'cust_status'},
            {data: 'date_added', name: 'date_added'},
            { "data": null,"sortable": false,
                render: function (data, type, row, meta) {
                    var result = '<a class="btn btn-success btn-sm" \
                                    data-id = '+row.id+' \
                                    data-name = \''+row.name+'\' \
                                    data-email = '+row.email+' \
                                    data-phone = \''+row.phone+'\' \
                                    data-status_id = '+row.status_id+' \
                                onclick="editCustomer(this)" data-toggle="modal" data-target="#InputModal"><i class="fa fa-edit"></i> edit</a>&nbsp;';
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
        order: [[1, "asc"]],
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

function editCustomer(e) {
        $('#id').val($(e).data('id'));
        $('#name').val($(e).data('name'));
        $('#email').val($(e).data('email'));
        $('#phone').val($(e).data('phone'));
        $('#status_id').val($(e).data('status_id')).trigger('change');

        $('.alert').hide();
    }

  function saveCustomer()
  {
    let id = document.getElementById('id').value;
    let name = document.getElementById('name').value;
    let email = document.getElementById('email').value;
    let phone = document.getElementById('phone').value;
    let status_id = $('#status_id').val();

    var url = "{{ url('admin/customer/save') }}";

    if(name == ''){
          Swal.fire("Error!", "Name is required", "error");
       }else{
        // swalLoading();
           document.getElementById("submitCustomer").disabled = true;
            var form_data = new FormData();
                form_data.append('id', id);
                form_data.append('name', name);
                form_data.append('email', email);
                form_data.append('phone', phone);
                form_data.append('status_id', status_id);

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
                    document.getElementById("submitCustomer").disabled = false;

                    $('#InputModal').modal('hide');
                    Swal.fire("Success!", result.message, "success");
                    loadList();
                } ,error: function(xhr, status, error) {
                    Swal.fire("Error!", JSON.stringify(xhr.responseJSON.errors), "error");
                    document.getElementById("submitCustomer").disabled = false;
                },

            });
       }
  }

    function destroy(id){
        var url = "{{url('admin/product/destroy')}}"+"/"+id;
        Swal.fire({
            title: `Are you sure?`,
            text: ` will be permanantly deleted!`,
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
    $('#name').val('');
    $('#email').val('');
    $('#phone').val('');
    $('#status_id').val('').trigger('change');
    $('.alert').hide();
    $('#formCustomer').trigger("reset");
}
</script>
@stop
