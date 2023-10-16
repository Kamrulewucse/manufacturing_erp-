@extends('layouts.app')
@section('title','Manufacturer Edit')
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
                <form enctype="multipart/form-data" action="{{route('manufacture.edit',['finishedGoods'=>$finishedGoods->id])}}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('template_product') ? 'has-error' :'' }}">
                                    <label for="template_product">Choose Template  <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="template_product" style="width: 100%;" name="template_product">
                                        <option value="">Select Template</option>
                                        @foreach($configProducts as $configProduct)
                                            <option value="{{ $configProduct->id }}" {{ old('template_product',$finishedGoods->config_product_id) ==$configProduct->id  ? 'selected' : '' }}>{{ $configProduct->finishedGoods->name ?? '' }}</option>
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
                                            <option value="{{ $product->id }}" {{ old('finished_goods',$finishedGoods->product_id) ==$product->id  ? 'selected' : '' }}>{{ $product->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('finished_goods')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('finished_goods_unit_price') ? 'has-error' :'' }}">
                                    <label for="finished_goods_unit_price">Finished Goods Sale Unit Price <span class="text-danger">*</span></label>
                                    <input type="text" id="finished_goods_unit_price"  value="{{ old('finished_goods_unit_price',$finishedGoods->selling_price) }}" name="finished_goods_unit_price" class="form-control" placeholder="Enter Unit Price">
                                    @error('finished_goods_unit_price')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                    <label for="date">Manufacture Date <span class="text-danger">*</span></label>
                                    <input type="text" id="date" autocomplete="off"  value="{{ old('date',$finishedGoods->date ? \Carbon\Carbon::parse($finishedGoods->date)->format('d-m-Y') : date('d-m-Y')) }}" name="date" class="form-control date-picker" placeholder="manufacture date">
                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive-sm">
                                    <table class="table table-bordered  table-custom-form">
                                        <thead>
                                        <tr>
                                            <th colspan="4"></th>
                                            <th colspan="6" class="text-center">Consumption</th>
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
                                        <tbody id="row-material-container">
                                            @foreach($finishedGoods->finishedGoodsRowMaterials as $finishedGoodsRowMaterial)
                                                <?php
                                                $inventory = \App\Models\Inventory::where('id',$finishedGoodsRowMaterial->inventory_id)->first();
                                                ?>
                                                <tr class="product-item">
                                                    <td class="text-center sl-row-product" >{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="hidden" name="product_id[]" value="{{ $finishedGoodsRowMaterial->product_id }}">
                                                            <input type="hidden" name="inventory_id[]" value="{{ $finishedGoodsRowMaterial->inventory_id }}">
                                                            <input type="hidden" name="remaining_quantity[]" value="{{ \App\Models\Inventory::where('product_id',$finishedGoodsRowMaterial->product_id)->max('quantity') + $finishedGoodsRowMaterial->consumption_quantity }}">
                                                            <input readonly  value="{{ $finishedGoodsRowMaterial->product->name ?? '' }} - {{ $finishedGoodsRowMaterial->product->unit->name ?? '' }}" type="text" name="product_name[]" class="form-control product_name">
                                                        </div>
                                                    </td>
                                                    <td class="text-center unit_price">{{ $inventory->unit_price ?? 0 }}</td>
                                                    <td class="text-center remain-qty">{{ ($inventory->quantity ?? 0) + $finishedGoodsRowMaterial->consumption_quantity }}</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input   value="{{ $finishedGoodsRowMaterial->per_unit_quantity }}" type="text" name="quantity[]" class="form-control quantity">
                                                        </div>
                                                    </td>
                                                    <td><span class="total_consumption_quantity"></span> {{ $finishedGoodsRowMaterial->product->unit->name ?? '' }}</td>
                                                    <td class="total-cost"></td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text"  value="{{ $finishedGoodsRowMaterial->consumption_loss_quantity_percent }}" name="loss_quantity_percent[]" class="form-control loss_quantity_percent">
                                                        </div>
                                                    </td>
                                                    <td><span class="total_loss_quantity"></span> {{ $finishedGoodsRowMaterial->product->unit->name ?? '' }}</td>
                                                    <td class="total-loss-cost"></td>
                                                    <td class="text-center">
                                                        <a role="button" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-right">Sub Total</th>
                                            <th  id="total-consumption-qty"></th>
                                            <th  id="total-consumption-cost"></th>
                                            <th></th>
                                            <th  id="total-consumption-loss-qty"></th>
                                            <th  id="total-consumption-loss-cost"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button id="btn-save" type="submit" class="btn btn-dark bg-gradient-dark">Save</button>
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
            calculate();
            formSubmitConfirm('btn-save');
            var finished_goods_finished_goods_unit_price_old = '{{ old('finished_goods_unit_price',$finishedGoods->unit_price) }}';

            $('body').on('change','#finished_goods', function () {
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
                        }
                        calculate();
                    });
                }

            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
                calculate();
            });

            $('body').on('keyup', '.quantity,#finished_goods_quantity', function () {
                calculate();
            });

            calculate();
        });
        function calculate() {
            var subConsumptionQty = 0;
            var subConsumptionLossQty = 0;
            var subConsumptionCost = 0;
            var subConsumptionLossCost = 0;
            var finishedGoodsQuantity = $("#finished_goods_quantity").val();

            if (finishedGoodsQuantity === '' || finishedGoodsQuantity < 0 || !$.isNumeric(finishedGoodsQuantity))
                finishedGoodsQuantity = 1;

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
                $('.total-loss-cost:eq('+i+')').text(totalConsumptionLossCost.toFixed(2));

                subConsumptionQty += parseFloat(totalConsumptionQty);
                subConsumptionLossQty += parseFloat(totalConsumptionLoss);
                subConsumptionCost += parseFloat(totalConsumptionCost);
                subConsumptionLossCost += parseFloat(totalConsumptionLossCost);
            });
            $("#total-consumption-qty").text(subConsumptionQty.toFixed(2));
            $("#total-consumption-loss-qty").text(subConsumptionLossQty.toFixed(2));
            $("#total-consumption-cost").text(subConsumptionCost.toFixed(2));
            $("#total-consumption-loss-cost").text(subConsumptionLossCost.toFixed(2));
        }

        function intSelect2(){

            $('.select2').select2();

        }

    </script>
@endsection
