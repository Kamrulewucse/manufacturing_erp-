@extends('layouts.app')
@section('title')
    Edit Bank Voucher(BV) # <b>{{ $receiptPayment->receipt_payment_no }}</b>
@endsection
@section('style')
    <style>
        .form-control {
            height: calc(1.9rem + 2px);
            font-size: .8rem;
            border-radius: 0;
        }
        .select2-container--default .select2-selection--single {
            height: calc(1.9rem + 2px);
            font-size: .8rem;
            border-radius: 0;
            padding: 0.26875rem 0.75rem;
        }
        .form-control::placeholder {
            color: #000000;
            opacity: 1; /* Firefox */
        }

        .form-control:-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: #000000;
        }

        .form-control::-ms-input-placeholder { /* Microsoft Edge */
            color: #000000;
        }
        .form-group {
            margin-bottom: 0.6rem;
        }

        legend {
            font-size: 1.4rem;
            font-weight: bold;
        }

        fieldset {
            border-width: 1.4px;
            margin-top: 10px;
        }
        .card-title {
            font-size: 1.5rem;
        }
        .table-bordered thead td, .table-bordered thead th {
            white-space: nowrap;
            font-size: 14px;
        }
        .table td, .table th {
            padding: 5px;
        }
        .table td .form-group{
            margin-bottom: 0!important;
        }
        label:not(.form-check-label):not(.custom-file-label) {
            font-size: 14px;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .account_head_code_area > .select2{
            width: 100% !important;
            max-width: 200px;
        }
        .table td, .table th {
            text-align: center;
        }
        .table td .form-group input{
            text-align: center;

        }
        .table.other th,.table.other td{
            text-align: left;
        }
        input.form-control.amount {
            width: 100px;
        }
        .bank_account_area > .select2 {
            width: 100% !important;
            max-width: 503px !important;
        }
        #payee_select_area > .select2 {
            width: 100% !important;
            max-width: 430px !important;
        }

    </style>
@endsection
@section('content')

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="text-danger">{{$error}}</div>
        @endforeach
    @endif

    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-default">
                <div class="card-header">
                    <h3 class="card-title">Bank Voucher(BV) Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{ route('bank_voucher.edit',['receiptPayment'=>$receiptPayment->id]) }}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="receipt-payment-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend>Basic Information:</legend>
                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('financial_year') ? 'has-error' :'' }}">
                                                    <label for="financial_year">Financial Year <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="financial_year" id="financial_year" readonly value="{{ $receiptPayment->financial_year }}" class="form-control">
                                                    @error('financial_year')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                                    <label for="date">Date <span class="text-danger">*</span></label>
                                                    <input type="text" autocomplete="off" id="date" value="{{ old('date',\Carbon\Carbon::parse($receiptPayment->date)->format('d-m-Y')) }}" name="date" class="form-control date-picker-fiscal-year" placeholder="Enter Date">
                                                    @error('date')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('cheque_no') ? 'has-error' :'' }}">
                                                    <label for="cheque_no">Cheque No <span class="text-danger">*</span></label>
                                                    <input type="text" name="cheque_no" value="{{ old('cheque_no',$receiptPayment->cheque_no) }}" id="cheque_no" class="form-control" placeholder="Enter Cheque no.">
                                                    @error('cheque_no')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group bank_account_area {{ $errors->has('bank_account_code') ? 'has-error' :'' }}">
                                                    <label for="bank_account_code">Bank Account Code <span class="text-danger">*</span></label>
                                                    <input type="hidden" name="bank_account_code" value="{{ $receiptPayment->payment_account_head_id }}">
                                                    <input readonly id="bank_account_code" value="Account:{{ $receiptPayment->paymentAccountHead->name }}|Account Code:{{ $receiptPayment->paymentAccountHead->account_code }}" type="text" class="form-control">

                                                    @error('bank_account_code')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <legend>Payee Information:</legend>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="client_type">Type <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="client_type" name="client_type">
                                                        <option {{ old('client_type',1) == 1 ? 'selected' : '' }} value="1">Existing</option>
                                                        <option {{ old('client_type') == 2 ? 'selected' : '' }} value="2">New</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12" id="payee_select_area">
                                                <div class="form-group {{ $errors->has('payee') ? 'has-error' :'' }}">
                                                    <label for="payee">Payee <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="payee" style="width: 100%;" name="payee">
                                                        <option value="{{ $receiptPayment->payee_depositor_account_head_id }}" selected>{{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->id_no ?? '' }} - {{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->name ?? '' }}</option>
                                                    </select>
                                                    @error('payee')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                    <input type="hidden" name="payee_select_name" id="payee_select_name" value="{{ employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->name ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row hide client_name_area">
                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('payee_name') ? 'has-error' :'' }}">
                                                    <label for="payee_name">Payee Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="payee_name" value="{{ old('payee_name',employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->name ?? '') }}" id="payee_name" class="form-control">
                                                    @error('payee_name')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('designation') ? 'has-error' :'' }}">
                                                    <label for="designation">Designation</label>
                                                    <input type="text" name="designation" id="designation" value="{{ old('designation',employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->designation ?? '') }}" class="form-control" placeholder="Enter Designation">
                                                    @error('designation')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6" >
                                                <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                                                    <label for="mobile_no">Mobile No.</label>
                                                    <input type="text" name="mobile_no" id="mobile_no" value="{{ old('mobile_no',employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->mobile_no ?? '')  }}" class="form-control" placeholder="Enter Mobile No">
                                                    @error('mobile_no')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('email') ? 'has-error' :'' }}">
                                                    <label for="email">Email</label>
                                                    <input type="text" name="email" value="{{ old('email',employeeClientInfo($receiptPayment->payee_depositor_account_head_id)->email ?? '') }}" id="email" class="form-control" placeholder="Enter Email">
                                                    @error('email')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6" >
                                                <div class="form-group {{ $errors->has('e_tin') ? 'has-error' :'' }}">
                                                    <label for="e_tin">Payee eTIN</label>
                                                    <input type="text" name="e_tin" id="e_tin" value="{{ old('e_tin',$receiptPayment->e_tin) }}" class="form-control" placeholder="Enter eTin">
                                                    @error('e_tin')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('nature_of_organization') ? 'has-error' :'' }}">
                                                    <label for="nature_of_organization">Nature of Organization</label>
                                                    <select name="nature_of_organization" id="nature_of_organization" class="form-control select2">
                                                        <option value="">Nature of Organization</option>
                                                        @foreach($taxSections as $taxSection)
                                                            <option {{ old('nature_of_organization',$receiptPayment->tax_section_id) == $taxSection->id ? 'selected' : ''  }} value="{{ $taxSection->id }}">{{ $taxSection->source }} - {{ $taxSection->section }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('nature_of_organization')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-md-12 mt-3 mb-2">
                                    <div class="table-responsive">
                                        <fieldset>
                                            <legend>Payment & Other Details:</legend>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Account Head <span class="text-danger">*</span></th>
                                                    <th>Amount <span class="text-danger">*</span></th>
                                                    <th>VAT Base Amount</th>
                                                    <th>VAT Rate</th>
                                                    <th>VAT Amount</th>
                                                    <th>AIT Base Amount</th>
                                                    <th>AIT Rate</th>
                                                    <th>AIT Amount</th>
                                                    <th></th>

                                                </tr>
                                                </thead>
                                                <tbody id="receipt-payment-container">
                                                @if (old('account_head_code') != null && sizeof(old('account_head_code')) > 0)
                                                    @foreach(old('account_head_code') as $item)
                                                        <tr class="receipt-payment-item">
                                                            <td>
                                                                <div class="form-group account_head_code_area {{ $errors->has('account_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <select class="form-control select2 account_head_code" name="account_head_code[]" data-placeholder="Search Account Head Code">
                                                                        <option value="">Select Account Head Code</option>
                                                                        @if (old('account_head_code.'.$loop->index) != '')
                                                                            <option value="{{ old('account_head_code.'.$loop->index) }}" selected>{{ old('account_head_code_name.'.$loop->index) }}</option>
                                                                        @endif
                                                                    </select>
                                                                    <input type="hidden" name="account_head_code_name[]" class="account_head_code_name" value="{{ old('account_head_code_name.'.$loop->index) }}">

                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('amount.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <input type="text" value="{{ old('amount.'.$loop->index) }}" name="amount[]" class="form-control amount">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('vat_base_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <input type="text" name="vat_base_amount[]" value="{{ old('vat_base_amount.'.$loop->index) }}" class="form-control vat_base_amount">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('vat_rate.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <input type="text" name="vat_rate[]" value="{{ old('vat_rate.'.$loop->index) }}" class="form-control vat_rate">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('vat_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <input type="text" readonly name="vat_amount[]" value="{{ old('vat_amount.'.$loop->index) }}" class="form-control vat_amount">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('ait_base_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <input type="text" name="ait_base_amount[]" value="{{ old('ait_base_amount.'.$loop->index) }}" class="form-control ait_base_amount">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('ait_rate.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <input type="text" name="ait_rate[]" value="{{ old('ait_rate.'.$loop->index) }}" class="form-control ait_rate">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('ait_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <input type="text" readonly name="ait_amount[]" value="{{ old('ait_amount.'.$loop->index) }}" class="form-control ait_amount">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-times"></i></a>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @else
                                                    @foreach($receiptPayment->receiptPaymentDetails as $receiptPaymentDetail)
                                                    <tr class="receipt-payment-item">

                                                        <td>
                                                            <div class="form-group account_head_code_area">
                                                                <select class="form-control select2 account_head_code" style="width: 100%;" name="account_head_code[]" data-placeholder="Search Account Head Code" required>
                                                                    <option value="">Search Account Head Code</option>
                                                                   <option value="{{ $receiptPaymentDetail->account_head_id }}" selected>{{ $receiptPaymentDetail->accountHead->account_code }} - {{ $receiptPaymentDetail->accountHead->name }}</option>
                                                                </select>
                                                                <input type="hidden" name="account_head_code_name[]" value="{{ $receiptPaymentDetail->accountHead->name }}" class="account_head_code_name">

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" value="{{ $receiptPaymentDetail->amount }}" name="amount[]"  class="form-control amount">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" value="{{ $receiptPaymentDetail->vat_base_amount }}" name="vat_base_amount[]" class="form-control vat_base_amount">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" value="{{ $receiptPaymentDetail->vat_rate }}" name="vat_rate[]" class="form-control vat_rate">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" value="{{ $receiptPaymentDetail->ait_amount }}" readonly name="vat_amount[]" class="form-control vat_amount">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" value="{{ $receiptPaymentDetail->ait_base_amount }}" name="ait_base_amount[]" class="form-control ait_base_amount">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" value="{{ $receiptPaymentDetail->ait_rate }}" name="ait_rate[]" class="form-control ait_rate">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" readonly name="ait_amount[]" class="form-control ait_amount">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-times"></i></a>
                                                        </td>

                                                    </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th colspan="1" class="text-right">
                                                        Total
                                                    </th>
                                                    <th class="text-center" id="total-amount">0.00</th>
                                                    <th class="text-center" id="total-vat-base-amount">0.00</th>
                                                    <th></th>
                                                    <th class="text-center" id="total-vat-amount">0.00</th>

                                                    <th class="text-center" id="total-ait-base-amount">0.00</th>
                                                    <th></th>
                                                    <th class="text-center" id="total-ait-amount">0.00</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-left">
                                                        <a role="button" class="btn btn-dark btn-sm" id="btn-add-voucher"><i class="fa fa-plus"></i></a>
                                                    </th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                            <table class="table other table-bordered">
                                                <thead>
                                                <tr>
                                                    <th width="30%">Parent Account Head</th>
                                                    <th width="30%">Other Deduction Account Head</th>
                                                    <th width="15%" class="text-center">Deduction Amount</th>
                                                    <th width="4%" class="text-center"></th>
                                                </tr>
                                                </thead>
                                                <tbody id="receipt-payment-other-container">
                                                @if (old('other_account_head_code') != null && sizeof(old('other_account_head_code')) > 0)
                                                    @foreach(old('other_account_head_code') as $item)
                                                        <tr class="receipt-payment-other-item">
                                                            <td>
                                                                <div class="form-group other_account_head_code_area {{ $errors->has('parent_account_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <select class="form-control select2 parent_account_head_code" name="parent_account_head_code[]" data-placeholder="Search Parent Account Head Code">
                                                                        <option value="">Select Parent Account Head Code</option>
                                                                        @if (old('parent_account_head_code.'.$loop->index) != '')
                                                                            <option value="{{ old('parent_account_head_code.'.$loop->index) }}" selected>{{ old('parent_account_head_code_name.'.$loop->index) }}</option>
                                                                        @endif
                                                                    </select>
                                                                    <input type="hidden" name="parent_account_head_code_name[]" class="parent_account_head_code_name" value="{{ old('parent_account_head_code_name.'.$loop->index) }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group other_account_head_code_area {{ $errors->has('other_account_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <select class="form-control select2 other_account_head_code" name="other_account_head_code[]" data-placeholder="Search Other Account Head Code">
                                                                        <option value="">Select Other Account Head Code</option>
                                                                        @if (old('account_head_code.'.$loop->index) != '')
                                                                            <option value="{{ old('other_account_head_code.'.$loop->index) }}" selected>{{ old('other_account_head_code_name.'.$loop->index) }}</option>
                                                                        @endif
                                                                    </select>
                                                                    <input type="hidden" name="other_account_head_code_name[]" class="other_account_head_code_name" value="{{ old('other_account_head_code_name.'.$loop->index) }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('other_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <input type="text" value="{{ old('other_amount.'.$loop->index) }}" name="other_amount[]" class="form-control other_amount">
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-other-remove"><i class="fa fa-times"></i></a>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @else
                                                    @foreach($receiptPayment->receiptPaymentOtherDetails as $receiptPaymentOtherDetail)
                                                        <tr class="receipt-payment-other-item">
                                                            <td>
                                                                <div class="form-group other_account_head_code_area">
                                                                    <select class="form-control select2 parent_account_head_code" name="parent_account_head_code[]">
                                                                        <option value="">Search Parent Account Head Code</option>
                                                                        <option value="{{ $receiptPaymentOtherDetail->parent_deduction_account_head_id }}" selected>{{ $receiptPaymentOtherDetail->parentDeductionAccountHead->account_code ?? '' }} - {{ $receiptPaymentOtherDetail->parentDeductionAccountHead->name ?? '' }}</option>
                                                                    </select>
                                                                    <input type="hidden" name="parent_account_head_code_name[]" value="{{ $receiptPaymentOtherDetail->parentDeductionAccountHead->name ?? '' }}" class="parent_account_head_code_name">

                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group other_account_head_code_area">
                                                                    <select class="form-control select2 other_account_head_code" name="other_account_head_code[]">
                                                                        <option value="">Search Other Account Head Code</option>
                                                                        <option value="{{ $receiptPaymentOtherDetail->account_head_id }}" selected>{{ $receiptPaymentOtherDetail->accountHead->account_code }} - {{ $receiptPaymentOtherDetail->accountHead->name }}</option>
                                                                    </select>
                                                                    <input type="hidden" name="other_account_head_code_name[]" value="{{ $receiptPaymentOtherDetail->accountHead->name }}" class="other_account_head_code_name">

                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="text" value="{{ $receiptPaymentOtherDetail->other_amount }}" name="other_amount[]" class="form-control other_amount">
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-other-remove"><i class="fa fa-times"></i></a>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-right">Total</th>
                                                    <th class="text-center" id="total-other-amount">0.00</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-left">
                                                        <a role="button" class="btn btn-dark btn-sm" id="btn-add-other-voucher"><i class="fa fa-plus"></i></a>
                                                    </th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-6 offset-md-6">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="56.5%" class="text-right">Sub Total</th>
                                                            <th class="text-right" id="sub-total">0.00</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right">VAT Total</th>
                                                            <th class="text-right" id="vat-total">0.00</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right">AIT Total</th>
                                                            <th class="text-right" id="ait-total">0.00</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right">Other Total</th>
                                                            <th class="text-right" id="other-total">0.00</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right">Net Total</th>
                                                            <th class="text-right" id="grand-total">0.00</th>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Payment Details (Narration)</label>
                                                    <div class="form-group {{ $errors->has('notes') ? 'has-error' :'' }}">
                                                        <input type="text" value="{{ old('notes',$receiptPayment->notes) }}" name="notes" class="form-control" placeholder="Enter Payment Details (Narration)...">
                                                        @error('notes')
                                                        <span class="help-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <label>Supporting Documents</label>
                                                    <div class="form-group {{ $errors->has('supporting_document') ? 'has-error' :'' }}">
                                                        <input type="file" name="supporting_document[]" multiple class="form-control">
                                                        @error('supporting_document')
                                                        <span class="help-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button id="btn-save" type="submit" class="btn btn-dark">Save</button>
                        <a href="{{ route('bank_voucher') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
    <template id="receipt-payment-template">
        <tr class="receipt-payment-item">
            <td>
                <div class="form-group account_head_code_area">
                    <select class="form-control select2 account_head_code" style="width: 100%;" name="account_head_code[]" data-placeholder="Search Account Head Code">
                        <option value="">Search Account Head Code</option>
                    </select>
                    <input type="hidden" name="account_head_code_name[]" class="account_head_code_name">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" name="amount[]" class="form-control amount">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" name="vat_base_amount[]" class="form-control vat_base_amount">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" name="vat_rate[]" class="form-control vat_rate">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" readonly name="vat_amount[]" class="form-control vat_amount">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" name="ait_base_amount[]" class="form-control ait_base_amount">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" name="ait_rate[]" class="form-control ait_rate">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" readonly name="ait_amount[]" class="form-control ait_amount">
                </div>
            </td>
            <td class="text-center">
                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-times"></i></a>
            </td>

        </tr>
    </template>
    <template id="receipt-other-payment-template">
        <tr class="receipt-payment-other-item">
            <td>
                <div class="form-group other_account_head_code_area">
                    <select class="form-control select2 parent_account_head_code" name="parent_account_head_code[]">
                        <option value="">Search Parent Account Head Code</option>
                    </select>
                    <input type="hidden" name="parent_account_head_code_name[]" class="parent_account_head_code_name">
                </div>
            </td>
            <td>
                <div class="form-group other_account_head_code_area">
                    <select class="form-control select2 other_account_head_code" name="other_account_head_code[]">
                        <option value="">Search Other Account Head Code</option>
                    </select>
                    <input type="hidden" name="other_account_head_code_name[]" class="other_account_head_code_name">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" name="other_amount[]" class="form-control other_amount">
                </div>
            </td>
            <td class="text-center">
                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-other-remove"><i class="fa fa-times"></i></a>

            </td>
        </tr>
    </template>
@endsection
@section('script')
    <script>
        $(function (){
            intSelect2();
            fiscalYearDateRange('{{$fiscalYear}}');
            formSubmitConfirm('btn-save');

            $('#client_type').change(function (){
                var clientType = $(this).val();
                if(clientType != ''){
                    if(clientType == 1){
                        $("#payee_select_area").show();
                        $(".client_name_area").hide();
                    }else{
                        $("#payee_select_area").hide();
                        $(".client_name_area").show();
                    }
                }
            })
            $('#client_type').trigger("change");


            $('#btn-add-voucher').click(function () {
                var html = $('#receipt-payment-template').html();
                var item = $(html);

                $('#receipt-payment-container').append(item);

                intSelect2();

                if ($('.receipt-payment-item').length >= 1 ) {
                    $('.btn-remove').show();
                }

                calculate();
            });
            $('#btn-add-other-voucher').click(function () {
                var html2 = $('#receipt-other-payment-template').html();
                var item2 = $(html2);
                $('#receipt-payment-other-container').append(item2);
                intSelect2();
                if ($('.receipt-payment-other-item').length >= 0 ) {
                    $('.btn-other-remove').show();
                }

                calculate();
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.receipt-payment-item').remove();
                if ($('.receipt-payment-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
                calculate();
            });
            $('body').on('click', '.btn-other-remove', function () {
                $(this).closest('.receipt-payment-other-item').remove();
                if ($('.receipt-payment-other-item').length <= 1 ) {
                    $('.btn-other-remove').hide();
                }
                calculate();
            });

            if ($('.receipt-payment-item').length <= 0 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }
            if ($('.receipt-payment-other-item').length <= 0 ) {
                $('.btn-other-remove').hide();
            } else {
                $('.btn-other-remove').show();
            }


            $('body').on('keyup', '.amount,.other_amount,.vat_base_amount,.vat_rate,.ait_base_amount,.ait_rate', function () {
                calculate();
            });
            calculate();
        });
        function calculate() {

            var totalAmount = 0;
            var totalOtherAmount = 0;
            var totalVatBaseAmount =0;
            var totalVatAmount = 0;

            var totalAitBaseAmount =0;
            var totalAitAmount = 0;

            $('.receipt-payment-item').each(function(i, obj) {
                var amount = $('.amount:eq('+i+')').val();
                var vatRate = $('.vat_rate:eq('+i+')').val();
                var vatBaseAmount = $('.vat_base_amount:eq('+i+')').val();
                var aitRate = $('.ait_rate:eq('+i+')').val();
                var aitBaseAmount = $('.ait_base_amount:eq('+i+')').val();


                if (amount == '' || amount < 0 || !$.isNumeric(amount))
                    amount = 0;

                if (vatBaseAmount == '' || vatBaseAmount < 0 || !$.isNumeric(vatBaseAmount))
                    vatBaseAmount = 0;

                if (vatRate == '' || vatRate < 0 || !$.isNumeric(vatRate))
                    vatRate = 0;

                if (aitRate == '' || aitRate < 0 || !$.isNumeric(aitRate))
                    aitRate = 0;


                if (aitBaseAmount == '' || aitBaseAmount < 0 || !$.isNumeric(aitBaseAmount))
                    aitBaseAmount = 0;

                $('.vat_amount:eq('+i+')').val(((vatBaseAmount * vatRate)/100).toFixed(2));
                $('.ait_amount:eq('+i+')').val(((aitBaseAmount * aitRate)/100).toFixed(2));


                totalAmount += parseFloat(amount);
                totalVatBaseAmount += parseFloat(vatBaseAmount);
                totalVatAmount += (vatBaseAmount * vatRate)/100;
                totalAitBaseAmount += parseFloat(aitBaseAmount);
                totalAitAmount += (aitBaseAmount * aitRate)/100;

            });

            $('.receipt-payment-other-item').each(function(i, obj) {
                var otherAmount = $('.other_amount:eq('+i+')').val();
                if (otherAmount == '' || otherAmount < 0 || !$.isNumeric(otherAmount))
                    otherAmount = 0;

                totalOtherAmount += parseFloat(otherAmount);

            });
            $('#total-other-amount').html(jsNumberFormat(totalOtherAmount));

            $('#total-amount').html(jsNumberFormat(totalAmount));
            $('#total-vat-base-amount').html(jsNumberFormat(totalVatBaseAmount));
            $('#total-vat-amount').html(jsNumberFormat(totalVatAmount));
            $('#total-ait-base-amount').html(jsNumberFormat(totalAitBaseAmount));
            $('#total-ait-amount').html(jsNumberFormat(totalAitAmount));

            $('#sub-total').html(jsNumberFormat(totalAmount));
            $('#vat-total').html(jsNumberFormat(totalVatAmount));
            $('#ait-total').html(jsNumberFormat(totalAitAmount));
            $('#other-total').html(jsNumberFormat(totalOtherAmount));

            var grandTotal = parseFloat(totalAmount) - (parseFloat(totalVatAmount) + parseFloat(totalAitAmount) + parseFloat(totalOtherAmount))

            $('#grand-total').html(jsNumberFormat(grandTotal));
        }

        function intSelect2(){
            $('.date-picker').datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy'
            });
            $('.select2').select2()

            $('#payee').select2({
                ajax: {
                    url: "{{ route('payee_json') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $('#payee').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $("#payee").index(this);
                $('#payee_select_name:eq('+index+')').val(data.text);
            });

            $('.account_head_code').select2({
                ajax: {
                    url: "{{ route('account_head_code.json') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $('.account_head_code').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $(".account_head_code").index(this);
                $('.account_head_code_name:eq('+index+')').val(data.text);
            });

            $('.parent_account_head_code').select2({
                ajax: {
                    url: "{{ route('account_head_code.json') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $('.parent_account_head_code').on('select2:select', function (e) {
                var data4 = e.params.data;
                var index4 = $(".parent_account_head_code").index(this);
                $('.parent_account_head_code_name:eq('+index4+')').val(data4.text);
            });

            $('.other_account_head_code').select2({
                ajax: {
                    url: "{{ route('account_head_code.json') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });

            $('.other_account_head_code').on('select2:select', function (e) {
                var data2 = e.params.data;
                var index2 = $(".other_account_head_code").index(this);
                $('.other_account_head_code_name:eq('+index2+')').val(data2.text);
            });

        }
    </script>
@endsection
