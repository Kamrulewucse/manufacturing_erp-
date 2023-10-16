@extends('layouts.app')
@section('title','Sales Order')
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
                            <div class="col-md-4">
                                <input type="hidden" name="category_type" value="2">
                                <label>Customer</label>
                                <select class="form-control select2" style="width: 100%;" name="customer" data-placeholder="Select Customer">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                    <label>Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : date('Y-m-d')) : old('date') }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="white-space: nowrap;">Category Name</th>
                                <th style="white-space: nowrap">Sub Category</th>
                                <th style="white-space: nowrap">Product</th>
                                <th style="white-space: nowrap">Color</th>
                                <th style="white-space: nowrap">Size</th>
                                <th style="white-space: nowrap">Quantity</th>
                                <th style="white-space: nowrap">Unit Price</th>
                                <th style="white-space: nowrap">Selling Price</th>
                                <th style="white-space: nowrap">Total Cost</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody id="category-container">
                            @if (old('category') != null && sizeof(old('category')) > 0)
                                @foreach(old('category') as $item)
                                    <tr class="category-item">
                                        <td>
                                            <div class="form-group {{ $errors->has('category.'.$loop->index) ? 'has-error' :'' }}">
                                                <select class="form-control category select2" style="width: 100%;" name="category[]" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category.'.$loop->parent->index) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        <td >
                                            <div class="form-group {{ $errors->has('subcategory.'.$loop->index) ? 'has-error' :'' }}">
                                                <select class="form-control subcategory" style="width: 100%;" data-selected-subcategory="{{ old('subcategory.'.$loop->index) }}" name="subcategory[]" required>
                                                    <option value="">Select Sub Category</option>
                                                </select>
                                            </div>
                                        </td>

                                        <td >
                                            <div class="form-group {{ $errors->has('product.'.$loop->index) ? 'has-error' :'' }}">
                                                <select class="form-control product" style="width: 100%;" data-selected-product="{{ old('product.'.$loop->index) }}" name="product[]" required>
                                                    <option value="">Select Product</option>
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group {{ $errors->has('color_id.'.$loop->index) ? 'has-error' :'' }}">
                                                <select class="form-control color" style="width: 100%;" name="color_id[]" required>
                                                    <option value="">Select Color</option>
                                                    @foreach($colors as $color)
                                                        <option value="{{ $color->id }}" {{ old('color_id.'.$loop->parent->index) == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group {{ $errors->has('size_id.'.$loop->index) ? 'has-error' :'' }}">
                                                <select class="form-control size" style="width: 100%;" name="size_id[]" required>
                                                    <option value="">Select Size</option>
                                                    @foreach($sizes as $size)
                                                        <option value="{{ $size->id }}" {{ old('size_id.'.$loop->parent->index) == $size->id ? 'selected' : '' }}>{{ $size->size }}</option>                                                        @endforeach
                                                </select>
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

                                        <td>
                                            <div class="form-group {{ $errors->has('selling_price.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="text" class="form-control selling_price" name="selling_price[]" value="{{ old('selling_price.'.$loop->index) }}">
                                            </div>
                                        </td>

                                        <td class="total-cost">৳0.00</td>
                                        <td class="text-center">
                                            <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="category-item">
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control category" style="width: 100%;" name="category[]" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <select class="form-control subcategory" style="width: 100%;" name="subcategory[]" required>
                                                <option value="">Select Sub Category</option>
                                            </select>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <select class="form-control product" style="width: 100%;" name="product[]" required>
                                                <option value="">Select Product</option>
                                            </select>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <select class="form-control color" style="width: 100%;" name="color_id[]" required>
                                                <option value="">Select Color</option>
                                                @foreach($colors as $color)
                                                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <select class="form-control size" style="width: 100%;" name="size_id[]" required>
                                                <option value="">Select Size</option>
                                                @foreach($sizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->size }}</option>
                                                @endforeach
                                            </select>
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

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control selling_price" name="selling_price[]">
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
                                    <a role="button" class="btn btn-info btn-sm" id="btn-add-category">Add Product</a>
                                </td>
                                <th colspan="7" class="text-right">Total Amount</th>
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
                                        <select class="form-control select2" id="modal-pay-type" name="payment_type">
                                            <option value="1" {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash</option>
                                            <option value="2" {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank</option>
                                            <option value="3" {{ old('payment_type') == '3' ? 'selected' : '' }}>Mobile Banking</option>
                                        </select>
                                    </div>

                                    <div id="modal-bank-info">
                                        <div class="form-group {{ $errors->has('bank') ? 'has-error' :'' }}">
                                            <label>Bank</label>
                                            <select class="form-control select2" id="modal-bank" name="bank">
                                                <option value="">Select Bank</option>

                                                {{--                                                        @foreach($banks as $bank)--}}
                                                {{--                                                            <option value="{{ $bank->id }}" {{ old('bank') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>--}}
                                                {{--                                                        @endforeach--}}
                                            </select>
                                        </div>

                                        <div class="form-group {{ $errors->has('branch') ? 'has-error' :'' }}">
                                            <label>Branch</label>
                                            <select class="form-control select2" id="modal-branch" name="branch">
                                                <option value="">Select Branch</option>
                                            </select>
                                        </div>

                                        <div class="form-group {{ $errors->has('account') ? 'has-error' :'' }}">
                                            <label>Account</label>
                                            <select class="form-control select2" id="modal-account" name="account">
                                                <option value="">Select Account</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Cheque No.</label>
                                            <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No." value="{{ old('cheque_no') }}">
                                        </div>

                                        <div class="form-group {{ $errors->has('cheque_image') ? 'has-error' :'' }}">
                                            <label>Cheque Image</label>
                                            <input class="form-control" name="cheque_image" type="file">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="4" class="text-right"> Transport Cost *</th>
                                            <td>
                                                <div class="form-group {{ $errors->has('transport_cost') ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control" name="transport_cost" id="transport_cost" value="{{ old('transport_cost',0) }}" required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="text-right">Sub Total </th>
                                            <th id="final-amount"> ৳0.00 </th>
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
                {{--                        <a href="{{ route('purchase_receipt.all') }}" class="btn btn-default float-right">Cancel</a>--}}
            </div>
            </form>
        </div>
    </div>
    </div>

    <template id="template-category">
        <tr class="category-item">
            <td>
                <div class="form-group">
                    <select class="form-control category" style="width: 100%;" name="category[]" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name}}</option>
                        @endforeach
                    </select>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <select class="form-control subcategory" style="width: 100%;" name="subcategory[]" required>
                        <option value="">Select Sub Category</option>
                    </select>
                </div>
            </td>

            <td >
                <div class="form-group">
                    <select class="form-control product" style="width: 100%;" name="product[]" required>
                        <option value="">Select Product</option>
                    </select>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <select class="form-control color" style="width: 100%;" name="color_id[]" required>
                        <option value="">Select Color</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name}}</option>
                        @endforeach
                    </select>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <select class="form-control size" style="width: 100%;" name="size_id[]" required>
                        <option value="">Select Size</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->size}}</option>
                        @endforeach
                    </select>
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

            <td>
                <div class="form-group">
                    <input type="text" class="form-control selling_price" name="selling_price[]">
                </div>
            </td>

            <td class="total-cost">৳0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
            </td>
        </tr>
    </template>
@endsection

{{--@section('script')--}}
{{--    <!-- Select2 -->--}}
{{--    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>--}}
{{--    <!-- bootstrap datepicker -->--}}
{{--    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>--}}
{{--    <script>--}}
{{--        $(function () {--}}
{{--            intSelect2();--}}

{{--            $("#payment_type").change(function (){--}}
{{--                var payType = $(this).val();--}}

{{--                if(payType != ''){--}}
{{--                    if(payType == 1){--}}
{{--                        $(".bank-area").show();--}}
{{--                    }else{--}}
{{--                        $(".bank-area").hide();--}}
{{--                    }--}}
{{--                }--}}
{{--            })--}}
{{--            $("#payment_type").trigger("change");--}}

{{--            $("#purchase_type").change(function (){--}}
{{--                var purchaseType = $(this).val();--}}

{{--                if(purchaseType != ''){--}}
{{--                    if(purchaseType == 1){--}}
{{--                        $("#lc_type").show();--}}
{{--                    }else{--}}
{{--                        $("#lc_type").hide();--}}
{{--                    }--}}
{{--                }--}}
{{--            })--}}
{{--            $("#purchase_type").trigger("change");--}}


{{--            $('body').on('change','.category', function () {--}}
{{--                var categoryID = $(this).val();--}}
{{--                var itemCategory = $(this);--}}
{{--                itemCategory.closest('tr').find('.subcategory').html('<option value="">Select Sub Category</option>');--}}
{{--                var selected = itemCategory.closest('tr').find('.subcategory').attr("data-selected-subcategory");--}}

{{--                if (categoryID != '') {--}}
{{--                    $.ajax({--}}
{{--                        method: "GET",--}}
{{--                        url: "{{ route('get_subCategory') }}",--}}
{{--                        data: { categoryID: categoryID }--}}
{{--                    }).done(function( data ) {--}}
{{--                        $.each(data, function( index, item ) {--}}
{{--                            if (selected == item.id)--}}
{{--                                itemCategory.closest('tr').find('.subcategory').append('<option value="'+item.id+'" selected>'+item.name+'</option>');--}}
{{--                            else--}}
{{--                                itemCategory.closest('tr').find('.subcategory').append('<option value="'+item.id+'">'+item.name+'</option>');--}}
{{--                        });--}}
{{--                        itemCategory.closest('tr').find('.subcategory').trigger('change');--}}
{{--                    });--}}
{{--                }--}}

{{--            });--}}
{{--            $('.category').trigger('change');--}}

{{--            // select Sub Category--}}
{{--            $('body').on('change','.subcategory', function () {--}}
{{--                var subcategoryID = $(this).val();--}}
{{--                var itemSubCategory = $(this);--}}
{{--                itemSubCategory.closest('tr').find('.product').html('<option value="">Select Product</option>');--}}
{{--                var selected = itemSubCategory.closest('tr').find('.product').attr("data-selected-product");--}}

{{--                if (subcategoryID != '') {--}}
{{--                    $.ajax({--}}
{{--                        method: "GET",--}}
{{--                        url: "{{ route('get_product') }}",--}}
{{--                        data: { subcategoryID: subcategoryID }--}}
{{--                    }).done(function( data ) {--}}

{{--                        $.each(data, function( index, item ) {--}}
{{--                            if (selected == item.id)--}}
{{--                                itemSubCategory.closest('tr').find('.product').append('<option value="'+item.id+'" selected>'+item.name+'</option>');--}}

{{--                            else--}}
{{--                                itemSubCategory.closest('tr').find('.product').append('<option value="'+item.id+'">'+item.name+'</option>');--}}

{{--                        });--}}

{{--                    });--}}
{{--                }--}}
{{--            })--}}

{{--            //Date picker--}}
{{--            $('#date').datepicker({--}}
{{--                autoclose: true,--}}
{{--                format: 'yyyy-mm-dd'--}}
{{--            });--}}
{{--            $('#next_payment').datepicker({--}}
{{--                autoclose: true,--}}
{{--                format: 'yyyy-mm-dd',--}}
{{--                startDate: new Date()--}}
{{--            });--}}

{{--            $('#btn-add-category').click(function () {--}}
{{--                var html = $('#template-category').html();--}}
{{--                var item = $(html);--}}

{{--                item.find('.serial').val('CGSP' + Math.floor((Math.random() * 100000)));--}}
{{--                $('#category-container').append(item);--}}

{{--                initCategory();--}}

{{--                if ($('.category-item').length >= 1 ) {--}}
{{--                    $('.btn-remove').show();--}}
{{--                }--}}

{{--                $('.type').trigger('change');--}}
{{--            });--}}

{{--            $('body').on('click', '.btn-remove', function () {--}}
{{--                $(this).closest('.category-item').remove();--}}
{{--                calculate();--}}

{{--                if ($('.category-item').length <= 1 ) {--}}
{{--                    $('.btn-remove').hide();--}}
{{--                }--}}
{{--            });--}}

{{--            $('body').on('keyup', '.quantity, .unit_price,#paid,#transport_cost', function () {--}}
{{--                calculate();--}}
{{--            });--}}

{{--            if ($('.category-item').length <= 1 ) {--}}
{{--                $('.btn-remove').hide();--}}
{{--            } else {--}}
{{--                $('.btn-remove').show();--}}
{{--            }--}}

{{--            $('body').on('change', '.type', function () {--}}
{{--                var type = $(this).val();--}}
{{--                var count = $(this).closest('tr').find('.quantity').val();--}}
{{--                var categoryId = $(this).closest('tr').find('.category').val();--}}
{{--                var warranty = $(this).closest('tr').find('.warranty').val();--}}
{{--                var unitPrice = $(this).closest('tr').find('.unit_price').val();--}}
{{--                var includingPrice = $(this).closest('tr').find('.including_price').val();--}}
{{--                var sellingPrice = $(this).closest('tr').find('.selling_price').val();--}}

{{--                if (type == '1') {--}}
{{--                    $(this).closest('tr').find('.quantity').val('1');--}}
{{--                    $(this).closest('tr').find('.quantity').prop('readonly', true);--}}

{{--                    if (count > 1) {--}}
{{--                        for(i=1; i<count; i++) {--}}
{{--                            var html = $('#template-category').html();--}}
{{--                            var item = $(html);--}}

{{--                            item.find('.category').val(categoryId);--}}
{{--                            item.find('.serial').val('CGSP' + Math.floor((Math.random() * 100000)));--}}
{{--                            item.find('.type').val('1');--}}
{{--                            item.find('.warranty').val(warranty);--}}
{{--                            item.find('.unit_price').val(unitPrice);--}}
{{--                            item.find('.including_price').val(includingPrice);--}}
{{--                            item.find('.selling_price').val(sellingPrice);--}}
{{--                            $('#category-container').append(item);--}}

{{--                            initCategory();--}}
{{--                        }--}}

{{--                        $('.type').trigger('change');--}}
{{--                        calculate();--}}
{{--                    }--}}
{{--                } else {--}}
{{--                    $(this).closest('tr').find('.quantity').prop('readonly', false);--}}
{{--                }--}}
{{--            });--}}

{{--            $('.type').trigger('change');--}}
{{--            initCategory();--}}

{{--            //payment--}}
{{--            $('#modal-pay-type').change(function() {--}}
{{--                if ($(this).val() == '1') {--}}
{{--                    $('#modal-bank-info').hide();--}}
{{--                    $('#modal-mobile-bank-info').hide();--}}
{{--                }--}}
{{--                if($(this).val() == '3'){--}}
{{--                    $('#modal-bank-info').hide();--}}
{{--                    $('#modal-mobile-bank-info').show();--}}
{{--                }--}}
{{--                if($(this).val() == '2') {--}}
{{--                    $('#modal-mobile-bank-info').hide();--}}
{{--                    $('#modal-bank-info').show();--}}
{{--                }--}}
{{--            });--}}

{{--            $('#modal-pay-type').trigger('change');--}}
{{--            //-------end payment type -------}}

{{--            var selectedBranch = '{{ old('branch') }}';--}}
{{--            var selectedAccount = '{{ old('account') }}';--}}

{{--            $('#modal-bank').change(function () {--}}
{{--                var bankId = $(this).val();--}}
{{--                $('#modal-branch').html('<option value="">Select Branch</option>');--}}
{{--                $('#modal-account').html('<option value="">Select Account</option>');--}}

{{--                if (bankId != '') {--}}
{{--                    $.ajax({--}}
{{--                        method: "GET",--}}
{{--                        url: "{{ route('get_branch') }}",--}}
{{--                        data: { bankId: bankId }--}}
{{--                    }).done(function( response ) {--}}
{{--                        $.each(response, function( index, item ) {--}}
{{--                            if (selectedBranch == item.id)--}}
{{--                                $('#modal-branch').append('<option value="'+item.id+'" selected>'+item.name+'</option>');--}}
{{--                            else--}}
{{--                                $('#modal-branch').append('<option value="'+item.id+'">'+item.name+'</option>');--}}
{{--                        });--}}

{{--                        $('#modal-branch').trigger('change');--}}
{{--                    });--}}
{{--                }--}}

{{--                $('#modal-branch').trigger('change');--}}
{{--            });--}}

{{--            $('#modal-branch').change(function () {--}}
{{--                var branchId = $(this).val();--}}
{{--                $('#modal-account').html('<option value="">Select Account</option>');--}}

{{--                if (branchId != '') {--}}
{{--                    $.ajax({--}}
{{--                        method: "GET",--}}
{{--                        url: "{{ route('get_bank_account') }}",--}}
{{--                        data: { branchId: branchId }--}}
{{--                    }).done(function( response ) {--}}
{{--                        $.each(response, function( index, item ) {--}}
{{--                            if (selectedAccount == item.id)--}}
{{--                                $('#modal-account').append('<option value="'+item.id+'" selected>'+item.account_no+'</option>');--}}
{{--                            else--}}
{{--                                $('#modal-account').append('<option value="'+item.id+'">'+item.account_no+'</option>');--}}
{{--                        });--}}
{{--                    });--}}
{{--                }--}}
{{--            });--}}

{{--            $('#modal-bank').trigger('change');--}}

{{--            calculate();--}}
{{--        });--}}

{{--        function calculate() {--}}
{{--            var total = 0;--}}
{{--            var paid = $('#paid').val() || 0;--}}
{{--            var transport_cost = $('#transport_cost').val() || 0;--}}

{{--            $('.category-item').each(function(i, obj) {--}}
{{--                var quantity = $('.quantity:eq('+i+')').val();--}}
{{--                var unit_price = $('.unit_price:eq('+i+')').val();--}}

{{--                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))--}}
{{--                    quantity = 0;--}}

{{--                if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))--}}
{{--                    unit_price = 0;--}}

{{--                $('.total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );--}}
{{--                total += quantity * unit_price;--}}
{{--            });--}}
{{--            var subTotal = parseFloat(total)+parseFloat(transport_cost);--}}
{{--            var due = parseFloat(subTotal)-parseFloat(paid);--}}
{{--            $('#final-amount').html('৳' + subTotal.toFixed(2));--}}
{{--            $('#total-amount').html('৳' + total.toFixed(2));--}}
{{--            $('#total').val(total);--}}
{{--            $('#due').html('৳' + due.toFixed(2));--}}

{{--            if (due > 0) {--}}
{{--                $('#tr-next-payment').show();--}}
{{--            } else {--}}
{{--                $('#tr-next-payment').hide();--}}
{{--            }--}}
{{--        }--}}

{{--        function initCategory() {--}}
{{--            $('.category').select2();--}}
{{--            $('.subcategory').select2();--}}
{{--            $('.product').select2();--}}
{{--            $('.lc_no').select2();--}}
{{--            $('.color').select2();--}}
{{--            $('.size').select2();--}}
{{--        }--}}

{{--        $(document).ready(function() {--}}
{{--            $(window).keydown(function(event){--}}
{{--                if(event.keyCode == 13) {--}}
{{--                    event.preventDefault();--}}
{{--                    return false;--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}

{{--        function intSelect2(){--}}
{{--            $('.select2').select2()--}}
{{--            $('#account').on('select2:select', function (e) {--}}
{{--                var data = e.params.data;--}}
{{--                var index = $("#account").index(this);--}}
{{--                $('#account_name:eq('+index+')').val(data.text);--}}
{{--            });--}}

{{--        }--}}
{{--    </script>--}}
{{--@endsection--}}
