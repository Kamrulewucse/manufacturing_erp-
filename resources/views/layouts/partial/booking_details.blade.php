<tr>
    <th colspan="" ><b>S/L</b></th>
    <th colspan="" ><b>Product Name</b></th>
    <th>Order Quantity</th>
    <th>Delivery Quantity</th>
    <th>Complete Quantity</th>
    <th>Cancel Product</th>
</tr>
@php
    $totalQuantity =0;
    $cancelQuantity =0;
@endphp
@foreach($booking->bookingDetails->groupBy('product_id') as $productId => $bookingDetailsGroup)
    <tr class="product-item1">
        <td class="text-center sl-row-product" >{{ $loop->iteration }}</td>
        <td>
            <div class="form-group">
                <input readonly  value="{{ $bookingDetailsGroup->first()->product->name ?? '' }} - {{ $bookingDetailsGroup->first()->product->unit->name ?? '' }}" type="text" name="product_name1[]" class="form-control product_name1">
                <input  value="{{ $bookingDetailsGroup->first()->product->id ?? '' }}" type="hidden" name="product_id[]" class="form-control product_id">
            </div>
        </td>

        <td class="text-center quantity">{{ $bookingDetailsGroup->sum('quantity') ?? 0 }}</td>
        <td class="text-center quantity">{{ $bookingDetailsGroup->sum('delivery_quantity') ?? 0 }}</td>
        <td class="text-center quantity">{{ $bookingDetailsGroup->filter(function ($detail) { return $detail->status == 3; })->sum('quantity') }}</td>
        <td class="text-center quantity">{{ $bookingDetailsGroup->filter(function ($detail) { return $detail->status == 4; })->sum('quantity') }}</td>
    </tr>

    @php
        $totalQuantity += $bookingDetailsGroup->filter(function ($detail) { return $detail->status == 3; })->sum('quantity');
        $cancelQuantity += $bookingDetailsGroup->filter(function ($detail) { return $detail->status == 4; })->sum('quantity');
    @endphp

@endforeach
<tr>
    <td colspan="2" class="text-right"><b>Order Quantity</b></td>
    <td class="text-center"><b>{{$booking->quantity ?? ''}}</b></td>
    <td class="text-center"><b>{{$booking->delivery_quantity ?? ''}}</b></td>
    <td class="text-center"><b>{{$totalQuantity}}</b></td>
    <td class="text-center"><b>{{$cancelQuantity}}</b></td>

</tr>

<tr>
    <td colspan="2" class="text-right"><b>Total/Remaining Advance</b></td>
    <td class="text-center"><b>৳{{$booking->advance_amount ?? ''}}</b></td>
</tr>

