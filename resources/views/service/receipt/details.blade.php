@extends('layouts.app')
@section('title')
    Service Receipt Details
@endsection
@section('style')
    <style>
        hr.hrbold {
            border: 1px solid red;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 ">
                            <a style="float: right;" target="_blank" href="{{ route('service_receipt.print', ['order' => $order->id]) }}" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
                            <a style="float: right; margin-right: 5px !important" target="_blank" href="{{ route('service_receipt.print_with_header', ['order' => $order->id]) }}" class="btn btn-primary"><i class="fa fa-print"></i> Print With Header</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center col-md-offset-1">
                            {{--                            <img src="{{ asset('img/logo.png') }}" height="70" width="300px" style="margin-top: 10px">--}}
                            <h1>SAFETY MARK MANUFACTURING FACTORY</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h2><b>Invoice</b></h2>
                        </div>
                    </div>
                    <hr class="hrbold">
                    <div class="row ">
                        <div class="col-md-7 pl-5">
                            <h5>{{ $order->client->name??'' }}</h5>
                            <p style="line-height: 0.5;">{{ $order->client->address??'' }}</p>
                            <p  style="line-height: 0.5;"> {{ $order->client->mobile??'' }}</p><br>
                        </div>
                        <div class="col-md-5 pr-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <p> <b>Order No</b></p>
                                    <p style="line-height: 0.5;"><b>Date</b></p>
                                    <p  style="line-height: 2;"><b>Note</b> </p>
                                </div>
                                <div class="col-md-1">
                                    <p><b>:</b></p>
                                    <p><b>:</b></p>
                                    <p><b>:</b></p>
                                </div>
                                <div class="col-md-7">
                                    <p> {{ $order->order_no }}</p>
                                    <p style="line-height: 0.5;">{{ $order->date }}</p>
                                    <p  style="line-height: 2;">{{ $order->note??'' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    @if(count($order->products) > 0)

                        <div class="row">
                            <div class="col-md-12" style="margin-top: 20px">
                                <h5 class="text-center">{{$order->product->name}}(<b>{{$order->serial}}</b>)</h5>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th class="text-center">Row Product</th>
                                        <th class="text-center">Qty/Pcs</th>
                                        <th class="text-center">Selling Price</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($order->products as $product)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $product->product->name ?? '' }}</td>
                                            <td class="text-center">{{ $product->row_product_quantity }} ({{ $product->product->unit->name ?? '' }})</td>
                                            <td class="text-center">৳{{ number_format(($product->selling_price), 2) }}</td>
                                            <td class="text-center">৳{{ number_format($product->selling_price * $product->row_product_quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif


                    <div class="row">
                        <div class="offset-7 col-md-5 ">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Row Product Total</th>
                                    <td>৳ {{ number_format(($order->row_product_total), 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Service Charge</th>
                                    <td>৳ {{ number_format(($order->service_charge), 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td class="">৳{{ number_format($order->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Paid</th>
                                    <td>৳ {{ number_format($order->paid, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Due</th>
                                    <td>৳ {{ number_format(($order->due), 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $('#table-payments').DataTable({
                "order": [[ 0, "desc" ]],
            });
        });
    </script>
@endsection
