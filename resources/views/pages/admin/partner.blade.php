@extends('adminlte::page')

@section('title', 'Partners')

@section('content_header')
<h1>Manage Partners</h1>
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
                        onclick="$('#InputModal').modal('show');resetForm()">+ Add Partner
                </button>
            </div>
            <div class="table-responsive mt-4">
                <table id="product" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Profit Share</th>
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
                <h5 class="modal-title" id="InputModalLabel">Add/Edit Partner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ url('admin/partner/save') }}" id="formPartner">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" id="id" name="id">
                        <label for="name" class="col-sm-3 col-form-label">Full Name *</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" autocomplete="off" id="name" name="name"
                                   placeholder="Full Name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label">Email*</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" autocomplete="off" id="email" name="email"
                                   placeholder="Email" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="phone" class="col-sm-3 col-form-label">Phone*</label>
                        <div class="col-sm-8">
                            <input type="numeral" class="form-control" autocomplete="off" id="phone" name="phone"
                                   placeholder="Phone No." required>
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
                    <div class="form-group row">
                        <label for="profit_share" class="col-sm-3 col-form-label">Profit Share*</label>
                        <div class="col-sm-8">
                            <input type="numeral" class="form-control" autocomplete="off" id="profit_share"
                                   name="profit_share"
                                   placeholder="Percentage profit Share" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="savePartner()" data-dismiss="modal" id="submitPartner"
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
        const page_url = '{{ url('admin/partner/get-data')}}';

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
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'profit_share', name: 'profit_share'},
                {data: 'date_added', name: 'date_added'},
                {
                    "data": null, "sortable": false,
                    render: function (data, type, row, meta) {
                        var result = '<a class="btn btn-success btn-sm" \
                                    data-id = ' + row.id + ' \
                                    data-name = \'' + row.name + '\' \
                                    data-email = ' + row.email + ' \
                                    data-phone = \'' + row.phone + '\' \
                                    data-profit_share = ' + row.profit_share + ' \
                                    data-status_id = ' + row.status_id + ' \
                                onclick="editPartner(this)" data-toggle="modal" data-target="#InputModal"><i class="fa fa-edit"></i> edit</a>&nbsp;';
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
            buttons: [],
            initComplete: function (settings, json) {
                $(".dt-buttons .btn").removeClass("btn-secondary")
            },
            drawCallback: function (settings) {
                console.log(settings.json);
            }
        });

    }

    function editPartner(e) {
        $('#id').val($(e).data('id'));
        $('#name').val($(e).data('name'));
        $('#email').val($(e).data('email'));
        $('#phone').val($(e).data('phone'));
        $('#profit_share').val($(e).data('profit_share'));
        $('#status_id').val($(e).data('status_id')).trigger('change');

        $('.alert').hide();
    }

    function savePartner() {
        let id = document.getElementById('id').value;
        let name = document.getElementById('name').value;
        let email = document.getElementById('email').value;
        let phone = document.getElementById('phone').value;
        let profit_share = document.getElementById('profit_share').value;
        let status_id = $('#status_id').val();

        var url = "{{ url('admin/partner/save') }}";

        if (name === '') {
            Swal.fire("Error!", "Name is required", "error");
        } else if (email === '') {
            Swal.fire("Error!", "Email is required", "error");
        } else if (phone === '') {
            Swal.fire("Error!", "Phone Number is required", "error");
        } else if (profit_share === null) {
            Swal.fire("Error!", "Profit share is required", "error");
        } else {
            // swalLoading();
            document.getElementById("submitPartner").disabled = true;
            var form_data = new FormData();
            form_data.append('id', id);
            form_data.append('name', name);
            form_data.append('email', email);
            form_data.append('phone', phone);
            form_data.append('profit_share', profit_share);
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
                cache: false,
                processData: false,
                success: function (result) {
                    document.getElementById("submitPartner").disabled = false;

                    $('#InputModal').modal('hide');
                    Swal.fire("Success!", result.message, "success");
                    loadList();
                }, error: function (xhr, status, error) {
                    Swal.fire("Error!", JSON.stringify(xhr.responseJSON.errors), "error");
                    document.getElementById("submitPartner").disabled = false;
                },

            });
        }
    }

    function resetForm() {
        $('#id').val('');
        $('#name').val('');
        $('#email').val('');
        $('#phone').val('');
        $('#profit_share').val('');
        $('#status_id').val('').trigger('change');
        $('.alert').hide();
        $('#formPartner').trigger("reset");
    }
</script>
@stop
