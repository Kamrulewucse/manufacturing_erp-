@extends('layouts.app')
@section('title','Supplier Payment')
@section('style')
    <style>
        .input-group-addon i{
            padding-top:10px;
            padding-right: 10px;
            border: 1px solid #cecccc;
            padding-bottom: 10.5px;
            padding-left: 10px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->mobile }}</td>
                                <td>৳{{ number_format($supplier->total, 2) }}</td>
                                <td>৳{{ number_format($supplier->paid, 2) }}</td>
                                <td><span class="total-due" data-supplier-id="{{ $supplier->id }}" data-total-due="{{ $supplier->due }}">৳{{ number_format($supplier->due, 2) }}</span></td>

                                <td>
                                    <a class="btn btn-info btn-sm btn-pay" role="button" data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}">Payment</a>
                                    <a href="{{route('supplier_payment.order_details',['supplier'=>$supplier->id])}}" class="btn btn-warning btn-sm">Details</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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
                            <label>Total Due</label>
                            <input  class="form-control" id="modal-order-due" readonly>
                        </div>
                        <input type="hidden" id="supplier_id" name="supplier_id">

                        <div class="form-group">
                            <label>Payment Type</label>
                            <select class="form-control" id="payment_type" name="payment_type">
                                <option value="1">Bank</option>
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
            $('#table').DataTable();
            intSelect2();

            $('#payment_type').change(function () {
                if ($(this).val() == '1') {
                    $('#modal-bank-info').show();
                } else {
                    $('#modal-bank-info').hide();
                }
            });
            $('#modal-pay-type').trigger('change');

            $('body').on('click', '.btn-pay', function () {
                var supplierId = $(this).data('id'); // Change to supplier ID
                var totalDue = $(this).closest('tr').find('.total-due').data('total-due'); // Get the total due from the data attribute

                $('#modal-order-due').val(totalDue);
                $('#supplier_id').val(supplierId);
                $('#modal-pay').modal('show');
            });



            $('#modal-btn-pay').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('supplier_payment.make_payment') }}",
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
