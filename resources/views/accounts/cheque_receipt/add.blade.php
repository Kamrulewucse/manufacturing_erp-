@extends('layouts.app')
@section('title','Create Cheque Receipt(CR)')
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

        .table td .form-group {
            margin-bottom: 0 !important;
        }

        label:not(.form-check-label):not(.custom-file-label) {
            font-size: 14px;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .table td, .table th {
            text-align: center;
        }

        .table td .form-group input {
            text-align: center;

        }

        .table.other th, .table.other td {
            text-align: left;
        }

        .bank_account_area > .select2 {
            width: 100% !important;
            max-width: 430px !important;
        }

        #payee_select_area > .select2 {
            width: 100% !important;
            max-width: 430px !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-default">
                <div class="card-header">
                    <h3 class="card-title">Cheque Receipt(CR) Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{ route('cheque_receipt.create') }}"
                      class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="receipt-payment-item">
                            <div class="row">

                                <div class="col-md-6">
                                    <fieldset>
                                        <legend>Basic Information:</legend>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div
                                                    class="form-group {{ $errors->has('financial_year') ? 'has-error' :'' }}">
                                                    <label for="financial_year">Select Financial Year <span
                                                            class="text-danger">*</span></label>
                                                    @php
                                                        $currentFinancialYear = date('m')>6? date('Y'): (date('Y')-1);
                                                    @endphp
                                                    <select class="form-control select2" name="financial_year"
                                                            id="financial_year">
                                                        <option value="">Select Year</option>
                                                        @for($i=2022; $i <= date('Y'); $i++)
                                                            <option
                                                                value="{{ $i }}" {{ old('financial_year',$currentFinancialYear) == $i ? 'selected' : '' }}>{{ $i }}
                                                                -{{ $i+1 }}</option>
                                                        @endfor
                                                    </select>
                                                    @error('financial_year')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                                    <label for="date">Date <span class="text-danger">*</span></label>
                                                    <input type="text" value="{{ old('date',date('Y-m-d')) }}" autocomplete="off"
                                                           id="date" name="date"
                                                           class="form-control date-picker"
                                                           placeholder="Enter Date">
                                                    @error('date')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div
                                                    class="form-group bank_account_area {{ $errors->has('bank_account_code') ? 'has-error' :'' }}">
                                                    <label for="bank_account_code">Bank Account Code <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="bank_account_code"
                                                            name="bank_account_code">
                                                        <option value="">Search Bank Account Code</option>
                                                        @if (old('bank_account_code') != '')
                                                            <option value="{{ old('bank_account_code') }}" selected>{{ old('bank_account_code_name') }}</option>
                                                        @endif
                                                    </select>
                                                    <input type="hidden" name="bank_account_code_name" class="bank_account_code_name" id="bank_account_code_name" value="{{ old('bank_account_code_name') }}">

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
                                        <legend>Depositor Information:</legend>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="client_type">Type <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="client_type" name="client_type">
                                                        <option
                                                            {{ old('client_type') == 1 ? 'selected' : '' }} value="1">
                                                            Existing
                                                        </option>
                                                        <option
                                                            {{ old('client_type') == 2 ? 'selected' : '' }} value="2">
                                                            New
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12" id="payee_select_area">
                                                <div
                                                    class="form-group {{ $errors->has('depositor') ? 'has-error' :'' }}">
                                                    <label for="payee">Depositor <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="payee" style="width: 100%;"
                                                            name="depositor" data-placeholder="Search Depositor">
                                                        <option value="">Search Depositor</option>
                                                        @if (old('depositor') != '')
                                                            <option value="{{ old('depositor') }}"
                                                                    selected>{{ old('payee_select_name') }}</option>
                                                        @endif
                                                    </select>
                                                    @error('depositor')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                    <input type="hidden" name="payee_select_name" id="payee_select_name"
                                                           value="{{ old('payee_select_name') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row hide client_name_area">
                                            <div class="col-md-6">
                                                <div
                                                    class="form-group {{ $errors->has('depositor_name') ? 'has-error' :'' }}">
                                                    <label for="payee_name">Depositor's Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="depositor_name"
                                                           value="{{ old('depositor_name') }}" id="payee_name"
                                                           class="form-control" placeholder="Enter Depositor's Name">
                                                    @error('depositor_name')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="form-group {{ $errors->has('designation') ? 'has-error' :'' }}">
                                                    <label for="designation">Depositor's Designation</label>
                                                    <input type="text" name="designation"
                                                           value="{{ old('designation') }}" id="designation"
                                                           class="form-control" placeholder="Enter Designation">
                                                    @error('designation')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('address') ? 'has-error' :'' }}">
                                                    <label for="address">Depositor's Address</label>
                                                    <input type="text" name="address" value="{{ old('address') }}"
                                                           id="address" class="form-control"
                                                           placeholder="Enter Address">
                                                    @error('address')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                                                    <label for="mobile_no">Depositor's Mobile No.</label>
                                                    <input type="text" name="mobile_no" id="mobile_no"
                                                           value="{{ old('mobile_no') }}" class="form-control"
                                                           placeholder="Enter Mobile No">
                                                    @error('mobile_no')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('email') ? 'has-error' :'' }}">
                                                    <label for="email">Email</label>
                                                    <input type="text" name="email" value="{{ old('email') }}"
                                                           id="email" class="form-control" placeholder="Enter Email">
                                                    @error('email')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group {{ $errors->has('e_tin') ? 'has-error' :'' }}">
                                                    <label for="e_tin">Depositor's eTIN</label>
                                                    <input type="text" name="e_tin" id="e_tin"
                                                           value="{{ old('e_tin') }}" class="form-control"
                                                           placeholder="Enter eTin">
                                                    @error('e_tin')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="form-group {{ $errors->has('nature_of_organization') ? 'has-error' :'' }}">
                                                    <label for="nature_of_organization">Nature of Organization</label>
                                                    <select name="nature_of_organization" id="nature_of_organization"
                                                            class="form-control select2">
                                                        <option value="">Nature of Organization</option>
                                                        @foreach($taxSections as $taxSection)
                                                            <option
                                                                {{ old('nature_of_organization') == $taxSection->id ? 'selected' : ''  }} value="{{ $taxSection->id }}">{{ $taxSection->source }}
                                                                - {{ $taxSection->section }}</option>
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
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend>Received Cheque Details Information:</legend>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div
                                                    class="form-group {{ $errors->has('cheque_no') ? 'has-error' :'' }}">
                                                    <label for="cheque_no">Cheque No <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" value="{{ old('cheque_no') }}" id="cheque_no"
                                                           name="cheque_no" class="form-control"
                                                           placeholder="Enter Cheque no.">
                                                    @error('cheque_no')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="form-group {{ $errors->has('cheque_date') ? 'has-error' :'' }}">
                                                    <label for="cheque_date">Cheque Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" autocomplete="off"
                                                           value="{{ old('cheque_date') }}" id="cheque_date"
                                                           name="cheque_date" class="form-control date-picker"
                                                           placeholder="Enter Cheque Date">
                                                    @error('cheque_date')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div
                                                    class="form-group {{ $errors->has('issuing_bank_name') ? 'has-error' :'' }}">
                                                    <label for="issuing_bank_name">Issuing Bank Name</label>
                                                    <input type="text" value="{{ old('issuing_bank_name') }}"
                                                           id="issuing_bank_name" name="issuing_bank_name"
                                                           class="form-control" placeholder="Enter Issuing Bank Name">
                                                    @error('issuing_bank_name')
                                                    <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div
                                                    class="form-group {{ $errors->has('issuing_branch_name') ? 'has-error' :'' }}">
                                                    <label for="issuing_branch_name">Issuing Branch Name </label>
                                                    <input type="text" value="{{ old('issuing_branch_name') }}"
                                                           id="issuing_branch_name" name="issuing_branch_name"
                                                           class="form-control"
                                                           placeholder="Enter Issuing Bank Branch Name">
                                                    @error('issuing_branch_name')
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
                                            <legend>Received & Other Details:</legend>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th width="70%">Account Head <span class="text-danger">*</span></th>
                                                    <th width="">Received Amount <span class="text-danger">*</span></th>
                                                    <th colspan="8%"></th>

                                                </tr>
                                                </thead>
                                                <tbody id="receipt-payment-container">
                                                @if (old('account_head_code') != null && sizeof(old('account_head_code')) > 0)
                                                    @foreach(old('account_head_code') as $item)
                                                        <tr class="receipt-payment-item">
                                                            <td>
                                                                <div
                                                                    class="form-group account_head_code_area {{ $errors->has('account_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <select
                                                                        class="form-control select2 account_head_code"
                                                                        name="account_head_code[]"
                                                                        data-placeholder="Search Account Head Code">
                                                                        <option value="">Select Account Head Code
                                                                        </option>
                                                                        @if (old('account_head_code.'.$loop->index) != '')
                                                                            <option
                                                                                value="{{ old('account_head_code.'.$loop->index) }}"
                                                                                selected>{{ old('account_head_code_name.'.$loop->index) }}</option>
                                                                        @endif
                                                                    </select>
                                                                    <input type="hidden" name="account_head_code_name[]"
                                                                           class="account_head_code_name"
                                                                           value="{{ old('account_head_code_name.'.$loop->index) }}">

                                                                </div>

                                                                <div style="display: none"
                                                                     class="mt-2 deprecation_area">
                                                                    <div class="form-group">
                                                                        <input type="text"
                                                                               name="deprecation_percentage[]"
                                                                               class="form-control deprecation_percentage"
                                                                               placeholder="Enter Deprecation %">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div
                                                                    class="form-group {{ $errors->has('amount.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <input type="text"
                                                                           value="{{ old('amount.'.$loop->index) }}"
                                                                           name="amount[]" class="form-control amount">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a role="button" style="display: none"
                                                                   class="btn btn-danger btn-sm btn-remove"><i
                                                                        class="fa fa-times"></i></a>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr class="receipt-payment-item">
                                                        <td>
                                                            <div class="form-group account_head_code_area">
                                                                <select class="form-control select2 account_head_code"
                                                                        style="width: 100%;" name="account_head_code[]"
                                                                        data-placeholder="Search Account Head Code"
                                                                        required>
                                                                    <option value="">Search Account Head Code</option>
                                                                </select>
                                                                <input type="hidden" name="account_head_code_name[]"
                                                                       class="account_head_code_name">
                                                            </div>
                                                            <div style="display: none" class="mt-2 deprecation_area">
                                                                <div class="form-group">
                                                                    <input type="text" name="deprecation_percentage[]"
                                                                           class="form-control deprecation_percentage"
                                                                           placeholder="Enter Deprecation %">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" name="amount[]"
                                                                       class="form-control amount">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a role="button" style="display: none"
                                                               class="btn btn-danger btn-sm btn-remove"><i
                                                                    class="fa fa-times"></i></a>
                                                        </td>

                                                    </tr>
                                                @endif
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th class="text-right">
                                                        Total
                                                    </th>
                                                    <th class="text-center" id="total-amount">0.00</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-left">
                                                        <a role="button" class="btn btn-dark btn-sm"
                                                           id="btn-add-voucher"><i class="fa fa-plus"></i></a>
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
                                                                <div
                                                                    class="form-group other_account_head_code_area {{ $errors->has('parent_account_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <select
                                                                        class="form-control select2 parent_account_head_code"
                                                                        name="parent_account_head_code[]"
                                                                        data-placeholder="Search Parent Account Head Code">
                                                                        <option value="">Select Parent Account Head
                                                                            Code
                                                                        </option>
                                                                        @if (old('parent_account_head_code.'.$loop->index) != '')
                                                                            <option
                                                                                value="{{ old('parent_account_head_code.'.$loop->index) }}"
                                                                                selected>{{ old('parent_account_head_code_name.'.$loop->index) }}</option>
                                                                        @endif
                                                                    </select>
                                                                    <input type="hidden"
                                                                           name="parent_account_head_code_name[]"
                                                                           class="parent_account_head_code_name"
                                                                           value="{{ old('parent_account_head_code_name.'.$loop->index) }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div
                                                                    class="form-group other_account_head_code_area {{ $errors->has('other_account_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                                    <select
                                                                        class="form-control select2 other_account_head_code"
                                                                        name="other_account_head_code[]"
                                                                        data-placeholder="Search Other Account Head Code">
                                                                        <option value="">Select Other Account Head
                                                                            Code
                                                                        </option>
                                                                        @if (old('account_head_code.'.$loop->index) != '')
                                                                            <option
                                                                                value="{{ old('other_account_head_code.'.$loop->index) }}"
                                                                                selected>{{ old('other_account_head_code_name.'.$loop->index) }}</option>
                                                                        @endif
                                                                    </select>
                                                                    <input type="hidden"
                                                                           name="other_account_head_code_name[]"
                                                                           class="other_account_head_code_name"
                                                                           value="{{ old('other_account_head_code_name.'.$loop->index) }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div
                                                                    class="form-group {{ $errors->has('other_amount.'.$loop->index) ? 'has-error' :'' }}">

                                                                    <input type="text"
                                                                           value="{{ old('other_amount.'.$loop->index) }}"
                                                                           name="other_amount[]"
                                                                           class="form-control other_amount">
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <a role="button" style="display: none"
                                                                   class="btn btn-danger btn-sm btn-other-remove"><i
                                                                        class="fa fa-times"></i></a>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th class="text-right" colspan="2">Total</th>
                                                    <th class="text-center" id="total-other-amount">0.00</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-left">
                                                        <a role="button" class="btn btn-dark btn-sm"
                                                           id="btn-add-other-voucher"><i class="fa fa-plus"></i></a>
                                                    </th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="70%" class="text-right">Received Sub Total</th>
                                                            <th class="text-right" id="sub-total">0.00</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right">Other Deduction Total</th>
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
                                                    <label>Receipt Details (Narration)</label>
                                                    <div
                                                        class="form-group {{ $errors->has('notes') ? 'has-error' :'' }}">
                                                        <input type="text" value="{{ old('notes') }}" name="notes"
                                                               class="form-control"
                                                               placeholder="Enter Receipt Details (Narration)...">
                                                        @error('notes')
                                                        <span class="help-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <label>Supporting Documents</label>
                                                    <div
                                                        class="form-group {{ $errors->has('supporting_document') ? 'has-error' :'' }}">
                                                        <input type="file" name="supporting_document[]" multiple
                                                               class="form-control">
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
                    <select class="form-control select2 account_head_code" style="width: 100%;"
                            name="account_head_code[]" data-placeholder="Search Account Head Code">
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
                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i
                        class="fa fa-times"></i></a>
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
                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-other-remove"><i
                        class="fa fa-times"></i></a>

            </td>
        </tr>
    </template>
@endsection
@section('script')
    <script>
        $(function () {
            intSelect2();
            $('body').on('click', '#btn-save', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure to save?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Save It!'

                }).then((result) => {
                    if (result.isConfirmed) {
                        $('form').submit();
                    }
                })

            });

            $('#client_type').change(function () {
                var clientType = $(this).val();
                if (clientType != '') {
                    if (clientType == 1) {
                        $("#payee_select_area").show();
                        $(".client_name_area").hide();
                    } else {
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

                if ($('.receipt-payment-item').length >= 1) {
                    $('.btn-remove').show();
                }

                calculate();
            });
            $('#btn-add-other-voucher').click(function () {
                var html2 = $('#receipt-other-payment-template').html();
                var item2 = $(html2);
                $('#receipt-payment-other-container').append(item2);
                intSelect2();
                if ($('.receipt-payment-other-item').length >= 1) {
                    $('.btn-other-remove').show();
                }

                calculate();
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.receipt-payment-item').remove();
                if ($('.receipt-payment-item').length <= 1) {
                    $('.btn-remove').hide();
                }
                calculate();
            });
            $('body').on('click', '.btn-other-remove', function () {
                $(this).closest('.receipt-payment-other-item').remove();
                if ($('.receipt-payment-other-item').length <= 1) {
                    $('.btn-other-remove').hide();
                }
                calculate();
            });

            if ($('.receipt-payment-item').length <= 1) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }
            if ($('.receipt-payment-other-item').length <= 1) {
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

            $('.receipt-payment-item').each(function (i, obj) {
                var amount = $('.amount:eq(' + i + ')').val();


                if (amount == '' || amount < 0 || !$.isNumeric(amount))
                    amount = 0;


                totalAmount += parseFloat(amount);

            });

            $('.receipt-payment-other-item').each(function (i, obj) {
                var otherAmount = $('.other_amount:eq(' + i + ')').val();
                if (otherAmount == '' || otherAmount < 0 || !$.isNumeric(otherAmount))
                    otherAmount = 0;

                totalOtherAmount += parseFloat(otherAmount);

            });
            $('#total-other-amount').html(jsNumberFormat(totalOtherAmount));

            $('#total-amount').html(jsNumberFormat(totalAmount));

            $('#sub-total').html(jsNumberFormat(totalAmount));

            $('#other-total').html(jsNumberFormat(totalOtherAmount));

            var grandTotal = parseFloat(totalAmount) - parseFloat(totalOtherAmount)

            $('#grand-total').html(jsNumberFormat(grandTotal));
        }

        function intSelect2() {
            $('.date-picker').datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy'
            });
            $('.select2').select2()
            $('#bank_account_code').select2({
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
            $('#bank_account_code').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $("#bank_account_code").index(this);
                $('#bank_account_code_name').val(data.text);
            });
            $('#payee').select2({
                ajax: {
                    url: "{{ route('payee_json1') }}",
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
                $('#payee_select_name:eq(' + index + ')').val(data.text);
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
                $('.account_head_code_name:eq(' + index + ')').val(data.text);
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
                $('.parent_account_head_code_name:eq(' + index4 + ')').val(data4.text);
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
                $('.other_account_head_code_name:eq(' + index2 + ')').val(data2.text);
            });

        }
    </script>
@endsection
