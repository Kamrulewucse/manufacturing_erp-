@extends('layouts.app')
@section('title','Purchase Order')
@section('style')
    <style>
        .btn-info {
            color: #fff;
            background-color: #e34f0d;
            border-color: #e34f0d;
            card-shadow: none;
        }
        .btn-info:hover {
            color: #fff;
            background-color: #e34f0d;
            border-color: #e34f0d;
        }
        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
            white-space: nowrap;
        }
        input.form-control.quantity{
            width: 120px;
        }
        input.form-control.unit_price{
            width: 120px;
        }
        .input-group-addon i{
            padding-top:10px;
            padding-right: 10px;
            border: 1px solid #cecccc;
            padding-bottom: 10px;
            padding-left: 10px;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            text-align: center;
        }
        .table-bordered>tfoot>tr>td {
            white-space: nowrap;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Order Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('purchase_order.create') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="row pb-3">
                            <div class="col-md-3">
                                <input type="hidden" name="categor_type" value="2">
                                <div class="form-group {{ $errors->has('supplier') ? 'has-error' :'' }}">
                                    <label>Supplier</label>
                                    <select class="form-control select2 supplier" style="width: 100%;" id="supplier" name="supplier" data-placeholder="Select Supplier">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
{{--                            <div class="col-md-3">--}}
{{--                                <div class="form-group {{ $errors->has('financial_year') ? 'has-error' :'' }}">--}}
{{--                                    <label for="financial_year">Select Financial Year</label>--}}
{{--                                    <select class="form-control select2" name="financial_year" id="financial_year" required>--}}
{{--                                        <option value="">Select Year</option>--}}
{{--                                        @for($i=2022; $i <= date('Y'); $i++)--}}
{{--                                            <option value="{{ $i }}" {{ old('financial_year') == $i ? 'selected' : '' }}>{{ $i }}-{{ $i+1 }}</option>--}}
{{--                                        @endfor--}}
{{--                                    </select>--}}
{{--                                    @error('financial_year')--}}
{{--                                    <span class="help-block">{{ $message }}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                    <label>Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right date-picker" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : date('d-m-Y')) : old('date') }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('notes') ? 'has-error' :'' }}">
                                    <label for="notes">Payment Details (Narration)</label>

                                    <div class="input-group notes">
                                        <input type="text" class="form-control " name="notes" value="{{ old('notes') }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                    @error('notes')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('supporting_document') ? 'has-error' :'' }}">
                                    <label for="supporting_document">Supporting Document</label>

                                    <div class="input-group supporting_document">
                                        <input type="file" class="form-control " name="supporting_document" value="{{ empty(old('supporting_document')) }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                    @error('supporting_document')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="white-space: nowrap" width="30%">Product</th>
                                    <th style="white-space: nowrap" width="15%">Unit</th>
                                    <th style="white-space: nowrap" width="20%">Quantity</th>
                                    <th style="white-space: nowrap" width="20%">Unit Price</th>
                                    <th style="white-space: nowrap" width="15%">Total Cost</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody id="product-container">
                                @if (old('product') != null && sizeof(old('product')) > 0)
                                    @foreach(old('product') as $item)
                                        <tr class="product-item">
                                            <td >
                                                <div class="form-group {{ $errors->has('product.'.$loop->index) ? 'has-error' :'' }}">
                                                    <select class="form-control product" style="width: 100%;" id="product" name="product[]" required>
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" {{ old('product.'.$loop->parent->index) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group {{ $errors->has('unit.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" step="any" class="form-control unit" name="unit[]" value="{{ old('unit.'.$loop->index) }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{ old('quantity.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control unit_price" name="unit_price[]" value="{{ old('unit_price.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td class="total-cost">৳0.00</td>
                                            <td class="text-center">
                                                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="product-item">
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control product" style="width: 100%;" id="product"  name="product[]" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" step="any" class="form-control unit" name="unit[]" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" step="any" class="form-control quantity" name="quantity[]">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control unit_price" name="unit_price[]">
                                            </div>
                                        </td>


                                        <td class="total-cost">৳0.00</td>
                                        <td class="text-center">
                                            <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>

                                <tfoot>
                                <tr>
                                    <td>
                                        <a role="button" class="btn btn-info btn-sm" id="btn-add-product">Add Product</a>
                                    </td>
                                    <th colspan="3" class="text-right">Total Amount</th>
                                    <th id="total-amount">৳0.00</th>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->

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
                                                <label>Payment Type</label>
                                                <select class="form-control" id="modal-pay-type" name="payment_type">
                                                    <option value="1" {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash</option>
                                                    <option value="2" {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank</option>
                                                </select>
                                            </div>
                                            <div id="modal-bank-info">
                                                <div class="form-group">
                                                    <label>Cheque No.</label>
                                                    <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No." value="{{ old('cheque_no') }}">
                                                </div>
                                            </div>
                                            <div class="form-group bank_account_area {{ $errors->has('cash_account_code') ? 'has-error' :'' }}">
                                                <label for="cash_account_code">Payment Head <span class="text-danger">*</span></label>
                                                <select style="max-width: 300px !important;" class="form-control select2" id="cash_account_code" name="cash_account_code">
                                                    <option value="">Select Payment Cash/Bank Head</option>
                                                    @if (old('cash_account_code') != '')
                                                        <option value="{{ old('cash_account_code') }}" selected>{{ old('cash_account_code_name') }}</option>
                                                    @endif
                                                </select>
                                                <input type="hidden" name="cash_account_code_name" class="cash_account_code_name" id="cash_account_code_name" value="{{ old('cash_account_code_name') }}">

                                                @error('cash_account_code')
                                                <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th colspan="4" class="text-right">Sub Total </th>
                                                    <th id="product-sub-total"> ৳0.00 </th>
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
                                                <tr>
                                                    <th colspan="4" class="text-right">Total</th>
                                                    <th id="final-amount">৳0.00</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-right"> Paid *</th>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('paid') ? 'has-error' :'' }}">
                                                            <input type="text" class="form-control" name="paid" id="paid" value="{{ old('paid',0) }}" required>
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
                                                <tr id="tr-next-payment">
                                                    <th colspan="4" class="text-right">Next Payment Date</th>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('next_payment') ? 'has-error' :'' }}">
                                                            <div class="input-group date">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                                <input type="text" class="form-control pull-right" id="next_payment" name="next_payment" value="{{ old('next_payment', date('Y-m-d', strtotime('+1 month'))) }}" autocomplete="off">
                                                            </div>
                                                            <!-- /.input group -->
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

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="template-product">
        <tr class="product-item">

            <td>
                <div class="form-group">
                    <select class="form-control product" style="width: 100%;" id="product"  name="product[]" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control unit" name="unit[]" readonly>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" name="quantity[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control unit_price" name="unit_price[]">
                </div>
            </td>


            <td class="total-cost">৳0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
            </td>
        </tr>
    </template>
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
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

            $("#purchase_type").change(function (){
                var purchaseType = $(this).val();
                if(purchaseType != ''){
                    if(purchaseType == 1){
                        $("#lc_type").show();
                    }else{
                        $("#lc_type").hide();
                    }
                }
            })
            $("#purchase_type").trigger("change");


            // select product

            $('body').on('change','.product', function () {
                var productID = $(this).val();
                var itemProduct = $(this);
                var itemProduct = itemProduct.closest('tr');

                var existingProduct = itemProduct.siblings().find('.product').filter(function () {
                    return $(this).val() === productID;
                });

                if (existingProduct.length === 0 && productID !== '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_unit') }}",
                        data: {
                            productID: productID,
                        }
                    }).done(function(response) {
                            itemProduct.closest('tr').find('.unit').val(response.unit.name);
                            itemProduct.closest('tr').find('.unit_price').val(response.lastPurchasePrice.unit_price);
                            calculate();
                    });
                }else if (existingProduct.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Product Already Added',
                        text: 'This product is already added to the list.',
                    });
                }
            })
            $('.product').each(function () {
                if ($(this).val() !== '') {
                    $(this).trigger('change');
                }
            });


            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
            $('#next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                startDate: new Date()
            });

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);

                item.find('.serial').val('CGSP' + Math.floor((Math.random() * 100000)));
                $('#product-container').append(item);

                initProduct();

                if ($('.product-item').length >= 1 ) {
                    $('.btn-remove').show();
                }

                $('.type').trigger('change');
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
            });

            $('body').on('keyup', '.quantity, .unit_price,.product,.brand,#paid,#brand,#discount', function () {
                calculate();
            });
            $('body').on('change', '.quantity, .unit_price,.product,.brand,#paid,#brand,#discount', function () {
                calculate();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }

            initProduct();

            //payment
            $('#modal-pay-type').change(function () {
                if ($(this).val() == '1') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            });

            $('#modal-pay-type').trigger('change');

            calculate();
        });

        function calculate() {
            var productSubTotal = 0;
            var paid = $('#paid').val() || 0;

            //handle discount
            let discount = $('#discount').val();
            let discount_amount = 0;


            $('.product-item').each(function(i, obj) {
                var quantity = $('.quantity:eq('+i+')').val();
                var unit_price = $('.unit_price:eq('+i+')').val();

                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))
                    unit_price = 0;


                $('.total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );
                productSubTotal += quantity * unit_price;

            });


            $('#total-amount').html('৳' + productSubTotal.toFixed(2));
            $('#product-sub-total').html('৳' + productSubTotal.toFixed(2));

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

            var total = parseFloat(productSubTotal) - parseFloat(discount_amount);
            $('#discount_total').html(parseFloat(discount_amount).toFixed(2));
            var due = parseFloat(total) - parseFloat(paid);

            $('#final-amount').html('৳' + total.toFixed(2));
            $('#total').val(total);
            $('.discount_total').val(discount_amount);
            $('#due').html('৳' + due.toFixed(2));

            if (due > 0) {
                $('#tr-next-payment').show();
            } else {
                $('#tr-next-payment').hide();
            }
        }

        function initProduct() {
            $('.product').select2();
            $('.lc_no').select2();
            $('.dieNo').select2();
            $('.color').select2();
            $('.size').select2();
        }

        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });

        function intSelect2(){
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

        };

    </script>
@endsection
