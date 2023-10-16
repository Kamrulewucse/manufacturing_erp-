@extends('admin.layouts.admin')

@section('style')
    <style>
        select.form-control.product {
            width: 138px !important;
        }

        input.form-control.quantity {
            width: 90px;
        }

        input.form-control.unit_price, input.form-control.selling_price {
            width: 130px;
        }

        th {
            text-align: center;
        }

        select.form-control {
            min-width: 120px;
        }

        .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
            vertical-align: middle;
        }

        td .form-group {
            margin-bottom: 0;
        }
    </style>
@endsection

@section('title')
    Sales
@endsection

@section('content')
    <form method="POST" target="" enctype="multipart/form-data" action="{{ route('sale_receipt.edit',['order'=>$order->id]) }}">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Order Information</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('warehouse') ? 'has-error' :'' }}">
                                    <label for="warehouse">Center Point</label>

                                    <select id="warehouse" class="form-control select2 warehouse" style="width: 100%;"
                                            name="warehouse" data-placeholder="Select Center Point">
                                        @foreach($warehouses as $warehouse)

                                            <option selected value="{{ $warehouse->id }}" {{ empty(old('warehouse')) ? ($errors->has('warehouse') ? '' : ($order->warehouse_id == $warehouse->id ? 'selected' : '')) :
                                            (old('warehouse') == $warehouse->id ? 'selected' : '') }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse')
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
                                        <input type="text" class="form-control pull-right" id="date" name="date"
                                               value="{{ date('Y-m-d',strtotime($order->date)) }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if(Auth::user()->role==4)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Marketing Representative</label>

                                        <input readonly type="text" class="form-control"
                                               value="{{Auth::user()->name }}">

                                        <!-- /.input group -->
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('hide_show') ? 'has-error' :'' }}">
                                    <label for="hide_show"></label>
                                    <div class="checkbox">
                                        <label>

                                            <input type="checkbox" checked name="hide_show"
                                                   {{ empty(old('hide_show')) ? ($errors->has('hide_show') ? '' : ($order->hide_show == 1 ? 'selected' : '')) :
                                            (old('hide_show') == 1 ? 'selected' : '') }}
                                                   value="1"> <span class="text-danger text-bold">Status</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Customer Information</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('customer_type') ? 'has-error' :'' }}">
                                    <label>Customer Type </label>
                                    <select class="form-control" id="customer_type" name="customer_type">
                                        <option value="1">New</option>
                                        <option {{ !empty($order->customer_id)  ? 'selected' : '' }} value="2">Old
                                        </option>
                                    </select>
                                    @error('customer_type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div id="old_customer_area">
                                <div class="col-md-3">
                                    <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                                        <label>Customer Select</label>
                                        <select class="form-control select2" name="customer">
                                            <option value="">Select Customer</option>
                                            @foreach ($customers as $customer)
                                                <option
                                                    {{ $order->customer_id == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">{{ $customer->name.'-'.$customer->mobile_no}}</option>
                                            @endforeach
                                        </select>
                                        @error('customer')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <div id="new_customer_area">
                                <div class="col-md-3">
                                    <div class="form-group {{ $errors->has('customer_name') ? 'has-error' :'' }}">
                                        <label>Customer Name </label>
                                        <input type="text" id="customer_name" name="customer_name"
                                               value="{{ old('customer_name') }}" class="form-control">
                                        @error('customer_name')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                                        <label>Customer Mobile</label>
                                        <input type="text" id="mobile_no" value="{{ old('mobile_no') }}"
                                               name="mobile_no" class="form-control">
                                        @error('mobile_no')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group {{ $errors->has('address') ? 'has-error' :'' }}">
                                        <label>Customer Address</label>
                                        <input type="text" id="address" value="{{ old('address') }}" name="address"
                                               class="form-control">
                                        @error('address')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Products</h3>

                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Brand</th>
                                            <th>LC No</th>
                                            <th>Stock</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total Cost</th>
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody id="product-container">
                                        @if (old('category') != null && sizeof(old('category')) > 0)
                                            @foreach(old('category') as $item)
                                                <tr class="product-item">
                                                    <td>
                                                        <div
                                                            class="form-group {{ $errors->has('category.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 category"
                                                                    style="width: 100%;" name="category[]">
                                                                <option value="">Select Category</option>
                                                                @foreach($categories as $category)
                                                                    <option
                                                                        value="{{ $category->id }}" {{ old('category.'.$loop->parent->index) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="form-group {{ $errors->has('product.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 product"
                                                                    style="width: 100%;"
                                                                    data-selected-product="{{ old('product.'.$loop->index) }}"
                                                                    name="product[]" required>
                                                                <option value="">Select Product</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="form-group {{ $errors->has('brand.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 brand"
                                                                    style="width: 100%;"
                                                                    data-selected-brand="{{ old('brand.'.$loop->index) }}"
                                                                    name="brand[]">
                                                                <option value="">Select Brand</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="form-group {{ $errors->has('lc_no.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 lc_no"
                                                                    style="width: 100%;"
                                                                    data-selected-lc-no="{{ old('lc_no.'.$loop->index) }}"
                                                                    name="lc_no[]">
                                                                <option value="">Select LC No</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="product_stock"></span>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="number" step="any"
                                                                   class="form-control quantity" name="quantity[]"
                                                                   value="{{ old('quantity.'.$loop->index) }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" step="any"
                                                                   class="form-control unit_price" name="unit_price[]"
                                                                   value="{{ old('unit_price.'.$loop->index) }}">
                                                        </div>
                                                    </td>
                                                    <td class="total-cost">৳0.00</td>
                                                    <td class="text-center">
                                                        <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach($order->products as $product)
                                                <tr class="product-item">
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control select2 category"
                                                                    style="width: 100%;" name="category[]">
                                                                <option value="">Select Category</option>
                                                                @foreach($categories as $category)
                                                                    <option
                                                                        value="{{ $category->id }}" {{$product->pivot->category_id==$category->id ? 'selected' : ''}}>{{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control select2 product"
                                                                    style="width: 100%;"
                                                                    data-selected-product="{{ $product->pivot->product_id }}"
                                                                    name="product[]" required>
                                                                <option value="">Select Product</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control select2 brand"
                                                                    style="width: 100%;"
                                                                    data-selected-brand="{{ $product->pivot->brand_id }}"
                                                                    name="brand[]">
                                                                <option value="">Select Brand</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control select2 lc_no"
                                                                    style="width: 100%;"
                                                                    data-selected-lc-no="{{ $product->pivot->lc_no }}"
                                                                    name="lc_no[]">
                                                                <option value="">Select LC</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="product_stock"></span>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="number" step="any"
                                                                   class="form-control quantity"
                                                                   value="{{$product->pivot->quantity}}"
                                                                   name="quantity[]">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" step="any"
                                                                   class="form-control unit_price"
                                                                   value="{{$product->pivot->unit_price}}"
                                                                   name="unit_price[]">
                                                        </div>
                                                    </td>
                                                    <td class="total-cost">৳ 0.00</td>
                                                    <td class="text-center">
                                                        <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td>
                                                <a role="button" class="btn btn-info btn-sm" id="btn-add-product">Add
                                                    Product</a>
                                            </td>
                                            <th colspan="6" class="text-right">Total Amount</th>
                                            <th id="total-amount">৳0.00</th>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <select class="form-control" id="modal-pay-type" name="payment_type">
                                        <option value="1" {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash
                                        </option>
                                        <option value="2" {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank
                                        </option>
                                        <option value="3" {{ old('payment_type') == '3' ? 'selected' : '' }}>Mobile
                                            Banking
                                        </option>
                                    </select>
                                </div>

                                <div id="modal-bank-info">
                                    <div class="form-group {{ $errors->has('bank') ? 'has-error' :'' }}">
                                        <label>Bank</label>
                                        <select class="form-control" id="modal-bank" name="bank">
                                            <option value="">Select Bank</option>

                                            @foreach($banks as $bank)
                                                <option
                                                    value="{{ $bank->id }}" {{ old('bank') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group {{ $errors->has('branch') ? 'has-error' :'' }}">
                                        <label>Branch</label>
                                        <select class="form-control" id="modal-branch" name="branch">
                                            <option value="">Select Branch</option>
                                        </select>
                                    </div>

                                    <div class="form-group {{ $errors->has('account') ? 'has-error' :'' }}">
                                        <label>Account</label>
                                        <select class="form-control" id="modal-account" name="account">
                                            <option value="">Select Account</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Cheque No.</label>
                                        <input class="form-control" type="text" name="cheque_no"
                                               placeholder="Enter Cheque No." value="{{ old('cheque_no') }}">
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
                                        <th colspan="4" class="text-right">Sub Total</th>
                                        <th id="product-sub-total">৳0.00</th>
                                    </tr>

                                    <tr>
                                        <th colspan="4" class="text-right">VAT (%)</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('vat') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="vat" id="vat"
                                                       value="{{ old('vat', $order->vat_percentage) }}">
                                                <span id="vat_total">৳0.00</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="4" class="text-right">Discount (%)</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('discount') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="discount" id="discount"
                                                       value="{{ old('vat', $order->discount_percentage) }}">
                                                <span id="discount_total">৳0.00</span>
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
                                                <input type="text" class="form-control" name="paid" id="paid"
                                                       value="{{ old('vat', $order->paid) }}">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">Due</th>
                                        <th id="due">৳0.00</th>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
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
                <div class="form-group">
                    <select class="form-control select2 category" style="width: 100%;" name="category[]">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="form-control select2 product" style="width: 100%;" name="product[]">
                        <option value="">Select Product</option>
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="form-control select2 brand" style="width: 100%;" name="brand[]">
                        <option value="">Select Brand</option>
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="form-control select2 lc_no" style="width: 100%;" name="lc_no[]">
                        <option value="">Select LC</option>
                    </select>
                </div>
            </td>
            <td>
                <span class="product_stock"></span>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" name="quantity[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control unit_price" name="unit_price[]">
                </div>
            </td>
            <td class="total-cost">৳ 0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
            </td>
        </tr>
    </template>
@endsection

@section('script')
    <script>


        var oldPaid = {{ old('paid') ? old('paid') : 0 }};
        $(function () {


            //Initialize Select2 Elements
            $('.select2').select2()

            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

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


            // select Category
            $('body').on('change', '.category', function () {
                var categoryId = $(this).val();
                var itemCategory = $(this);

                itemCategory.closest('tr').find('.product_stock').html('');
                itemCategory.closest('tr').find('.product').html('<option value="">Select Product</option>');
                itemCategory.closest('tr').find('.brand').html('<option value="">Select Brand</option>');
                itemCategory.closest('tr').find('.lc_no').html('<option value="">Select LC No</option>');
                var selectedProduct = itemCategory.closest('tr').find('.product').attr("data-selected-product");

                if (categoryId != '') {

                    if ($('#warehouse').val() == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Please, Select Center Point First !',
                        });
                        return false;
                    }

                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product') }}",
                        data: {categoryId: categoryId}
                    }).done(function (data) {
                        $.each(data, function (index, item) {
                            if (selectedProduct == item.id)
                                itemCategory.closest('tr').find('.product').append('<option value="' + item.id + '" selected>' + item.name + '</option>');
                            else
                                itemCategory.closest('tr').find('.product').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                        itemCategory.closest('tr').find('.product').trigger('change');
                    });

                }
            });
            //Select Product
            $('body').on('change', '.product', function () {
                var productId = $(this).val();
                var warehouseId = $('#warehouse').val();
                var itemProduct = $(this);

                itemProduct.closest('tr').find('.product_stock').html('');
                itemProduct.closest('tr').find('.brand').html('<option value="">Select Brand</option>');
                itemProduct.closest('tr').find('.lc_no').html('<option value="">Select LC NO</option>');
                var selectedBrand = itemProduct.closest('tr').find('.brand').attr("data-selected-brand");

                if (productId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_brand') }}",
                        data: {productId: productId, warehouseId: warehouseId}
                    }).done(function (data) {
                        $.each(data, function (index, item) {
                            if (selectedBrand == item.id)
                                itemProduct.closest('tr').find('.brand').append('<option value="' + item.id + '" selected>' + item.name + '</option>');
                            else
                                itemProduct.closest('tr').find('.brand').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });

                        itemProduct.closest('tr').find('.brand').trigger("change");
                    });

                }
            });
            //Select Brand
            $('body').on('change', '.brand', function () {
                var brandId = $(this).val();
                var itemBrand = $(this);

                var warehouseId = $('#warehouse').val();
                var productId = itemBrand.closest('tr').find('.product').val();
                itemBrand.closest('tr').find('.product_stock').html('');
                itemBrand.closest('tr').find('.lc_no').html('<option value="">Select LC No</option>');
                var selectedLCNo = itemBrand.closest('tr').find('.lc_no').attr("data-selected-lc-no");
                if (brandId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_lc_no') }}",
                        data: {warehouseId: warehouseId, productId: productId, brandId: brandId}
                    }).done(function (data) {
                        $.each(data, function (index, item) {
                            if (selectedLCNo == item.lc_no)
                                itemBrand.closest('tr').find('.lc_no').append('<option value="' + item.lc_no + '" selected>' + item.lc_no + '</option>');
                            else
                                itemBrand.closest('tr').find('.lc_no').append('<option value="' + item.lc_no + '">' + item.lc_no + '</option>');
                        });
                        itemBrand.closest('tr').find('.lc_no').trigger("change");
                    });

                }
            });
            //Select LC No
            $('body').on('change', '.lc_no', function () {
                var lcNo = $(this).val();
                var itemLcNo = $(this);

                var warehouseId = $('#warehouse').val();
                var productId = itemLcNo.closest('tr').find('.product').val();
                var brandId = itemLcNo.closest('tr').find('.brand').val();

                var checkOldUnitPrice = itemLcNo.closest('tr').find('.unit_price').val();


                itemLcNo.closest('tr').find('.product_stock').html('');

                if (lcNo != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_inventory_stock') }}",
                        data: {warehouseId: warehouseId, productId: productId, brandId: brandId, lcNo: lcNo}
                    }).done(function (response) {
                        itemLcNo.closest('tr').find('.product_stock').html(response.quantity);

                        itemLcNo.closest('tr').find('.quantity').attr({
                            "max": response.quantity,
                        });

                        if (checkOldUnitPrice == '')
                            itemLcNo.closest('tr').find('.unit_price').val(response.selling_price);
                    });

                }
            });

            $('.category').trigger('change');
            $('.product').trigger('change');
            $('.brand').trigger('change');
            $('.lc_no').trigger('change');

            $('#customer_type').change(function () {
                var customerType = $(this).val();
                if (customerType == '1' || customerType == '3') {
                    $("#customer_barcode_area").hide();
                    $("#old_customer_area").hide();
                    $("#new_customer_area").show();
                } else {
                    $("#new_customer_area").hide();
                    $("#old_customer_area").show();
                }

            });

            $('#customer_type').trigger("change");

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);

                $('#product-container').append(item);

                if ($('.product-item').length >= 1) {
                    $('.btn-remove').show();
                }
                initProduct();
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1) {
                    $('.btn-remove').hide();
                }

            });

            $('body').on('keyup', '.quantity, .unit_price,  #vat, #discount, #received_amount', function () {
                calculate();
            });

            $('body').on('keyup', '#paid', function () {
                calculateDue();
            });

            if ($('.product-item').length <= 1) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }

            calculate();




            $('#modal-pay-type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3' || $(this).val() == '4') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            });

            $('#modal-pay-type').trigger('change');

            var selectedBranch = '{{ old('branch') }}';
            var selectedAccount = '{{ old('account') }}';

            $('#modal-bank').change(function () {
                var bankId = $(this).val();
                $('#modal-branch').html('<option value="">Select Branch</option>');
                $('#modal-account').html('<option value="">Select Account</option>');

                if (bankId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_branch') }}",
                        data: {bankId: bankId}
                    }).done(function (response) {
                        $.each(response, function (index, item) {
                            if (selectedBranch == item.id)
                                $('#modal-branch').append('<option value="' + item.id + '" selected>' + item.name + '</option>');
                            else
                                $('#modal-branch').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });

                        $('#modal-branch').trigger('change');
                    });
                }

                $('#modal-branch').trigger('change');
            });

            $('#modal-branch').change(function () {
                var branchId = $(this).val();
                $('#modal-account').html('<option value="">Select Account</option>');

                if (branchId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_bank_account') }}",
                        data: {branchId: branchId}
                    }).done(function (response) {
                        $.each(response, function (index, item) {
                            if (selectedAccount == item.id)
                                $('#modal-account').append('<option value="' + item.id + '" selected>' + item.account_no + '</option>');
                            else
                                $('#modal-account').append('<option value="' + item.id + '">' + item.account_no + '</option>');
                        });
                    });
                }
            });

            $('#modal-bank').trigger('change');

        });

        function calculateDue() {
            //jhghjgjhghjghghj

            var total = $('#total').val();
            var paid = $('#paid').val();
            var due = parseFloat(total) - parseFloat(paid);
            $('#due').html('৳' + due.toFixed(2));
        }

        function calculate() {
            var productSubTotal = 0;

            var vat = $('#vat').val();
            var discount = $('#discount').val();
            var paid = $('#paid').val();

            if (vat == '' || vat < 0 || !$.isNumeric(vat))
                vat = 0;

            if (discount == '' || discount < 0 || !$.isNumeric(discount))
                discount = 0;

            if (paid == '' || paid < 0 || !$.isNumeric(paid))
                paid = 0;


            $('.product-item').each(function (i, obj) {
                var quantity = $('.quantity:eq(' + i + ')').val();
                var unit_price = $('.unit_price:eq(' + i + ')').val();


                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))
                    unit_price = 0;

                $('.total-cost:eq(' + i + ')').html('৳' + (quantity * unit_price).toFixed(2));
                productSubTotal += quantity * unit_price;
            });


            var productTotalVat = (productSubTotal * vat) / 100;

            var productTotalDiscount = (productSubTotal * discount) / 100;


            $('#product-sub-total').html('৳' + productSubTotal.toFixed(2));


            $('#vat_total').html('৳' + productTotalVat.toFixed(2));

            $('#discount_total').html('৳' + productTotalDiscount.toFixed(2));


            var total = parseFloat(productSubTotal) +
                parseFloat(productTotalVat) -
                parseFloat(productTotalDiscount);

            var due = parseFloat(total) - parseFloat(paid);
            $('#final-amount').html('৳' + total.toFixed(2));
            $('#due').html('৳' + due.toFixed(2));
            $('#total').val(total.toFixed(2));
            $('#due_total').val(due.toFixed(2));



        }

        function calculateDue() {
            //jhghjgjhghjghghj
            var total = $('#total').val();
            var paid = $('#paid').val();
            var due = parseFloat(total) - parseFloat(paid);
            $('#due').html('৳' + due.toFixed(2));
            $('#due_total').val(due.toFixed(2));
        }

        function initProduct() {
            $('.select2').select2();
        }
    </script>
@endsection
