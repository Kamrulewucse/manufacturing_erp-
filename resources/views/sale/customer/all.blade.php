@extends('layouts.app')
@section('title','Customer')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary" href="{{ route('customer.add') }}">Add Customer</a>

                    <hr>

                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Address</th>
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
            var i = 1;
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('customer.datatable') }}',
                columns: [
                    {
                        "render": function() {
                            return i++;
                        }
                    },
                    {data: 'id_no', name: 'id_no',serachable:true},
                    {data: 'name', name: 'name'},
                    {data: 'mobile', name: 'mobile'},
                    {data: 'address', name: 'address'},
                    {data: 'action', name: 'action', orderable: false},
                ],
            });
        })
    </script>
@endsection
