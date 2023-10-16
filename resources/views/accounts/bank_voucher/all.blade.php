@extends('layouts.app')
@section('title','Bank Voucher(BV) Lists')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <div class="card-header">

                    <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                               <a href="{{ route('bank_voucher.create') }}" class="btn btn-primary bg-gradient-primary pull-left">Bank Voucher Create</a>

                           </div>
                        </div>
                        <div class="col-md-9">
                            <form target="_blank" action="{{ route('bank_voucher_range_print') }}" class="row">
                                <div class="col-md-4">
                                   <div class="form-group">
                                       <select style="max-width: 300px !important;" required class="form-control select2" id="bank_account_code" name="bank_account_code">
                                           <option value="">Search Bank Account Code</option>
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
                                    <button class="btn btn-default bg-gradient-primary pull-right"><i class="fa fa-print"></i> Print</button>
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
            $('#bank_account_code').select2({
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
            $('#bank_account_code').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $("#bank_account_code").index(this);
                $('#account_head_code_name').val(data.text);
            });

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('bank_voucher.datatable') }}',

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
