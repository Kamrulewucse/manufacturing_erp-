@extends('layouts.app')
@section('title','Accounts Edit')

@section('style')
    <style>
        select.form-control.product {
            width: 138px !important;
        }
        input.form-control.unit_price{
            width: 130px;
        }
        input.form-control.quantity{
            width: 130px;
        }
        th {
            text-align: center;
        }
        select.form-control {
            min-width: 120px;
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
    <form method="POST" enctype="multipart/form-data"  action="{{ route('accounts_receipt.edit',['order'=>$order->id]) }}">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Order Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('warehouse') ? 'has-error' :'' }}">
                                    <label for="warehouse">Warehouse</label>

                                    <select id="warehouse" class="form-control select2 warehouse" style="width: 100%;" name="warehouse" data-placeholder="Select Warehouse">
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ empty(old('warehouse')) ? ($errors->has('warehouse') ? '' : ($order->warehouse_id == $warehouse->id ? 'selected' : '')) :
                                            (old('warehouse') == $warehouse->id ? 'selected' : '') }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('zone') ? 'has-error' :'' }}">
                                    <label for="zone">Zone</label>
                                    <select id="zone" class="form-control select2 zone" style="width: 100%;" name="zone" data-placeholder="Select Zone">
                                        <option value="">Select Zone</option>
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" {{ empty(old('zone')) ? ($errors->has('zone') ? '' : ($order->zone_id == $zone->id ? 'selected' : '')) :
                                            (old('zone') == $zone->id ? 'selected' : '') }}>{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('zone')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('sales_by') ? 'has-error' :'' }}">
                                    <label for="sales_by">Sales By</label>
                                    <select id="sales_by" class="form-control select2 sales_by" style="width: 100%;" name="sales_by" data-placeholder="Select Sales By">
                                        <option value="">Select Sales By</option>
                                        @foreach($employeeTypes as $employeeType)
                                            <option value="{{ $employeeType->id }}" {{ empty(old('sales_by')) ? ($errors->has('sales_by') ? '' : ($order->employee_id == $employeeType->id ? 'selected' : '')) :
                                            (old('sales_by') == $employeeType->id ? 'selected' : '') }}>{{ $employeeType->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sales_by')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('type') ? 'has-error' :'' }}">
                                    <label>Sale Type</label>

                                    <select class="form-control" name="type" id="type">
                                        <option value="1" {{ old('type',$order->type) == 1 ? 'selected' : '' }}>Customer</option>
                                        <option value="2" {{ old('type',$order->type) == 2 ? 'selected' : '' }}>Dealer</option>
                                    </select>

                                    @error('type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('install_date') ? 'has-error' :'' }}">
                                    <label>Date</label>

                                    <div class="input-group date">
                                        <input type="text" class="form-control pull-right date-picker" id="install_date" name="install_date"
                                               value="{{ date('Y-m-d',strtotime($order->install_date)) }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('install_date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('install_time') ? 'has-error' :'' }}">
                                    <label>Time</label>

                                    <div class="input-group date">
                                        <input type="time" class="form-control pull-right" id="install_time" name="install_time"
                                               value="{{old('install_time',$order->install_time) }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('install_time')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group {{ $errors->has('pre_filter') ? 'has-error' :'' }}">
                                    <label for="pre_filter"></label>
                                    <div class="checkbox" style="margin-top: 17px;margin-left: 22px;">
                                        <label>
                                            <input type="checkbox" name="pre_filter" {{ old('pre_filter',$order->pre_filter) == 1 ? 'checked' : '' }} value="1"> <span class="text-danger text-bold" style="color: #f58655!important;">Pre-Filter</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('accounts_note') ? 'has-error' :'' }}">
                                    <label>Accounts Note</label>
                                    <input type="text" id="accounts_note" name="accounts_note" value="{{ old('accounts_note', $order->accounts_note) }}" class="form-control">
                                    @error('accounts_note')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="all-customer-area">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Customer Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row" id="customer-type-area">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('customer_type') ? 'has-error' :'' }}">
                                    <label>Customer Type </label>
                                    <select class="form-control" id="customer_type" name="customer_type">
                                        <option {{ old('customer_type',$order->customer_type) == 2 ? 'selected' : '' }} value="2">Old Customer</option>
                                        <option {{ old('customer_type',$order->customer_type) == 1 ? 'selected' : '' }} value="1">New Customer</option>
                                    </select>
                                    @error('customer_type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="old_customer_area">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                                    <label>Customer</label>

                                    <select class="form-control customer select2" style="width: 100%;" name="customer">
                                        <option value="">Select Customer</option>

                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ empty(old('customer')) ? ($errors->has('customer') ? '' : ($order->customer_id == $customer->id ? 'selected' : '')) :
                                            (old('customer') == $customer->id ? 'selected' : '') }}>
                                                {{ $customer->name.' - '.$customer->mobile_no.' - '.$customer->address }}</option>
                                        @endforeach
                                    </select>

                                    @error('customer')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="new_customer_area">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('customer_name') ? 'has-error' :'' }}">
                                    <label>Customer Name</label>
                                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name',$order->customer_name) }}" class="form-control">
                                    @error('customer_name')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                                    <label>Customer Mobile</label>
                                    <input type="text" id="mobile_no" value="{{ old('mobile_no',$order->mobile_no) }}" name="mobile_no" class="form-control" >
                                    @error('mobile_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('address') ? 'has-error' :'' }}">
                                    <label>Customer Address</label>
                                    <input type="text" id="address" value="{{ old('address',$order->address) }}" name="address" class="form-control"  >
                                    @error('address')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('email') ? 'has-error' :'' }}">
                                    <label>Email</label>
                                    <input type="email" id="email" value="{{ old('email',$order->email) }}" name="email" class="form-control"  >
                                    @error('email')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="all-dealer-area">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Dealer Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row" id="dealer-type-area">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('dealer_type') ? 'has-error' :'' }}">
                                    <label>Dealer Type </label>
                                    <select class="form-control" id="dealer_type" name="dealer_type">
                                        <option {{ old('dealer_type',$order->dealer_type) == 2 ? 'selected' : '' }} value="2">Old Dealer</option>
                                        <option {{ old('dealer_type',$order->dealer_type) == 1 ? 'selected' : '' }} value="1">New Dealer</option>
                                    </select>
                                    @error('dealer_type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="old_dealer_area">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('dealer') ? 'has-error' :'' }}">
                                    <label>Dealer </label>

                                    <select class="form-control dealer select2" style="width: 100%;" name="dealer">
                                        <option value="">Select Dealer </option>
                                        @foreach($dealers as $dealer)
                                            <option value="{{ $dealer->id }}"
                                                {{ empty(old('dealer')) ? ($errors->has('dealer') ? '' : ($order->dealer_id == $dealer->id ? 'selected' : '')) :
                                            (old('dealer') == $dealer->id ? 'selected' : '') }}>
                                                {{ $dealer->name.' - '.$dealer->mobile.' - '.$dealer->address }}</option>
                                        @endforeach
                                    </select>

                                    @error('dealer')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="new_dealer_area">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('dealer_name') ? 'has-error' :'' }}">
                                    <label>Dealer Name </label>
                                    <input type="text" id="dealer_name" name="dealer_name" value="{{ old('dealer_name',$order->dealer_name) }}" class="form-control">
                                    @error('dealer_name')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('dealer_mobile_no') ? 'has-error' :'' }}">
                                    <label>Dealer Mobile</label>
                                    <input type="text" id="dealer_mobile_no" value="{{ old('dealer_mobile_no',$order->dealer_mobile_no) }}" name="dealer_mobile_no" class="form-control" >
                                    @error('dealer_mobile_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('dealer_address') ? 'has-error' :'' }}">
                                    <label>Dealer Address</label>
                                    <input type="text" id="dealer_address" value="{{ old('dealer_address',$order->dealer_address) }}" name="dealer_address" class="form-control"  >
                                    @error('dealer_address')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('dealer_email') ? 'has-error' :'' }}">
                                    <label>Dealer Email</label>
                                    <input type="email" id="dealer_email" value="{{ old('dealer_email',$order->dealer_email) }}" name="dealer_email" class="form-control"  >
                                    @error('dealer_email')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('dealer_code') ? 'has-error' :'' }}">
                                    <label>Dealer</label>
                                    <input type="text" id="dealer_code" value="{{ old('dealer_code',$order->dealer_code) }}" name="dealer_code" class="form-control"  >
                                    @error('dealer_code')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Products</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Sub Category</th>
                                            <th>Product</th>
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
                                                        <div class="form-group {{ $errors->has('category.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 category" style="width: 100%;" name="category[]" >
                                                                <option value="">Select Category</option>
                                                                @foreach($categories as $category)
                                                                    <option value="{{ $category->id }}" {{ old('category.'.$loop->parent->index) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('sub_category.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 sub_category" style="width: 100%;" data-selected-sub-category="{{ old('sub_category.'.$loop->index) }}" name="sub_category[]">
                                                                <option value="">Select Sub Category</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('product.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 product" style="width: 100%;" data-selected-product="{{ old('product.'.$loop->index) }}" name="product[]">
                                                                <option value="">Select Product</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('lc_no.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 lc_no" style="width: 100%;" data-selected-lc-no="{{ old('lc_no.'.$loop->index) }}" name="lc_no[]">
                                                                <option value="">Select LC No</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="product_stock"></span>
                                                    </td>
                                                    <td >
                                                        <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{ old('quantity.'.$loop->index) }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" step="any" class="form-control unit_price" name="unit_price[]" value="{{ old('unit_price.'.$loop->index) }}">
                                                        </div>
                                                    </td>
                                                    <td  class="total-cost">৳0.00</td>
                                                    <td  class="text-center">
                                                        <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach($order->products as $product)
                                                <tr class="product-item">
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control select2 category" style="width: 100%;" name="category[]" >
                                                                <option value="">Select Category</option>
                                                                @foreach($categories as $category)
                                                                    <option value="{{ $category->id }}" {{$product->product_category_id == $category->id ? 'selected' : '' }}>{{$product->productCategory->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control select2 sub_category" data-selected-sub-category="{{ $product->product_sub_category_id }}"
                                                                    style="width: 100%;" name="sub_category[]">
                                                                <option value="">Select Sub Category</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td >
                                                        <div class="form-group">
                                                            <select class="form-control select2 product" data-selected-product="{{ $product->purchase_product_id }}"
                                                                    style="width: 100%;" name="product[]">
                                                                <option value="">Select Product</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control select2 lc_no" data-selected-lc-no="{{ $product->lc_id }}"
                                                                    style="width: 100%;" name="lc_no[]" >
                                                                <option value="">Select LC</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="product_stock"></span>
                                                    </td>
                                                    <td >
                                                        <div class="form-group">
                                                            <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{$product->quantity}}">
                                                        </div>
                                                    </td>
                                                    <td >
                                                        <div class="form-group">
                                                            <input type="text" step="any" class="form-control unit_price" name="unit_price[]"  value="{{$product->unit_price}}">
                                                        </div>
                                                    </td>
                                                    <td  class="total-cost">৳ 0.00</td>
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
                                                <a role="button" class="btn btn-info btn-sm" id="btn-add-product">Add Product</a>
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
                                        <option value="1" {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash</option>
                                        <option value="2" {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank</option>
                                        <option value="3" {{ old('payment_type') == '3' ? 'selected' : '' }}>Mobile Banking</option>
                                    </select>
                                </div>

                                <div id="modal-bank-info">
                                    <div class="form-group {{ $errors->has('bank') ? 'has-error' :'' }}">
                                        <label>Bank</label>
                                        <select class="form-control" id="modal-bank" name="bank">
                                            <option value="">Select Bank</option>

                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}" {{ old('bank') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
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
                                        <th colspan="4" class="text-right">Sub Total</th>
                                        <th id="product-sub-total">৳0.00</th>
                                    </tr>

                                    <tr>
                                        <th colspan="4" class="text-right">VAT (%)</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('vat') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="vat" id="vat" value="{{ old('vat',$order->vat_percentage) }}">
                                                <span id="vat_total">৳0.00</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="4" class="text-right">Discount (%)</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('discount') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="discount" id="discount" value="{{ old('discount',$order->discount_percentage) }}">
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
                                                <input type="text" class="form-control" name="paid" id="paid" value="{{ old('paid',$order->paid) }}">
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
                <div class="form-group">
                    <select class="form-control select2 category" style="width: 100%;" name="category[]" >
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="form-control select2 sub_category" style="width: 100%;" name="sub_category[]">
                        <option value="">Select Sub Category</option>
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
                    <select class="form-control select2 lc_no" style="width: 100%;" name="lc_no[]" >
                        <option value="">Select LC</option>
                    </select>
                </div>
            </td>
            <td>
                <span class="product_stock"></span>
            </td>
            <td >
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" name="quantity[]">
                </div>
            </td>

            <td >
                <div class="form-group">
                    <input type="text" step="any" class="form-control unit_price" name="unit_price[]">
                </div>
            </td>
            <td  class="total-cost">৳ 0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
            </td>
        </tr>
    </template>
@endsection

@section('script')
    <script>
        var oldPaid ={{ old('paid',$order->paid)  }};
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

            $('#type').change(function () {
                if ($(this).val() == 1) {
                    $("#all-customer-area").show();
                    $("#all-dealer-area").hide();
                } else {
                    $("#all-customer-area").hide();
                    $("#all-dealer-area").show();
                }
            });

            $('#type').trigger('change');

            $('#customer_type').change(function (){
                var customerType = $(this).val();
                if (customerType == '1'){

                    $("#old_customer_area").hide();
                    $("#new_customer_area").show();
                }else{
                    $("#new_customer_area").hide();
                    $("#old_customer_area").show();
                }

            });

            $('#customer_type').trigger("change");



            $('#dealer_type').change(function (){
                var dealerType = $(this).val();
                if (dealerType == '1'){

                    $("#old_dealer_area").hide();
                    $("#new_dealer_area").show();
                }else{
                    $("#old_dealer_area").show();
                    $("#new_dealer_area").hide();
                }

            });

            $('#dealer_type').trigger("change");

            // select Category
            $('body').on('change','.category', function () {
                var categoryId = $(this).val();
                var itemCategory = $(this);

                itemCategory.closest('tr').find('.product_stock').html('');
                itemCategory.closest('tr').find('.sub_category').html('<option value="">Select Sub Category</option>');
                itemCategory.closest('tr').find('.product').html('<option value="">Select Product</option>');
                itemCategory.closest('tr').find('.lc_no').html('<option value="">Select LC No</option>');
                var selectedSubCategory = itemCategory.closest('tr').find('.sub_category').attr("data-selected-sub-category");

                if (categoryId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_sale_sub_category') }}",
                        data: {categoryId:categoryId}
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (selectedSubCategory == item.id)
                                itemCategory.closest('tr').find('.sub_category').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                itemCategory.closest('tr').find('.sub_category').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        itemCategory.closest('tr').find('.sub_category').trigger("change");
                    });

                }
            });
            //Select Sub Category
            $('body').on('change','.sub_category', function () {
                var subCategoryId = $(this).val();
                var warehouseId = $('#warehouse').val();
                var subCategory = $(this);

                subCategory.closest('tr').find('.product_stock').html('');
                subCategory.closest('tr').find('.product').html('<option value="">Select Product</option>');
                subCategory.closest('tr').find('.lc_no').html('<option value="">Select LC NO</option>');
                var selectedProduct = subCategory.closest('tr').find('.product').attr("data-selected-product");


                if (subCategoryId != '') {

                    if ($('#warehouse').val() == ''){
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Please, Select Warehouse First !',
                        });
                        return false;
                    }

                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_sale_product') }}",
                        data: {subCategoryId:subCategoryId,warehouseId:warehouseId}
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (selectedProduct == item.id)
                                subCategory.closest('tr').find('.product').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                subCategory.closest('tr').find('.product').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                        subCategory.closest('tr').find('.product').trigger('change');
                    });

                }
            });
            //Select Product
            $('body').on('change','.product', function () {
                var productId = $(this).val();
                var itemProduct= $(this);

                var warehouseId = $('#warehouse').val();
                var subCategoryId = itemProduct.closest('tr').find('.sub_category').val();
                itemProduct.closest('tr').find('.product_stock').html('');
                itemProduct.closest('tr').find('.lc_no').html('<option value="">Select LC No</option>');
                var selectedLCNo = itemProduct.closest('tr').find('.lc_no').attr("data-selected-lc-no");
                if (productId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_lc_no') }}",
                        data: {warehouseId:warehouseId,productId:productId,subCategoryId:subCategoryId}
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (selectedLCNo == item.id)
                                itemProduct.closest('tr').find('.lc_no').append('<option value="'+item.id+'" selected>'+item.lc_number+'</option>');
                            else
                                itemProduct.closest('tr').find('.lc_no').append('<option value="'+item.id+'">'+item.lc_number+'</option>');
                        });
                        itemProduct.closest('tr').find('.lc_no').trigger("change");
                    });

                }
            });
            //Select LC No
            $('body').on('change','.lc_no', function () {
                var lcNo = $(this).val();
                var itemLcNo= $(this);

                var warehouseId = $('#warehouse').val();
                var productId = itemLcNo.closest('tr').find('.product').val();
                var subCategoryId = itemLcNo.closest('tr').find('.sub_category').val();

                var checkOldUnitPrice = itemLcNo.closest('tr').find('.unit_price').val();


                itemLcNo.closest('tr').find('.product_stock').html('');

                if (lcNo != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_inventory_stock') }}",
                        data: {warehouseId:warehouseId,subCategoryId:subCategoryId,productId:productId,lcNo:lcNo}
                    }).done(function(response) {
                        itemLcNo.closest('tr').find('.product_stock').html(response.quantity);

                        itemLcNo.closest('tr').find('.quantity').attr({
                            "max" : response.quantity,
                        });

                        if (checkOldUnitPrice == '')
                            itemLcNo.closest('tr').find('.unit_price').val(response.selling_price);
                    });

                }
            });

            $('.category').trigger('change');
            $('.sub_category').trigger('change');
            $('.product').trigger('change');
            $('.lc_no').trigger('change');

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);

                $('#product-container').append(item);

                if ($('.product-item').length >= 1 ) {
                    $('.btn-remove').show();
                }
                initProduct();
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
            });

            $('body').on('keyup', '.quantity, .unit_price,  #vat, #discount, #received_amount', function () {
                calculate();
            });

            $('body').on('keyup','#paid', function () {
                calculateDue();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }

            calculate();

            calculateDue();



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
                        data: { bankId: bankId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (selectedBranch == item.id)
                                $('#modal-branch').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#modal-branch').append('<option value="'+item.id+'">'+item.name+'</option>');
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
                        data: { branchId: branchId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (selectedAccount == item.id)
                                $('#modal-account').append('<option value="'+item.id+'" selected>'+item.account_no+'</option>');
                            else
                                $('#modal-account').append('<option value="'+item.id+'">'+item.account_no+'</option>');
                        });
                    });
                }
            });

            $('#modal-bank').trigger('change');

        });

        function calculateDue(){
            var total= $('#total').val();
            //var paid= $('#paid').val();
            var paid= '{{$order->paid}}';
            var due = parseFloat(total) - parseFloat(paid);
            $('#due').html('৳' + due.toFixed(2));
            $('#due_total').val(due.toFixed(2));
        }

        function calculate() {
            var productSubTotal = 0;

            var vat = $('#vat').val();
            var discount = $('#discount').val();
            //var paid = $('#paid').val();
            var paid= '{{$order->paid}}';

            if (vat == '' || vat < 0 || !$.isNumeric(vat))
                vat = 0;

            if (discount == '' || discount < 0 || !$.isNumeric(discount))
                discount = 0;

            if (paid == '' || paid < 0 || !$.isNumeric(paid))
                paid = 0;


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


            var productTotalVat = (productSubTotal * vat) / 100;

            var productTotalDiscount = (productSubTotal * discount) / 100;


            $('#product-sub-total').html('৳' + productSubTotal.toFixed(2));
            $('#total-amount').html('৳' + productSubTotal.toFixed(2));


            $('#vat_total').html('৳' + productTotalVat.toFixed(2));

            $('#discount_total').html('৳' + productTotalDiscount.toFixed(2));


            var total = parseFloat(productSubTotal) +
                parseFloat(productTotalVat)  -
                parseFloat(productTotalDiscount) ;

            var due = parseFloat(total) - parseFloat(paid);
            $('#final-amount').html('৳' + total.toFixed(2));
            $('#due').html('৳' + due.toFixed(2));
            $('#total').val(total.toFixed(2));
            $('#due_total').val(due.toFixed(2));
            if (oldPaid!=0){
                $('#paid').val(oldPaid.toFixed(2));
            }else {
                $('#paid').val(total.toFixed(2));
            }

            calculateDue();

        }


        function initProduct() {
            $('.select2').select2();
        }
    </script>
@endsection
