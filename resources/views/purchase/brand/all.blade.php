@extends('layouts.app')
@section('title','Brand')
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
                    <a href="{{ route('brand.add') }}" class="btn btn-primary bg-gradient-primary">Add Brand</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Discount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($brands as $brand)
                                <tr>
                                    <td>{{ $brand->name }}</td>
                                    <td>{{ number_format($brand->discount, 2) }}</td>
                                    <td>
                                        @if ($brand->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="{{ route('brand.edit', ['brand' => $brand->id]) }}">Edit</a>
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

    <script>
        $(function () {
            $('#table').DataTable({
                "order": [[ 1, "asc" ]]
            });
        })
    </script>
@endsection
