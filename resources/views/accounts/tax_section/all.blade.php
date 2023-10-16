@extends('layouts.app')
@section('title','Section Number of TDS')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <!-- /.card-header -->
                <div class="card-header">
                    <a href="{{ route('section_number_of_tds.add') }}" class="btn btn-primary bg-gradient-primary">Add Section Number of TDS</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/L</th>
                                <th>Source</th>
                                <th>Section</th>
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
                ajax: '{{ route('section_number_of_tds.datatable') }}',

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'sort', name: 'sort'},
                    {data: 'source', name: 'source'},
                    {data: 'section', name: 'section'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                order: [
                    [0, 'asc'],
                ],
                'columnDefs': [
                    {
                        "targets": 1, // your case first column
                        "className": "text-left",
                    },

                ],
                "responsive": true, "autoWidth": false,
            });

        })
    </script>
@endsection
