@extends('layouts.app')
@section('title','Manufacturer')
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-default">
                <div class="card-header">
                    <h3 class="card-title">Manufacture Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{route('manufacture.create')}}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('template_product') ? 'has-error' :'' }}">
                                    <label for="template_product">Choose Template  <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="template_product" style="width: 100%;" name="template_product">
                                        <option value="">Select Template</option>
                                        @foreach($configProducts as $configProduct)
                                            <option value="{{ $configProduct->id }}" {{ old('template_product') ==$configProduct->id  ? 'selected' : '' }}>{{ $configProduct->finishedGoods->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('template_product')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('finished_goods') ? 'has-error' :'' }}">
                                    <label for="finished_goods">Finished Goods <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="finished_goods" style="width: 100%;" name="finished_goods">
                                        <option value="">Select Finished Goods</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('finished_goods') ==$product->id  ? 'selected' : '' }}>{{ $product->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('finished_goods')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('finished_goods_quantity') ? 'has-error' :'' }}">
                                    <label for="finished_goods_quantity">Finished Goods Quantity <span class="text-danger">*</span></label>
                                    <input type="text" id="finished_goods_quantity"  value="{{ old('finished_goods_quantity') }}" name="finished_goods_quantity" class="form-control" placeholder="Enter Quantity">
                                    @error('finished_goods_quantity')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('finished_goods_unit_price') ? 'has-error' :'' }}">
                                    <label for="finished_goods_unit_price">Finished Goods Sale Unit Price <span class="text-danger">*</span></label>
                                    <input type="text" id="finished_goods_unit_price"  value="{{ old('finished_goods_unit_price') }}" name="finished_goods_unit_price" class="form-control" placeholder="Enter Selling Unit Price">
                                    @error('finished_goods_unit_price')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                    <label for="date">Manufacture Date <span class="text-danger">*</span></label>
                                    <input type="text" id="date" autocomplete="off"  value="{{ old('date',date('d-m-Y')) }}" name="date" class="form-control date-picker" placeholder="manufacture date">
                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr style="background-color: #E1694C;">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive-sm">
                                    <table class="table table-bordered  table-custom-form">
                                        <thead>
                                        <tr>
                                            <th colspan="4"></th>
                                            <th colspan="6" class="text-center">Consumption</th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2" class="text-center">S/L</th>
                                            <th rowspan="2" class="text-center">Row Product Item <span class="text-danger">*</span></th>
                                            <th rowspan="2" class="text-center">Unit Price</th>
                                            <th rowspan="2" class="text-center">Remain Qty</th>
                                            <th class="text-center">Per Unit Qty <span class="text-danger">*</span></th>
                                            <th class="text-center">Total Qty<span class="text-danger">*</span></th>
                                            <th class="text-center">Total Cost Value</th>
                                            <th class="text-center">Loss Qty(%) <span class="text-danger">*</span></th>
                                            <th class="text-center">Total Loss Qty</th>
                                            <th class="text-center">Total Loss Value</th>
                                            <th rowspan="2"></th>
                                        </tr>
                                        </thead>
                                        <tbody id="row-material-container"></tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-right">Extra Cost</th>
                                            <th></th>
                                            <th>
                                                <input type="text" class="form-control extra_cost"  value="{{ old('extra_cost', 0) }}" name="extra_cost" readonly>
                                            </th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th colspan="5" class="text-right">Sub Total</th>
                                            <th  id="total-consumption-qty"></th>
                                            <th  id="total-consumption-cost"></th>
                                            <th  id="total-consumption-loss-qty"></th>
                                            <th  id="total-consumption-loss-cost"></th>
                                            <th></th>
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
                        <a  href="{{ route('finished_goods') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>

@endsection
@section('script')
    <script>
        $(function (){
            intSelect2();
            formSubmitConfirm('btn-save');
            var finished_goods_finished_goods_unit_price_old = '{{ old('finished_goods_unit_price') }}';

            $('body').on('change','#template_product', function () {
                $("#row-material-container").html(' ');
                var templateId = $(this).val();
                if (templateId !== '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_template_details') }}",
                        data: { templateId: templateId }
                    }).done(function(response) {
                        $("#row-material-container").html(response.html);
                        if(finished_goods_finished_goods_unit_price_old == ''){
                            $("#finished_goods_unit_price").val(response.last_price > 0 ? response.last_price : '');
                            $(".extra_cost").val(response.extra_cost ? response.extra_cost : 0);
                        }
                        calculate();
                    });
                }

            });
            $('#template_product').trigger('change');
            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
                calculate();
            });

            $('body').on('keyup', '.quantity,#finished_goods_quantity,#extra_cost', function () {
                calculate();
            });

            calculate();
        });
        function calculate() {
            var subConsumptionQty = 0;
            var extraAmount = 0;
            var subConsumptionLossQty = 0;
            var subConsumptionCost = 0;
            var subConsumptionLossCost = 0;
            var finishedGoodsQuantity = $("#finished_goods_quantity").val();
            var finishedGoodsExtra = $("#finished_goods_quantity").val();
            //alert(finishedGoodsExtra);
            var extra_cost = $(".extra_cost").val();
            //alert(extra_cost);

            if (finishedGoodsQuantity === '' || finishedGoodsQuantity < 0 || !$.isNumeric(finishedGoodsQuantity))
                finishedGoodsQuantity = 1;

            if (finishedGoodsExtra === '' || finishedGoodsExtra < 0 || !$.isNumeric(finishedGoodsExtra))
                finishedGoodsExtra = 0;

            if (extra_cost === '' || extra_cost < 0 || !$.isNumeric(extra_cost))
                extra_cost = 0;

            $('.product-item').each(function(i, obj) {

                var quantity = $('.quantity:eq('+i+')').val();
                var lossQuantityPercent = $('.loss_quantity_percent:eq('+i+')').val();
                var remainQty = $('.remain-qty:eq('+i+')').text();
                var unit_price = $('.unit_price:eq('+i+')').text();

                if (quantity === '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (unit_price === '' || unit_price < 0 || !$.isNumeric(unit_price))
                    unit_price = 0;

                if (lossQuantityPercent === '' || lossQuantityPercent < 0 || !$.isNumeric(lossQuantityPercent))
                    lossQuantityPercent = 0;

                if (remainQty === '' || remainQty < 0 || !$.isNumeric(remainQty))
                    remainQty = 0;

                let totalConsumptionQty = parseFloat(finishedGoodsQuantity) * parseFloat(quantity);
                let totalConsumptionLoss = (totalConsumptionQty / 100) * lossQuantityPercent;
                let totalConsumptionCost = totalConsumptionQty * unit_price;
                let totalConsumptionLossCost = totalConsumptionLoss * unit_price;

                if(totalConsumptionQty > remainQty){
                    $(this).addClass('bg-danger');
                    $('.remain-qty:eq('+i+')').text(remainQty).removeClass('text-success').addClass('text-danger text-white');
                }else{
                    $(this).removeClass('bg-danger');
                    $('.remain-qty:eq('+i+')').text(remainQty).removeClass('text-danger text-white ').addClass('text-success text-black');
                }

                $('.sl-row-product:eq('+i+')').text(parseFloat(i + 1));
                $('.total_consumption_quantity:eq('+i+')').text(quantity+' X '+finishedGoodsQuantity +' = '+(totalConsumptionQty));
                $('.total_loss_quantity:eq('+i+')').text(totalConsumptionLoss.toFixed(2));
                $('.total-cost:eq('+i+')').text(totalConsumptionCost.toFixed(2));
                //$('.extra_cost:eq('+i+')').val(totalExtraCost.toFixed(2));
                $('.total-loss-cost:eq('+i+')').text(totalConsumptionLossCost.toFixed(2));

                subConsumptionQty += parseFloat(totalConsumptionQty);
                subConsumptionLossQty += parseFloat(totalConsumptionLoss);
                subConsumptionCost += parseFloat(totalConsumptionCost);
                //subConsumptionExtraCost += parseFloat(totalExtraCost);
                subConsumptionLossCost += parseFloat(totalConsumptionLossCost);
            });
            extraAmount = extra_cost * finishedGoodsExtra;
            //alert(extra);
            //var total = parseFloat(subConsumptionCost)+parseFloat(extraCost);
            $("#total-consumption-qty").text(subConsumptionQty.toFixed(2));
            $("#total-consumption-loss-qty").text(subConsumptionLossQty.toFixed(2));
            $("#total-consumption-cost").html(subConsumptionCost + extraAmount);
            //$("#total-extra-cost").text(subConsumptionExtraCost.toFixed(2));
            $("#total-consumption-loss-cost").text(subConsumptionLossCost.toFixed(2));
        }
        function intSelect2(){

            $('.select2').select2();

        }

    </script>
@endsection
