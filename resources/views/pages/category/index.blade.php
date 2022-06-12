@extends('adminlte::page')

@section('title', 'Categories')

@section('content_header')
    <h1>Manage Categories</h1>
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
              <button type="button" class="btn btn-primary mt-3 ml-3" onclick="$('#InputModal').modal('show');resetForm()">+ Add Category</button>
            </div>
            <div class="table-responsive mt-4">
                <table id="category" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
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
        <h5 class="modal-title" id="InputModalLabel">Add/Edit Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{ url('admin/category/save') }}" id="formCategory">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="modal-body">
          <div class="form-group row">
            <input type="hidden" id="id" name="id">
            <label for="name" class="col-sm-3 col-form-label">Name *</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" autocomplete="off" id="name" name="name" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" onclick="saveCategory()" data-dismiss="modal" id="submitCategory" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('css')
@stop

@section('js')
<script type="text/javascript">
$(document).ready(function(){
  $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
  });
  loadList();

});

function loadList() {
    const page_url = '{{ url('admin/category/get-data') }}';

    $.fn.dataTable.ext.errMode = 'ignore';
    var table = $('#category').DataTable({
        processing: true,
        serverSide: true,
        "bDestroy": true,
        ajax: {
            url: page_url,
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
            {data: 'name', name: 'name'},
            { "data": null,"sortable": false,
                render: function (data, type, row, meta) {
                    var result = '<a class="btn btn-success btn-sm" \
                                    data-id = '+row.id+' \
                                    data-name = '+row.name+' \
                                onclick="editCategory(this)" data-toggle="modal" data-target="#InputModal"><i class="fa fa-edit"></i> edit</a>&nbsp;';
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

function editCategory(e) {
        $('#id').val($(e).data('id'));
        $('#name').val($(e).data('name'));
    }

  function saveCategory()
  {
    let id = document.getElementById('id').value;
    let name = document.getElementById('name').value;

    var url = "{{ url('admin/category/save') }}";

    if(name == ''){
          Swal.fire("Error!", "Name is required", "error");
       }else{
        // swalLoading();
           document.getElementById("submitCategory").disabled = true;
            var form_data = new FormData();
                form_data.append('id', id);
                form_data.append('name', name);

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
                    document.getElementById("submitCategory").disabled = false;

                    $('#InputModal').modal('hide');
                    Swal.fire("Success!", result.message, "success");
                    loadList();
                } ,error: function(xhr, status, error) {
                    Swal.fire("Error!", xhr.responseJSON.errors.name, "error");
                    document.getElementById("submitCategory").disabled = false;
                },

            });
       }
  }

    function destroy(id){
        var url = "{{url('admin/category/destroy')}}"+"/"+id;
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
    $('#formCategory').trigger("reset");
}
</script>
@stop
