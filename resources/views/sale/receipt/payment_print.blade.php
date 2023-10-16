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
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <img src="{{ asset('img/logo.png') }}" height="50px" style="float: left">
            <h2 style="margin: 0px; float: left">RECEIPT</h2>
        </div>

        <div class="col-md-4 text-center">
            <b>Date: </b> {{ $payment->date }}
        </div>

        <div class="col-md-4 text-right">
            <b>No: </b> {{ $payment->id + 1000 }}
        </div>
    </div>

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th width="20%">
                        @if($payment->type == 1)
                            From
                        @elseif($payment->type == 2)
                            To
                        @endif
                    </th>
                    <td>{{ $payment->salesOrder->customer->name??'' }}</td>
                    <th width="10%">Amount</th>
                    <td width="15%">৳{{ number_format($payment->amount, 2) }}</td>
                </tr>

                <tr>
                    <th>Amount (In Word)</th>
                    <td colspan="3">{{ $payment->amount_in_word }}</td>
                </tr>

                <tr>
                    <th>For Payment of</th>
                    <td colspan="3">Order No. {{ $payment->salesOrder->order_no }}</td>
                </tr>

                <tr>
                    <th>Paid By</th>
                    <td colspan="3">
                        @if($payment->transaction_method == 1)
                            Cash
                        @elseif($payment->transaction_method == 3)
                            Mobile Banking {{$payment->mobileBank->name ?? ''}}
                        @elseif($payment->transaction_method == 4)
                            Card
                        @else
                            Bank - {{ $payment->bank->name.' - '.$payment->branch->name.' - '.$payment->account->account_no }}
                        @endif
                    </td>
                </tr>

                @if($payment->transaction_method == 2)
                    <tr>
                        <th>Cheque No.</th>
                        <td colspan="3">{{ $payment->cheque_no }}</td>
                    </tr>
                @endif

                <tr>
                    <th>Note</th>
                    <td colspan="3">{{ $payment->note }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>


<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
