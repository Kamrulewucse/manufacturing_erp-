@extends('layouts.app')
@section('title','Manufacture Template')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <div class="card-header">
                    <a href="{{route('manufacture_template.create')}}" class="btn btn-primary">Manufacture Template Create</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/L</th>
                                <th>Finished Goods</th>
                                <th>Row Material Items</th>
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
                ajax: '{{ route('manufacture_template.datatable') }}',

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'product_name', name: 'finishedGoods.name'},
                    {data: 'finished_goods_template_items', name: 'finished_goods_template_items'},
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
                            url: "{{ route('manufacture_template.delete') }}",
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
