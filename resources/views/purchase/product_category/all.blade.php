@extends('layouts.app')
@section('title','Product Category')
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
                    <a href="{{ route('product_category.add') }}" class="btn btn-primary bg-gradient-primary">Add Category</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        @if ($category->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="{{ route('product_category.edit', ['category' => $category->id]) }}">Edit</a>
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
