@extends('layouts.app')
@section('title','Product Service')

@section('style')
    <style>
        .form-control {
            width: 100%;
        }

        input.form-control.service_customer_mobile {
            width: 138px !important;
        }
        input.form-control.service_customer_email {
            width: 138px !important;
        }
        select.form-control.zone {
            width: 138px !important;
        }
        select.form-control.product {
            width: 138px !important;
        }
        input.form-control.selling_price{
            width: 130px;
        }
        input.form-control.stock{
            width: 130px;
        }
        input.form-control.quantity{
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
    <form method="POST" enctype="multipart/form-data"  action="{{ route('service_order.create') }}">
        @csrf

        <div class="row" id="">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Service Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('product_type') ? 'has-error' :'' }}">
                                    <label>Product Type <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="product_type" name="product_type">
                                        <option {{ old('product_type') == 1 ? 'selected' : '' }} value="1">Own Product</option>
                                        <option {{ old('product_type') == 2 ? 'selected' : '' }} value="2">Other Product</option>
                                        <option {{ old('product_type') == 3 ? 'selected' : '' }} value="3">Re Service</option>
                                    </select>
                                    @error('product_type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('service_payment_type') ? 'has-error' :'' }}">
                                    <label>Service Payment Type <span class="text-danger">*</span></label>
                                    <select class="form-control select2 service_payment_type" id="service_payment_type" name="service_payment_type" required>
                                        <option value="" disabled selected>Paid Type</option>
                                        <option {{ old('service_payment_type') == 1 ? 'selected' : '' }} value="1">Paid</option>
                                        <option {{ old('service_payment_type') == 2 ? 'selected' : '' }} value="2">Unpaid</option>
                                    </select>
                                    @error('service_payment_type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                                    <label>Customer<span class="text-danger">*</span></label>
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
                                <div class="form-group {{ $errors->has('note') ? 'has-error' :'' }}">
                                    <label>Note</label>
                                        <input type="text" class="form-control" name="note" id="note" value="{{ old('note') }}">
                                    @error('note')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Products Details</h3>
                    </div>
                    <div class="card-body">
                        <div id="own_product_area">
                          <div class="row">
                              <div class="col-xs-9 col-md-4">
                                  <div class="form-group">
                                      <label for="supporting_document">Serial Search</label>
                                      <input autocomplete="off" type="text" id="serial_no" class="form-control" placeholder="Enter Serial No">
                                  </div>
                              </div>
                          </div>
                          <div class="row" id="multiple-sale-product">
                              <div class="col-md-12">
                                  <div class="table-responsive">
                                      <table class="table table-bordered table-custom-form">
                                          <thead>
                                          <tr>
                                              <th style="white-space: nowrap; min-width: 160px;">Serial</th>
                                              <th style="white-space: nowrap; min-width: 160px;">Product</th>
                                              <th style="white-space: nowrap; min-width: 140px;">Sale Date</th>
                                              <th style="white-space: nowrap; min-width: 140px;">Warranty</th>
                                              <th style="white-space: nowrap;"></th>
                                          </tr>
                                          </thead>

                                          <tbody id="product-container">
{{--                                          Search product details here--}}
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div>
                        <div id="other_product_area">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group {{ $errors->has('new_serial_no') ? 'has-error' :'' }}">
                                        <label>Serial No <span class="text-danger">*</span></label>
                                        <input type="text" id="new_serial_no" name="new_serial_no" value="{{ old('new_serial_no') }}" class="form-control" placeholder="Enter Serial No">
                                        @error('new_serial_no')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group {{ $errors->has('product_name') ? 'has-error' :'' }}">
                                        <label>Product Name <span class="text-danger">*</span></label>
                                        <input type="text" id="product_name" value="{{ old('product_name') }}" name="product_name" class="form-control" placeholder="product name">
                                        @error('product_name')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div id="reservice_product_area">
                            <div class="row">
                                <div class="col-xs-9 col-md-4">
                                    <div class="form-group">
                                        <label for="supporting_document">Serial Search</label>
                                        <input autocomplete="off" type="text" id="service_serial_no" class="form-control" placeholder="Enter Serial No">
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-custom-form">
                                            <thead>
                                            <tr>
                                                <th style="white-space: nowrap; min-width: 160px;">Serial</th>
                                                <th style="white-space: nowrap; min-width: 160px;">Product</th>
                                                <th style="white-space: nowrap; min-width: 140px;">Service Date</th>
                                                <th style="white-space: nowrap; min-width: 140px;">Service Warranty</th>

                                            </tr>
                                            </thead>

                                            <tbody id="service-product-container">
                                            {{--                                          Search product details here--}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="multiple-customer-sale-type">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header with-border">
                                        <h3 class="card-title">Service Item (Raw Materials)</h3>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th style="white-space: nowrap; min-width: 140px;">Raw Material</th>
                                                <th style="white-space: nowrap">Unit</th>
                                                <th style="white-space: nowrap">Stock</th>
                                                <th style="white-space: nowrap">Unit Price</th>
                                                <th style="white-space: nowrap">Servicce Quantity</th>
                                                <th style="white-space: nowrap">Selling Price</th>
                                                <th style="white-space: nowrap">Total Cost</th>
                                                <th></th>
                                            </tr>
                                            </thead>

                                            <tbody id="service-container">
                                            @if (old('service_row_product') != null && sizeof(old('service_row_product')) > 0)
                                                @foreach(old('service_row_product') as $item)
                                                    <tr class="service-item">
                                                        <td>
                                                            <div class="form-group {{ $errors->has('service_row_product.'.$loop->index) ? 'has-error' :'' }}">
                                                                <select class="form-control service_row_product select2" style="width: 100%; min-width: 140px;" name="service_row_product[]"  >
                                                                    <option value="">Select Product</option>
                                                                    @foreach($rawMaterials as $product)
                                                                        <option value="{{ $product->id }}" {{ old('service_row_product.'.$loop->parent->index) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('row_product_unit.'.$loop->index) ? 'has-error' :'' }}">
                                                                <input type="text" step="any" class="form-control text-center row_product_unit" name="row_product_unit[]" value="{{ old('row_product_unit.'.$loop->index) }}" readonly>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('stock.'.$loop->index) ? 'has-error' :'' }}">
                                                                <input type="text" step="any" class="form-control text-center stock" name="stock[]" value="{{ old('stock.'.$loop->index) }}" readonly>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="form-group {{ $errors->has('row_product_unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                                <input type="text" step="any" class="form-control row_product_unit_price" name="row_product_unit_price[]" value="{{ old('row_product_unit_price.'.$loop->index) }}" readonly>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('row_product_quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                                <input type="text" step="any" class="form-control text-center row_product_quantity" name="row_product_quantity[]" value="{{ old('row_product_quantity.'.$loop->index) }}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('row_product_selling_price.'.$loop->index) ? 'has-error' :'' }}">
                                                                <input type="text" step="any" class="form-control row_product_selling_price" name="row_product_selling_price[]" value="{{ old('row_product_selling_price.'.$loop->index) }}">
                                                            </div>
                                                        </td>
                                                        <td  class="total-service-cost text-right">0.00</td>
                                                        <td class="text-center">
                                                            <a role="button" class="btn btn-danger btn-sm btn-remove-service"><i class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td>
                                                    <a role="button" class="btn btn-primary btn-sm" id="btn-add-service"><i class="fa fa-plus"></i></a>
                                                </td>
                                                <th id=""></th>
                                                <th id=""></th>
                                                <th id="total-service-quantity" colspan="2" class="text-right">0.00</th>
                                                <th colspan="1" class="text-right">Total Amount</th>
                                                <th id="total-service-amount" class="text-right">0.00</th>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th colspan="6" class="text-right">Service Warranty</th>
                                                <td>
                                                    <div class="form-group {{ $errors->has('service_warranty') ? 'has-error' :'' }}">
                                                        <input type="text" class="form-control text-center" name="service_warranty" id="service_warranty" value="{{ old('service_warranty') }}">
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="payment_area">
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
                                                        <option {{ old('payment_type') == 1 ? 'selected' : '' }} value="1">Cheque</option>
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
                                                        <th colspan="4" class="text-right">Raw Material</th>
                                                        <th id="service_subtotal">৳0.00</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" class="text-right">Service Charge</th>
                                                        <td>
                                                            <div class="form-group {{ $errors->has('service_charge') ? 'has-error' :'' }}">
                                                                <input type="text" class="form-control text-center" name="service_charge" id="service_charge" value="{{ empty(old('service_charge')) ? ($errors->has('service_charge') ? '' : '0') : old('service_charge') }}">
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


    <template id="template-service">
        <tr class="service-item">
            <td>
                <div class="form-group">
                    <select class="form-control service_row_product select2" style="width: 100%; min-width: 140px;" name="service_row_product[]"  >
                        <option value="">Select Product</option>
                        @foreach($rawMaterials as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control row_product_unit text-center" name="row_product_unit[]" readonly>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control stock text-center" name="stock[]" readonly>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control row_product_unit_price text-center" name="row_product_unit_price[]" readonly>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control row_product_quantity text-center" name="row_product_quantity[]">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" step="any" class="form-control row_product_selling_price" name="row_product_selling_price[]">
                </div>
            </td>
            <td  class="total-service-cost text-right"> 0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove-reservice"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
    </template>
@endsection

@section('script')

    <script>
        var oldPaid ={{ old('paid') ? old('paid') : 0 }};
        $(function () {
            initProduct();
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

            $("#service_payment_type").change(function (){
                var serviceType = $(this).val();
                    if(serviceType == 2){
                        $("#payment_area").hide();
                    }else{
                        $("#payment_area").show();
                    }
            })
            $("#service_payment_type").trigger("change");

            //product type
            $('#product_type').change(function (){
                var productType = $(this).val();
                if (productType == '1'){
                    $("#own_product_area").show();
                    $("#other_product_area").hide();
                    $("#reservice_product_area").hide();
                }else if (productType == '2'){
                    $("#other_product_area").show();
                    $("#own_product_area").hide();
                    $("#reservice_product_area").hide();
                }else if (productType == '3'){
                    $("#reservice_product_area").show();
                    $("#other_product_area").hide();
                    $("#own_product_area").hide();
                }


            });

            $('#product_type').trigger("change");

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




            $('body').on('change','.service_row_product', function () {
                var serviceId = $(this).val();
                //alert(serviceId);
                var itemService= $(this);
                itemService.closest('tr').find('.stock').val('');

                if (serviceId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_service_product_details') }}",
                        data: {serviceId:serviceId}
                    }).done(function(response) {
                        if(response.inventory){
                            itemService.closest('tr').find('.row_product_unit_price').val(response.inventory.unit_price);
                        }
                        if(response.product) {
                            itemService.closest('tr').find('.stock').val(response.product.quantity);
                            itemService.closest('tr').find('.row_product_unit').val(response.product.unit.name);
                        }
                        calculate();
                    }).fail(function () {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Out Of Stock!',
                        });
                    });

                }
            });
            $('.service_row_product').trigger('change');


            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
            });

            // Service
            $('#btn-add-service').click(function () {
                var html = $('#template-service').html();
                var item = $(html);

                $('#service-container').append(item);

                $( ".date-picker" ).datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    changeYear: true,
                });

                if ($('.product-item').length + $('.service-item').length >= 1 ) {
                    $('.btn-remove').show();
                    $('.btn-remove-service').show();
                }
                initProduct();
                intSelect2();
            });

            $('body').on('click', '.btn-remove-service', function () {
                $(this).closest('.service-item').remove();
                calculate();

                if ($('.product-item').length + $('.service-item').length <= 1 ) {
                    $('.btn-remove').hide();
                    $('.btn-remove-service').hide();
                }
            });
            //End Service

            $('body').on('keyup',' .selling_price, .quantity, #advance_deduct, #paid,#service_charge, #discount, .stock,.row_product_quantity, .row_product_selling_price', function () {
                calculate();
            });

            $('body').on('change',' .selling_price,.quantity, #advance_deduct, #paid,#service_charge, #discount, .stock,.row_product_quantity, .row_product_selling_price', function () {
                calculate();
            });


            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }
            calculate();

        });

        function calculate() {
            var productSubTotal = 0;
            var productSubQuantity = 0;
            var paid = $('#paid').val() || 0;
            var service_charge = $('#service_charge').val() || 0;
            var advance_deduct = $('#advance_deduct').val() || 0;
            var discount = $('#discount').val();

            //Service
            var serviceSubTotal = 0;
            var serviceSubQuantity = 0;

            if (discount == '' || discount < 0 || !$.isNumeric(discount))
                discount = 0;

            //Service
            $('.service-item').each(function(i, obj) {
                var row_product_quantity = $('.row_product_quantity:eq('+i+')').val();
                var row_product_selling_price = $('.row_product_selling_price:eq('+i+')').val();

                if (row_product_quantity == '' || row_product_quantity < 0 || !$.isNumeric(row_product_quantity))
                    row_product_quantity = 0;

                if (row_product_selling_price == '' || row_product_selling_price < 0 || !$.isNumeric(row_product_selling_price))
                    row_product_selling_price = 0;

                $('.total-service-cost:eq('+i+')').html('৳' + (row_product_quantity * row_product_selling_price).toFixed(2) );
                serviceSubTotal += row_product_quantity * row_product_selling_price;
                serviceSubQuantity += parseInt(row_product_quantity);
            });

            parseFloat(paid)

            $('#total-amount').html('৳' + productSubTotal.toFixed(2));
            $('#total-service-amount').html('৳' + serviceSubTotal.toFixed(2));
            $('#service_subtotal').html('৳' + serviceSubTotal.toFixed(2));
            $('#product-sub-total').html('৳' + (productSubTotal + serviceSubTotal).toFixed(2));
            $('#total-quantity').html('' + productSubQuantity .toFixed(2));
            $('#total-service-quantity').html('' + serviceSubQuantity .toFixed(2));

            var productTotalDiscount = parseFloat(discount);
            $('#discount_total').html('৳' + productTotalDiscount.toFixed(2));

            var total = parseFloat(productSubTotal) + parseFloat(serviceSubTotal) - parseFloat(productTotalDiscount)+parseFloat(service_charge);

            $('#final-amount').html('৳' + total.toFixed(2));
            $('#total').val(total.toFixed(2));


            if(parseFloat(productSubTotal) < parseFloat(advance_deduct))
                var due = 0;
            else

                var due = parseFloat(total)-parseFloat(paid)-parseFloat(advance_deduct);

            $('#due').html('৳' + due.toFixed(2));
            $('#due_total').val( due.toFixed(2));
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
                //$('#account_name:eq('+index+')').val(data.text);
                $('#account_name').val(data.text);
            });
        }


        function initProduct() {
            $('.select2').select2();
            $('.service_row_product').select2();
        }
    </script>
    <!-- Add this script at the bottom of your Blade view -->
    <script>
        $(document).ready(function() {
            $('#serial_no').keydown(function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); // Prevent form submission
                    searchProduct();
                }
            });

            function searchProduct() {
                var serial = $('#serial_no').val();

                if (isSerialAlreadyAdded(serial)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Serial number already exists below.',
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('search_service_product') }}",
                    method: 'GET',
                    data: { serial: serial },
                    success: function(response) {
                        if (response.success) {
                            // Append the product details to the table
                            $('.product-item').remove();
                            var newRow = '<tr class="product-item">' +
                                '<td><input type="text" class="form-control serial" name="serial" value="' + response.product.serial + '" readonly></td>' +
                                '<td><input type="text" class="form-control product" name="product" value="' + response.product.name + '" readonly></td>' +
                                '<td style="display: none"><input type="text" class="form-control product_id" name="product_id" value="' + response.product.product_id + '" readonly></td>' +
                                '<td><input type="text" class="form-control sale_date" name="sale_date" value="' + response.product.date + '" readonly></td>' +
                                '<td><input type="text" class="form-control warranty" name="warranty" value="' + response.product.warranty + '" readonly></td>' +
                                '<td class="text-center"><a role="button" class="btn btn-danger btn-sm btn-remove">X</a></td>' +
                                '</tr>';

                            $('#product-container').append(newRow);

                            // Clear the input fields
                            $('#serial_no').val('');

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text:response.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Input fields empty please enter serial number !',
                        });
                    }
                });
            }

            // Check if a serial number already exists
            function isSerialAlreadyAdded(serial) {
                var serials = $('.serial').map(function() {
                    return $(this).val();
                }).get();

                return serials.includes(serial);
            }

            // Re Service

            $('#service_serial_no').keydown(function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); // Prevent form submission
                    searchServiceProduct();
                }
            });


            function searchServiceProduct() {
                var serviceSerial = $('#service_serial_no').val();

                if (isServiceSerialAlreadyAdded(serviceSerial)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Serial number already exists below.',
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('search_re_service_product') }}",
                    method: 'GET',
                    data: { serviceSerial: serviceSerial },
                    success: function(response) {
                        if (response.success) {
                            // Append the product details to the table
                            $('.product-item1').remove();
                            var newRow = '<tr class="product-item1">' +
                                '<td><input type="text" class="form-control serial" name="serial" value="' + response.product.serial + '" readonly></td>' +
                                '<td><input type="text" class="form-control product" name="product" value="' + response.product.name + '" readonly></td>' +
                                '<td style="display: none"><input type="text" class="form-control product_id" name="product_id" value="' + response.product.product_id + '" readonly></td>' +
                                '<td><input type="text" class="form-control sale_date" name="sale_date" value="' + response.product.date + '" readonly></td>' +
                                '<td><input type="text" class="form-control warranty" name="warranty" value="' + response.product.warranty + '" readonly></td>' +
                                '</tr>';

                            $('#service-product-container').append(newRow);

                            // Clear the input fields
                            $('#serviceSerial').val('');

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text:response.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Input fields empty please enter serial number !',
                        });
                    }
                });
            }

            // Check if a serial number already exists
            function isServiceSerialAlreadyAdded(serial) {
                var serviceSerials = $('.service_serial_no').map(function() {
                    return $(this).val();
                }).get();

                return serviceSerials.includes(serial);
            }
        });
    </script>


@endsection
