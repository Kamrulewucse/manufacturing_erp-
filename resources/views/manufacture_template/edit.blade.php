@extends('layouts.app')
@section('title', 'Manufacture Template Edit')
@section('style')
    <style>
        .product_area>.select2 {
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
                    <h3 class="card-title">Manufacture Template Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data"
                    action="{{ route('manufacture_template.edit', ['configProduct' => $configProduct->id]) }}"
                    class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('finished_goods') ? 'has-error' : '' }}">
                                    <label for="finished_goods">Finished Goods <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="finished_goods" name="finished_goods">
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ old('finished_goods', $configProduct->product_id) == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('finished_goods')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive-sm">
                                    <table class="table table-bordered table-custom-form">
                                        <thead>
                                            <tr>
                                                <th width="48%">Product <span class="text-danger">*</span></th>
                                                <th class="text-center" width="10%">Unit</th>
                                                <th class="text-center">Quantity <span class="text-danger">*</span></th>
                                                <th class="text-center" width="10%">Price</th>
                                                <th class="text-center">Total Price<span class="text-danger">*</span></th>
                                                <th class="text-center">Loss Quantity(%) <span class="text-danger">*</span>
                                                </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="product-container">
                                            @if (old('product') != null && sizeof(old('product')) > 0)
                                                @foreach (old('product') as $item)
                                                    <tr class="product-item">
                                                        <td>
                                                            <div
                                                                class="form-group product_area {{ $errors->has('product.' . $loop->index) ? 'has-error' : '' }}">
                                                                <select class="form-control select2 product"
                                                                    name="product[]">
                                                                    <option value="">Select Product</option>
                                                                    @if (old('product.' . $loop->index) != '')
                                                                        <option value="{{ old('product.' . $loop->index) }}"
                                                                            selected>
                                                                            {{ old('product_name.' . $loop->index) }}
                                                                        </option>
                                                                    @endif
                                                                </select>
                                                                <input type="hidden" name="product_name[]"
                                                                    class="product_name"
                                                                    value="{{ old('product_name.' . $loop->index) }}">
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="unit_name"></div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="form-group {{ $errors->has('quantity.' . $loop->index) ? 'has-error' : '' }}">
                                                                <input type="text"
                                                                    value="{{ old('quantity.' . $loop->index) }}"
                                                                    name="quantity[]" class="form-control quantity">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="form-group {{ $errors->has('unit_price.' . $loop->index) ? 'has-error' : '' }}">
                                                                <input type="text"
                                                                    value="{{ old('unit_price.' . $loop->index) }}"
                                                                    name="unit_price[]" class="form-control unit_price"
                                                                    readonly>
                                                            </div>
                                                        </td>
                                                        <td class="total-cost"></td>
                                                        <td>
                                                            <div
                                                                class="form-group {{ $errors->has('loss_quantity_percent.' . $loop->index) ? 'has-error' : '' }}">
                                                                <input type="text"
                                                                    value="{{ old('loss_quantity_percent.' . $loop->index) }}"
                                                                    name="loss_quantity_percent[]"
                                                                    class="form-control loss_quantity_percent">
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <a role="button" style="display: none"
                                                                class="btn btn-danger btn-sm btn-remove"><i
                                                                    class="fa fa-trash"></i></a>
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            @else
                                                @if (count($configProduct->configProductDetails) > 0)
                                                    @foreach ($configProduct->configProductDetails as $configProductDetail)
                                                        <tr class="product-item">
                                                            <td>
                                                                <div class="form-group product_area">
                                                                    <select class="form-control select2 product"
                                                                        style="width: 100%;" name="product[]">
                                                                        <option value="">Select Product</option>
                                                                        @if ($configProductDetail->product_id)
                                                                            <option
                                                                                value="{{ $configProductDetail->product_id }}"
                                                                                selected>
                                                                                {{ $configProductDetail->product->name . ' - ' . $configProductDetail->product->unit->name ?? '' }}
                                                                            </option>
                                                                        @endif
                                                                    </select>
                                                                    <input type="hidden"
                                                                        value="{{ $configProductDetail->product->name ?? '' }}"
                                                                        name="product_name[]" class="product_name">
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="unit_name">
                                                                    {{ $configProductDetail->product->unit->name ?? '' }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="text"
                                                                        value="{{ $configProductDetail->quantity }}"
                                                                        name="quantity[]" class="form-control quantity">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input readonly type="text" name="unit_price[]"
                                                                        value="{{ $configProductDetail->product->unit_price ?? '' }}"
                                                                        class="form-control unit_price">
                                                                </div>
                                                            </td>
                                                            <td class="total-cost"></td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="text"
                                                                        value="{{ $configProductDetail->loss_quantity_percent }}"
                                                                        name="loss_quantity_percent[]"
                                                                        class="form-control loss_quantity_percent">
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <a role="button" style="display: none"
                                                                    class="btn btn-danger btn-sm btn-remove"><i
                                                                        class="fa fa-trash"></i></a>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr class="product-item">
                                                        <td>
                                                            <div class="form-group product_area">
                                                                <select class="form-control select2 product"
                                                                    style="width: 100%;" name="product[]">
                                                                    <option value="">Select Product</option>
                                                                </select>
                                                                <input type="hidden" name="product_name[]"
                                                                    class="product_name">
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="unit_name"></div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" name="quantity[]"
                                                                    class="form-control quantity">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input readonly type="text" name="unit_price[]"
                                                                    class="form-control unit_price">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" name="loss_quantity_percent[]"
                                                                    class="form-control loss_quantity_percent">
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <a role="button" style="display: none"
                                                                class="btn btn-danger btn-sm btn-remove"><i
                                                                    class="fa fa-trash"></i></a>
                                                        </td>

                                                    </tr>
                                                @endif
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="" class="text-right">Extra Cost</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>
                                                    <input type="text" class="form-control extra_cost"
                                                        value="{{ $configProduct->extra_cost ?? '0' }}"
                                                        name="extra_cost">
                                                </th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th colspan="1" class="text-right">
                                                    Total
                                                </th>
                                                <th></th>
                                                <th class="text-center" id="total-quantity">0</th>
                                                <th></th>
                                                <th class="text-center" id="total-price">0</th>
                                                <th class="text-center"></th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th colspan="5" class="text-left">
                                                    <a role="button" class="btn btn-primary btn-sm"
                                                        id="btn-add-product"><i class="fa fa-plus"></i></a>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button id="btn-save" type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('manufacture_template') }}" id="btn-save"
                            class="btn btn-default float-right">Cancel</a>
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
            <td class="text-center">
                <div class="unit_name"></div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" name="quantity[]" class="form-control quantity">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input readonly type="text" name="unit_price[]" class="form-control unit_price">
                </div>
            </td>
            <td class="total-cost"></td>
            <td>
                <div class="form-group">
                    <input type="text" name="loss_quantity_percent[]" class="form-control loss_quantity_percent">
                </div>
            </td>
            <td class="text-center">
                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i
                        class="fa fa-trash"></i></a>
            </td>

        </tr>
    </template>
@endsection
@section('script')
    <script>
        $(function() {
            intSelect2();
            formSubmitConfirm('btn-save');

            $('body').on('change', '.product', function() {
                var productId = $(this).val();
                var itemProduct = $(this);
                itemProduct.closest('tr').find('.unit_name').html('');
                if (productId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product_details') }}",
                        data: {
                            productId: productId
                        }
                    }).done(function(response) {
                        //console.log(response.product.unit_price);
                        itemProduct.closest('tr').find('.unit_name').html(response.product.unit
                            .name);
                        itemProduct.closest('tr').find('.unit_price').val(response.product
                            .unit_price);
                        calculate();
                    });
                }
            });
            $('.product').trigger('change');

            $('#btn-add-product').click(function() {
                var html = $('#product-template').html();
                var item = $(html);

                $('#product-container').append(item);

                intSelect2();

                if ($('.product-item').length >= 1) {
                    $('.btn-remove').show();
                }

                calculate();
            });

            $('body').on('click', '.btn-remove', function() {
                $(this).closest('.product-item').remove();
                if ($('.product-item').length <= 1) {
                    $('.btn-remove').hide();
                }
                calculate();
            });

            if ($('.product-item').length <= 1) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }

            $('body').on('keyup', '.quantity, .extra_cost, .price_name, .unit_price', function() {
                calculate();
            });
            calculate();
        });

        function calculate() {
            var totalQuantity = 0;
            var totalPrice = 0;
            var totalUnitPrice = 0;
            var subConsumptionCost = 0;
            var totalCount = 0;
            var extraCost = parseFloat($(".extra_cost").val());

            if (isNaN(extraCost) || extraCost < 0) {
                extraCost = 0;
            }

            $('.product-item').each(function(i, obj) {
                var quantity = $('.quantity:eq(' + i + ')').val();
                var unit_price = parseFloat($('.unit_price:eq(' + i + ')').val());

                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (isNaN(unit_price) || unit_price < 0) {
                    unit_price = 0;
                }

                let totalConsumptionCost = parseFloat(quantity) * unit_price;

                totalQuantity += parseFloat(quantity);
                totalUnitPrice += unit_price;
                totalCount++;

                $('.total-cost:eq(' + i + ')').text(totalConsumptionCost.toFixed(2));

                subConsumptionCost += parseFloat(totalConsumptionCost);
            });
            //var totalExtra = extraCost * totalCount;
            // var subTotalAmount = totalUnitPrice + extraCost;
            $('#total-quantity').html(jsNumberFormat(totalQuantity));
            // $('#total-price').html(jsNumberFormat(subTotalAmount));
            $('#total-price').html(jsNumberFormat(subConsumptionCost + extraCost));

        }

        function intSelect2() {
            $('.select2').select2();


            $('.product').select2({
                ajax: {
                    url: function(params) {
                        var finishedGoodsId = $('#finished_goods').val();
                        return "{{ route('product.json') }}" + "?finishedGoodsId=" + finishedGoodsId;
                    },
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $('.product').on('select2:select', function(e) {
                let data = e.params.data;
                let index = $(".product").index(this);
                $('.product_name:eq(' + index + ')').val(data.text);
            });

        }
    </script>
@endsection