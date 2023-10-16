@extends('layouts.app')
@section('title')
    Balance Transfer
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
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('balance_transfer.add') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('financial_year') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="financial_year">Financial Year <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                @php
                                    $currentFinancialYear = date('m')>6? date('Y'): (date('Y')-1);
                                @endphp
                                <select class="form-control select2" name="financial_year" id="financial_year">
                                <option value="">Select Year</option>
                                @for($i=2022; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ old('financial_year',$currentFinancialYear) == $i ? 'selected' : '' }}>{{ $i }}-{{ $i+1 }}</option>
                                @endfor
                            </select>
                                @error('financial_year')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('type') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="type">Transfer Type <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="type" id="type">
                                    <option value="">Select Transfer Type</option>
                                    <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>Bank To Cash</option>
                                    <option value="3" {{ old('type') == '3' ? 'selected' : '' }}>Bank To Bank</option>
                                </select>
                                @error('type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div id="source-bank-info">
                            <div class="form-group row {{ $errors->has('source_bank_account_code') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="source_bank_account_code">Source Bank Account Code <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" name="source_bank_account_code" id="source_bank_account_code">
                                        <option value="">Search Source Bank Account Code</option>
                                        @if (old('source_bank_account_code') != '')
                                            <option value="{{ old('source_bank_account_code') }}" selected>{{ old('source_bank_account_code_name') }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="source_bank_account_code_name" class="source_bank_account_code_name" value="{{ old('source_bank_account_code_name') }}">

                                    @error('source_bank_account_code')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row {{ $errors->has('source_bank_cheque_no') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="source_bank_cheque_no">Source Bank Cheque No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="source_bank_cheque_no" name="source_bank_cheque_no" placeholder="Enter Source Bank Cheque No.">
                                    @error('source_bank_cheque_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row {{ $errors->has('source_bank_cheque_date') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="source_bank_cheque_date">Source Bank Cheque Date</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control pull-right date-picker" placeholder="Enter Source Bank Cheque Date" id="source_bank_cheque_date" name="source_bank_cheque_date" value="{{ empty(old('source_bank_cheque_date')) ? ($errors->has('source_bank_cheque_date') ? '' : '') : old('source_bank_cheque_date') }}" autocomplete="off">
                                    @error('source_bank_cheque_date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div id="source-cash-info">
                            <div class="form-group row {{ $errors->has('source_cash_account_code') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="source_cash_account_code">Source Cash Account Code <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" name="source_cash_account_code" id="source_cash_account_code">
                                        <option value="">Search Source Cash Account Code</option>
                                    </select>
                                    @error('source_cash_account_code')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div id="target-bank-info">
                            <div class="form-group row {{ $errors->has('target_bank_account_code') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="target_bank_account_code">Target Bank Account Code <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" name="target_bank_account_code" id="target_bank_account_code">
                                        <option value="">Search Target Bank Account Code</option>
                                        @if (old('target_bank_account_code') != '')
                                            <option value="{{ old('target_bank_account_code') }}" selected>{{ old('target_bank_account_code_name') }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="target_bank_account_code_name" class="target_bank_account_code_name" value="{{ old('target_bank_account_code_name') }}">

                                    @error('target_bank_account_code')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row {{ $errors->has('target_bank_cheque_no') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="target_bank_cheque_no">Target Bank Cheque No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="target_bank_cheque_no" name="target_bank_cheque_no" placeholder="Enter Target Bank Cheque No.">
                                    @error('target_bank_cheque_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row {{ $errors->has('target_bank_cheque_date') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="target_bank_cheque_date">Target Bank Cheque Date</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control pull-right date-picker" placeholder="Enter Target Bank Cheque Date" id="target_bank_cheque_date" name="target_bank_cheque_date" value="{{ empty(old('target_bank_cheque_date')) ? ($errors->has('target_bank_cheque_date') ? '' : '') : old('target_bank_cheque_date') }}" autocomplete="off">
                                    @error('target_bank_cheque_date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div id="target-cash-info">
                            <div class="form-group row {{ $errors->has('target_cash_account_code') ? 'has-error' :'' }}">
                                <label class="col-sm-3 col-form-label" for="target_cash_account_code">Target Cash Account Code <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" name="target_cash_account_code" id="target_cash_account_code">
                                        <option value="">Search Target Cash Account Code</option>
                                        @if (old('target_cash_account_code') != '')
                                            <option value="{{ old('target_cash_account_code') }}" selected>{{ old('target_cash_account_code_name') }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="target_cash_account_code_name" class="target_cash_account_code_name" value="{{ old('target_cash_account_code_name') }}">

                                    @error('target_cash_account_code')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="form-group row {{ $errors->has('amount') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="amount">Amount <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="amount" name="amount" placeholder="Enter Amount" value="{{ old('amount') }}">
                                <span style="font-weight: bold" id="amount-show"></span>
                                @error('amount')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('date') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="date">Date <span class="text-danger">*</span></label>

                            <div class="col-sm-9">
                                <div class="input-group date">
                                    <input type="text" class="form-control pull-right" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : date('Y-m-d')) : old('date') }}" autocomplete="off">
                                </div>
                                @error('date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row {{ $errors->has('notes') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label" for="note">Note</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="notes" name="notes" placeholder="Enter Note" value="{{ old('notes') }}">
                                @error('notes')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button id="btn-save" type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            intSelect2();
            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

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
            $('body').on('keyup', '#amount', function () {
                calculate();
            });
            calculate();

            $('#type').change(function () {
                var type = $(this).val();
                if (type != '') {
                    if (type == '1') {
                        $('#source-bank-info').show();
                        $('#target-cash-info').show();
                        $('#source-cash-info').hide();
                        $('#target-bank-info').hide();
                    } else if (type == '2') {
                        $('#source-bank-info').hide();
                        $('#target-cash-info').hide();
                        $('#source-cash-info').show();
                        $('#target-bank-info').show();
                    } else if (type == '3') {
                        $('#source-bank-info').show();
                        $('#target-bank-info').show();
                        $('#source-cash-info').hide();
                        $('#target-cash-info').hide();
                    }else if (type == '4') {
                        $('#source-bank-info').hide();
                        $('#target-bank-info').hide();
                        $('#source-cash-info').show();
                        $('#target-cash-info').show();
                    }
                } else {
                    $('#source-bank-info').hide();
                    $('#target-bank-info').hide();
                    $('#source-cash-info').hide();
                    $('#target-cash-info').hide();
                }
            });

            $('#type').trigger('change');

        });

        function calculate(){
            var amount = $("#amount").val();

            if (amount == '' || amount < 0 || !$.isNumeric(amount))
                amount = 0;

            $("#amount-show").html(jsNumberFormat(amount));
        }
        function intSelect2(){
            $('.date-picker').datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy'
            });
            $('.select2').select2()

            $('#source_bank_account_code').select2({
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
            $('#source_bank_account_code').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $("#source_bank_account_code").index(this);
                $('#source_bank_account_code_name:eq('+index+')').val(data.text);
            });

            $('#target_cash_account_code').select2({
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
            $('#target_cash_account_code').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $("#target_cash_account_code").index(this);
                $('#target_cash_account_code_name:eq('+index+')').val(data.text);
            });

            $('#target_bank_account_code').select2({
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
            $('#target_bank_account_code').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $("#target_bank_account_code").index(this);
                $('#target_bank_account_code_name:eq('+index+')').val(data.text);
            });

        }
    </script>
@endsection
