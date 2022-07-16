@extends('adminlte::page')

@section('title', 'Add Stock')

@section('content_header')
    <h1>Add Stock</h1>
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
              <button type="button" class="btn btn-primary mt-3 ml-3" onclick="$('#InputModal').modal('show');resetForm()">+ Add Stock</button>
            </div>
            <div class="table-responsive mt-4">
                <table id="product" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Units</th>
                            <th>Total Cost</th>
                            <th>Added By</th>
                            <th>Date Added</th>
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
        <h5 class="modal-title" id="InputModalLabel">Add Stock</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{ url('admin/stock/save') }}" id="formStock">
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
            <label for="cost" class="col-sm-3 col-form-label">Total Cost *</label>
            <div class="col-sm-8">
                <div class="input-group mb-3">
                    <span class="input-group-text">KES</span>
                        <input type="text" autocomplete="off" class="form-control numeral-mask" id="cost" name="cost" placeholder="Total cost for the batch" required>
                    <span class="input-group-text">.00</span>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" onclick="saveStock()" data-dismiss="modal" id="submitStock" class="btn btn-primary">Save</button>
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
    const page_url = '{{ url('admin/stock/get-data') }}';

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
            {data: 'product_name', name: 'product_name'},
            {data: 'units', name: 'units'},
            {data: 'total_cost', name: 'total_cost'},
            {data: 'added_by', name: 'added_by'},
            {data: 'date_added', name: 'date_added'},
        ],
        responsive: true,
        oLanguage: {
            sLengthMenu: "_MENU_",
            sSearch: ""
        },
        aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
        pageLength: 20,
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

  function saveStock()
  {
    let id = document.getElementById('id').value;
    let product_id = $('#product_id').val();
    let units = document.getElementById('units').value;
    let cost = document.getElementById('cost').value;

    var url = "{{ url('admin/stock/save') }}";

    if(units == ''){
          Swal.fire("Error!", "Name of units required", "error");
       } else if(cost == ''){
          Swal.fire("Error!", "Total cost is required", "error");
       } else {
        // swalLoading();
           document.getElementById("submitStock").disabled = true;
            var form_data = new FormData();
                form_data.append('id', id);
                form_data.append('product_id', product_id);
                form_data.append('cost', cost);
                form_data.append('units', units);

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
                    document.getElementById("submitStock").disabled = false;

                    $('#InputModal').modal('hide');
                    Swal.fire("Success!", result.message, "success");
                    loadList();
                } ,error: function(xhr, status, error) {
                    Swal.fire("Error!", JSON.stringify(xhr.responseJSON.errors), "error");
                    document.getElementById("submitStock").disabled = false;
                },

            });
       }
  }

function resetForm(){
    $('#id').val('');
    $('#cost').val('');
    $('#units').val('');
    $('#input').val('');
    $('#product_id').val('').trigger('change');
    $('.alert').hide();
    $('#formStock').trigger("reset");
}
</script>
<script>
    $('.numeral-mask').each(function (index, ele) {
        var cleaveCustom = new Cleave(ele, {
            numeral:true,
            numeralDecimalMark: ',',
            delimiter: '.'
        });
    });
    window.addEventListener('DOMContentLoaded', function () {
      var avatar = document.getElementById('avatar');
      var image = document.getElementById('image');
      var input = document.getElementById('input');
      var $progress = $('.progress');
      var $progressBar = $('.progress-bar');
      var $alert = $('.alert');
      var $modal = $('#modal');
      var cropper;

      $('[data-toggle="tooltip"]').tooltip();

      input.addEventListener('change', function (e) {
        var files = e.target.files;
        var done = function (url) {
        //   input.value = '';
          image.src = url;
          $alert.hide();
          $modal.modal('show');
        };
        var reader;
        var file;
        var url;

        if (files && files.length > 0) {
          file = files[0];

          if (URL) {
            done(URL.createObjectURL(file));
          } else if (FileReader) {
            reader = new FileReader();
            reader.onload = function (e) {
              done(reader.result);
            };
            reader.readAsDataURL(file);
          }
        }
      });

      $modal.on('shown.bs.modal', function () {
        cropper = new Cropper(image, {
          aspectRatio: 1,
          viewMode: 0,
        });
      }).on('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
      });

      document.getElementById('crop').addEventListener('click', function () {
        var initialAvatarURL;
        var canvas;

        $modal.modal('hide');

        if (cropper) {
          canvas = cropper.getCroppedCanvas({
            width: 160,
            height: 160,
          });
          initialAvatarURL = avatar.src;
          avatar.src = canvas.toDataURL();
          $progress.show();
          $alert.removeClass('alert-success alert-warning');
          canvas.toBlob(function (blob) {
            var formData = new FormData();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            formData.append('avatar', blob, 'avatar.jpg');
            $.ajax("{{ url('admin/upload-product') }}", {
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,

              xhr: function () {
                var xhr = new XMLHttpRequest();

                xhr.upload.onprogress = function (e) {
                  var percent = '0';
                  var percentage = '0%';

                  if (e.lengthComputable) {
                    percent = Math.round((e.loaded / e.total) * 100);
                    percentage = percent + '%';
                    $progressBar.width(percentage).attr('aria-valuenow', percent).text(percentage);
                  }
                };

                return xhr;
              },

              success: function (result) {
                  console.log(result);
                $alert.show().addClass('alert-success').text('Upload success');
              },

              error: function () {
                avatar.src = initialAvatarURL;
                $alert.show().addClass('alert-warning').text('Upload error');
              },

              complete: function () {
                $progress.hide();
              },
            });
          });
        }
      });
    });
</script>
@stop
