@extends('layouts.app')
@section('title','Sales')

@section('style')
    <style>
        .form-control {
            width: 100%;
        }
        input.form-control.p_quantity {
            width: 70px !important;
        }
        input.form-control.warranty {
            width: 70px !important;
        }
        input.form-control.unit_price {
            width: 70px !important;
        }
        input.form-control.last_sell_price {
            width: 70px !important;
        }
        select.form-control.zone {
            width: 138px !important;
        }
        select.form-control.product {
            width: 138px !important;
        }
        input.form-control.selling_price{
            width: 90px;
        }
        input.form-control.stock{
            width: 130px;
        }
        input.form-control.p_quantity{
            width: 130px;
        }
        input.form-control.single_customer_quantity{
            width: 130px;
        }
        th {
            text-align: center;
        }
        select.form-control {
            min-width: 130px;
        }
        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
            vertical-align: middle;
        }
        td .form-group {
            margin-bottom: 0;
        }
    </style>
@endsection


@section('content')
    <form method="POST" enctype="multipart/form-data"  action="{{ route('sales_order.create') }}">
        @csrf

        <div class="row" id="all-technician-area">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Order Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('sale_type') ? 'has-error' :'' }}">
                                    <label>Sale Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="sale_type" name="sale_type">
                                        <option {{ old('sale_type') == 1 ? 'selected' : '' }} value="1">Normal Sale</option>
                                        <option {{ old('sale_type') == 2 ? 'selected' : '' }} value="2">Booking Sale</option>
                                    </select>
                                    @error('sale_type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3" id="customer-hide">
                                <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                                    <label>Customer</label>

                                    <select class="form-control customer select2" style="width: 100%;" name="customer">
                                        <option value="">Select Customer</option>

                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name.' - '.$customer->mobile.' - '.$customer->address }}</option>
                                        @endforeach
                                    </select>

                                    @error('customer')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3" id="sale_type_area">
                                <div class="form-group {{ $errors->has('booking') ? 'has-error' :'' }}">
                                    <label>Booking Customer</label>

                                    <select class="form-control booking select2" style="width: 100%;" name="booking">
                                        <option value="">Select Customer</option>
                                        @foreach($bookings as $booking)
                                            @if($booking->quantity >($booking->delivery_quantity+$booking->cancel_quantity))
                                            <option value="{{ $booking->id }}" {{ old('booking') == $booking->id ? 'selected' : '' }}>{{ $booking->customer->name }} ({{$booking->order_no}}) {{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y')}}</option>
                                            @endif
                                        @endforeach
                                    </select>

                                    @error('booking')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                    <label>Date</label>

                                    <div class="input-group date">
                                        <input type="text" class="form-control pull-right date-picker" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : date('d-m-Y')) : old('date') }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('supporting_document') ? 'has-error' :'' }}">
                                    <label for="supporting_document">Supporting Document</label>

                                    <div class="input-group supporting_document">
                                        <input type="file" class="form-control " name="supporting_document">
                                    </div>
                                    <!-- /.input group -->
                                    @error('supporting_document')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="booking-info-area">
                            <div class="col-md-3">
                                <label>Booking Quantity</label>
                                <div class="form-group">
                                  <div class="booking_quantity"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>Booking Advance</label>
                                <div class="form-group">
                                  <div class="booking_advance"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="background-color: white">
                <div class="table-responsive-sm">
                    <table class="table table-bordered  table-custom-form">
                        <tbody id="booking-detail-container"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Products</h3>
                    </div>
                    <div class="card-body">
                        <div class="row" id="multiple-customer-sale-type">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-custom-form">
                                        <thead>
                                        <tr>
                                            <th class="text-center" >Product <span class="text-danger">*</span></th>
                                            <th class="text-center" >Unit </th>
                                            <th class="text-center" >Quantity</th>
                                            <th class="text-center" >Warranty</th>
                                            <th class="text-center" >Unit Price</th>
                                            <th class="text-center" >Last Sell Price</th>
                                            <th class="text-center" >Selling Price <span class="text-danger">*</span></th>
                                            <th class="text-center" >Total Cost</th>
                                            <th class="text-center" ></th>
                                        </tr>
                                        </thead>

                                        <tbody id="product-container">
                                        @if (old('product') != null && sizeof(old('product')) > 0)
                                            @foreach(old('product') as $item)
                                                <tr class="product-item">
                                                    <td>
                                                        <div class="form-group product_area {{ $errors->has('product.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 product" style="width: 100%;" name="product[]" >
                                                                <option value="">Select Product</option>
                                                                @if (old('product.'.$loop->index) != '')
                                                                    <option value="{{ old('product.'.$loop->index) }}" selected>{{ old('product_name.'.$loop->index) }}</option>
                                                                @endif
                                                            </select>
                                                            <input type="hidden" name="product_name[]" class="product_name" value="{{ old('product_name.'.$loop->index) }}">

                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="unit_name"></div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('p_quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" step="any" class="form-control text-center p_quantity" name="p_quantity[]" value="{{ old('p_quantity.'.$loop->index) }}" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('warranty.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" step="any" class="form-control warranty" name="warranty[]" value="{{ old('warranty.'.$loop->index) }}">
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" step="any" class="form-control unit_price" name="unit_price[]" value="{{ old('unit_price.'.$loop->index) }}" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('last_sell_price.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" step="any" class="form-control last_sell_price" name="last_sell_price[]" value="{{ old('last_sell_price.'.$loop->index) }}" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('selling_price.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" step="any" class="form-control selling_price" name="selling_price[]" value="{{ old('selling_price.'.$loop->index) }}">
                                                        </div>
                                                    </td>
                                                    <td  class="total-cost text-right">0.00</td>
                                                    <td  class="text-center">
                                                        <a role="button" class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="product-item">
                                                <td>
                                                    <div class="form-group product_area">
                                                        <select class="form-control select2 product" style="width: 100%;" name="product[]" >
                                                            <option value="">Select Product</option>
                                                        </select>
                                                        <input type="hidden" name="product_name[]" class="product_name">
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="unit_name"></div>
                                                </td>

                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" step="any" class="form-control p_quantity text-center" name="p_quantity[]" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" step="any" class="form-control warranty" name="warranty[]">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" step="any" class="form-control unit_price" name="unit_price[]" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" step="any" class="form-control last_sell_price" name="last_sell_price[]" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" step="any" class="form-control selling_price" name="selling_price[]">
                                                    </div>
                                                </td>
                                                <td  class="total-cost text-right"> 0.00</td>
                                                <td class="text-center">
                                                    <a role="button" class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td>
                                                <a role="button" class="btn btn-primary btn-sm" id="btn-add-product"><i class="fa fa-plus"></i></a>
                                            </td>
                                            <th id="total-quantity" colspan="2" class="text-right">0.00</th>
                                            <th></th>
                                            <th colspan="3" class="text-right">Total Amount</th>
                                            <th id="total-amount" class="text-right">0.00</th>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header with-border">
                                        <h3 class="card-title">Payment</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Payment Type </label>
                                                    <select class="form-control select2" id="payment_type" name="payment_type">
                                                        <option value="">Select Payment Type</option>
                                                        <option {{ old('payment_type') == 1 ? 'selected' : '' }} value="1">Bank</option>
                                                        <option {{ old('payment_type') == 2 ? 'selected' : '' }} value="2">Cash</option>

                                                    </select>
                                                    @error('payment_type')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group bank_account_area {{ $errors->has('account') ? 'has-error' :'' }}">
                                                    <label for="account">Payment Head <span class="text-danger">*</span></label>
                                                    <select style="max-width: 300px !important;" class="form-control select2" id="account" name="account">
                                                        <option value="">Select Payment Cash/Bank Head</option>
                                                        @if (old('account') != '')
                                                            <option value="{{ old('account') }}" selected>{{ old('account_name') }}</option>
                                                        @endif
                                                    </select>
                                                    <input type="hidden" name="account_name" class="account_name" id="account_name" value="{{ old('account_name') }}">

                                                    @error('account')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group  bank-area {{ $errors->has('cheque_date') ? 'has-error' :'' }}" style="display: none">
                                                    <label>Cheque Date <span class="text-danger">*</span></label>
                                                    <div class="input-group date">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                        <input type="text" class="form-control pull-right date-picker"
                                                               id="cheque_date" name="cheque_date" value="{{ old('cheque_date',date('Y-m-d'))  }}" autocomplete="off">
                                                    </div>
                                                    @error('cheque_date')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group  bank-area {{ $errors->has('cheque_no') ? 'has-error' :'' }}" style="display: none">
                                                    <label>Cheque No. <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control"
                                                           id="cheque_no" name="cheque_no" value="{{ old('cheque_no') }}">

                                                    @error('cheque_no')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group bank-area" style="display: none">
                                                    <label for="issuing_bank_name">Issuing Bank Name</label>
                                                    <input type="text" value="" id="issuing_bank_name" name="issuing_bank_name" class="form-control" placeholder="Enter Issuing Bank Name">
                                                </div>
                                                <div class="form-group bank-area" style="display: none">
                                                    <label for="issuing_branch_name">Issuing Branch Name </label>
                                                    <input type="text" value="" id="issuing_branch_name" name="issuing_branch_name" class="form-control" placeholder="Enter Issuing Bank Branch Name">
                                                </div>

                                            </div>

                                            <div class="col-6 col-md-6">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th colspan="4" class="text-right">Sub Total</th>
                                                        <th id="product-sub-total">৳0.00</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" class="text-right">Discount (Tk/%)</th>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('discount') ? 'has-error' :'' }}">
                                                                <input type="text" class="form-control" id="discount" value="{{ empty(old('discount')) ? ($errors->has('discount') ? '' : '0') : old('discount') }}">
                                                                <span>৳<span id="discount_total">0.00</span></span>
                                                                <input type="hidden" class="discount_total" name="discount" value="{{ empty(old('discount')) ? ($errors->has('discount') ? '' : '0') : old('discount') }}">
                                                                <input type="hidden" class="discount_percentage" name="discount_percentage" value="{{ empty(old('discount_percentage')) ? ($errors->has('discount_percentage') ? '' : '0') : old('discount_percentage') }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr id="advance_booking_area">
                                                        <th colspan="4" class="text-right">From Advance Payment</th>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('advance_deduct') ? 'has-error' :'' }}">
                                                                <input type="text" class="form-control text-center advance_deduct" name="advance_deduct" id="advance_deduct" value="{{ empty(old('advance_deduct')) ? ($errors->has('advance_deduct') ? '' : '0') : old('advance_deduct') }}" readonly>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr class="advance_normal_area">
                                                        <th colspan="4" class="text-right">Advance Amount Total</th>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('advance_normal_total') ? 'has-error' :'' }}">
                                                                <input type="text" class="form-control text-center advance_normal_total" name="advance_normal_total" id="advance_normal_total" value="{{ empty(old('advance_normal_total')) ? ($errors->has('advance_normal_total') ? '' : '0') : old('advance_normal_total') }}" readonly>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr class="advance_normal_area">
                                                        <th colspan="4" class="text-right">From Advance Payment</th>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('advance_deduct_normal') ? 'has-error' :'' }}">
                                                                <input type="text" class="form-control text-center advance_deduct_normal" name="advance_deduct_normal" id="advance_deduct_normal" value="{{ empty(old('advance_deduct_normal')) ? ($errors->has('advance_deduct_normal') ? '' : '0') : old('advance_deduct_normal') }}" readonly>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" class="text-right">Total</th>
                                                        <th id="final-amount">৳0.00</th>
                                                    </tr>

                                                    <tr>
                                                        <th colspan="4" class="text-right">Paid</th>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('paid') ? 'has-error' :'' }}">
                                                                <input type="text" class="form-control text-center" name="paid" id="paid" value="{{ empty(old('paid')) ? ($errors->has('paid') ? '' : '0') : old('paid') }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" class="text-right">Due</th>
                                                        <th id="due">৳0.00</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" class="text-right"> Note </th>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('note') ? 'has-error' :'' }}">
                                                                <input type="text" class="form-control" name="note" id="note" value="{{ old('note') }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" class="text-right">Received By </th>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('received_by') ? 'has-error' :'' }}">
                                                                <input type="text" class="form-control" name="received_by" id="received_by" value="{{ old('received_by') }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <input type="hidden" name="total" id="total">
                        <input type="hidden" name="due_total" id="due_total">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <template id="template-product">
        <tr class="product-item">
            <td>
                <div class="form-group product_area">
                    <select class="form-control select2 product" style="width: 100%;" name="product[]" >
                        <option value="">Select Product</option>
                    </select>
                    <input type="hidden" name="product_name[]" class="product_name">
                </div>
            </td>
            <td class="text-center">
                <div class="unit_name"></div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control p_quantity text-center" name="p_quantity[]" readonly>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control warranty" name="warranty[]">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control unit_price" name="unit_price[]" readonly>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control last_sell_price" name="last_sell_price[]" readonly>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control selling_price" name="selling_price[]">
                </div>
            </td>
            <td  class="total-cost text-right"> 0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
    </template>
@endsection

@section('script')

    <script>
        var oldPaid ={{ old('paid') ? old('paid') : 0 }};
        var oldAdvanceDeduct ={{ old('advance_deduct') ? old('advance_deduct') : 0 }};
        $(function () {
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

            $(".customer").change(function (){
                $('.product-item').remove();

                var customerID = $(this).val();
                if (customerID != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_customer_details') }}",
                        data: {customerID:customerID}
                    }).done(function(response) {
                        console.log(response);
                        $(".advance_normal_total").val(response.advance_amount);
                    });
                    // calculate();
                }
            })
            $(".customer").trigger("change");

            //Initialize Select2 Elements
            //$('.pre_filter_category').select2()

            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            var message = '{{ session('message') }}';
            var message = '{{ session('message') }}';

            if (!window.performance || window.performance.navigation.type != window.performance.navigation.TYPE_BACK_FORWARD) {
                if (message != '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: message,
                    });
                }
            }


            $('#sale_type').change(function (){
                var sale_type = $(this).val();
                if (sale_type == '1'){
                    $("#sale_type_area").hide();
                    $("#customer-hide").show();
                    $("#booking-info-area").hide();
                    $("#all-customer-area").show();
                    $("#booking-detail-container").hide();
                    //added by hasan
                    $(".advance_normal_area").show();
                    $("#advance_booking_area").hide();
                }else{
                    $("#sale_type_area").show();
                    $("#customer-hide").hide();
                    $("#all-customer-area").hide();
                    $("#booking-detail-container").show();
                    //added by hasan
                    $(".advance_normal_area").hide();
                    $("#advance_booking_area").show();
                }
            });

            $('#sale_type').trigger("change");


            $('body').on('change', '.product', function () {
                var productId = $(this).val();
                var customerId = $('.customer').val();
                var itemProduct = $(this);
                var itemProduct = itemProduct.closest('tr');


                var existingProduct = itemProduct.siblings().find('.product').filter(function () {
                    return $(this).val() === productId;
                });

                if (existingProduct.length === 0 && productId !== '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_sale_details') }}",
                        data: {
                            productId: productId,
                            customerId: customerId
                        }
                    }).done(function (response) {
                        if (response.inventory) {
                            itemProduct.find('.p_quantity').val(response.inventory.quantity);
                            itemProduct.find('.unit_price').val(response.inventory.unit_price);
                            itemProduct.find('.selling_price').val(response.inventory.selling_price);
                        } else {
                            itemProduct.find('.p_quantity').text('Stock out').addClass('text-danger');
                            itemProduct.find('.unit_price').val(' ');
                            itemProduct.find('.selling_price').val(' ');
                        }
                        if (response.product) {
                            itemProduct.find('.unit_name').text(response.product.unit.name);
                            itemProduct.find('.warranty').val(response.product.warranty);
                        } else {
                            itemProduct.find('.unit_name').text(' ');
                        }
                        if (response.lastSellPrice) {
                            itemProduct.find('.last_sell_price').val(response.lastSellPrice.selling_price);
                        }
                        calculate();
                    });
                }else if (existingProduct.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Product Already Added',
                        text: 'This product is already added to the list.',
                    });
                }
            });
            $('.product').each(function () {
                if ($(this).val() !== '') {
                    $(this).trigger('change');
                }
            });


            $("#booking-info-area").hide();

            $('body').on('change', '.booking', function () {
                $("#booking-detail-container").html(' ');
                var bookingId = $(this).val();
                if (bookingId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_booking_details') }}",
                        data: {bookingId:bookingId}
                    }).done(function(response) {
                        var bookingAdvance= response.advance;
                        $('#advance_deduct').val(bookingAdvance);
                        $("#booking-detail-container").html(response.html);

                    });
                    calculate();
                }
            });

            $('.booking').trigger('change');




            $('#btn-add-product').click(function () {
                // Newly added
                var sale_type = $('#sale_type').val();
                sale_type = parseInt(sale_type) || 0;

                if (sale_type == 1) { // Normal Sale
                    var customerId = $('.customer').val();
                    if (!customerId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Please Customer Select First',
                        });
                        return;
                    }
                } else {
                    // Booking Sale
                    var booking = $('.booking').val();
                    if (!booking) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Please Customer Select First',
                        });
                        return;
                    }
                }

                var html = $('#template-product').html();
                var item = $(html);

                $('#product-container').append(item);

                if ($('.product-item').length >= 1 ) {
                    $('.btn-remove').show();
                }
                intSelect2();
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
            });

            $('body').on('keyup',' .selling_price,.p_quantity,#advance_deduct,#paid,#discount', function () {
                calculate();
            });

            $('body').on('change',' .selling_price,.p_quantity,#advance_deduct,#paid,#discount', function () {
                calculate();
            });


            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }
            calculate();
            //payment
            $('#modal-pay-type').change(function() {
                if ($(this).val() == '1') {
                    $('#modal-bank-info').hide();
                    $('#modal-mobile-bank-info').hide();
                }
                if($(this).val() == '3'){
                    $('#modal-bank-info').hide();
                    $('#modal-mobile-bank-info').show();
                }
                if($(this).val() == '2') {
                    $('#modal-mobile-bank-info').hide();
                    $('#modal-bank-info').show();
                }
            });

            $('#modal-pay-type').trigger('change');
            //-------end payment type -----

        });


        function calculate() {
            var productSubTotal = 0;
            var productSubQuantity = 0;
            var paid = $('#paid').val() || 0;
            var advance_deduct = $('#advance_deduct').val() || 0;
            //handle discount
            let discount = $('#discount').val() || "0";
            let discount_amount = 0;
            let advance_normal_total = $('.advance_normal_total').val();
            // alert(advance_normal_total);

            $('.product-item').each(function(i, obj) {
                var selling_price = $('.selling_price:eq('+i+')').val();
                var p_quantity = $('.p_quantity:eq('+i+')').val();

                if (selling_price === '' || selling_price < 0 || !$.isNumeric(selling_price))
                    selling_price = 0;

                if (p_quantity === '' || p_quantity < 0 || !$.isNumeric(p_quantity))
                    p_quantity = 0;


                $('.total-cost:eq('+i+')').html('' + (1 * selling_price).toFixed(2) );
                productSubTotal += (1 * selling_price);
                productSubQuantity += parseInt(p_quantity);
            });

            $('#total-amount').html('৳' + productSubTotal.toFixed(2));
            $('#product-sub-total').html('৳' + productSubTotal.toFixed(2));
            $('#total-quantity').html('' + productSubQuantity .toFixed(2));


            // var productTotalDiscount = parseFloat(discount);
            // $('#discount_total').html('৳' + productTotalDiscount.toFixed(2));

            if(discount.includes('%')){
                let discount_percent = discount.split('%')[0];
                discount_amount = (productSubTotal * discount_percent)/100;
                $('.discount_percentage').val(discount_percent);
            }else{
                discount_amount = discount;
                $('.discount_percentage').val(0);
            }

            var total = parseFloat(productSubTotal) - parseFloat(discount_amount) - parseFloat(advance_deduct);
            //added by Hasan
            if(advance_normal_total>0){
                if(advance_normal_total > total){
                    $('.advance_deduct_normal').val(total);
                    total = 0;
                }else{
                    let totalAmount = total-advance_normal_total;
                    $('.advance_deduct_normal').val(advance_normal_total);
                    total=totalAmount;
                }
            }
            //End added by Hasan
            $('#discount_total').html(parseFloat(discount_amount).toFixed(2));
            $('#final-amount').html('৳' + total.toFixed(2));
            $('#total').val(total);
            $('.discount_total').val(discount_amount);

            if(parseFloat(productSubTotal) < parseFloat(advance_deduct))
                var due = 0;
            else

            var due = parseFloat(total)-parseFloat(paid);

            $('#due').html('৳' + due.toFixed(2));

        }



        function initProduct() {
            $('.select2').select2();
            $('.category').select2();
            $('.sub_category').select2();
            $('.product').select2();
        }
        function intSelect2(){
            $('.select2').select2()
            $('.product').select2({
                ajax: {
                    url: "{{ route('sale_product.json') }}",
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
            $('.product').on('select2:select', function (e) {
                let data = e.params.data;
                let index = $(".product").index(this);
                $('.product_name:eq('+index+')').val(data.text);
            });
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
                $('#account_name').val(data.text);
            });

        };
    </script>

@endsection
