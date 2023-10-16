@extends('layouts.app')
@section('title','Cash Voucher(CV) Lists')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                            <a href="{{ route('cash_voucher.create') }}" class="btn btn-dark bg-gradient-dark">Cash Voucher Create</a>
                            </div>
                            </div>
                        <div class="col-md-9">
                            <form target="_blank" action="{{ route('cash_voucher_range_print') }}" class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                    <select required class="form-control cash_account_code_area select2" id="cash_account_code" name="cash_account_code">
                                        <option value="">Search Name of Cash Point</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" required name="from" class="form-control" placeholder="Voucher no from...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" required name="to" class="form-control" placeholder="Voucher no to..">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-default bg-gradient-dark pull-right"><i class="fa fa-print"></i> Print</button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Voucher No</th>
                                <th>Bank Account</th>
                                <th>Payee Name</th>
                                <th>Expenses Code</th>
                                <th>Net Amount</th>
                                <th width="14%">Action</th>
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
            $('#cash_account_code').select2({
                ajax: {
                    url: "{{ route('account_head_code.json') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $('#cash_account_code').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $("#cash_account_code").index(this);
                $('#cash_account_code_name').val(data.text);
            });

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('cash_voucher.datatable') }}',

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'date',name:'date'},
                    { data: 'receipt_payment_no',name:'receipt_payment_no'},
                    {data: 'payment_account_name', name: 'paymentAccountHead.name'},
                    {data: 'payee_depositor_account_head_name', name: 'payeeDepositorAccountHead.name'},
                    {data: 'expenses_code', name: 'expenses_code',searchable: false},
                    {data: 'net_total', name: 'net_total'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                order: [[0, 'asc']],

                "responsive": true, "autoWidth": false,
            });
        })
    </script>
@endsection
