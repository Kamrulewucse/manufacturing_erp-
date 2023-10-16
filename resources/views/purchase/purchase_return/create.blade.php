@extends('layouts.app')
@section('title','Purchase Return')
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
            width: 100px;
        }
        input.form-control.unit_price{
            width: 100px;
        }
        input.form-control.available_quantity{
            width: 120px;
        }
        input.form-control.unit{
            width: 60px;
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
                <form method="POST" action="{{ route('purchase.return') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="row pb-3">
                            <div class="col-md-3">
                                <input type="hidden" name="categor_type" value="2">
                                <div class="form-group {{ $errors->has('purchase_order') ? 'has-error' :'' }}">
                                    <label>Return Order</label>
                                    <select class="form-control select2 purchase_order" style="width: 100%;" id="purchase_order" name="purchase_order" data-placeholder="Select purchase order">
                                        <option value="">Select Supplier</option>
                                        @foreach($purchaseOrders as $purchaseOrder)
                                            <option value="{{ $purchaseOrder->id }}" {{ old('purchase_order') == $purchaseOrder->id ? 'selected' : '' }}>{{ $purchaseOrder->order_no }}</option>
                                        @endforeach
                                    </select>
                                    @error('purchase_order')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
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
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="white-space: nowrap" width="30%">Product</th>
                                    <th style="white-space: nowrap" width="15%">Unit</th>
                                    <th style="white-space: nowrap" width="15%">Available to Return</th>
                                    <th style="white-space: nowrap" width="20%">Return Quantity</th>
                                    <th style="white-space: nowrap" width="20%">Buying Price</th>
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
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group {{ $errors->has('unit.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" step="any" class="form-control unit" name="unit[]" value="{{ old('unit.'.$loop->index) }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group {{ $errors->has('available_quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" step="any" class="form-control available_quantity" name="available_quantity[]" value="{{ old('available_quantity.'.$loop->index) }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{ old('quantity.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control unit_price" name="unit_price[]" value="{{ old('unit_price.'.$loop->index) }}" readonly>
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
                                                <input type="text" step="any" class="form-control available_quantity" name="available_quantity[]" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" step="any" class="form-control quantity" name="quantity[]">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control unit_price" name="unit_price[]" readonly>
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
                                    <th colspan="4" class="text-right">Total Amount</th>
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

                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th colspan="4" class="text-right">Return Grand Total </th>
                                                    <th id="return-grand-total"> ৳0.00 </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-right">Deduction Amount(Tk/%)</th>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('deduction_amount') ? 'has-error' :'' }}">
                                                            <input type="text" class="form-control" id="deduction_amount" value="{{ empty(old('deduction_amount')) ? ($errors->has('deduction_amount') ? '' : '0') : old('deduction_amount') }}">
                                                            <span>৳<span id="deduction_amount_total">0.00</span></span>
                                                            <input type="hidden" class="deduction_amount_total" name="deduction_amount" value="{{ empty(old('deduction_amount')) ? ($errors->has('deduction_amount') ? '' : '0') : old('deduction_amount') }}">
                                                            <input type="hidden" class="deduction_amount_percentage" name="deduction_amount_percentage" value="{{ empty(old('deduction_amount_percentage')) ? ($errors->has('deduction_amount_percentage') ? '' : '0') : old('deduction_amount_percentage') }}">
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
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <input type="hidden" name="total" id="total">
                        <input type="hidden" name="due_total" id="due_total">
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
                    <input type="text" step="any" class="form-control available_quantity" name="available_quantity[]" readonly>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" name="quantity[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control unit_price" name="unit_price[]" readonly>
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


            // select return order
            $('body').on('change','.purchase_order', function () {
                var purchaseOrderId = $(this).val();
                // alert(categoryID);
                var itemCategory = $('#product-container tr');
                itemCategory.last().find('.product').html('<option value="">Select Product</option>');

                if (purchaseOrderId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_order_product') }}",
                        data: { purchaseOrderId: purchaseOrderId }
                    }).done(function(response) {
                        $.each(response, function( index, item ) {
                            itemCategory.last('tr').find('.product').append('<option value="'+item.product_id+'">'+item.name+'</option>');
                        });
                        calculate();
                    });
                }
            })
            $('.purchase_order').trigger('change');
            // select product

            $('body').on('change','.product', function () {
                var productID = $(this).val();
                var purchaseOrderID =$('#purchase_order').val() || 0;
                var itemProduct = $(this);
                var itemProduct = itemProduct.closest('tr');

                var existingProduct = itemProduct.siblings().find('.product').filter(function () {
                    return $(this).val() === productID;
                });

                if (existingProduct.length === 0 && productID !== '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_purchase_details') }}",
                        data: {
                            productID: productID,
                            purchaseOrderID: purchaseOrderID,
                        }
                    }).done(function(response) {
                        itemProduct.closest('tr').find('.unit').val(response.unit.name);
                        itemProduct.closest('tr').find('.available_quantity').val(response.purchaseDetail.quantity);
                        itemProduct.closest('tr').find('.quantity').val(response.purchaseDetail.quantity);
                        itemProduct.closest('tr').find('.unit_price').val(response.purchaseDetail.unit_price);
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
                $('.purchase_order').trigger('change');
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
            });

            $('body').on('keyup', '.quantity, .unit_price,.product,.brand,#paid,#brand,#deduction_amount', function () {
                calculate();
            });
            $('body').on('change', '.quantity, .unit_price,.product,.brand,#paid,#brand,#deduction_amount', function () {
                calculate();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }

            initProduct();

            //payment
            $('#payment_type').change(function () {
                if ($(this).val() == '1') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            });

            $('#payment_type').trigger('change');

            calculate();
        });

        function calculate() {
            var productSubTotal = 0;
            var paid = $('#paid').val() || 0;

            //handle deduction_amount
            let deduction_amount = $('#deduction_amount').val();
            let deduction_amount_amount = 0;


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
            $('#return-grand-total').html('৳' + productSubTotal.toFixed(2));

            // var productTotaldeduction_amount = parseFloat(deduction_amount);
            // $('#deduction_amount_total').html('৳' + productTotaldeduction_amount.toFixed(2));

            if(deduction_amount.includes('%')){
                let deduction_amount_percent = deduction_amount.split('%')[0];
                deduction_amount_amount = (productSubTotal * deduction_amount_percent)/100;
                $('.deduction_amount_percentage').val(deduction_amount_percent);
            }else{
                deduction_amount_amount = deduction_amount;
                $('.deduction_amount_percentage').val(0);
            }

            var total = parseFloat(productSubTotal) - parseFloat(deduction_amount_amount);
            $('#deduction_amount_total').html(parseFloat(deduction_amount_amount).toFixed(2));
            var due = parseFloat(total) - parseFloat(paid);

            $('#final-amount').html('৳' + total.toFixed(2));
            $('#total').val(total);

            $('.deduction_amount_total').val(deduction_amount_amount);
            $('#due').html('৳' + due.toFixed(2));
            $('#due_total').val( due.toFixed(2));
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
