<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!--Favicon-->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">

    <style>

        #pageHeader{
            position: running(pageHeader);
        }

        table.table-bordered{
            border:1px solid black !important;
            margin-top:20px;
        }
        table.table-bordered th{
            border:1px solid black !important;
        }
        table.table-bordered td{
            border:1px solid black !important;
        }

        .product-table th, .table-summary th {
            padding: 2px !important;
            text-align: center !important;
        }

        .product-table td, .table-summary td {
            padding: 2px !important;
            text-align: center !important;
        }

        .row {
            margin-right: 0;
            margin-left: 0;
        }

        .container-fluid{
            padding: 0 2px;
        }
        .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
            border: 1px solid #ddd;
            font-size: 12px;
            padding: 2px;
            text-align: center;

        }
        .table {
            margin-bottom: 0;
        }
        /*new style*/
        span.date-top {
            width: 100%;
            display: block;
        }
        span.date-top.challan-top {
            margin-top: 43px;
        }
        .table>caption+thead>tr:first-child>td, .table>caption+thead>tr:first-child>th, .table>colgroup+thead>tr:first-child>td, .table>colgroup+thead>tr:first-child>th, .table>thead:first-child>tr:first-child>td, .table>thead:first-child>tr:first-child>th {
            vertical-align: middle;
        }
        span.in-word{
            position: relative;
        }
        span.in-word:before {
            position: absolute;
            left: 10%;
            width: 74%;
            bottom: -1px;
            content: "";
            height: 1px;
            border-bottom: 2px dotted;
        }
        .sign-box {
            width: 100%;
            min-height: 128px;
            border: 2px solid #000;
        }

        .sign-box h3 {
            border-bottom: 2px solid #000;
            background: #d8d8d8 !important;;
            font-weight: bold;
            font-size: 16px;
        }
        .sign-box {
            min-height: 141px;
        }
        address {
            font-size: 15px;
        }
        address {
            padding: 16px 0;
        }
        address{
            margin: 0 !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 text-center" style="font-size: 16px">
            <h2 style="margin-bottom: 0 !important;"><img width="75px" src="{{ asset('img/logo.png') }}" alt=""> <strong style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i></strong></h2>
            <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">CASH / BILL</strong>
        </div>
        <div class="col-xs-3 col-xs-offset-9">
            <span class="date-top">Date: <br> <strong style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;display: block;font-weight: normal;">{{ $order->date->format('d-m-Y') }}</strong></span>

        </div>
        <div class="col-xs-7">
            <span><strong>To</strong></span><br>
            <span style="width: 100%; display: block;border-bottom: 1.5px dotted #000;font-weight: bold;"><strong style="font-size: 18px">M/S.</strong> <span>Name: {{ $order->customer->name }}</span></span>
            <span style="width: 100%; display: block;border-bottom: 1.5px dotted #000;font-weight: bold;">Mobile No: <span>{{ $order->customer->mobile_no }}</span></span>
            <span style="width: 100%; display: block;border-bottom: 1.5px dotted #000;font-weight: bold;">Address: <span>{{ $order->customer->address }}</span></span>
        </div>
        <div class="col-xs-3 col-xs-offset-2">
            <span class="date-top challan-top">Challan No: <br> <strong style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;display: block;font-weight: normal;">{{ $order->order_no }}</strong></span>

        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th width="8%" rowspan="2" class="text-center">Item No.</th>
                    <th width="50%" rowspan="2" class="text-center">Description/ Specification</th>
                    <th width="10%" rowspan="2" class="text-center">Qty./ Ctn.</th>
                    <th width="23%" colspan="2" class="text-center">Price</th>
                </tr>
                <tr>
                    <th>Unit Price</th>
                    <th>Amount</th>
                </tr>
                </thead>

                <tbody>
                @foreach($order->products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-left">{{ $product->pivot->name }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                        <td>{{ $product->pivot->unit_price }}</td>
                        <td>{{ number_format($product->pivot->total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>

                    <th colspan="4" style="text-align: right !important;border-left: none !important;border-bottom: none !important;border-left: 1px solid #fff !important;border-bottom: 1px solid #fff!important;" class="text-right">Total</th>
                    <th class="text-right">{{ number_format($order->total) }}</th>
                </tr>
                </tfoot>
            </table>
            <div class="col-xs-12" style="margin-bottom: 70px !important;">
                <span class="in-word" style="width: 100%;display: block;margin-left: -18px;margin-top: -21px;"><strong>In word:- {{ $order->amount_in_word }}</strong></span>
            </div>
            <div class="col-xs-6 text-center" style="margin-bottom: 20px !important;">
                <span style="border-top: 1px solid #000 !important;font-size: 18px;">Receiver's Signature</span>
            </div>
            <div class="col-xs-6 text-center" style="margin-bottom: 20px !important;">
                <span style="border-top: 1px solid #000 !important;font-size: 18px;">Authorized Signature</span>
            </div>
            <div class="col-xs-6 text-center">
                <span style="font-size: 11px;font-weight: bold;">(RECEIVED THE ABOVE GOODS IN GOOD CONDITION)</span>
                <div class="sign-box text-center">
                    <h3 style="margin: 0 !important;">HEAD OFFICE & LABORATORY</h3>
                    <address>
                        40 New Chashara, Jamtola, <br>
                        Narayanganj, Bangladesh. <br>
                        Tel: 7641895,7641894 <br>
                        Fax: 88 02 7641737

                    </address>
                </div>
            </div>
            <div class="col-xs-6 text-center" style="margin-top: 20px !important;">
                <div class="sign-box">
                    <h3 style="margin: 0 !important;">DHAKA OFFICE</h3>
                    <address>
                        Lucky Chanber (4th Floor) <br>
                        89, Motijheel Commercial Area <br>
                        Dhaka, Bangladesh.
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        window.print();
        window.onafterprint = function(){ window.close()};
    </script>
</body>
</html>
