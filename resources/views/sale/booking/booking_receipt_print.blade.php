<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
{{--    <title>Cash_Voucher_{{$receiptPayment->receipt_payment_no}}</title>--}}

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
                font-size: 106%;
            }
            html {
                font-size: 106%;
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
<body style="margin-top: 20px !important">
<div class="container-fluid">
    <header id="pageHeader"  style="margin-left: 520px !important">
        <div class="row">
            <div class="col-xs-12">
                <h4 class=""><b>SAFETY MARK MANUFACTURING FACTORY</b></h4>
                <p style="font-size: 25px !important">H-30,R-6,Block-C,Rampura,Banasree,Dhaka</p>
            </div>
        </div>
    </header>
    <div class="row">
        <div class="col-md-12 text-center">
            <h2><b>Booking Invoice</b></h2>
        </div>
    </div>
    <hr class="hrbold">
    <div class="row" style="border: 1px solid black; margin-top: 5px !important; font-size: 12px">
        @if(auth()->user()->role !=2)
        <div class="col-md-7 pl-5" style="margin-top: 15px !important;">
            <div class="row">
                <div class="col-md-3">
                    <h6><b>Customer Name</b></h6>
                    <h6><b>Address</b></h6>
                    <h6><b>Mobile</b></h6>
                </div>
                <div class="col-md-1">
                    <h5><b>:</b></h5>
                    <h6><b>:</b></h6>
                    <h6><b>:</b></h6>
                </div>
                <div class="col-md-8">
                    <h5> {{ $booking->customer->name ?? ''}}</h5>
                    <h6> {{ $booking->customer->mobile ?? '' }}</h6>
                    <h6> {{ $booking->customer->address ?? '' }}</h6>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-5 pr-5" style="margin-top: 15px !important;">
            <div class="row">
                <div class="col-md-4">
                    <h6 class="text-right"><b>Order Date</b></h6>
                </div>
                <div class="col-md-1">
                    <h6><b>:</b></h6>
                </div>
                <div class="col-md-7">
                    <h6> {{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h6 class="text-right"><b>Delivery Date</b></h6>
                </div>
                <div class="col-md-1">
                    <h6><b>:</b></h6>
                </div>
                <div class="col-md-7">
                    <h6> {{ \Carbon\Carbon::parse($booking->delivery_date)->format('d-m-Y') }}}</h6>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
        <div class="col-md-12" style="margin-top: 20px">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                </tr>
                </thead>

                <tbody>
                @if ($booking->bookingDetails)
                    @foreach($booking->bookingDetails as $bookingDetail)
                        <tr>
                            <td>{{ $bookingDetail->product->name ?? '' }}</td>
                            <td class="">{{ $bookingDetail->quantity }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="1" class="text-right">Total</th>
                    <th class="">{{$booking->quantity }}</th>
                </tr>

                </tfoot>
            </table>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-center">
                    @if(auth()->user()->role !=2)
                        <b>Advance Amount : </b>   ৳{{ number_format($booking->advance_amount, 2) }}
                    @endif
                </div>
            </div>
        </div>
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

{{--    <div class="row">--}}
{{--        <div class="col-md-12 text-center"> <br>--}}
{{--            Software developed by 2A IT. Mobile: 01740059414--}}
{{--        </div>--}}
{{--    </div>--}}

</div>


<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
