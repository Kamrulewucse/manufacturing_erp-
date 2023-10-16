<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @if($balanceTransfer->type == 3)
        Bank_Receipt_{{$balanceTransfer->receipt_no }}
        @else
            Cash_Receipt_{{$balanceTransfer->receipt_no }}
        @endif
    </title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('themes/backend/dist/css/adminlte.min.css') }}">
    <style>
        /*html { background-color: #ffd21b; }*/
        body { margin: 0 50px;
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
        .footer-signature-area {
            position: absolute;
            left: 0;
            bottom: 300px;
            width: 100%;
        }
        .invoice {
            border: none;
        }
        .fs-style{
            font-size: 24px !important;
            letter-spacing: 7px!important;
            font-weight: 900!important;
        }
        @page {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Main content -->
    <section class="invoice">
        <!-- title row -->
        <div class="row">
            <div class="col-12">
                <h1 class="text-center m-0" style="font-size: 45px !important;font-weight: bold">
                    <img height="80px" src="{{ asset(auth()->user()->sisterConcern->logo ?? 'img/logo.png') }}" alt="">
                    {{ auth()->user()->sisterConcern->company_name ?? '' }}
                </h1>
                @if($balanceTransfer->type == 3)
                    <h3 class="text-center m-0" style="font-size: 28px !important;">Bank Receipt</h3>
                    <h4 class="text-center m-0" style="font-size: 28px !important;"><b>{{ $balanceTransfer->targetAccountHead->name }}</b></h4>
                @else
                    <h3 class="text-center m-0" style="font-size: 28px !important;">Cash Receipt</h3>
                    <h4 class="text-center m-0" style="font-size: 28px !important;"><b>{{ $balanceTransfer->targetAccountHead->name }}</b></h4>
                @endif

                <h3 class="text-center m-0 fs-style" style="font-size: 30px !important;">FY : {{ $balanceTransfer->financial_year }}</h3>

            </div>
            <!-- /.col -->
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-4 offset-8">
                <h4 style="margin: 0;font-size: 20px!important;">Receipt No: {{ $balanceTransfer->receipt_no }}</h4>
                <h4 style="margin: 0;font-size: 20px!important;">Date: {{ \Carbon\Carbon::parse($balanceTransfer->date)->format('d-m-Y') }}</h4>
            </div>
        </div>
        <div class="row"  style="margin-top: 15px">
            <div class="col-12">
                <table class="table table-bordered">
                    @if($balanceTransfer->targetAccountHead)
                        <tr>
                            <th width="24%">Bank Name & A/c No.</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="" colspan="2">{{ $balanceTransfer->targetAccountHead->name }}</td>
                        </tr>
                    @else
                        <tr>
                            <th width="24%">Cash</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="" colspan="2">{{ $balanceTransfer->targetAccountHead->name }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th width="24%">Cheque No.</th>
                        <th width="2%" class="text-center">:</th>
                        <td width="" colspan="">{{ $balanceTransfer->source_cheque_no }}</td>
                    </tr>
                    <tr>
                        <th width="24%">Cheque Date</th>
                        <th width="2%" class="text-center">:</th>
                        <td width="" colspan="">{{ $balanceTransfer->source_cheque_date ? \Carbon\Carbon::parse($balanceTransfer->source_cheque_date)->format('d-m-Y') : '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row " style="min-height: 40px !important;margin-top: 50px">
        <div class="col-12">
            <table class="table body-table table-bordered">
                <tr>
                    <th  class="text-center" width="50%">Brief Description</th>
                    <th class="text-center">Account</th>
                    <th class="text-center"></th>
                    <th class="text-center">Amount(TK)</th>
                </tr>
                <tr>
                    <td style="height: 187px;border-bottom: 1px solid #000 !important;"><b>Fund Transfer From </b> {{ $balanceTransfer->sourceAccountHead->name }}
                        <br> <b>To</b>
                        @if($balanceTransfer->type == 3)
                            {{ $balanceTransfer->targetAccountHead->name ?? '' }}
                        @else
                            {{ $balanceTransfer->targetAccountHead->name ?? '' }}
                        @endif
                    </td>
                    <td style="border-bottom: 1px solid #000 !important;" class="text-center">
                        {{ $balanceTransfer->sourceAccountHead->account_code }}
                    </td>
                    <td class="text-center" style="border-top: 1px solid #000 !important;border-bottom: 1px solid #000 !important;">CR.</td>
                    <th style="border-bottom: 1px solid #000 !important;" class="text-right">{{ number_format($balanceTransfer->amount,2) }}</th>
                </tr>

                <tr>
                    <th class="text-left" colspan="">Total(in word) = {{ $balanceTransfer->amount_in_word }} Only.</th>

                    <th class="text-center">
                        @if($balanceTransfer->type == 3)
                            {{ $balanceTransfer->targetAccountHead->account_code }}
                        @else
                            {{ $balanceTransfer->targetAccountHead->account_code }}
                        @endif
                    </th>

                    <th class="text-center">DR.</th>
                    <th class="text-right">{{ number_format($balanceTransfer->amount,2) }}</th>
                </tr>
            </table>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <p><b>Note:</b> {{ $balanceTransfer->notes }}</p>
                </div>
            </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<div class="footer-signature-area">
    <div class="row signature-area" style="padding:0 50px!important;">
        <div class="col text-center"><span style="border: 1px solid #000 !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">Prepared By</span></div>
        <div class="col text-center"><span style="border: 1px solid #000 !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">Checked By</span></div>
        <div class="col text-center"><span style="border: 1px solid #000 !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">Approved By</span></div>
    </div>
</div>
<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
