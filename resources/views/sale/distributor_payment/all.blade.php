@extends('layouts.app')
@section('title')
    Distributor Payment
@endsection

@section('content')
    @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
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

    <div class="modal fade" id="modal-pay">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Distributor Payment Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                        <div class="form-group">
                            <label for="financial_year">Select Financial Year <span
                                    class="text-danger">*</span></label>
                            <select class="form-control select2" style="width: 100%" name="financial_year">
                                <option value="">Select Year</option>
                                @for($i=2022; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ old('financial_year') == $i ? 'selected' : '' }}>{{ $i }}-{{ $i+1 }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="modal-name" disabled>
                            <input type="hidden" id="distributor_id" name="distributor_id">
                        </div>

                        <div class="form-group">
                            <label>Order</label>
                            <select class="form-control select2" id="modal-order" name="order">
                                <option value="">Select Order</option>
                            </select>
                        </div>

                        <div id="modal-order-info" style="background-color: lightgrey; padding: 10px; border-radius: 3px;"></div>

                        <div class="form-group">
                            <label>Payment Type</label>
                            <select class="form-control" id="payment_type" name="payment_type">
                                <option value="">Select Payment Type</option>
                                <option value="1">Cheque</option>
                                <option value="2">Cash</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label> Account </label>
                            <select class="form-control select2" style="width: 100%" id="account" name="account">
                                <option value=""> Select Cash/Bank Account </option>
                            </select>
                        </div>

                        <div class="form-group bank-area" style="display: none">
                            <label>Cheque No.</label>
                            <input class="form-control" type="text" name="cheque_no" placeholder="Cheque No.">
                        </div>

                        <div class="form-group bank-area" style="display: none">
                            <label>Cheque date</label>
                            <input class="form-control" type="text" autocomplete="off" id="cheque_date" name="cheque_date" placeholder="Enter Cheque Date">
                        </div>

                        <div class="form-group bank-area" style="display: none">
                            <label> Cheque image </label>
                            <input class="form-control" name="cheque_image" type="file">
                        </div>

                        <div class="form-group bank-area" style="display: none">
                            <label for="issuing_bank_name">Issuing Bank Name</label>
                            <input type="text"  id="issuing_bank_name" name="issuing_bank_name" class="form-control" placeholder="Enter Issuing Bank Name">
                        </div>
                        <div class="form-group bank-area" style="display: none">
                            <label for="issuing_branch_name">Issuing Branch Name </label>
                            <input type="text" value="" id="issuing_branch_name" name="issuing_branch_name" class="form-control" placeholder="Enter Issuing Bank Branch Name">
                        </div>

                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control" name="amount" placeholder="Enter Amount">
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <input type="text" class="form-control pull-right date-picker" id="date" name="date" value="{{ date('Y-m-d') }}" autocomplete="off">
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
    <!-- /.modal -->
@endsection

@section('script')
    <script>
        var due;

        $(function () {
            intSelect2();

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('distributor_payment.datatable') }}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'address', name: 'address'},
                    {data: 'total', name: 'total', orderable: false},
                    {data: 'paid', name: 'paid', orderable: false},
                    {data: 'due', name: 'due', orderable: false},
                    {data: 'action', name: 'action', orderable: false},
                ],
            });

            //Date picker
            $('#date, #next-payment-date, #date-refund').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('body').on('click', '.btn-pay', function () {
                var distributorId = $(this).data('id');
                var distributorName = $(this).data('name');
                $('#modal-order').html('<option value="">Select Order</option>');
                $('#modal-order-info').hide();
                $('#modal-name').val(distributorName);
                $('#distributor_id').val(distributorId);

                $.ajax({
                    method: "GET",
                    url: "{{ route('distributor_payment.get_orders') }}",
                    data: { distributorId: distributorId }
                }).done(function( response ) {
                    $.each(response, function( index, item ) {
                        $('#modal-order').append('<option value="'+item.id+'">'+item.order_no+'</option>');
                    });

                    checkNextPayment();
                    $('#modal-pay').modal('show');
                });
            });

            $('#modal-btn-pay').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('distributor_payment.make_payment') }}",
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

            $('#modal-pay-type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            });

            $('#modal-pay-type').trigger('change');

            $('#modal-order').change(function () {
                var orderId = $(this).val();
                $('#modal-order-info').hide();

                if (orderId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_order_details') }}",
                        data: { orderId: orderId }
                    }).done(function( response ) {
                        due = parseFloat(response.due).toFixed(2);
                        $('#modal-order-info').html('<strong>Total: </strong>৳'+parseFloat(response.total).toFixed(2)+' <strong>Paid: </strong>৳'+parseFloat(response.paid).toFixed(2)+' <strong>Due: </strong>৳'+parseFloat(response.due).toFixed(2));
                        $('#modal-order-info').show();
                    });
                }
            });
        });

        function intSelect2(){
            $('.select2').select2()
            $('#account').select2({
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
            $('#account').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $("#account").index(this);
                $('#account_name:eq('+index+')').val(data.text);
            });

        }

        function checkNextPayment() {
            var paid = $('#amount').val();

            if (paid == '' || paid < 0 || !$.isNumeric(paid))
                paid = 0;

            if (parseFloat(paid) >= due)
                $('#fg-next-payment-date').hide();
            else
                $('#fg-next-payment-date').show();
        }
    </script>
@endsection
