@extends('layouts.app')
@section('title','Employee Type')
@section('style')
    <style>
        table.table-bordered.dataTable th, table.table-bordered.dataTable td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <a href="{{ route('employee_type.add') }}" class="btn btn-primary bg-gradient-primary">Add Employee Type</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Employee Type Name</th>
                                <th>Employee Type Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($employee_types as $employee_type)
                                <tr>
                                    <td>{{ $employee_type->name }}</td>
                                    <td>{{ $employee_type->description }}</td>
                                    <td>
                                        @if ($employee_type->status == 1)
                                            <span class="label label-success">Active</span>
                                        @else
                                            <span class="label label-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee_type->id != 1 && $employee_type->id != 2)
                                            <a class="btn btn-success btn-sm btn-edit" href="{{ route('employee_type.edit', ['employee_type' => $employee_type->id]) }}"><i class="fa fa-edit"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables -->
    <script>
        $(function () {
            $('#table').DataTable();
        })
    </script>
@endsection
