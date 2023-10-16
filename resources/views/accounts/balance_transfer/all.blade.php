@extends('layouts.app')
@section('title','Balance Transfer')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-default">
            <div class="card-header">
                <a href="{{ route('balance_transfer.add') }}" class="btn btn-primary bg-gradient-primary">Add Balance Transfer</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table id="table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Voucher</th>
                            <th>Receipt</th>
                            <th>Source</th>
                            <th>Target</th>
                            <th>Amount</th>
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
                ajax: '{{ route('balance_transfer.datatable') }}',

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'date', name: 'date'},
                    {data: 'type', name: 'type',searchable:false},
                    {data: 'voucher_no', name: 'voucher_no'},
                    {data: 'receipt_no', name: 'receipt_no'},
                    {data: 'source_account_head', name: 'sourceAccountHead.name'},
                    {data: 'target_account_head', name: 'targetAccountHead.name'},
                    {data: 'amount', name: 'amount'},
                    {data: 'action', name: 'action'},
                ],
                "responsive": true, "autoWidth": false,
            });
        });
    </script>
@endsection
