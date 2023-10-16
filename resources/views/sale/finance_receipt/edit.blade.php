@extends('layouts.app')
@section('title','Finance Edit')

@section('style')
    <style>
        .form-control {
            width: 100%;
        }
        input.form-control.service_customer_name {
            width: 138px !important;
        }
        input.form-control.service_customer_mobile {
            width: 138px !important;
        }
        input.form-control.service_customer_address {
            width: 138px !important;
        }
        input.form-control.service_customer_email {
            width: 138px !important;
        }
        input.form-control.zone {
            width: 138px !important;
        }
        input.form-control.product {
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
        input.form-control {
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
    <form method="POST" enctype="multipart/form-data"  action="{{ route('finance_receipt.edit',['order'=>$order->id]) }}">
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

                                    <select id="warehouse" class="form-control select2 warehouse" style="width: 100%;" name="warehouse" data-placeholder="Select Warehouse" disabled>
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
                                <div class="form-group {{ $errors->has('type') ? 'has-error' :'' }}">
                                    <label>Sale Type</label>

                                    <select class="form-control" name="type" id="type" disabled>
                                        <option value="1" {{ old('type',$order->type) == 1 ? 'selected' : '' }}>Customer</option>
                                        <option value="2" {{ old('type',$order->type) == 2 ? 'selected' : '' }}>Dealer</option>
                                        <option value="3" {{ old('type',$order->type) == 3 ? 'selected' : '' }}>Distributor</option>
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
                                        <input type="text" class="form-control pull-right date-picker" id="install_date" name="install_date" disabled
                                               value="{{ date('Y-m-d',strtotime($order->install_date)) }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('install_date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('finance_note') ? 'has-error' :'' }}">
                                    <label>Finance Note</label>
                                    <input type="text" id="finance_note" name="finance_note" value="{{ old('finance_note', $order->finance_note) }}" class="form-control">
                                    @error('finance_note')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('sale_note') ? 'has-error' :'' }}">
                                    <label>Sale Note</label>

                                    <div>
                                        <input type="text" class="form-control pull-right" name="sale_note" disabled
                                               value="{{ $order->sale_note }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('sale_note')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('agm_note') ? 'has-error' :'' }}">
                                    <label>AGM Note</label>

                                    <div>
                                        <input type="text" class="form-control pull-right" name="agm_note" disabled
                                               value="{{$order->agm_note}}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('agm_note')
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
                                    <select class="form-control" id="customer_type" name="customer_type" disabled>
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

                                    <select class="form-control customer select2" style="width: 100%;" name="customer" disabled>
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
                                    <input type="text" disabled id="customer_name" name="customer_name" value="{{ old('customer_name',$order->customer_name) }}" class="form-control">
                                    @error('customer_name')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                                    <label>Customer Mobile</label>
                                    <input type="text" disabled id="mobile_no" value="{{ old('mobile_no',$order->mobile_no) }}" name="mobile_no" class="form-control" >
                                    @error('mobile_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('address') ? 'has-error' :'' }}">
                                    <label>Customer Address</label>
                                    <input type="text" disabled id="address" value="{{ old('address',$order->address) }}" name="address" class="form-control"  >
                                    @error('address')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('email') ? 'has-error' :'' }}">
                                    <label>Email</label>
                                    <input type="email" disabled id="email" value="{{ old('email',$order->email) }}" name="email" class="form-control"  >
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
                                    <select class="form-control" id="dealer_type" name="dealer_type" disabled>
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

                                    <select class="form-control dealer select2" style="width: 100%;" name="dealer" disabled>
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
                                    <input type="text" disabled id="dealer_name" name="dealer_name" value="{{ old('dealer_name',$order->dealer_name) }}" class="form-control">
                                    @error('dealer_name')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('dealer_mobile_no') ? 'has-error' :'' }}">
                                    <label>Dealer Mobile</label>
                                    <input type="text" disabled id="dealer_mobile_no" value="{{ old('dealer_mobile_no',$order->dealer_mobile_no) }}" name="dealer_mobile_no" class="form-control" >
                                    @error('dealer_mobile_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('dealer_address') ? 'has-error' :'' }}">
                                    <label>Dealer Address</label>
                                    <input type="text" disabled id="dealer_address" value="{{ old('dealer_address',$order->dealer_address) }}" name="dealer_address" class="form-control"  >
                                    @error('dealer_address')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('dealer_email') ? 'has-error' :'' }}">
                                    <label>Dealer Email</label>
                                    <input type="email" disabled id="dealer_email" value="{{ old('dealer_email',$order->dealer_email) }}" name="dealer_email" class="form-control"  >
                                    @error('dealer_email')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('dealer_code') ? 'has-error' :'' }}">
                                    <label>Dealer</label>
                                    <input type="text" disabled id="dealer_code" value="{{ old('dealer_code',$order->dealer_code) }}" name="dealer_code" class="form-control"  >
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
        <div class="row" id="all-distributor-area">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Distributor Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row" id="distributor-type-area">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('distributor_type') ? 'has-error' :'' }}">
                                    <label>Distributor Type </label>
                                    <select class="form-control" id="distributor_type" name="distributor_type">
                                        <option {{ old('distributor_type',$order->distributor_type) == 2 ? 'selected' : '' }} value="2">Old Distributor</option>
                                        <option {{ old('distributor_type',$order->distributor_type) == 1 ? 'selected' : '' }} value="1">New Distributor</option>
                                    </select>
                                    @error('distributor_type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="old_distributor_area">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('distributor') ? 'has-error' :'' }}">
                                    <label>Distributor </label>

                                    <select class="form-control dealer select2" style="width: 100%;" name="distributor">
                                        <option value="">Select Distributor </option>
                                        @foreach($distributors as $distributor)
                                            <option value="{{ $distributor->id }}"
                                                {{ empty(old('distributor')) ? ($errors->has('distributor') ? '' : ($order->distributor_id == $distributor->id ? 'selected' : '')) :
                                            (old('distributor') == $distributor->id ? 'selected' : '') }}>
                                                {{ $distributor->name.' - '.$distributor->mobile.' - '.$distributor->address }}</option>
                                        @endforeach
                                    </select>

                                    @error('distributor')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="new_distributor_area">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('distributor_name') ? 'has-error' :'' }}">
                                    <label>Distributor Name </label>
                                    <input type="text" id="distributor_name" name="distributor_name" value="{{ old('distributor_name',$order->distributor->name??'') }}" class="form-control">
                                    @error('distributor_name')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('distributor_mobile_no') ? 'has-error' :'' }}">
                                    <label>Distributor Mobile</label>
                                    <input type="text" id="dealer_mobile_no" value="{{ old('distributor_mobile_no',$order->distributor->mobile??'') }}"
                                           name="distributor_mobile_no" class="form-control" >
                                    @error('distributor_mobile_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('distributor_address') ? 'has-error' :'' }}">
                                    <label>Distributor Address</label>
                                    <input type="text" id="distributor_address" value="{{ old('distributor_address',$order->distributor->address??'') }}"
                                           name="distributor_address" class="form-control"  >
                                    @error('distributor_address')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('distributor_email') ? 'has-error' :'' }}">
                                    <label>Distributor Email</label>
                                    <input type="email" id="distributor_email" value="{{ old('distributor_email',$order->distributor->email??'') }}" name="distributor_email" class="form-control"  >
                                    @error('distributor_email')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('distributor_code') ? 'has-error' :'' }}">
                                    <label>Distributor Code</label>
                                    <input type="text" id="distributor_code" value="{{ old('distributor_code',$order->distributor->distributor_code??'') }}" name="distributor_code" class="form-control"  >
                                    @error('distributor_code')
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
                                            <th>Zone</th>
                                            <th>Category</th>
                                            <th>Sub Category</th>
                                            <th>Product</th>
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
                                                        <div class="form-group {{ $errors->has('zone.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 zone" style="width: 100%;" name="zone[]" >
                                                                <option value="">Select Zone</option>
                                                                @foreach($zones as $zone)
                                                                    <option value="{{ $zone->id }}" {{ old('zone.'.$loop->parent->index) == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
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
                                                            <input type="text" class="form-control zone" name="zone[]" readonly value="{{ $product->zone->name??''}}">
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control category" name="category[]" readonly value="{{ $product->productCategory->name}}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control sub_category" name="sub_category[]" readonly value="{{ $product->productSubCategory->name}}">
                                                        </div>
                                                    </td>
                                                    <td >
                                                        <div class="form-group">
                                                            <input type="text" class="form-control product" name="product[]" readonly value="{{ $product->product->name}}">
                                                        </div>
                                                    </td>
                                                    <td >
                                                        <div class="form-group">
                                                            <input type="number" step="any" class="form-control quantity" name="quantity[]" readonly  value="{{ $product->quantity}}">
                                                        </div>
                                                    </td>
                                                    <td >
                                                        <div class="form-group">
                                                            <input type="text" step="any" class="form-control unit_price" name="unit_price[]" readonly value="{{ $product->unit_price}}">
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
                                                {{--                                                <a role="button" class="btn btn-info btn-sm" id="btn-add-product">Add Product</a>--}}
                                            </td>
                                            <th colspan="5" class="text-right">Total Amount</th>
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
                                                <input type="text" class="form-control" name="vat" id="vat" readonly value="{{ old('vat',$order->vat_percentage) }}">
                                                <span id="vat_total">৳0.00</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="4" class="text-right">AIT (%)</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('ait') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="ait" id="ait" readonly value="{{ old('vat',$order->ait_percentage) }}">
                                                <span id="ait_total">৳0.00</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="4" class="text-right">Discount (Tk)</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('discount') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="discount" id="discount" value="{{ old('vat',$order->discount) }}">
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
                                                <input type="text" class="form-control" name="paid" id="paid" readonly value="{{ old('vat',$order->paid) }}">
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
@endsection

@section('script')

    <script>
        var oldPaid ={{ old('paid') ? old('paid') : 0 }};
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

            //Initialize Select2 Elements
            //$('.pre_filter_category').select2()

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
                    $("#all-distributor-area").hide();
                } else if($(this).val() == 2) {
                    $("#all-customer-area").hide();
                    $("#all-distributor-area").hide();
                    $("#all-dealer-area").show();
                    $("#multiple_customer").hide();
                } else {
                    $("#all-distributor-area").show();
                    $("#all-dealer-area").hide();
                    $("#all-customer-area").hide();
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

            $('#distributor_type').change(function (){
                var distributorType = $(this).val();
                if (distributorType == '1'){

                    $("#old_distributor_area").hide();
                    $("#new_distributor_area").show();
                }else{
                    $("#old_distributor_area").show();
                    $("#new_distributor_area").hide();
                }

            });

            $('#distributor_type').trigger("change");

            // select Category
            $('body').on('change','.category', function () {
                var categoryId = $(this).val();
                var itemCategory = $(this);

                itemCategory.closest('tr').find('.product_stock').html('');
                itemCategory.closest('tr').find('.sub_category').html('<option value="">Select Sub Category</option>');
                itemCategory.closest('tr').find('.product').html('<option value="">Select Product</option>');
                var selectedSubCategory = itemCategory.closest('tr').find('.sub_category').attr("data-selected-sub-category");

                if (categoryId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_sale_sub_category') }}",
                        data: {categoryId:categoryId}
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            // console.log(item.name);
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
            {{--$('body').on('change','.product', function () {--}}

            {{--    var product = $(this).val();--}}
            {{--    var itemProduct= $(this);--}}

            {{--    var warehouseId = $('#warehouse').val();--}}
            {{--    var saleType = $('#type').val();--}}
            {{--    var productId = itemProduct.closest('tr').find('.product').val();--}}
            {{--    var subCategoryId = itemProduct.closest('tr').find('.sub_category').val();--}}
            {{--    var categoryId = itemProduct.closest('tr').find('.category').val();--}}
            {{--    itemProduct.closest('tr').find('.unit_price').val('');--}}
            {{--    itemProduct.closest('tr').find('.product_stock').html('');--}}

            {{--    if (product != '') {--}}

            {{--        if ($('#type').val() == ''){--}}
            {{--            Swal.fire({--}}
            {{--                icon: 'error',--}}
            {{--                title: 'Oops...',--}}
            {{--                text: 'Please, Select Sale Type First !',--}}
            {{--            });--}}
            {{--            return false;--}}
            {{--        }--}}

            {{--        --}}{{--$.ajax({--}}
            {{--        --}}{{--    method: "GET",--}}
            {{--        --}}{{--    url: "{{ route('get_inventory_stock') }}",--}}
            {{--        --}}{{--    data: {warehouseId:warehouseId,--}}
            {{--        --}}{{--        subCategoryId:subCategoryId,--}}
            {{--        --}}{{--        productId:productId,--}}
            {{--        --}}{{--        categoryId:categoryId,--}}
            {{--        --}}{{--        saleType:saleType,--}}
            {{--        --}}{{--    }--}}
            {{--        --}}{{--}).done(function(response) {--}}
            {{--        --}}{{--    //itemProduct.closest('tr').find('.product_stock').html(response.totalStock);--}}
            {{--        --}}{{--    //itemProduct.closest('tr').find('.unit_price').val(response.sellingPrice);--}}

            {{--        --}}{{--    itemProduct.closest('tr').find('.quantity').attr({--}}
            {{--        --}}{{--        "max" : response.quantity,--}}
            {{--        --}}{{--    });--}}

            {{--        --}}{{--});--}}

            {{--    }--}}
            {{--});--}}

            $('.category').trigger('change');
            $('.sub_category').trigger('change');
            $('.product').trigger('change');

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

            $('body').on('keyup', '.quantity, .unit_price, #vat, #ait, #discount, #received_amount', function () {
                calculate();
            });

            $('body').on('change', '.quantity, .unit_price, #vat, #ait, #discount, #received_amount', function () {
                calculate();
            });

            $('body').on('keyup','#paid', function () {
                calculateDue();
            });
            $('body').on('change','#paid', function () {
                calculateDue();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }
            calculate();
            calculateDue();
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
            var paid= $('#paid').val();
            var due = parseFloat(total) - parseFloat(paid);
            $('#due').html('৳' + due.toFixed(2));
            $('#due_total').val(due.toFixed(2));
        }

        function calculate() {
            var productSubTotal = 0;

            var vat = $('#vat').val();
            var ait = $('#ait').val();
            var discount = $('#discount').val();
            var paid = $('#paid').val();

            if (vat == '' || vat < 0 || !$.isNumeric(vat))
                vat = 0;

            if (ait == '' || ait < 0 || !$.isNumeric(ait))
                ait = 0;

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
            $('#product-sub-total').html('৳' + productSubTotal.toFixed(2));
            $('#total-amount').html('৳' + productSubTotal.toFixed(2));

            var productTotalAit = (productSubTotal * ait) / 100;
            $('#product-sub-total').html('৳' + productSubTotal.toFixed(2));
            $('#total-amount').html('৳' + productSubTotal.toFixed(2));

            var productTotalDiscount = parseFloat(discount);

            $('#vat_total').html('৳' + productTotalVat.toFixed(2));
            $('#ait_total').html('৳' + productTotalAit.toFixed(2));

            $('#discount_total').html('৳' + productTotalDiscount.toFixed(2));

            var total = parseFloat(productSubTotal) +
                parseFloat(productTotalVat) + parseFloat(productTotalAit) -
                parseFloat(productTotalDiscount) ;




            var due = parseFloat(total) - parseFloat(paid);
            $('#final-amount').html('৳' + total.toFixed(2));
            $('#due').html('৳' + due.toFixed(2));
            $('#total').val(total.toFixed(2));
            $('#due_total').val(due.toFixed(2));
            if (oldPaid!=0){
                $('#paid').val(oldPaid.toFixed(2));
            }
            // else {
            //     $('#paid').val(total.toFixed(2));
            // }

            calculateDue();

        }

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


        function initProduct() {
            $('.select2').select2();
        }
    </script>
@endsection
