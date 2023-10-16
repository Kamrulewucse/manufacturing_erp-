<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cash_Receipt_{{ $from }}_to_{{ $to }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('themes/backend/dist/css/adminlte.min.css') }}">
    <style>
        /*html { background-color: #ffd21b; }*/
        body { margin:0 50px;
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
            bottom: 20px;
            width: 100%;
        }
        .invoice {
            border: none;
        }
        .single-page{
            position: relative;
        }
        @page {
            margin: 0;
        }
        @media print {
            .single-page {
                margin-top: 0px !important;
                page-break-after: always;
            }
        }
        .fs-style{
            font-size: 24px !important;
            letter-spacing: 7px!important;
            font-weight: 900!important;
        }
    </style>
</head>
<body>
@foreach($receiptPayments as $receiptPayment)
<div class="single-page">
    <div class="wrapper">
        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center m-0" style="font-size: 50px !important;font-weight: bold">
                        <img height="80px" src="{{ asset(auth()->user()->sisterConcern->logo ?? 'img/logo.png') }}" alt="">
                        {{ auth()->user()->sisterConcern->company_name ?? '' }}
                    </h1>
                    <h3 class="text-center m-0" style="font-size: 28px !important;">Cheque Receipt</h3>
                    <h3 class="text-center m-0 fs-style" style="font-size: 30px !important;">FY : {{ $receiptPayment->financial_year }}</h3>
                </div>
                <!-- /.col -->
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-4 offset-8">
                    <h4 style="margin: 0;font-size: 20px!important;">Receipt No: {{ $receiptPayment->receipt_payment_no }}</h4>
                    <h4 style="margin: 0;font-size: 20px!important;">Date: {{ \Carbon\Carbon::parse($receiptPayment->date)->format('d-m-Y') }}</h4>
                </div>
            </div>
            <div class="row"  style="margin-top: 15px">
                <div class="col-12">
                    <table class="table table-bordered">
                        <tr>
                            <th width="24%">Payee Name & Designation</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="">{{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->name ?? '' }}, {{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->designation->name ?? '' }}</td>
                            <td><b>ID:</b> {{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->id_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <th width="24%">Address</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="" colspan="2">{{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->address ?? '' }}</td>
                        </tr>
                        <tr>
                            <th width="24%">Payee e-tin</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="">{{ $receiptPayment->e_tin }}</td>
                            <td width=""><b>Category:</b> {{ $receiptPayment->taxSetion->source ?? '' }}-{{ $receiptPayment->taxSetion->section ?? '' }}</td>
                        </tr>
                        <tr>
                            <th width="24%">Mobile No</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="">{{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->mobile_no ?? '' }}</td>
                            <td width=""><b>Email:</b> {{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->email  ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <span><b>Received Details:</b></span>
                    <table class="table body-table table-bordered">
                        <tr>
                            <th  class="text-center" width="60%">Brief Description</th>
                            <th class="text-center">Account</th>
                            <th class="text-center"></th>
                            <th class="text-center">Amount(TK)</th>
                        </tr>

                        <tr>
                            <td style="border-bottom: 1px solid transparent !important;"><b>Received:</b></td>
                            <td style="border-bottom: 1px solid transparent !important;" class="text-center"></td>
                            <td style="border-bottom: 1px solid transparent !important;"></td>
                            <td style="border-bottom: 1px solid transparent !important;" class="text-right"></td>
                        </tr>
                        @foreach($receiptPayment->receiptPaymentDetails as $key => $receiptPaymentDetail)
                            <tr>
                                <td style="border-bottom: 1px solid transparent !important;">{{ $receiptPaymentDetail->accountHead->name }}</td>
                                <td style="border-bottom: 1px solid transparent !important;" class="text-center">{{ $receiptPaymentDetail->accountHead->account_code }}</td>
                                <td style="{{ count($receiptPayment->receiptPaymentDetails) == $key + 1 ? 'border-bottom: 1px solid #000 !important' : 'border-bottom: 1px solid transparent !important'  }};"></td>
                                <td style="{{ count($receiptPayment->receiptPaymentDetails) == $key + 1 ? 'border-bottom: 1px solid #000 !important' : 'border-bottom: 1px solid transparent !important'  }};" class="text-right">{{ number_format($receiptPaymentDetail->amount,2) }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td style="border-bottom: 1px solid transparent !important;"></td>
                            <td style="border-bottom: 1px solid transparent !important;" class="text-center"></td>
                            <td class="text-center" style="border-bottom: 1.5px solid #000 !important;">Cr</td>
                            <th style="border-bottom: 1.5px solid #000 !important;" class="text-right">{{ number_format($receiptPayment->receiptPaymentDetails->sum('amount'),2) }}</th>
                        </tr>


                        @if($receiptPayment->receiptPaymentOtherDetails)
                            <tr>
                                <td style="border-bottom: 1px solid transparent !important;"><b>Deductions:</b></td>
                                <td style="border-bottom: 1px solid transparent !important;" class="text-center"></td>
                                <td style="border-bottom: 1px solid transparent !important;"></td>
                                <td style="border-bottom: 1px solid transparent !important;" class="text-right"></td>
                            </tr>
                        @endif
                        @foreach($receiptPayment->receiptPaymentOtherDetails as $key => $receiptPaymentOtherDetail)
                            <tr>
                                <td style="border-top: 1px solid transparent !important;border-bottom: 1px solid transparent !important;">{{ $receiptPaymentOtherDetail->accountHead->name ?? '' }}</td>
                                <td style="border-top: 1px solid transparent !important;border-bottom: 1px solid transparent !important;" class="text-center">{{ $receiptPaymentOtherDetail->accountHead->account_code ?? '' }}</td>
                                <td style="{{ count($receiptPayment->receiptPaymentOtherDetails) == $key + 1 ? 'border-bottom: 1px solid #000 !important' : 'border-bottom: 1px solid transparent !important'  }};"></td>
                                <td style="{{ count($receiptPayment->receiptPaymentOtherDetails) == $key + 1 ? 'border-bottom: 1px solid #000 !important' : 'border-bottom: 1px solid transparent !important'  }};" class="text-right">{{ number_format($receiptPaymentOtherDetail->other_amount ,2)}}</td>
                            </tr>
                        @endforeach
                        @if($receiptPayment->receiptPaymentOtherDetails)
                            <tr>
                                <td  style="border-top: 1px solid transparent !important;" ></td>
                                <td style="border-top: 1px solid transparent !important;"  class="text-center"></td>
                                <td class="text-center" style="border-top: 1.5px solid #000 !important;">Dr</td>
                                <th style="border-top: 1.5px solid #000 !important;" class="text-right">{{ number_format($receiptPayment->receiptPaymentOtherDetails->sum('other_amount') ,2) }}</th>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-left" colspan="">Total(in word) = {{ $inWordAmount->convert($receiptPayment->receiptPaymentOtherDetails->sum('other_amount'),'Taka','Poisa') }} Only.</th>
                            <th class="text-center">{{ $receiptPayment->paymentAccountHead->account_code }}</th>
                            <th class="text-center">Dr.</th>
                            <th class="text-right">{{ number_format($receiptPayment->receiptPaymentDetails->sum('amount') - $receiptPayment->receiptPaymentOtherDetails->sum('other_amount')),2 }}</th>
                        </tr>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <p><b>Note:</b> {{ $receiptPayment->notes }}</p>
                        </div>

                    </div>
                </div>
            </div>
            <!-- /.row -->
        </section>
    </div>
    <div class="footer-signature-area">
        <div class="row signature-area" style="padding:0 50px!important;">
            <div class="col text-center"><span style="border: 1px solid #000 !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">Prepared By</span></div>
            <div class="col text-center"><span style="border: 1px solid #000 !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">Checked By</span></div>
            <div class="col text-center"><span style="border: 1px solid #000 !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">Approved By</span></div>
        </div>
    </div>
</div>
@endforeach

<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
