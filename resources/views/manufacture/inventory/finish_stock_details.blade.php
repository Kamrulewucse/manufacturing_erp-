@extends('layouts.app')
@section('title','Inventory')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-striped display nowrap" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Serial</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Selling Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($inventories as $inventorie)
                                <tr>
                                    <td>{{$inventorie->product->name}}</td>
                                    <td>{{$inventorie->serial}}</td>
                                    <td>{{$inventorie->quantity}}</td>
                                    <td>{{$inventorie->unit_price}}</td>
                                    <td>{{$inventorie->selling_price}}</td>
                                    <td>
                                        <a role="button" class="btn btn-info btn-sm" onclick="priceUpdate({{$inventorie->id}})">Add</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span></button>
                    <h4 class="modal-title" style="margin-right: 80px !important;">Add Serials</h4>
                </div>
                <div class="modal-body">
                    <form id="modal-edit-form" enctype="multipart/form-data" name="modal-edit-form">

                        <div class="form-group">
                            <input type="hidden" id="product_id" name="product_id">
                            <label>Serial Number</label>
                            <input class="form-control" id="serial" name="serial"  placeholder="Updated product serial">
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-update">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection


@section('script')
    <script>
        $(function () {
            $('#table').DataTable();

            $('#modal-btn-update').click(function () {
                var formData = new FormData($('#modal-edit-form')[0]);
                $.ajax({
                    type: "POST",
                    url: "{{route('update_serial')}}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $("#modal-edit").modal("hide");
                            Swal.fire(
                                'Updated!',
                                response.message,
                                'success'
                            ).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });
        })

        function priceUpdate(product_id){
            $("#product_id").val(product_id);
            $("#modal-edit").modal("show");
        }
    </script>
@endsection
