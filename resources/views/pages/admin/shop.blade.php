@extends('adminlte::page')

@section('title', 'Shops')

@section('content_header')
<h1>Manage Shops</h1>
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
                        onclick="$('#InputModal').modal('show');resetForm()">+ Add Shop
                </button>
            </div>
            <div class="table-responsive mt-4">
                <table id="shop" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Location</th>
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
                <h5 class="modal-title" id="InputModalLabel">Add/Edit Shop</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ url('admin/shop/save') }}" id="formShop">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" id="id" name="id">
                        <label for="name" class="col-sm-3 col-form-label">Name *</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" autocomplete="off" id="name" name="name"
                                   placeholder="Shop Name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location" class="col-sm-3 col-form-label">Location *</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" autocomplete="off" id="location" name="location" placeholder="Shop Location" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="saveShop()" data-dismiss="modal" id="submitShop"
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
        const page_url = '{{ url('admin/shop/get-data')}}';

        $.fn.dataTable.ext.errMode = 'ignore';
        var table = $('#shop').DataTable({
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
                {data: 'name', name: 'name'},
                {data: 'location', name: 'location'},
                {data: 'date_added', name: 'date_added'},
                {
                    "data": null, "sortable": false,
                    render: function (data, type, row, meta) {
                        var result = '<a class="btn btn-success btn-sm" \
                                    data-id = ' + row.id + ' \
                                    data-name = \'' + row.name + '\' \
                                    data-location = \'' + row.location + '\' \
                                onclick="editShop(this)" data-toggle="modal" data-target="#InputModal"><i class="fa fa-edit"></i> edit</a>&nbsp;';
                        return result;
                    }
                }
            ],
            oLanguage: {
                sLengthMenu: "_MENU_",
                sSearch: ""
            },
            aLengthMenu: [[5, 10, 15, 20], [4, 10, 15, 20]],
            order: [[1, "asc"]],
            pageLength: 10,
            buttons: [],
            initComplete: function (settings, json) {
                $(".dt-buttons .btn").removeClass("btn-secondary")
            },
            drawCallback: function (settings) {
                console.log(settings.json);
            }
        });

    }

    function editShop(e) {
        $('#id').val($(e).data('id'));
        $('#name').val($(e).data('name'));
        $('#location').val($(e).data('location'));

        $('.alert').hide();
    }

    function saveShop() {
        let id = document.getElementById('id').value;
        let name = document.getElementById('name').value;
        let location = document.getElementById('location').value;

        var url = "{{ url('admin/shop/save') }}";

        if (name === '') {
            Swal.fire("Error!", "Shop Name is required", "error");
        } else if (location === '') {
            Swal.fire("Error!", "Shop Location is required", "error");
        } else {
            // swalLoading();
            document.getElementById("submitShop").disabled = true;
            var form_data = new FormData();
            form_data.append('id', id);
            form_data.append('name', name);
            form_data.append('location', location);

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
                    document.getElementById("submitShop").disabled = false;

                    $('#InputModal').modal('hide');
                    Swal.fire("Success!", result.message, "success");
                    loadList();
                }, error: function (xhr, status, error) {
                    Swal.fire("Error!", JSON.stringify(xhr.responseJSON.errors), "error");
                    document.getElementById("submitShop").disabled = false;
                },

            });
        }
    }

    function resetForm() {
        $('#id').val('');
        $('#name').val('');
        $('#location').val('');
        $('.alert').hide();
        $('#formShop').trigger("reset");
    }
</script>
@stop
