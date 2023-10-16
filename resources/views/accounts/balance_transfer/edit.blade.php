@extends('layouts.app')
@section('title')
    Balance Transfer Edit
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-default">
                <div class="card-header">
                    <h3 class="card-title">Balance Transfer Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('balance_transfer.edit',['balanceTransfer'=>$balanceTransfer->id]) }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('financial_year') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="financial_year">Financial Year <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="financial_year" readonly value="{{ $balanceTransfer->financial_year }}" class="form-control">
                                @error('financial_year')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('type') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="type">Transfer Type <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="hidden" name="type" value="{{ $balanceTransfer->type }}">
                                @if($balanceTransfer->type == 1)
                                <input type="text" readonly class="form-control" value="Bank To Cash">
                                @else
                                    <input type="text" readonly class="form-control" value="Bank To Bank">
                                @endif
                                @error('type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @if($balanceTransfer->type == 1 || $balanceTransfer->type == 3)
                        <div id="source-bank-info">
                            <div class="form-group row {{ $errors->has('source_bank_account_code') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="source_bank_account_code">Source Bank Account Code <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" value="{{ $balanceTransfer->sourceAccountHead->name ?? '' }}" readonly class="form-control">
                                </div>
                            </div>

                            <div class="form-group row {{ $errors->has('source_bank_cheque_no') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="source_bank_cheque_no">Source Bank Cheque No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="{{ $balanceTransfer->source_cheque_no }}" id="source_bank_cheque_no" name="source_bank_cheque_no" placeholder="Enter Source Bank Cheque No.">

                                </div>
                            </div>
                            <div class="form-group row {{ $errors->has('source_bank_cheque_date') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="source_bank_cheque_date">Source Bank Cheque Date</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control pull-right date-picker"  placeholder="Enter Source Bank Cheque Date" id="source_bank_cheque_date" name="source_bank_cheque_date" value="{{ old('source_bank_cheque_date',$balanceTransfer->source_cheque_date ? \Carbon\Carbon::parse($balanceTransfer->source_cheque_date)->format('d-m-Y') : '') }}" autocomplete="off">
                                    @error('source_bank_cheque_date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($balanceTransfer->type == 3)
                        <div id="target-bank-info">
                            <div class="form-group row {{ $errors->has('target_bank_account_code') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="target_bank_account_code">Target Bank Account Code <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" value="{{ $balanceTransfer->targetAccountHead->name ?? '' }}" readonly class="form-control">
                                </div>
                            </div>
                            <div class="form-group row {{ $errors->has('target_bank_cheque_no') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="target_bank_cheque_no">Target Bank Cheque No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="{{ $balanceTransfer->target_cheque_no }}" id="target_bank_cheque_no" name="target_bank_cheque_no" placeholder="Enter Target Bank Cheque No.">
                                    @error('target_bank_cheque_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row {{ $errors->has('target_bank_cheque_date') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="target_bank_cheque_date">Target Bank Cheque Date</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control pull-right date-picker" placeholder="Enter Target Bank Cheque Date" id="target_bank_cheque_date" name="target_bank_cheque_date" value="{{ old('target_bank_cheque_date',$balanceTransfer->target_bank_cheque_date ? \Carbon\Carbon::parse($balanceTransfer->target_bank_cheque_date)->format('d-m-Y') : '') }}" autocomplete="off">
                                    @error('target_bank_cheque_date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        @endif
                        @if($balanceTransfer->type == 1)
                        <div id="target-cash-info">
                            <div class="form-group row {{ $errors->has('target_cash_account_code') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="target_cash_account_code">Target Cash Account Code <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" id="target_cash_account_code" value="{{ $balanceTransfer->targetAccountHead->name ?? '' }}" readonly class="form-control">
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="form-group row {{ $errors->has('amount') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="amount">Amount <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="amount" name="amount" placeholder="Enter Amount" value="{{ old('amount',$balanceTransfer->amount) }}">
                                <span style="font-weight: bold" id="amount-show"></span>
                                @error('amount')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('date') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="date">Date <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control pull-right date-picker-fiscal-year" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : \Carbon\Carbon::parse($balanceTransfer->date)->format('d-m-Y')) : old('date') }}" autocomplete="off">
                                @error('date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('notes') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="note">Note</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="notes" name="notes" placeholder="Enter Note" value="{{ old('notes',$balanceTransfer->notes) }}">
                                @error('notes')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button id="btn-save" type="submit" class="btn btn-dark">Save</button>
                        <a href="{{ route('balance_transfer') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        $(function () {
            fiscalYearDateRange('{{$fiscalYear}}');
            formSubmitConfirm('btn-save');

            $('body').on('keyup', '#amount', function () {
                calculate();
            });
            calculate();


        });

        function calculate(){
            var amount = $("#amount").val();
            if (amount == '' || amount < 0 || !$.isNumeric(amount))
                amount = 0;

            $("#amount-show").html(jsNumberFormat(amount));
        }
    </script>
@endsection
