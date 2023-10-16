@extends('layouts.app')

@section('title')
    Purchase Receipt Details
@endsection

@section('style')
    <style>
        .img-overlay img {
            width: 600px;
            height: auto;
        }
        @media print {
            html {
                font-size: 110%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><br>
                        </div>
                    </div>

                    <hr>

                    <div id="prinarea">
                        <div class="row" id="heading_area" style="margin-bottom: 10px!important;display: none; position: relative">
                            <div class="col-sm-12 text-center" style="font-size: 16px">
                                <div style="float:left">
                                    <img style="width:350px;margin-top: -8px;opacity:0.4" src="{{ asset('img/topbar.jpg') }}">
                                </div>

                                <div style="float:right">
                                    <img style="width:200px;margin-top:50px" src="{{ asset('img/name.png') }}" alt="">
                                </div>
                                <div style="float:right">
                                    <img style="width:80px;margin-top:20px" src="{{ asset('img/logo.png') }}" alt="">
                                </div>

                                <br><br>

                                {{-- <h2 style="margin-bottom: 0 !important;"><img width="75px" src="{{ asset('img/logo.png') }}" alt=""> <strong style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i></strong></h2> --}}
                                <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Purchase Receipt Report</strong>
                                <p class="">Printed by: {{Auth::user()->name}}</p>
                            </div>

                            <div class="col-sm-3 col-sm-offset-9">
                                <span class="date-top">Date: <strong style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;font-weight: normal;">{{ date('d-m-Y') }}</strong></span>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Order No.</th>
                                        <td>{{ $order->order_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Order Date</th>
                                        <td>{{ $order->date }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th colspan="2" class="text-center">Supplier Info</th>
                                    </tr>

                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $order->supplier->name ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile</th>
                                        <td>{{ $order->supplier->mobile ?? ''}}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td style="white-space: break-spaces;">{{ $order->supplier->address ?? ''}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @if ($order->products)
                                            @foreach($order->products as $product)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $product->name }}</td>
                                                    <td class="">{{ $product->quantity }}</td>
                                                    <td class="">৳{{ number_format($product->unit_price, 2) }}</td>
                                                    <td class="text-right">৳{{ number_format($product->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-8 col-md-4">
                                <table class="table table-bordered">

                                    <tr>
                                        <th>Sub Total Amount</th>
                                        <th class="text-right">৳{{ number_format($order->sub_total, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Discount</th>
                                        <th class="text-right">৳{{ number_format($order->discount, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-right">৳{{ number_format($order->total, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Paid</th>
                                        <th class="text-right">৳{{ number_format($order->paid, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Due</th>
                                        <th class="text-right">৳{{ number_format($order->due, 2) }}</th>
                                    </tr>

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
        var APP_URL = '{!! url()->full()  !!}';
        function getprint(prinarea) {
            $('#heading_area').show();
            $('body').html($('#'+prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }

        $(function () {
            $('#table-payments').DataTable({
                "order": [[ 0, "desc" ]],
            });

        });
    </script>
@endsection
