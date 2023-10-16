@extends('layouts.app')
@section('title','Finished Goods')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <div class="card-header">
                    <a href="{{route('manufacture.create')}}" class="btn btn-primary">Manufacture</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/L</th>
                                <th>Date</th>
                                <th>Finished Goods</th>
                                <th>Quantity</th>
                                <th>Total Cost</th>
                                <th>Extra Cost</th>
                                <th>Consumption Unit Price</th>
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
@endsection
@section('script')
    <script>
        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('finished_goods.datatable') }}',

                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'date', name: 'date'},
                    {data: 'product_name', name: 'product.name'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'total', name: 'total'},
                    {data: 'extra_cost', name: 'extra_cost'},
                    {data: 'unit_price', name: 'unit_price'},
                    {data: 'selling_price', name: 'selling_price'},
                    {data: 'action', name: 'action', orderable: false},
                ],

                "responsive": true, "autoWidth": false,
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
                            url: "{{ route('finished_goods.delete') }}",
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
        });
    </script>
@endsection
