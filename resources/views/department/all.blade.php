@extends('layouts.app')
@section('title','Department')
@section('style')
    <style>
        table.table-bordered.dataTable th, table.table-bordered.dataTable td {
            text-align: center;
            vertical-align: middle;
        }
        .page-item.active .page-link {
            background-color: #D7784F;
            border-color: #D7784F;
        }
    </style>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <a href="{{ route('department.add') }}" class="btn btn-primary bg-gradient-primary">Add Department</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table id="table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Status</th>
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
                ajax: '{{ route('department.datatable') }}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "responsive": true, "autoWidth": false,
            });
        });
    </script>
    <script>
        $(function () {
            $('body').on('click', '#delete', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Delete This Department",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete Department!'

                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: route('department.delete'),
                            type: 'DELETE',
                            success: function (result) {
                                location.reload();
                            }
                        });
                    }
                })

            });
        });

    </script>
@endsection
