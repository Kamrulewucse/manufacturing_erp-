@extends('layouts.app')
@section('title','Designation')
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
                <a href="{{ route('designation.add') }}" class="btn btn-primary bg-gradient-primary">Add Designation</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table id="table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Department</th>
                            <th>Designation</th>
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
                ajax: '{{ route('designation.datatable') }}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'department', name: 'department.name'},
                    {data: 'name', name: 'name'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "responsive": true, "autoWidth": false,
            });
        });
    </script>
@endsection
