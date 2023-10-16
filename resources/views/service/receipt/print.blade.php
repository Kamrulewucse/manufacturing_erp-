<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('themes/backend/dist/css/adminlte.min.css') }}">
    <style>
        /*html { background-color: #ffd21b; }*/
        body { margin:50px;
            /*background-color: #ffd21b;*/
        }
        table,.table,table td,
            /*table th{background-color: #ffd21b !important;}*/
        .table-bordered{
            border: 1px solid #000000;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #000000 !important;
            /*background-color: #ffd21b !important;*/
        }
        .table.body-table td,.table.body-table th {
            padding: 2px 7px;
        }

        @page {
            margin: 0;
        }
        hr.hrbold {
            border: 1px solid black;
        }
        @media screen {
            div.divFooter {
                display: none;
            }
        }
        @media print {
            div.divFooter {
                position: fixed;
                bottom: 0;
            }
            html {
                font-size: 110%;
            }
        }
        @media screen {
            div.divSignature {
                display: none;
            }
        }
        @media print {
            div.divSignature {
                position: fixed;
                bottom: 30;
            }
        }
    </style>
</head>
<body style="margin-top: 250px !important">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2><b>Invoice</b></h2>
        </div>
    </div>
    <hr class="hrbold">
    <div class="row" style="border: 1px solid black; margin-top: 7px !important; font-size: 12px">
        <div class="col-md-7 pl-5" style="margin-top: 16px !important;">
            <div class="row">
                <div class="col-md-3">
                    <h6><b>Order No</b></h6>
                    <h6><b>Customer Name</b></h6>
                    <h6><b>Address</b></h6>
                    <h6><b>Mobile</b></h6>
                </div>
                <div class="col-md-1">
                    <h6><b>:</b></h6>
                    <h5><b>:</b></h5>
                    <h6><b>:</b></h6>
                    <h6><b>:</b></h6>
                </div>
                <div class="col-md-8">
                    <h6> {{ $order->order_no }}</h6>
                    <h5> {{ $order->client->name ?? '' }}</h5>
                    <h6> {{ $order->client->address ?? '' }}</h6>
                    <h6> {{ $order->client->mobile ?? '' }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-5 pr-5" style="margin-top: 15px !important;">
            <div class="row">
                <div class="col-md-4">
                    <h6 class="text-right"><b>Date</b></h6>
                </div>
                <div class="col-md-1">
                    <h6><b>:</b></h6>
                </div>
                <div class="col-md-7">
                    <h6> {{ \Carbon\Carbon::parse($order->date)->format('d-m-Y')}}</h6>
                </div>
            </div>
        </div>
    </div>
</div>

@if(count($order->products) > 0)
    <div class="row">
        <div class="col-md-12" style="margin-top: 20px">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="text-center">Sl</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">Qty/Pcs</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">Service Charge</th>
                    <th class="text-center">Total</th>
                </tr>
                </thead>

                <tbody>
                @foreach($order->products as $product)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td >{{ $product->product->name }}</td>
                        <td class="text-center">{{ $product->quantity }}</td>
                        <td class="text-center">{{ $product->product->unit->name ?? '' }}</td>
                        <td class="text-right">৳{{ number_format(($product->selling_price), 2) }}</td>
                        <td class="text-right">৳{{ number_format($product->selling_price * $product->quantity, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if(count($order->products) > 0)
    <div class="row">
        <div class="col-md-12" style="margin-top: 20px">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="text-center">Sl</th>
                    <th class="text-center">Raw Material</th>
                    <th class="text-center">Qty/Pcs</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">Rate</th>
                    <th class="text-center">Total</th>
                </tr>
                </thead>

                <tbody>
                @foreach($order->rawMaterial as $product)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td >{{ $product->product->name }}</td>
                        <td class="text-center">{{ $product->quantity }}</td>
                        <td class="text-center">{{ $product->product->unit->name ?? '' }}</td>
                        <td class="text-right">৳{{ number_format(($product->selling_price), 2) }}</td>
                        <td class="text-right">৳{{ number_format($product->selling_price * $product->quantity, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="row">
    <div class="offset-7 col-md-5">
        <table class="table table-bordered" >
            <tr>
                <th>Sub Total</th>
                <td width="35%">৳ {{ number_format(($order->sub_total), 2) }}</td>
            </tr>
            <tr>
                <th>Discount</th>
                <td class="">৳{{ number_format($order->discount, 2) }}</td>
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


<div class="text-left" style="clear: both">
    <p style="font-size: 20px !important"><b>In Word: </b>{{ $order->amount_in_word }} Only</p>
</div>


<div class="divFooter" style="width: 100%">

    <div class="row">
        <div class="col-md-4">

        </div>

        <div class="col-md-4">
            <p style="margin-left: 70px !important">{{ Auth::user()->name ?? '' }}</p>
        </div>

        <div class="col-md-4 text-center">

        </div>
    </div>
    <div class="row" style="margin-bottom: 120px">
        <div class="col-md-4">
            <span style="border-top: 1px solid black; margin-left: 30px !important"> Received By</span>
        </div>

        <div class="col-md-4">
            <span style="border-top: 1px solid black; margin-left: 70px !important">Prepared By</span>
        </div>

        <div class="col-md-4 text-center">
            <span style="border-top: 1px solid black; margin-right: 20px !important">Authorised Signature</span>
        </div>
    </div>
</div>


<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
