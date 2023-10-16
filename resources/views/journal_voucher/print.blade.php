<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Journal_Voucher_{{$journalVoucher->jv_no}}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('themes/backend/dist/css/adminlte.min.css') }}">
    <style>
        /*html { background-color: #ffd21b; }*/
        body {
            margin: 0 40px;
            /*background-color: #ffd21b;*/
        }
        .table-bordered td{
            border: 1px solid #000 !important;;
        }
        .table-bordered th{
            border: 1px solid #000 !important;
        }
        .table thead th {
            vertical-align: bottom;
            border: 2px solid #000 !important;
        }
        .table body td {
            vertical-align: bottom;
            border-bottom: 1px solid #000 !important;
        }
        .table tfoot th {
            vertical-align: bottom;
            border: 2px solid #000 !important;
        }
        tbody {
            border: 2px solid #000 !important;
        }
        .single-page{
            position: relative;
        }
        .footer-signature-area {
            position: absolute;
            left: 0;
            bottom: -150px;
            width: 100%;
        }
        .invoice {
            border: none;
        }



        @media print {

            @page:first {
                margin-top: 0 !important;

            }
            @page {
                margin-top: 50px !important;
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
<div class="single-page">
    <div class="wrapper">
        <!-- Main content -->
        <section class="invoice">
            <!-- title row -->
            <div class="row" style="">
                <div class="col-12">
                    <h1 class="text-center m-0" style="font-size: 50px !important;font-weight: bold">
                        <img height="80px" src="{{ asset(auth()->user()->sisterConcern->logo ?? 'img/logo.png') }}" alt="">
                        {{ auth()->user()->sisterConcern->company_name ?? '' }}
                    </h1>
                    <h3 class="text-center m-0" style="font-size: 28px !important;">Journal Voucher</h3>
                    <h3 class="text-center m-0 fs-style" style="font-size: 30px !important;">FY : {{ $journalVoucher->financial_year }}</h3>
                </div>
                <!-- /.col -->
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-4 offset-8">
                    <h4 style="margin: 0;font-size: 20px!important;">JV No: {{ $journalVoucher->jv_no }}</h4>
                    <h4 style="margin: 0;font-size: 20px!important;">Date: {{ \Carbon\Carbon::parse($journalVoucher->date)->format('d-m-Y') }}</h4>
                </div>
            </div>
            <div class="row"  style="margin-top: 15px">
                <div class="col-12">
                    <table class="table table-bordered">
                        <tr>
                            <th width="24%">Employee/Party</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="">{{ employeeClientInfo($journalVoucher->payee_depositor_account_head_id)->name ?? '' }}, {{ employeeClientInfo($journalVoucher->payee_depositor_account_head_id)->designation->name ?? '' }}</td>
                            <td><b>ID:</b> {{ employeeClientInfo($journalVoucher->payee_depositor_account_head_id)->id_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <th width="24%">Address</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="" colspan="2">{{ employeeClientInfo($journalVoucher->payee_depositor_account_head_id)->address ?? '' }}</td>
                        </tr>
                        <tr>
                            <th width="24%">Payee e-tin</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="">{{ $journalVoucher->e_tin }}</td>
                            <td width=""><b>Category:</b> {{ $journalVoucher->taxSetion->source ?? '' }}-{{ $journalVoucher->taxSetion->section ?? '' }}</td>
                        </tr>
                        <tr>
                            <th width="24%">Mobile No</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="">{{ employeeClientInfo($journalVoucher->payee_depositor_account_head_id)->mobile_no ?? '' }}</td>
                            <td width=""><b>Email:</b> {{ employeeClientInfo($journalVoucher->payee_depositor_account_head_id)->email  ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row" style="margin-top: 50px;">
                <div class="col-12">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th  class="text-center" width="40%">Brief Description</th>
                            <th class="text-center">Account</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($journalVoucher->journalVoucherDebitDetails as $key => $journalVoucherDebitDetail)
                            <tr>
                                <td>{{ $journalVoucherDebitDetail->accountHead->name }}</td>
                                <td class="text-center">{{ $journalVoucherDebitDetail->accountHead->account_code }}</td>
                                <td class="text-right">{{ number_format($journalVoucherDebitDetail->amount,2) }}</td>
                                <td class="text-right"></td>
                            </tr>
                        @endforeach
                        @foreach($journalVoucher->journalVoucherCreditDetails as $key => $journalVoucherCreditDetail)
                            <tr>
                                <td ><b style="margin-left: 20px !important;">To, </b>{{ $journalVoucherCreditDetail->accountHead->name }}</td>
                                <td class="text-center">{{ $journalVoucherCreditDetail->accountHead->account_code }}</td>
                                <td class="text-right"></td>
                                <td  class="text-right">{{ number_format($journalVoucherCreditDetail->amount,2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th class="text-left" colspan="2">Total(in word) = {{ $inWordAmount->convert($journalVoucher->journalVoucherDebitDetails->sum('amount'),'Taka','Poisa') }} Only.</th>
                            <th class="text-right">{{ number_format($journalVoucher->journalVoucherDebitDetails->sum('amount'),2) }}</th>
                            <th class="text-right">{{ number_format($journalVoucher->journalVoucherCreditDetails->sum('amount'),2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                <b>Note:</b> {{ $journalVoucher->notes }}
                            </p>
                        </div>
                </div>
            </div>
            <!-- /.row -->
            </div>
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
</div>

<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
