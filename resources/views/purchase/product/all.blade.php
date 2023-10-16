@extends('layouts.app')
@section('title','Product')
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary" href="{{ route('product_add') }}">Add Product</a>
                    <hr>
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Warranty</th>
                                <th>Warning Quantity</th>
                                <th>Product Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
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
                ajax: '{{ route('product.datatable') }}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'category', name: 'category'},
                    {data: 'unit', name: 'unit'},
                    {data: 'warranty', name: 'warranty'},
                    {data: 'warning_quantity', name: 'warning_quantity'},
                    {data: 'product_type', name: 'product_type', 'searchable': false },
                    {data: 'description', name: 'description'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "responsive": true, "autoWidth": false,
            });
        });
    </script>
@endsection
