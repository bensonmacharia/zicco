@extends('adminlte::page')

@section('title', 'Expenses')

@section('content_header')
<h1>Manage Expenses</h1>
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
                        onclick="$('#InputModal').modal('show');resetForm()">+ Add Expense
                </button>
            </div>
            <div class="table-responsive mt-4">
                <table id="expense" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Description</th>
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
                <h5 class="modal-title" id="InputModalLabel">Add/Edit Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ url('admin/expense/save')}}" id="formExpense">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="payment" class="col-sm-3 col-form-label">Expense Type *</label>
                        <input type="hidden" id="id" name="id">
                        <div class="col-sm-9">
                            <select class="form-control select2" id="expense_type" name="expense_type"
                                    style="width:80%">
                                <option value=''>--Select--</option>
                                <option value="1">Shop Rent</option>
                                <option value="2">Store Rent</option>
                                <option value="3">Store Transfers</option>
                                <option value="4">Customer Delivery</option>
                                <option value="5">Receipt Books/Invoices</option>
                                <option value="6">Business Permit</option>
                                <option value="7">Others</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="amount" class="col-sm-3 col-form-label">Expense Amount *</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-3">
                                <span class="input-group-text">KES</span>
                                <input type="text" autocomplete="off" class="form-control numeral-mask" id="amount"
                                       name="amount" placeholder="Expense amount" required>
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="amount" class="col-sm-3 col-form-label">Description *</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" autocomplete="off" id="description" name="description"
                                      placeholder="More details about the expense" rows="2" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="saveExpense()" data-dismiss="modal" id="submitExpense"
                            class="btn btn-primary">Save
                    </button>
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
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        loadList();

    });

    function loadList() {
        const page_url = '{{ url('admin/expense/get-data')}}';

        $.fn.dataTable.ext.errMode = 'ignore';
        var table = $('#expense').DataTable({
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
                {data: 'expense_category', name: 'expense_type'},
                {data: 'expense_amount', name: 'amount'},
                {data: 'description', name: 'description'},
                {data: 'date_added', name: 'date_added'},
                {
                    "data": null, "sortable": false,
                    render: function (data, type, row, meta) {
                        var result = '<a class="btn btn-success btn-sm" \
                                    data-id = ' + row.id + ' \
                                    data-expense_type =  \'' + row.expense_type + '\' \
                                    data-amount = \''+row.amount+'\' \
                                    data-description =  \'' + row.description + '\' \
                                    data-date_added =  \'' + row.date_added + '\' \
                                onclick="editExpense(this)" data-toggle="modal" data-target="#InputModal"><i class="fa fa-edit"></i> edit</a>&nbsp;';
                        return result;
                    }
                }
            ],
            oLanguage: {
                sLengthMenu: "_MENU_",
                sSearch: ""
            },
            aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
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

    function editExpense(e) {
        $('#id').val($(e).data('id'));
        $('#expense_type').val($(e).data('expense_type')).trigger('change');
        $('#amount').val($(e).data('amount'));
        $('#description').val($(e).data('description'));
    }

    function saveExpense() {
        let id = document.getElementById('id').value;
        let expense_type = $('#expense_type').val();
        let amount = document.getElementById('amount').value;
        let description = document.getElementById('description').value;

        var url = "{{ url('admin/expense/save') }}";

        if (amount === '') {
            Swal.fire("Error!", "AMount is required", "error");
        } else if (description === '') {
            Swal.fire("Error!", "Description is required", "error");
        } else {
            // swalLoading();
            document.getElementById("submitExpense").disabled = true;
            var form_data = new FormData();
            form_data.append('id', id);
            form_data.append('expense_type', expense_type);
            form_data.append('amount', amount);
            form_data.append('description', description);

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
                    document.getElementById("submitExpense").disabled = false;

                    $('#InputModal').modal('hide');
                    Swal.fire("Success!", result.message, "success");
                    loadList();
                }, error: function (xhr, status, error) {
                    Swal.fire("Error!", xhr.responseJSON.errors.name, "error");
                    document.getElementById("submitExpense").disabled = false;
                },

            });
        }
    }

    function resetForm() {
        $('#id').val('');
        $('#formExpense').trigger("reset");
    }
</script>
@stop
