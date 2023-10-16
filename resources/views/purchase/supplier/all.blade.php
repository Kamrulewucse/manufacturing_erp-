@extends('layouts.app')
@section('title','Supplier')
@section('style')
    <style>
        table.table-bordered.dataTable th, table.table-bordered.dataTable td {
            text-align: center;
            vertical-align: middle;
        }
        /*.page-item.active .page-link {*/
        /*    background-color: #009f4b;*/
        /*    border-color: #009f4b;*/
        /*}*/
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <a href="{{ route('supplier.add') }}" class="btn btn-primary bg-gradient-primary">Add Supplier</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Owner Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Opening Due</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->owner_name }}</td>
                                    <td>{{ $supplier->mobile }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->address }}</td>
                                    <td>{{ number_format($supplier->opening_due, 2) }}</td>
                                    <td>
                                        @if ($supplier->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-success btn-sm btn-edit" href="{{ route('supplier.edit', ['supplier' => $supplier->id]) }}"><i class="fa fa-edit"></i></a>
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
            $('#table').DataTable();
        })
    </script>
@endsection

