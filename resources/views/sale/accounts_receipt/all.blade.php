@extends('layouts.app')
@section('title','Accounts Receipt')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order No</th>
                                <th>Customer</th>
                                <th>Dealer</th>
                                <th>Distributor</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th>Technician Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var due;

        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('accounts_receipt.datatable') }}',
                columns: [
                    {data: 'date', name: 'date',searchable:false},
                    {data: 'order_no', name: 'order_no'},
                    {data: 'customer', name: 'customer.name'},
                    {data: 'dealer', name: 'dealer.name'},
                    {data: 'distributor', name: 'distributor.name'},
                    {data: 'total', name: 'total'},
                    {data: 'paid', name: 'paid'},
                    {data: 'due', name: 'due'},
                    {data: 'status', name: 'status'},
                    {data: 'technician_status', name: 'technician_status'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                order: [[ 0, "desc" ]],
            });

            $('body').on('click', '.btn-approve', function () {

                var orderId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Approved This Order!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Approve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "Post",
                            url: "{{ route('accounts_sales_order_approve') }}",
                            data: { orderId: orderId }
                        }).done(function( response ) {
                            if (response.success) {
                                Swal.fire(
                                    'Approved!',
                                    response.message,
                                    'success'
                                ).then((result) => {
                                    //location.reload();
                                    window.location.href = response.redirect_url;
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
