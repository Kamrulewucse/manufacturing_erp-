@extends('layouts.app')
@section('title','Product Type')
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
                    <a href="{{ route('product_type.add') }}" class="btn btn-primary bg-gradient-primary">Add Product Type</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Product Type Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($productTypes as $productType)
                                <tr>
                                    <td>{{ $productType->name }}</td>
                                    <td>
                                        @if ($productType->status == 1)
                                            <span class="label label-success">Active</span>
                                        @else
                                            <span class="label label-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-success btn-sm btn-edit" href="{{ route('product_type.edit', ['product_type' => $productType->id]) }}"><i class="fa fa-edit"></i></a>
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
