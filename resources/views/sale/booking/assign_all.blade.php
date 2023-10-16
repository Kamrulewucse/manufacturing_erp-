@extends('layouts.app')
@section('title','Pre Booking')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">

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
@endsection
@section('script')
    <script>
        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('assign_receipt.datatable') }}',

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



        });
    </script>
@endsection
