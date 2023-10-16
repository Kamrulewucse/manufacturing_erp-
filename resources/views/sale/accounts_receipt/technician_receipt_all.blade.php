@extends('layouts.app')
@section('title', 'New Setup Amount Receipt')

@section('style')
    <style>
        .table-bordered>thead>tr>th,
        .table-bordered>tbody>tr>th,
        .table-bordered>tfoot>tr>th,
        .table-bordered>thead>tr>td,
        .table-bordered>tbody>tr>td,
        .table-bordered>tfoot>tr>td {
            white-space: nowrap;
        }

        .modal-content {
            position: relative;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            /*width: 600px;*/
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, .2);
            border-radius: .3rem;
            box-shadow: 0 0.25rem 0.5rem rgb(0 0 0 / 50%);
            outline: 0;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Technician</th>
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

    <div class="modal fade" id="modal-receive">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Accept Service Payment From Technician</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modal-receive-info"
                         style="background-color: #ffaf6d; padding: 10px; border-radius: 3px;margin-bottom: 10px;">
                        <p>Product Info</p>
                    </div>

                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">

                        <input type="hidden" id="employee_id" name="employee_id">

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
                            <input class="form-control date-picker" type="text" autocomplete="off"  name="cheque_date" placeholder="Enter Cheque Date">
                        </div>

                        <div class="form-group bank-area" style="display: none">
                            <label> Cheque image </label>
                            <input class="form-control" name="cheque_image" type="file">
                        </div>

                        <div class="form-group">
                            <label>Receipt Amount</label>
                            <input class="form-control" name="amount" id="service-amount"
                                   placeholder ="Enter Receipt Amount">
                        </div>

                        <div class="form-group">
                            <label>Receipt Date</label>
                            <div class="input-group date">
                                <input type="text" class="form-control pull-right date-picker" id="date"
                                       name="date" value="{{ date('d-m-Y') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Receipt Note</label>
                            <input class="form-control" name="note"
                                   placeholder="Enter Receipt Note">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-receipt">Receipt</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        var due;

        $(function() {
            intSelect2();

            $("#payment_type").change(function (){
                var payType = $(this).val();

                if(payType != ''){
                    if(payType == 1){
                        $(".bank-area").show();
                    }else{
                        $(".bank-area").hide();
                    }
                }
            })

            $("#payment_type").trigger("change");

            var message = '{{ session('message') }}';
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('technician_accounts_receipt.datatable') }}',
                columns: [
                    {data: 'name', name: 'name'},
                    //{data: 'address', name: 'address'},
                    {data: 'total', name: 'total', orderable: false},
                    {data: 'paid', name: 'paid', orderable: false},
                    {data: 'due', name: 'due', orderable: false},
                    //{data: 'refund', name: 'refund', orderable: false},
                    {data: 'action', name: 'action', orderable: false},
                ],
                order: [
                    [0, "desc"]
                ],
            });

            $('body').on('click', '.btn-receive', function() {
                var employeeId = $(this).data('id');
                var employeeName = $(this).data('employee-name');

                $('#employee_id').val(employeeId);
                $('#modal-order').html('<option value="">Select Order</option>');
                $('#modal-order-info').hide();

                $('#modal-receive-info').html(
                    '<div class="content">' +
                    '<strong>Technician Name:</strong>' + ' ' + employeeName + '<br>' +
                    '</div>'
                );
                $('#modal-description').html('<strong>Description: </strong>' + 'New Setup');

                $.ajax({
                    method: "GET",
                    url: "{{ route('service_amount_receipt.get_orders') }}",
                    data: { employeeId: employeeId }
                }).done(function( response ) {
                    $.each(response, function( index, item ) {
                        if(item.amount_type == 2)
                        $('#modal-order').append('<option value="'+item.id+'">'+item.service.complain_job_id+'-(Product Sale)</option>');
                        if(item.amount_type == 3)
                        $('#modal-order').append('<option value="'+item.id+'">'+item.service.complain_job_id+'-(Service Charge)</option>');

                    });

                    $('#modal-receive').modal('show');
                });
            });

            $('#modal-order').change(function () {
                var orderId = $(this).val();
                $('#modal-order-info').hide();

                if (orderId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('service_order_details') }}",
                        data: { orderId: orderId }
                    }).done(function( response ) {
                        //due = parseFloat(response.due).toFixed(2);
                        $('#modal-order-info').html('<strong>Total: </strong>৳'+parseFloat(response.amount).toFixed(2)+' <strong>Paid: </strong>৳'+parseFloat(response.paid).toFixed(2)+' <strong>Due: </strong>৳'+parseFloat(response.due).toFixed(2));
                        $('#modal-order-info').show();
                    });
                }
            });

            $('#modal-btn-receipt').click(function() {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('service_receipt.make_payment') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-receive').modal('hide');
                            Swal.fire(
                                'Success!',
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

            $('body').on('click', '.btn-complete', function() {
                var serviceId = $(this).data('id');
                var employeeId = $(this).data('employee-id');
                var saleOrderId = $(this).data('sale-order-id');
                var productName = $(this).data('product-name');
                var employeeName = $(this).data('employee-name');
                var quantity = $(this).data('quantity');

                $('#service-Id').val(serviceId);
                $('#employee-Id').val(employeeId);
                $('#sale-order-id').val(saleOrderId);
                $('#modal-complete-info').html(
                    '<div class="content">' +
                    '<h4>Job/Setup Complete Info</h4>' +
                    '<strong>Product Name:</strong>' + ' ' + productName + '<br>' +
                    '<strong>Product Quantity:</strong>' + ' ' + quantity + '<br>' +
                    '<strong>Technician Name:</strong>' + ' ' + employeeName + '<br>' +
                    '</div>'
                );
                $('#modal-complete-description').html('<strong>Description: </strong>' + 'New Setup');
                $('#modal-complete').modal('show');
            });

            $('#modal-btn-complete').click(function() {
                var formData = new FormData($('#modal-complete-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('technician_setup.complete') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        if (response.success) {
                            $('#modal-complete').modal('hide');
                            Swal.fire(
                                'Success!',
                                response.message,
                                'success'
                            ).then((result) => {
                                //location.reload();
                                window.location.href = response.redirect_url;
                            });
                        }
                        else {
                            console.log(response);
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
    </script>
@endsection
