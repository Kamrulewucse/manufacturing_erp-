<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--    <title>Cash_Voucher_{{$receiptPayment->receipt_payment_no}}</title>--}}

    <!- <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('themes/backend/dist/css/adminlte.min.css') }}">
    <style>
        /*html { background-color: #ffd21b; }*/
        body { margin:50px;
            /*background-color: #ffd21b;*/
        }
        #printPaidReceipt {
            background-image: url("{{asset('img/paid.png')}}");
            background-repeat: no-repeat;
            background-position: left -130px top;
        }
        #printDueReceipt {
            background-image: url("{{asset('img/due.png')}}");
            background-repeat: no-repeat;
             background-position: left -60px top;
            background-size: 550px 500px;
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
        .vartical{
            border-left: 6px solid black;
            height: 123px;
            margin-top: 3px;
            position: absolute;
            left: 59%;
        }
        .vartical1{
            border-left: 6px solid red;
            height: 131px;
            position: absolute;
            left: 60%;
        }
        .vartical2{
            border-left: 6px solid black;
            height: 123px;
            margin-top: 3px;
            position: absolute;
            left: 61%;
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
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            div.divFooter {
                position: fixed;
                bottom: 0;
            }

            html {
                font-size: 110%;
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
        }

    </style>
</head>
<body>
<header id="pageHeader">
    <div class="row" style="margin-bottom: 20px !important">
        <div class="col-xs-7">
            <h1 class="" style="font-size: 90px;margin-bottom: 0px !important;"><b>SAFETY MARK</b></h1>
            <b style="font-size: 45px;color: red">MANUFACTURING FACTORY</b>
        </div>

        <div class="col-xs-5">
            <div class="vartical"></div>
            <div class="vartical1"></div>
            <div class="vartical2"></div>
            <p class="" style="margin-bottom: 0px !important; margin-left: 60px !important; font-size: 20px !important;"><b>Office: House-30,Road-06,Block-C,</b></p>
            <p class="" style="margin-bottom: 0px !important; margin-left: 60px !important; font-size: 20px !important"><b>Banasree,Rampura,Dhaka-1219.</b></p>
            <p class="" style="margin-bottom: 0px !important; margin-left: 60px !important; font-size: 20px !important"><b>Cell: +88 01842-120160</b></p>
            <p class="" style="margin-bottom: 0px !important; margin-left: 60px !important; font-size: 20px !important"><b>Email: rojobips1@gmail.com</b></p>
        </div>
    </div>
    <hr style="border: 1px solid black; margin-top: 5px !important;margin-bottom: 0px !important">
    <hr style="border: 1px solid red; margin-top: 5px !important">
</header>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2><b>Invoice</b></h2>
        </div>
    </div>
    <hr class="hrbold">
    <div class="row" style="border: 1px solid black; margin-top: 5px !important; font-size: 12px">
        <div class="col-md-7 pl-5" style="margin-top: 16px !important;">
            <div class="row">
                <div class="col-md-4 " style="margin: 0px !important">
                    <p style="font-size: 21px !important; margin: 0px !important; font-weight: 900"><b>Order No</b></p>
                    <p style="font-size: 21px !important; margin: 0px !important; font-weight: 900"><b>Customer Name</b></p>
                    <p style="font-size: 21px !important; margin: 0px !important; font-weight: 900"><b>Address</b></p>
                    <p style="font-size: 21px !important; margin: 0px !important; font-weight: 900"><b>Mobile</b></p>
                </div>
                <div class="col-md-1">
                    <h6><b>:</b></h6>
                    <h5><b>:</b></h5>
                    <h6><b>:</b></h6>
                    <h6><b>:</b></h6>
                </div>
                <div class="col-md-7">
                    <h6 style="font-size: 21px !important; font-weight: 500"> {{ $order->order_no }}</h6>
                    <h6 style="font-size: 21px !important; font-weight: 500">{{ $order->client->name ?? '' }}</h6>
                    <h6 style="font-size: 21px !important; font-weight: 500;">{{ $order->client->address ?? '' }}</h6>
                    <h6 style="font-size: 21px !important; font-weight: 500"> {{ $order->client->mobile ?? '' }}</h6>
                </div>
            </div>
        </div>
       <div class="col-md-5 pr-5" style="margin-top: 15px !important;">
            <div class="row">
                <div class="col-md-5 text-right">
                    <p style="font-size: 21px !important; margin: 0px !important; font-weight: 900"><b>Date</b></p>
                    <p style="font-size: 21px !important; margin: 0px !important; font-weight: 900"><b>Received By</b></p>
                </div>
                <div class="col-md-1" style="margin-top: 4px !important;">
                    <h6><b>:</b></h6>
                    <h6><b>:</b></h6>
                </div>
                <div class="col-md-6 text-left">
                    <h6 style="font-size: 21px !important; margin-top: 2px !important; font-weight: 500"> {{ \Carbon\Carbon::parse($order->date)->format('d-m-Y')}}</h6>
                    <h6 style="font-size: 21px !important; margin-top: 2px !important; font-weight: 500"> {{ $order->received_by?? ''}}</h6>
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
                    <th class="text-center">Rate</th>
                    <th class="text-center">Total</th>
                </tr>
                </thead>

                <tbody>
                @foreach($order->products->groupBy('product.name') as $productName => $products)
                    <tr>
                        <td class="text-center">
                            <h6 style="font-size: 21px !important; font-weight: 500">
                                {{ $loop->iteration }}</h6>
                        </td>
                        <td >
                            <h6 style="font-size: 21px !important; font-weight: 500">
                                {{ $productName }}<br>
                                <b>Serial No :</b>
                                @foreach($products as $product)
                                    {{ $product->serial ?? '' }},
                                @endforeach
                            </h6>
                        </td>
                        <td class="text-center"><h6 style="font-size: 21px !important; font-weight: 500">{{ $products->count() }}</h6></td>
                        <td class="text-center"><h6 style="font-size: 21px !important; font-weight: 500">{{$product->product->unit->name ?? ''}}</h6></td>
                        <td class="text-center"><h6 style="font-size: 21px !important; font-weight: 500">{{ number_format(($product->selling_price), 2) }}</h6></td>
                        <td class="text-center"><h6 style="font-size: 21px !important; font-weight: 500">{{ number_format($product->selling_price * $products->count(), 2) }}</h6></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="row" id="{{$order->due > 0 ? 'printDueReceipt' : 'printPaidReceipt'}}">
    <div class="offset-6 col-md-6">
        <table class="table table-bordered" >
            <tr>
                <th>Sub Total</th>
                <td width="35%"><h6 style="font-size: 21px !important; font-weight: 500"> {{ number_format(($order->sub_total), 2) }}</h6></td>
            </tr>
            <tr>
                <th>Discount</th>
                <td width="35%"><h6 style="font-size: 21px !important; font-weight: 500"> {{ number_format(($order->discount), 2) }}</h6></td>
            </tr>
            <tr>
                <th>Total</th>
                <td width="35%"><h6 style="font-size: 21px !important; font-weight: 500"> {{ number_format(($order->total), 2) }}</h6></td>
            </tr>
            <tr>
                <th>Adjust from Advance Payment</th>
                <td width="35%"><h6 style="font-size: 21px !important; font-weight: 500"> {{ number_format(($order->advance_total), 2) }}</h6></td>
            </tr>
            <tr>
                <th>Paid</th>
                <td width="35%"><h6 style="font-size: 21px !important; font-weight: 500"> {{ number_format(($order->paid), 2) }}</h6></td>
            </tr>
            <tr>
                <th>Due</th>
                <td width="35%"><h6 style="font-size: 21px !important; font-weight: 500"> {{ number_format(($order->due), 2) }}</h6></td>
            </tr>
        </table>
    </div>
</div>


<div class="text-left" style="clear: both">
    <p style="font-size: 20px !important; font-size: 21px !important; font-weight: 500"><b>In Word: </b>{{ $order->amount_in_word }} Only</p>
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
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-4">
            <span style="border-top: 1px solid black; margin-left: 30px !important;font-weight: 600"> <b>Received By</b></span>
        </div>

        <div class="col-md-4">
            <span style="border-top: 1px solid black; margin-left: 70px !important;font-weight: 600"><b>Prepared By</b></span>
        </div>

        <div class="col-md-4 text-center">
            <span style="border-top: 1px solid black; margin-right: 20px !important;font-weight: 600"><b>Authorised Signature</b></span>

        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <p style="font-size: 24px !important"></p>
        </div>
        <div class="col-md-10">
            <p style="font-size: 20px !important;margin-left: 50px !important;">
                <b>Warehouse:B-52/1, ICL Garden City, Post Office Road, East Badda, Dhaka-1212.<br>
                    <span style="margin-left: 220px !important;">Cell+88 4-402165, 01842-120160</span></b>
            </p>

        </div>
        <div class="col-md-1">
            <p style="font-size: 24px !important"></p>
        </div>
    </div>
</div>


<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
