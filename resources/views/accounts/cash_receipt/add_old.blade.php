@extends('layouts.app')
@section('title','Create Cash Receipt(CR)')
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
        legend {
            color: #33b26f;
        }
        fieldset {
            border-width: 1.4px;
            margin-top: 10px;
            border-color: #33b26f;
        }
        .card-title {
            font-size: 1.5rem;
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
                    <h3 class="card-title">Cash Receipt(CR) Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{ route('cash_receipt.create') }}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div  id="receipt-payment-container">
                            @if (old('receipt_no') != null && sizeof(old('receipt_no')) > 0)
                                @foreach(old('receipt_no') as $item)
                                <div class="receipt-payment-item">
                                    <div class="row">
                                        <div class="col-md-12">
                                           <a role="button" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-times"></i></a>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>Basic Information:</legend>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input type="checkbox" {{ old('reconciliation.'.$loop->index) == 1 ? 'checked' : '' }} value="1" name="reconciliation[]"> Reconciliation
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Receipt No</label>
                                                        <div class="form-group {{ $errors->has('receipt_no.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('receipt_no.'.$loop->index) }}" name="receipt_no[]" class="form-control" placeholder="Enter Receipt No">
                                                            @error('receipt_no.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Date</label>
                                                        <div class="form-group {{ $errors->has('date.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('date.'.$loop->index) }}" autocomplete="off" name="date[]" class="form-control date-picker" placeholder="Enter Date">
                                                            @error('date.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Cash Disbursed Point Code</label>
                                                        <div class="form-group {{ $errors->has('cash_account_code.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2" name="cash_account_code[]">
                                                                <option value="">Search Name of Cash Disbursed Point Code</option>
                                                                @foreach($cashes as $cash)
                                                                    <option {{ old('cash_account_code.'.$loop->parent->index) == $cash->id ? 'selected' : '' }} value="{{ $cash->id }}">Cash In Hand Name:{{ $cash->account_name }}|Existing Code:{{ $cash->existing_account_code }}|New Code:{{ $cash->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('cash_account_code.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>
                                            </fieldset>
                                        </div>

                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>Depositor Information:</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Depositor's Name</label>
                                                        <div class="form-group {{ $errors->has('depositor.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('depositor.'.$loop->index) }}" name="depositor[]" class="form-control" placeholder="Enter Depositor's Name(Required)">
                                                            @error('depositor.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Depositor's Designation</label>
                                                        <div class="form-group {{ $errors->has('designation.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('designation.'.$loop->index) }}" name="designation[]" class="form-control" placeholder="Enter Designation(Required)">
                                                            @error('designation.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Depositor's Address</label>
                                                        <div class="form-group {{ $errors->has('address.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('address.'.$loop->index) }}" name="address[]" class="form-control" placeholder="Enter Address(Required)">
                                                            @error('address.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Customer ID</label>
                                                        <div class="form-group {{ $errors->has('customer_id.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('customer_id.'.$loop->index) }}" name="customer_id[]" class="form-control" placeholder="Enter Customer ID(Optional)">
                                                            @error('customer_id.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Depositor's eTIN</label>
                                                        <div class="form-group {{ $errors->has('e_tin.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('e_tin.'.$loop->index) }}" name="e_tin[]" class="form-control" placeholder="Enter eTin(Optional)">
                                                            @error('e_tin.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Nature of Organization</label>
                                                        <div class="form-group {{ $errors->has('nature_of_organization.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('nature_of_organization.'.$loop->index) }}" name="nature_of_organization[]" class="form-control" placeholder="Enter Nature of Organization(Optional)">
                                                            @error('nature_of_organization.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>Details of Received Cheque:</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Account Head Code</label>
                                                        <div class="form-group {{ $errors->has('account_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2 account_head_code" name="account_head_code[]">
                                                                <option value="">Search Account Head Code</option>
                                                                @foreach($accountHeads as $accountHead)
                                                                    <option {{ old('account_head_code.'.$loop->parent->index) == $accountHead->id ? 'selected' : '' }} value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('account_head_code.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Amount</label>
                                                        <div class="form-group {{ $errors->has('amount.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('amount.'.$loop->index) }}" name="amount[]" class="form-control amount" placeholder="Enter Amount(Required)">
                                                            @error('amount.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>VAT Deduction:</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>VAT Account Head Code</label>
                                                        <div class="form-group {{ $errors->has('vat_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2" name="vat_head_code[]">
                                                                <option value="">Search VAT Account Code</option>
                                                                @foreach($accountHeads as $accountHead)
                                                                    <option {{ old('vat_head_code.'.$loop->parent->index) == $accountHead->id ? 'selected' : '' }} value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('vat_head_code.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>VAT Base Amount</label>
                                                        <div class="form-group {{ $errors->has('vat_base_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('vat_base_amount.'.$loop->index) }}" name="vat_base_amount[]" class="form-control" placeholder="Enter Base VAT Amount">
                                                            @error('vat_base_amount.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>VAT Rate</label>
                                                        <div class="form-group {{ $errors->has('vat_rate.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('vat_rate.'.$loop->index) }}" name="vat_rate[]" class="form-control vat_rate" placeholder="Enter VAT Rate">
                                                            @error('vat_rate.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>VAT Amount</label>
                                                        <div class="form-group {{ $errors->has('vat_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" readonly value="{{ old('vat_amount.'.$loop->index) }}" name="vat_amount[]" class="form-control vat_amount" placeholder="VAT Amount">
                                                            @error('vat_amount.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>AIT Deduction:</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Ait Account Head Code</label>
                                                        <div class="form-group {{ $errors->has('ait_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2" name="ait_head_code[]">
                                                                <option value="">Search AIT Account Code</option>
                                                                @foreach($accountHeads as $accountHead)
                                                                    <option {{ old('ait_head_code.'.$loop->parent->index) == $accountHead->id ? 'selected' : '' }} value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('ait_head_code.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>AIT Base Amount</label>
                                                        <div class="form-group {{ $errors->has('ait_base_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('ait_base_amount.'.$loop->index) }}" name="ait_base_amount[]" class="form-control" placeholder="Enter Base AIT Amount">
                                                            @error('ait_base_amount.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>AIT Rate</label>
                                                        <div class="form-group {{ $errors->has('ait_rate.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('ait_rate.'.$loop->index) }}" name="ait_rate[]" class="form-control ait_rate" placeholder="Enter AIT Rate">
                                                            @error('ait_rate.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>AIT Amount</label>
                                                        <div class="form-group {{ $errors->has('ait_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" readonly value="{{ old('ait_amount.'.$loop->index) }}" name="ait_amount[]" class="form-control ait_amount" placeholder="AIT Amount">
                                                            @error('ait_amount.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>Other Deduction:</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Other Account Code</label>
                                                        <div class="form-group {{ $errors->has('other_head_code.'.$loop->index) ? 'has-error' :'' }}">
                                                            <select class="form-control select2" name="other_head_code[]">
                                                                <option value="">Search Other Account Code</option>
                                                                @foreach($accountHeads as $accountHead)
                                                                    <option {{ old('other_head_code.'.$loop->parent->index) == $accountHead->id ? 'selected' : '' }} value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('other_head_code.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Other Amount</label>
                                                        <div class="form-group {{ $errors->has('other_amount.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('other_amount.'.$loop->index) }}" name="other_amount[]" class="form-control" placeholder="Enter Other Amount">
                                                            @error('other_amount.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>Payment Details (Narration)</label>
                                                        <div class="form-group {{ $errors->has('notes.'.$loop->index) ? 'has-error' :'' }}">
                                                            <input type="text" value="{{ old('notes.'.$loop->index) }}" name="notes[]" class="form-control" placeholder="Enter Payment Details (Narration)...">
                                                            @error('notes.'.$loop->index)
                                                            <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="receipt-payment-item">
                                    <div class="row">
                                        <div class="col-md-12">
                                           <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-times"></i></a>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>Basic Information:</legend>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input type="checkbox" value="1" name="reconciliation[]"> Reconciliation
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Receipt No</label>
                                                        <div class="form-group">
                                                            <input type="text" name="receipt_no[]" class="form-control" placeholder="Enter Receipt No">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Date</label>
                                                        <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                                            <input type="text" autocomplete="off" name="date[]" class="form-control date-picker" placeholder="Enter Date">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Cash Disbursed Point Code</label>
                                                        <div class="form-group">
                                                            <select class="form-control select2" name="cash_account_code[]">
                                                                <option value="">Search Name of Cash Disbursed Point Code</option>
                                                                @foreach($cashes as $cash)
                                                                    <option value="{{ $cash->id }}">Cash In Hand Name:{{ $cash->name }}|Existing Code:{{ $cash->existing_account_code }}|New Code:{{ $cash->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>Depositor Information:</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Depositor's Name</label>
                                                        <div class="form-group">
                                                            <input type="text" name="depositor[]" class="form-control" placeholder="Enter Depositor's name(Required)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Depositor's Designation</label>
                                                        <div class="form-group">
                                                            <input type="text" name="designation[]" class="form-control" placeholder="Enter Designation(Required)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Depositor's Address</label>
                                                        <div class="form-group">
                                                            <input type="text" name="address[]" class="form-control" placeholder="Enter Address(Required)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Customer ID</label>
                                                        <div class="form-group">
                                                            <input type="text" name="customer_id[]" class="form-control" placeholder="Enter Customer ID(Optional)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Depositor's eTIN</label>
                                                        <div class="form-group">
                                                            <input type="text" name="e_tin[]" class="form-control" placeholder="Enter eTin(Optional)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Nature of Organization</label>
                                                        <div class="form-group">
                                                            <input type="text" name="nature_of_organization[]" class="form-control" placeholder="Enter Nature of Organization(Optional)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>Received details (Cash):</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Account Head Code</label>
                                                        <div class="form-group">
                                                            <select class="form-control select2 account_head_code" name="account_head_code[]">
                                                                <option value="">Search Account Head Code</option>
                                                                @foreach($accountHeads as $accountHead)
                                                                    <option value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Amount</label>
                                                        <div class="form-group">
                                                            <input type="text" name="amount[]" class="form-control amount" placeholder="Enter Amount(Required)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>VAT Received:</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>VAT Account Code</label>
                                                        <div class="form-group">
                                                            <select class="form-control select2" name="vat_head_code[]">
                                                                <option value="">Search VAT Account Code</option>
                                                                @foreach($accountHeads as $accountHead)
                                                                    <option value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>VAT Base Amount</label>
                                                        <div class="form-group">
                                                            <input type="text" name="vat_base_amount[]" class="form-control" placeholder="Enter Base VAT Amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>VAT Rate</label>
                                                        <div class="form-group">
                                                            <input type="text" name="vat_rate[]" class="form-control vat_rate" placeholder="Enter VAT Rate">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>VAT Amount</label>
                                                        <div class="form-group">
                                                            <input type="text" readonly name="vat_amount[]" class="form-control vat_amount" placeholder="VAT Amount">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>AIT Received:</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>AIT Account Code</label>
                                                        <div class="form-group">
                                                            <select class="form-control select2" name="ait_head_code[]">
                                                                <option value="">Search AIT Account Code</option>
                                                                @foreach($accountHeads as $accountHead)
                                                                    <option value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>AIT Base Amount</label>
                                                        <div class="form-group">
                                                            <input type="text" name="ait_base_amount[]" class="form-control" placeholder="Enter Base AIT Amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>AIT Rate</label>
                                                        <div class="form-group">
                                                            <input type="text" name="ait_rate[]" class="form-control ait_rate" placeholder="Enter AIT Rate">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>AIT Amount</label>
                                                        <div class="form-group">
                                                            <input type="text" readonly name="ait_amount[]" class="form-control ait_amount" placeholder="AIT Amount">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12">
                                            <fieldset>
                                                <legend>Received for withheld:</legend>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Other Account Code</label>
                                                        <div class="form-group">
                                                            <select class="form-control select2" name="other_head_code[]">
                                                                <option value="">Search Other Account Code</option>
                                                                @foreach($accountHeads as $accountHead)
                                                                    <option value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Other Amount</label>
                                                        <div class="form-group">
                                                            <input type="text" name="other_amount[]" class="form-control" placeholder="Enter Other Amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>Payment Details (Narration)</label>
                                                        <div class="form-group">
                                                            <input type="text" name="notes[]" class="form-control" placeholder="Enter Payment Details (Narration)...">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <a role="button" class="btn btn-dark btn-sm" id="btn-add-voucher"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-dark">Save</button>
                        <a href="{{ route('cash_receipt') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
<template id="receipt-payment-template">
    <div class="receipt-payment-item">
        <div class="row mt-4">
            <div class="col-md-12">
                <a role="button" style="display: none" class="btn btn-danger btn-sm btn-remove"><i class="fa fa-times"></i></a>
            </div>
            <div class="col-md-12">
                <fieldset>
                    <legend>Basic Information:</legend>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="checkbox" value="1" name="reconciliation[]"> Reconciliation
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Receipt No</label>
                            <div class="form-group">
                                <input type="text" name="receipt_no[]" class="form-control" placeholder="Enter Receipt No">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Date</label>
                            <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                <input type="text" autocomplete="off" name="date[]" class="form-control date-picker" placeholder="Enter Date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Cash Disbursed Point Code</label>
                            <div class="form-group">
                                <select class="form-control select2" name="cash_account_code[]">
                                    <option value="">Search Name of Cash Disbursed Point Code</option>
                                    @foreach($cashes as $cash)
                                        <option value="{{ $cash->id }}">Cash In Hand Name:{{ $cash->name }}|Existing Code:{{ $cash->existing_account_code }}|New Code:{{ $cash->account_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-12">
                <fieldset>
                    <legend>Depositor Information:</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Depositor's Name</label>
                            <div class="form-group">
                                <input type="text" name="depositor[]" class="form-control" placeholder="Enter Depositor's name(Required)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Depositor's Designation</label>
                            <div class="form-group">
                                <input type="text" name="designation[]" class="form-control" placeholder="Enter Designation(Required)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Depositor's Address</label>
                            <div class="form-group">
                                <input type="text" name="address[]" class="form-control" placeholder="Enter Address(Required)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Customer ID</label>
                            <div class="form-group">
                                <input type="text" name="customer_id[]" class="form-control" placeholder="Enter Customer ID(Optional)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Depositor's eTIN</label>
                            <div class="form-group">
                                <input type="text" name="e_tin[]" class="form-control" placeholder="Enter eTin(Optional)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Nature of Organization</label>
                            <div class="form-group">
                                <input type="text" name="nature_of_organization[]" class="form-control" placeholder="Enter Nature of Organization(Optional)">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-12">
                <fieldset>
                    <legend>Received details (Cash):</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Account Head Code</label>
                            <div class="form-group">
                                <select class="form-control select2 account_head_code" name="account_head_code[]">
                                    <option value="">Search Account Head Code</option>
                                    @foreach($accountHeads as $accountHead)
                                        <option value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Amount</label>
                            <div class="form-group">
                                <input type="text" name="amount[]" class="form-control amount" placeholder="Enter Amount(Required)">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-12">
                <fieldset>
                    <legend>VAT Received:</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <label>VAT Account Code</label>
                            <div class="form-group">
                                <select class="form-control select2" name="vat_head_code[]">
                                    <option value="">Search VAT Account Code</option>
                                    @foreach($accountHeads as $accountHead)
                                        <option value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>VAT Base Amount</label>
                            <div class="form-group">
                                <input type="text" name="vat_base_amount[]" class="form-control" placeholder="Enter Base VAT Amount">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>VAT Rate</label>
                            <div class="form-group">
                                <input type="text" name="vat_rate[]" class="form-control vat_rate" placeholder="Enter VAT Rate">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>VAT Amount</label>
                            <div class="form-group">
                                <input type="text" readonly name="vat_amount[]" class="form-control vat_amount" placeholder="VAT Amount">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-12">
                <fieldset>
                    <legend>AIT Received:</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <label>AIT Account Code</label>
                            <div class="form-group">
                                <select class="form-control select2" name="ait_head_code[]">
                                    <option value="">Search AIT Account Code</option>
                                    @foreach($accountHeads as $accountHead)
                                        <option value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>AIT Base Amount</label>
                            <div class="form-group">
                                <input type="text" name="ait_base_amount[]" class="form-control" placeholder="Enter Base AIT Amount">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>AIT Rate</label>
                            <div class="form-group">
                                <input type="text" name="ait_rate[]" class="form-control ait_rate" placeholder="Enter AIT Rate">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>AIT Amount</label>
                            <div class="form-group">
                                <input type="text" readonly name="ait_amount[]" class="form-control ait_amount" placeholder="AIT Amount">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-12">
                <fieldset>
                    <legend>Received for withheld:</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Other Account Code</label>
                            <div class="form-group">
                                <select class="form-control select2" name="other_head_code[]">
                                    <option value="">Search Other Account Code</option>
                                    @foreach($accountHeads as $accountHead)
                                        <option value="{{ $accountHead->id }}">Head:{{ $accountHead->name }}|Existing Code:{{ $accountHead->existing_account_code }}|New Code:{{ $accountHead->account_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Other Amount</label>
                            <div class="form-group">
                                <input type="text" name="other_amount[]" class="form-control" placeholder="Enter Other Amount">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>Payment Details (Narration)</label>
                            <div class="form-group">
                                <input type="text" name="notes[]" class="form-control" placeholder="Enter Payment Details (Narration)...">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</template>
@endsection
@section('script')
    <script>
        $(function (){

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

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.receipt-payment-item').remove();
                if ($('.receipt-payment-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
                calculate();
            });

            if ($('.receipt-payment-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }
            calculate();

            $('body').on('keyup', '.amount,.vat_rate,.ait_rate', function () {
                calculate();
            });
        });
        function calculate() {


            $('.receipt-payment-item').each(function(i, obj) {
                var amount = $('.amount:eq('+i+')').val();
                var vatRate = $('.vat_rate:eq('+i+')').val();
                var aitRate = $('.ait_rate:eq('+i+')').val();


                if (amount == '' || amount < 0 || !$.isNumeric(amount))
                    amount = 0;

                if (vatRate == '' || vatRate < 0 || !$.isNumeric(vatRate))
                    vatRate = 0;

                if (aitRate == '' || aitRate < 0 || !$.isNumeric(aitRate))
                    aitRate = 0;

                if (vatRate > 0)
                    $('.vat_amount:eq('+i+')').val(((amount * vatRate)/100).toFixed(2) );

                if (aitRate > 0)
                    $('.ait_amount:eq('+i+')').val(((amount * aitRate)/100).toFixed(2) );

            });

        }

        function intSelect2(){
            $('.date-picker').datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy'
            });
            $('.select2').select2()
        }
    </script>
@endsection
