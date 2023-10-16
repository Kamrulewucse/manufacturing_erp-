@extends('layouts.app')
@section('title','Pre Booking')
@section('style')
    <style>
        /*.table tbody tr {*/
        /*    background-color: #0b2e13;*/
        /*}*/
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <div class="card-header">
                    <a href="{{route('booking.add')}}" class="btn btn-primary">Add Booking</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/L</th>
                                <th>Order No</th>
                                <th>Status</th>
                                <th>Assign Technician</th>
                                <th>Customer Name</th>
                                <th>Order Quantity</th>
                                <th>Order Date</th>
                                <th>Delivery Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span></button>
                    <h5 class="modal-title" style="margin-right: 50px !important;">Change Delivery Date</h5>
                </div>
                <div class="modal-body">
                    <form id="modal-edit-form" enctype="multipart/form-data" name="modal-edit-form">

                        <div class="form-group">
                            <input type="hidden" id="booking_id" name="booking_id">
                            <label>Delivery Date</label>
                            <input type="text" id="date" autocomplete="off"  value="" name="date" class="form-control date-picker">
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
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('booking.datatable') }}',

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'order_no', name: 'order_no'},
                    {data: 'status', name: 'status'},
                    {data: 'technician', name: 'technician.name'},
                    {data: 'customer', name: 'customer.name'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'date', name: 'date'},
                    {data: 'delivery_date', name: 'delivery_date'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "responsive": true, "autoWidth": false,
                rowCallback: function(row, data) {
                    if (data.remake_request == 1) {
                        $(row).css('background-color', '#00765E').css('color', 'white');
                    }
                },
            });

            $('body').on('click', '.btn-accept', function () {
                var accountHeadId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Accept it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "Post",
                            url: "{{ route('accept_assign_order') }}",
                            data: { id: accountHeadId }
                        }).done(function( response ) {
                            if (response.success) {
                                Swal.fire(
                                    'Accepted!',
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
                        });
                    }
                })
            });

            $('body').on('click', '.btn-stock', function () {
                var accountHeadId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Complete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "Post",
                            url: "{{ route('stock_assign_order') }}",
                            data: { id: accountHeadId }
                        }).done(function( response ) {
                            if (response.success) {
                                Swal.fire(
                                    'Completed!',
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
                        });

                    }
                })

            });

            $('body').on('click', '.btn-change-date', function () {
                var bookingID = $(this).data('id');
                var deliveryDate =moment($(this).data('date')).format('DD-MM-YYYY');
                $("#booking_id").val(bookingID);
                $('#date').val(deliveryDate);
                $("#modal-edit").modal("show");

            });

            $('body').on('click', '.btn-delete', function () {
                var accountHeadId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "Post",
                            url: "{{ route('booking.delete') }}",
                            data: { id: accountHeadId }
                        }).done(function( response ) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
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
                        });

                    }
                })

            });

            $('#modal-btn-update').click(function () {
                var formData = new FormData($('#modal-edit-form')[0]);
                $.ajax({
                    type: "POST",
                    url: "{{route('update_delivery_date')}}",
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
