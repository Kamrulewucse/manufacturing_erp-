@extends('layouts.app')
@section('title','Pre Booking')
@section('style')
    <style>
        .product_area > .select2 {
            width: 100% !important;
            max-width: 480px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-default">
                <div class="card-header">
                    <h3 class="card-title">Booking Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{route('booking.add')}}" class="form-horizontal" method="post">
                    @csrf
                    <div class="row" id="all-customer-area">
                        <div class="col-md-12">
                            <div class="card">
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row" id="customer-type-area">
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                                                <label>Customer<span class="text-danger">*</span></label>

                                                <select class="form-control customer select2" style="width: 100%;" name="customer">
                                                    <option value="">Select Customer</option>

                                                    @foreach($customers as $customer)
                                                        <option value="{{ $customer->id }}" {{ old('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name.' - '.$customer->address }}</option>
                                                    @endforeach
                                                </select>

                                                @error('customer')
                                                <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('technician') ? 'has-error' :'' }}">
                                                <label>Assign Technician <span class="text-danger">*</span></label>
                                                <select class="form-control technician select2" style="width: 100%;" name="technician[]" multiple>
                                                        @foreach($technicians as $technician)
                                                            <option value="{{ $technician->id }}" {{in_array($technician->id, old("technician") ?: []) ? "selected": ""}} >{{ $technician->name }}</option>
                                                        @endforeach
                                                </select>
                                                @error('technician')
                                                <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                                <label for="date">Booking Date <span class="text-danger">*</span></label>
                                                <input type="text" id="date" autocomplete="off"  value="{{ old('date',date('d-m-Y')) }}" name="date" class="form-control date-picker" placeholder="manufacture date">
                                                @error('date')
                                                <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('delivery_date') ? 'has-error' :'' }}">
                                                <label for="date">Delivery Date <span class="text-danger">*</span></label>
                                                <input type="text" id="date" autocomplete="off"  value="{{ old('delivery_date',date('d-m-Y')) }}" name="delivery_date" class="form-control date-picker" placeholder="delivery date">
                                                @error('delivery_date')
                                                <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('warning_date') ? 'has-error' :'' }}">
                                                <label for="date">Warning Date</label>
                                                <input type="text" id="date" autocomplete="off"  value="{{ old('warning_date',date('d-m-Y')) }}" name="warning_date" class="form-control date-picker" placeholder="warning date">
                                                @error('warning_date')
                                                <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive-sm">
                                    <table class="table table-bordered table-custom-form">
                                        <thead>
                                        <tr>
                                            <th width="48%">Product <span class="text-danger">*</span></th>
                                            <th class="text-center">Quantity <span class="text-danger">*</span></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody id="product-container">
                                        @if (old('product') != null && sizeof(old('product')) > 0)
                                            @foreach(old('product') as $item)
                                                <tr class="product-item">
                                                    <td>
                                                        <div class="form-group product_area {{ $errors->has('product.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 product" name="product[]">
                                                                <option value="">Select Product</option>
                                                                @if (old('product.'.$loop->index) != '')
                                                                    <option value="{{ old('product.'.$loop->index) }}" selected>{{ old('product_name.'.$loop->index) }}</option>
                                                                @endif
                                                            </select>
                                                            <input type="hidden" name="product_name[]" class="product_name" value="{{ old('product_name.'.$loop->index) }}">

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('quantity.'.$loop->index) }}" name="quantity[]" class="form-control quantity text-center">
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="product-item">
                                                <td>
                                                    <div class="form-group product_area">
                                                        <select class="form-control select2 product" style="width: 100%;" name="product[]">
                                                            <option value="">Select Product</option>
                                                        </select>
                                                        <input type="hidden" name="product_name[]" class="product_name">
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="quantity[]" class="form-control quantity text-center">
                                                    </div>
                                                </td>

                                                <td class="text-center">
                                                    <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
                                                </td>

                                            </tr>
                                        @endif
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="" class="text-right">
                                                Total
                                            </th>
                                            <th class="text-center" id="total-quantity">0</th>
                                            <th ></th>
                                        </tr>
                                        <tr>
                                            <th colspan="5" class="text-left">
                                                <a role="button" class="btn btn-primary btn-sm" id="btn-add-product"><i class="fa fa-plus"></i></a>
                                            </th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Payment Type </label>
                                            <select class="form-control select2" id="payment_type" name="payment_type">
                                                <option value="">Select Payment Type</option>
                                                <option {{ old('payment_type') == 1 ? 'selected' : '' }} value="1">Cheque</option>
                                                <option {{ old('payment_type') == 2 ? 'selected' : '' }} value="2">Cash</option>

                                            </select>
                                            @error('payment_type')
                                            <span class="help-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group {{ $errors->has('account') ? 'has-error' :'' }}">
                                            <label>Bank/Cash Account <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="account" name="account">
                                                <option value="">Select Bank/Cash Account</option>
                                                @if (old('account') != '')
                                                    <option value="{{ old('account') }}" selected>{{ old('account_name') }}</option>
                                                @endif
                                            </select>
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
                                                <th colspan="4" class="text-right">Advance Amount</th>
                                                <td>
                                                    <div class="form-group {{ $errors->has('advance_amount') ? 'has-error' :'' }}">
                                                        <input type="text" class="form-control text-center" name="advance_amount" id="advance_amount" value="{{ empty(old('advance_amount')) ? ($errors->has('advance_amount') ? '' : '0') : old('advance_amount') }}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-right"> Note </th>
                                                <td>
                                                    <div class="form-group {{ $errors->has('note') ? 'has-error' :'' }}">
                                                        <input type="text" class="form-control" name="note" id="note" value="{{ old('note') }}">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button id="btn-save" type="submit" class="btn btn-primary">Save</button>

                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
    <template id="product-template">
        <tr class="product-item">
            <td>
                <div class="form-group product_area">
                    <select class="form-control select2 product" style="width: 100%;" name="product[]">
                        <option value="">Select Product</option>
                    </select>
                    <input type="hidden" name="product_name[]" class="product_name">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" name="quantity[]" class="form-control quantity text-center">
                </div>
            </td>
            <td class="text-center">
                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
            </td>

        </tr>
    </template>
@endsection
@section('script')
    <script>
        $(function (){
            intSelect2();
            formSubmitConfirm('btn-save');


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


            $('#btn-add-product').click(function () {
                var html = $('#product-template').html();
                var item = $(html);

                $('#product-container').append(item);

                intSelect2();

                if ($('.product-item').length >= 1 ) {
                    $('.btn-remove').show();
                }

                calculate();
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
                calculate();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }

            $('body').on('keyup', '.quantity', function () {

                calculate();
            });
            calculate();
        });
        function calculate() {
            var totalQuantity = 0;
            var totalAmount = 0;

            $('.product-item').each(function(i, obj) {

                var quantity = $('.quantity:eq('+i+')').val();

                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                totalQuantity += parseFloat(quantity);
            });

            $('#total-quantity').html(jsNumberFormat(totalQuantity));

        }

        function intSelect2(){
            $('.select2').select2();

            $('.product').select2({
                ajax: {
                    url: "{{ route('finishProduct.json') }}",
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
            })

            $('.product').on('select2:select', function (e) {
                let data = e.params.data;
                let index = $(".product").index(this);
                $('.product_name:eq('+index+')').val(data.text);
            });
            $('#account').select2({
                ajax: {
                    url: "{{ route('sale_account_head_code.json') }}",
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
                //$('#account_name:eq('+index+')').val(data.text);
                $('#account_name').val(data.text);
            });


        }

    </script>
@endsection
