@foreach($template->configProductDetails as $configProductDetail)
        <?php
        $inventory = \App\Models\Inventory::where('product_id',$configProductDetail->product_id)
            ->orderBy('quantity','desc')
            ->first();
        ?>
    <tr class="product-item">
        <td class="text-center sl-row-product" >{{ $loop->iteration }}</td>
        <td>
            <div class="form-group">
                <input type="hidden" name="product_id[]" value="{{ $configProductDetail->product_id }}">
                <input type="hidden" name="inventory_id[]" value="{{ $inventory->id }}">
                <input readonly  value="{{ $configProductDetail->product->name ?? '' }} - {{ $configProductDetail->product->unit->name ?? '' }}" type="text" name="product_name[]" class="form-control product_name">
            </div>
        </td>

        <td class="text-center unit_price">{{ $configProductDetail->product->unit_price ?? 0 }}</td>
        <td class="text-center remain-qty">{{ $inventory->quantity ?? 0 }}</td>
        <td>
            <div class="form-group">
                <input   value="{{ $configProductDetail->quantity }}" type="text" name="quantity[]" class="form-control quantity">
            </div>
        </td>
        <td><span class="total_consumption_quantity"></span> {{ $configProductDetail->product->unit->name ?? '' }}</td>
        <td class="total-cost"></td>
        <td>
            <div class="form-group">
                <input type="text"  value="{{ $configProductDetail->loss_quantity_percent }}" name="loss_quantity_percent[]" class="form-control loss_quantity_percent">
            </div>
        </td>
        <td><span class="total_loss_quantity"></span> {{ $configProductDetail->product->unit->name ?? '' }}</td>
        <td class="total-loss-cost"></td>
        <td class="text-center">
            <a role="button" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
        </td>
    </tr>
@endforeach
