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
                <form enctype="multipart/form-data" action="{{route('booking.edit',['booking'=>$booking->id])}}" class="form-horizontal" method="post">
                    @csrf
                    <div class="row" id="all-customer-area">
                        <div class="col-md-12">
                            <div class="card">
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row" id="customer-type-area">
                                        <div class="col-md-3">
                                            <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                                                <label>Customer<span class="text-danger">*</span></label>

                                                <select class="form-control customer select2" style="width: 100%;" name="customer">
                                                    <option value="">Select Customer</option>

                                                    @foreach($customers as $customer)
                                                        <option value="{{ $customer->id }}" {{ old('customer',$booking->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->name.' - '.$customer->address }}</option>
                                                    @endforeach
                                                </select>

                                                @error('customer')
                                                <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group {{ $errors->has('technician') ? 'has-error' :'' }}">
                                                <label>Assign Technician <span class="text-danger">*</span></label>
                                                <select class="form-control technician select2" style="width: 100%;" name="technician[]" multiple>
                                                    @foreach($technicians as $technician)
                                                        <option value="{{ $technician->id }}" {{in_array($technician->id, old('technician',json_decode($booking->technician_id)) ?: []) ? "selected": ""}} >{{ $technician->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('technician')
                                                <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                                <label for="date">Booking Date <span class="text-danger">*</span></label>
                                                <input type="text" id="date" autocomplete="off"  value="{{ old('date',$booking->date ? \Carbon\Carbon::parse($booking->date)->format('d-m-Y') : date('d-m-Y')) }}" name="date" class="form-control date-picker">
                                                @error('date')
                                                <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group {{ $errors->has('delivery_date') ? 'has-error' :'' }}">
                                                <label for="date">Delivery Date <span class="text-danger">*</span></label>
                                                <input type="text" id="date" autocomplete="off"  value="{{ old('delivery_date',$booking->delivery_date ? \Carbon\Carbon::parse($booking->delivery_date)->format('d-m-Y') : date('d-m-Y')) }}" name="delivery_date" class="form-control date-picker" placeholder="delivery date">
                                                @error('delivery_date')
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
                                                            <input type="text" value="{{ old('quantity.'.$loop->index) }}" name="quantity[]" class="form-control quantity">
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @else
                                            @if(count($booking->bookingDetails) > 0)
                                                @foreach($booking->bookingDetails as $bookingDetail)
                                                 <tr class="product-item">
                                                <td>
                                                    <div class="form-group product_area">
                                                        <select class="form-control select2 product" style="width: 100%;" name="product[]">
                                                            @if ($bookingDetail->product_id)
                                                                <option value="{{ $bookingDetail->product_id }}" selected>{{ $bookingDetail->product->name.' - '.$bookingDetail->product->unit->name ?? '' }}</option>
                                                            @endif
                                                        </select>
                                                        <input type="hidden" value="{{ $bookingDetail->product->name ?? '' }}" name="product_name[]" class="product_name">
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" name="quantity[]" value="{{ $bookingDetail->quantity ?? '' }}" class="form-control quantity">
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
                                                </td>

                                            </tr>
                                                @endforeach
                                            @endif
                                        @endif
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="1" class="text-right">
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
                                    <div class="col-6 col-md-6"></div>
                                    <div class="col-6 col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th colspan="4" class="text-right">Advance Amount</th>
                                                <td>
                                                    <div class="form-group {{ $errors->has('advance_amount') ? 'has-error' :'' }}">
                                                        <input type="text" class="form-control text-center" name="advance_amount" id="advance_amount" value="{{ $booking->advance_amount ?? '' }}">
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
                        <a href="{{ route('manufacture_template') }}" id="btn-save" class="btn btn-default float-right">Cancel</a>
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
                    <input type="text" name="quantity[]" class="form-control quantity">
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

            $('body').on('keyup', '.advance_amount,.quantity', function () {

                calculate();
            });
            calculate();
        });
        function calculate() {
            var totalAmount = 0;
            var totalQuantity = 0;

            $('.product-item').each(function(i, obj) {

                var quantity = $('.quantity:eq('+i+')').val();
                var advance_amount = $('.advance_amount:eq('+i+')').val();

                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (advance_amount == '' || advance_amount < 0 || !$.isNumeric(advance_amount))
                    advance_amount = 0;

                totalAmount += parseFloat(advance_amount);
                totalQuantity += parseFloat(quantity);
            });

            $('#total-amount').html(jsNumberFormat(totalAmount));
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

        }

    </script>
@endsection
