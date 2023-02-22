@extends('adminlte::page')

@section('title', 'Share Capital')

@section('content_header')
<h1>Share Capital</h1>
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
                <h6 style="font-weight:bold">Business Capital Summary</h6>
            </div>
            <div class="row">

            </div>
            <div class="row">
                <div class="table-responsive mt-4">
                    <table id="assets" class="table table-hover table-sm">
                        <tbody>
                        <tr>
                            <th>ITEM</th>
                            <th>AMOUNT (KES.)</th>
                        </tr>
                        <tr>
                            <td>Inventory in the Shop and Store</td>
                            <td>{{number_format(round($balance, 2))}}</td>
                        </tr>
                        <tr>
                            <td>Inventory in Shipment</td>
                            <td>{{number_format($shipment[0]->total_product_cost)}}</td>
                        </tr>
                        <tr>
                            <td>Inventory Spoilt (*at 50% cost)</td>
                            <td>{{number_format(round($spoilt, 2))}}</td>
                        </tr>
                        <tr>
                            <td>Business Laptop (*at 2% depreciation per month)</td>
                            <td>{{number_format($laptop)}}</td>
                        </tr>
                        <tr>
                            <td>Cash in Hand</td>
                            <td>{{number_format($cash)}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold">TOTAL</td>
                            <td style="font-weight:bold">{{number_format($total)}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
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
                        <th>Name</th>
                        <th>Status</th>
                        <th>Profit Share</th>
                        <th>Capital Amount</th>
                    </tr>
                    </thead>

                </table>
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
                {data: 'partner_status', name: 'partner_status'},
                {data: 'profit_share_percentage', name: 'profit_share_percentage'},
                {data: 'capital_share', name: 'capital_share'},
            ],
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
        $('#profit_share_percentage').val($(e).data('profit_share_percentage').replace(/%\s?/g, ''));
        $('#status_id').val($(e).data('status_id')).trigger('change');
        //console.log($(e).data('profit_share_percentage').replace(/%\s?/g, ''));

        $('.alert').hide();
    }

    function savePartner() {
        let id = document.getElementById('id').value;
        let name = document.getElementById('name').value;
        let email = document.getElementById('email').value;
        let phone = document.getElementById('phone').value;
        let profit_share_percentage = document.getElementById('profit_share_percentage').value;
        let status_id = $('#status_id').val();

        var url = "{{ url('admin/partner/save') }}";

        if (name === '') {
            Swal.fire("Error!", "Name is required", "error");
        } else if (email === '') {
            Swal.fire("Error!", "Email is required", "error");
        } else if (phone === '') {
            Swal.fire("Error!", "Phone Number is required", "error");
        } else if (profit_share_percentage === null) {
            Swal.fire("Error!", "Profit share is required", "error");
        } else {
            // swalLoading();
            document.getElementById("submitPartner").disabled = true;
            var form_data = new FormData();
            form_data.append('id', id);
            form_data.append('name', name);
            form_data.append('email', email);
            form_data.append('phone', phone);
            form_data.append('profit_share_percentage', profit_share_percentage);
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
        $('#profit_share_percentage').val('');
        $('#status_id').val('').trigger('change');
        $('.alert').hide();
        $('#formPartner').trigger("reset");
    }
</script>
@stop
