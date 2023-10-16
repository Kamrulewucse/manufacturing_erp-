@extends('layouts.app')
@section('title')
    Sale transaction Report
@endsection
@section('style')
    <style>
        .img-overlay {
            position: absolute;
            left: 0;
            top: 200px;
            width: 100%;
            height: 100%;
            overflow: hidden;
            text-align: center;
            z-index: 9;
            opacity: 0.2;
        }

        .img-overlay img {
            width: 400px;
            height: auto;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('sales.transaction_report') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start">Start Date <span
                                            class="text-danger">*</span></label>
                                    <input required type="text" id="start" autocomplete="off"
                                           name="start" class="form-control date-picker"
                                           placeholder="Enter Start Date" value="{{ request()->get('start') ?? date('d-m-Y')  }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end">End Date <span
                                            class="text-danger">*</span></label>
                                    <input required type="text" id="end" autocomplete="off"
                                           name="end" class="form-control date-picker"
                                           placeholder="Enter Start Date" value="{{ request()->get('end') ?? date('d-m-Y')  }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer</label>

                                    <select class="form-control select2" name="customer">
                                        <option value="">All Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request()->get('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> &nbsp;</label>

                                    <input class="btn btn-primary form-control" type="submit" value="Search">
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button>
                    <br><br>
                    <div id="prinarea">
                        <div class="row" id="heading_area" style="margin-bottom: 10px!important;display: none">
                            <div class="col-sm-12 text-center" style="font-size: 16px">
                                <h2 style="margin-bottom: 0 !important;"><img width="75px" src="{{ asset('img/logo.jpeg') }}" alt="">
                                    <strong
                                        style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i>
                                    </strong>
                                </h2>
                                <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Customer Report</strong>
                                <p class="">Printed by: {{Auth::user()->name}}</p>
                            </div>
                            <div class="col-sm-3 col-sm-offset-9">
                                <span class="date-top">Date: <strong
                                        style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;font-weight: normal;">{{ date('d-m-Y') }}</strong></span>
                            </div>
                        </div>
                        <div style="clear: both">
                            <div class="table-responsive">
                                <table id="table" class="table table-bordered" style="z-index: 5;">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Order No</th>
                                        <th class="text-center">Customer Name</th>
                                        <th class="">Total</th>
                                        <th class="">paid</th>
                                        <th class="">due</th>
                                        <th class="">Product</th>
                                        <th class="">Quantity</th>
                                        <th class="">Unit Price</th>
                                        <th class="">Sub Total</th>
{{--                                        <th class="text-center">Product</th>--}}
{{--                                        <th class="text-center">Quantity</th>--}}
{{--                                        <th class="">Unit Price</th>--}}
{{--                                        <th class="">Sub Total</th>--}}

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                       $totalQty = 0;
                                    @endphp
                                    @foreach($orders as $order)
                                        @php
                                            $productCount = $order->products->count();
                                            $totalQty += $productCount;
                                        @endphp
                                            <tr>

                                                <td>{{ $order->date }}</td>
                                                <td>
                                                    <a target="_blank" href="{{ route('sale_receipt.details', ['order' => $order->id]) }}">
                                                        {{ $order->order_no }}
                                                    </a></td>
                                                <td>{{ $order->client->name  }}</td>
                                                <td>{{ $order->total }}</td>
                                                <td>{{ $order->paid }}</td>
                                                <td>{{ $order->due }}</td>
                                                <td>
                                                    {{ $order->products[0]->product->name??'' }}
                                                    @foreach($order->products as $key => $product)
                                                        {{ $product->serial }}<br/>
                                                    @endforeach
                                                </td>
                                                <td>{{ $productCount }}</td>
                                                <td>৳{{ number_format(($order->products[0]->selling_price??''), 2) }}</td>
                                                <td>৳{{ number_format(($productCount*$order->products[0]->selling_price??''), 2) }}</td>
{{--                                                @foreach($order->products as $key => $product)--}}

{{--                                                    <td >{{ $product->product->name ?? '' }}<br>--}}
{{--                                                        <b>Serial No :</b> {{ $product->serial ?? '' }}--}}
{{--                                                    </td>--}}
{{--                                                    <td class="text-center">{{ $product->quantity }}</td>--}}
{{--                                                    <td class="text-right">৳{{ number_format(($product->selling_price), 2) }}</td>--}}
{{--                                                    <td class="text-right">৳{{ number_format($product->selling_price * $product->quantity, 2) }}</td>--}}
                                            </tr>
{{--                                            @if ($key < $productCount - 1)--}}
{{--                                                <tr>--}}
{{--                                                    @endif--}}
{{--                                                    @endforeach--}}
{{--                                                </tr>--}}
                                     @endforeach
                                            <tr>
                                                <th class="text-right" colspan="3">Total</th>
                                                <th class="text-center">৳{{number_format($orders->sum('total'))}}</th>
                                                <th class="text-center">৳{{number_format($orders->sum('paid'))}}</th>
                                                <th class="text-center">৳{{number_format($orders->sum('due'))}}</th>
                                                <th class="text-center"></th>
                                                <th class="text-center">{{ $totalQty }}</th>
                                                <th class="text-center"></th>
                                                <th class="text-center">৳{{number_format($orders->sum('total'))}}</th>
                                            </tr>
                                    </tbody>
{{--                                    <tfoot>--}}
{{--                                    <tr>--}}
{{--                                        <th class="text-right" colspan="3">Total</th>--}}
{{--                                        <th class="text-center">{{number_format($orders->sum('total'))}}</th>--}}
{{--                                        <th class="text-center">{{number_format($orders->sum('paid'))}}</th>--}}
{{--                                        <th class="text-center">{{number_format($orders->sum('due'))}}</th>--}}
{{--                                        <th class="text-center" colspan="4"></th>--}}
{{--                                    </tr>--}}
{{--                                    </tfoot>--}}

                                </table>
                            </div>

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
            $('#table').DataTable();
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });

    </script>
    <script>
        var APP_URL = '{!! url()->full()  !!}';

        function getprint(prinarea) {
            $('#heading_area').show();
            $('body').html($('#' + prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
