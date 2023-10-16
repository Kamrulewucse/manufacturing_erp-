@extends('layouts.app')
@section('title')
   Cheque Receipt No: {{ $receiptPayment->receipt_payment_no }}
@endsection
@section('style')
    <style>

        table,.table,table td,

        .table-bordered{
            border: 1px solid #000000;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #000000 !important;
        }
        .table.body-table td,.table.body-table th {
            padding: 2px 7px;
        }
        ul.document-list {margin: 0;padding: 0;list-style: auto;}

        ul.document-list li {display: inline-block;}
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <a target="_blank" class="btn btn-dark btn-sm" href="{{ route('cheque_receipt_print',['receiptPayment'=>$receiptPayment->id]) }}"><i class="fa fa-print"></i></a>
        </div>
        <div class="card-body">
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
                            <th width="24%">Depositor's Name & Designation</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="">{{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->name ?? '' }}, {{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->designation->name ?? '' }}</td>
                            <td><b>ID:</b> {{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->id_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <th width="24%">Address</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="" colspan="2">{{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->address ?? '' }}</td>
                        </tr>
                        @if($receiptPayment->project)
                            <tr>
                                <th width="24%">Project Name</th>
                                <th width="2%" class="text-center">:</th>
                                <td colspan="2">{{ $receiptPayment->project->name??'' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th width="24%">Payee e-tin</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="">{{ $receiptPayment->e_tin }}</td>
                            <td width=""><b>Category:</b> {{ $receiptPayment->taxSetion->source ?? '' }}-{{ $receiptPayment->taxSetion->section ?? '' }}</td>
                        </tr>
                        <tr>
                            <th width="24%">Mobile No</th>
                            <th width="2%" class="text-center">:</th>
                            <td width="">{{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->mobile ?? '' }}</td>
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
                            <th class="text-left" colspan="">Total(in word) = {{ $inWordAmount->convert($receiptPayment->receiptPaymentDetails->sum('amount') -  $receiptPayment->receiptPaymentOtherDetails->sum('other_amount'),'Taka','Poisa') }} Only.</th>
                            <th class="text-center">{{ $receiptPayment->paymentAccountHead->account_code }}</th>
                            <th class="text-center">Dr.</th>
                            <th class="text-right">{{ number_format($receiptPayment->receiptPaymentDetails->sum('amount') - $receiptPayment->receiptPaymentOtherDetails->sum('other_amount'),2) }}</th>
                        </tr>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <p><b>Note:</b> {{ $receiptPayment->notes }}</p>
                        </div>
                        @if(count($receiptPayment->files) > 0)
                            <div class="col-md-6">
                                <b>Supporting Documents:</b>
                                <ul class="document-list">
                                    @foreach($receiptPayment->files as $file)
                                        <li>
                                            <a target="_blank" class="btn btn-dark btn-sm" href="{{ asset($file->file) }}">Download <i class="fa fa-file-download"></i></a>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row signature-area" style="margin-top: 30px">
                <div class="col text-center"><span style="border: 1px solid #000 !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">Prepared By</span></div>
                <div class="col text-center"><span style="border: 1px solid #000 !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">Checked By</span></div>
                <div class="col text-center"><span style="border: 1px solid #000 !important;display: block;padding: 18px;font-size: 20px;font-weight: bold">Approved By</span></div>
            </div>
        </div>
    </div>

@endsection



