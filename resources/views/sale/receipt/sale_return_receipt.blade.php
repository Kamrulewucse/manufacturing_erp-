@extends('layouts.app')
@section('title','Sale Return Receipt')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('sale.return') }}" class="btn btn-primary bg-gradient-primary">Add Return</a>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table id="table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Return Order No</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pay">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Supplier Payment Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                        <div class="form-group">
                            <label>Order No</label>
                            <input class="form-control" id="modal-order-number" disabled>
                            <input type="hidden" class="form-control" id="order_id" name="order" >
                        </div>
                        <div class="form-group">
                            <label>Order Due</label>
                            <input class="form-control" id="modal-order-due" disabled>
                        </div>

                        <div class="form-group">
                            <label>Payment Type</label>
                            <select class="form-control" id="payment_type" name="payment_type">
                                <option value="1">Cheque</option>
                                <option value="2">Cash</option>
                            </select>
                        </div>

                        <div class="form-group"  id="modal-bank-info">
                            <label>Cheque No.</label>
                            <input class="form-control" type="text" name="cheque_no" placeholder="Cheque No.">
                        </div>

                        <div class="form-group">
                            <label> Account Head </label>
                            <select class="form-control select2" style="width: 100%" id="cash_account_code" name="cash_account_code">
                                <option value=""> Select Cash/Bank Account </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control" name="amount" placeholder="Enter Amount">
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <input type="text" class="form-control pull-right date-picker" id="date" name="date" value="{{ date('d-m-Y') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Note</label>
                            <input class="form-control" name="note" placeholder="Enter Note">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-pay">Pay</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function () {
            intSelect2();
            $('#payment_type').change(function () {
                if ($(this).val() == '2') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            });
            $('#modal-pay-type').trigger('change');

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('sale_return_receipt.datatable') }}',
                columns: [
                    {data: 'date', name: 'date',searchable:false},
                    {data: 'order_no', name: 'order_no'},
                    {data: 'client', name: 'client.name'},
                    {data: 'total', name: 'total'},
                    {data: 'paid', name: 'paid'},
                    {data: 'due', name: 'due'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                order: [[ 0, "desc" ]],
            });

            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('body').on('click', '.btn-pay', function () {
                var orderId = $(this).data('id');
                var order_number = $(this).data('order');
                var order_due = $(this).data('due');
                $('#modal-order-number').val(order_number);
                $('#modal-order-due').val(order_due);
                $('#order_id').val(orderId);
                $('#modal-pay').modal('show');
            });

            $('#modal-btn-pay').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('sale_return_payment') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-pay').modal('hide');
                            Swal.fire(
                                'Paid!',
                                response.message,
                                'success'
                            ).then((result) => {
                                //location.reload();
                                window.location.href = response.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });
        });

        function intSelect2(){
            $('.date-picker').datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy'
            });
            $('.select2').select2()

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
        }
    </script>
@endsection

