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
                                <th>S/L</th>
                                <th>Product</th>
                                <th>Serial</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Selling Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
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
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('all_stock.datatable') }}',
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],

                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'product', name: 'product.name'},
                    {data: 'serial', name: 'serial'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'unit_price', name: 'unit_price'},
                    {data: 'selling_price', name: 'selling_price'},
                    {data: 'action', name: 'action'},
                ],
            });

            $('body').on('click', '.btn-add', function () {
                var productID = $(this).data('id');
                var productSerial = $(this).data('serial');
                $("#product_id").val(productID);
                $('#serial').val(productSerial);
                $("#modal-edit").modal("show");

            });

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

        });
    </script>
@endsection


