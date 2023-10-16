@extends('layouts.app')
@section('title','Journal Voucher(JV) Lists')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Data Filter</h3>
                </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form target="_blank" action="{{ route('accounts.journal_voucher_range_print') }}" class="row">
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
                <div class="row">
                    <div class="col-md-12">
                        <div  class="row">
                            <div class="col-md-5 ">
                                <div class="form-group">
                                    <input type="text" required id="start_date" autocomplete="off"
                                           name="start_date" class="form-control date-picker"
                                           placeholder="Enter Start Date" value="{{ request()->get('start_date')  }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <input type="text" required id="end_date" autocomplete="off"
                                           name="end_date" class="form-control date-picker"
                                           placeholder="Enter Start Date" value="{{ request()->get('end_date')  }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button id="date_range_search" class="btn btn-default bg-gradient-dark pull-right"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <div class="card-header">
                    <a href="{{ route('accounts.journal_voucher.create') }}" class="btn btn-dark bg-gradient-dark">Journal Voucher Create</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>

                                <th>Date</th>
                                <th>JV No</th>
                                <th>Employee/Party name</th>
                                <th>Debit Codes</th>
                                <th>Credit Codes</th>
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

           var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('accounts.journal_voucher.datatable') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val(),
                        d.end_date = $('#end_date').val()
                    }
                },

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'date',name:'date'},
                    { data: 'jv_no',name:'jv_no'},
                    {data: 'payee_depositor_account_head_name', name: 'payee_depositor_account_head_name'},
                    {data: 'debit_codes', name: 'debit_codes', orderable: false},
                    {data: 'credit_codes', name: 'credit_codes', orderable: false},
                    {data: 'total', name: 'total'},
                    {data: 'action', name: 'action', orderable: false},
                ],  order: [[0, 'asc']],
               "dom": 'lBfrtip',
               "buttons": [
                   {
                       "extend": "copy",
                       "text": "<i class='fas fa-copy'></i> Copy",
                       "className": "btn btn-info"
                   },{
                       "extend": "csv",
                       "text": "<i class='fas fa-file-csv'></i> Export to CSV",
                       "className": "btn btn-warning text-white"
                   },
                   {
                       "extend": "excel",
                       "text": "<i class='fas fa-file-excel'></i> Export to Excel",
                       "className": "btn btn-success"
                   },
                   {
                       "extend": "pdf",
                       "text": "<i class='fas fa-file-pdf'></i> Export to PDF",
                       "className": "btn btn-danger"
                   },
                   {
                       "extend": "print",
                       "text": "<i class='fas fa-print'></i> Print",
                       "className": "btn btn-dark bg-gradient-dark"
                   }
               ],
                "responsive": true, "autoWidth": false,
            });

            $('#start_date,#end_date').change(function () {
                table.ajax.reload();
            });
            $('#date_range_search').click(function () {
                table.ajax.reload();
            });
        })
    </script>
@endsection
