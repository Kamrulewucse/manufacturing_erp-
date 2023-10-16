@extends('layouts.app')
@section('title','Unit')
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
                    <a href="{{ route('unit.add') }}" class="btn btn-primary bg-gradient-primary">Add Unit</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Unit Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($units as $unit)
                                <tr>
                                    <td>{{ $unit->name }}</td>
                                    <td>
                                        @if ($unit->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-success btn-sm btn-edit" href="{{ route('unit.edit', ['unit' => $unit->id]) }}"><i class="fa fa-edit"></i></a>
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
