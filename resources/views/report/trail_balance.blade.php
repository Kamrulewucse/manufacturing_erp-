@extends('layouts.app')
@section('title','Trial Balance Report')
@section('style')
    <style>
        hr {
            margin-top: 0rem;
            margin-bottom: 0rem;
            border: 0;
            border-top: 1px solid black;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #000 !important;
            vertical-align: middle;
            border-bottom-width: 1px !important;
            border-left-width: 1px !important;
            text-align: center;
            padding: 0.2rem !important;
        }
        @media print{
            @page {
                size: auto;
                margin: 20px !important;
            }
        }

    </style>
@endsection
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Filter</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('report.trail_balance') }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span
                                            class="text-danger">*</span></label>
                                    <input required type="text" id="start_date" autocomplete="off"
                                           name="start_date" class="form-control date-picker"
                                           placeholder="Enter Start Date" value="{{ request()->get('start_date') ?? $currentDate  }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date <span
                                            class="text-danger">*</span></label>
                                    <input required type="text" id="end_date" autocomplete="off"
                                           name="end_date" class="form-control date-picker"
                                           placeholder="Enter Start Date" value="{{ request()->get('end_date') ?? date('d-m-Y')  }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="account_head">Account Head <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="account_head" id="account_head">
                                        <option value="">All</option>
                                        @foreach($accountHeads as $accountHead)
                                            <option value="{{ $accountHead->id }}">Account Name: {{ $accountHead->name }}|Account Code {{ $accountHead->account_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <input type="submit" name="search" class="btn btn-primary form-control" value="Search">
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
    @if(count($accountHeadsSearch) > 0 && request('search'))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <a href="#" onclick="getprint('printArea')" class="btn btn-default btn-lg"><i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive-sm" id="printArea">
                            <div class="row print-heading">
                                <div class="col-12">
                                    <h1 class="text-center m-0" style="font-size: 40px !important;font-weight: bold">
                                        <img height="50px" src="{{ asset('img/logo.png') }}" alt="">
                                        {{ config('app.name') }}
                                    </h1>
                                    <h3 class="text-center m-0" style="font-size: 25px !important;">Trial Balance Report</h3>
                                    <h3 class="text-center m-0 mb-2" style="font-size: 19px !important;">Date: {{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</h3>
                                </div>
                            </div>
                            <table id="table" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Head</th>
                                    <th>Debit(Taka)</th>
                                    <th>Credit(Taka)</th>
                                    <th>Balance(Taka)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $totalBalance = 0;
                                    $totalDebit = 0;
                                    $totalCredit = 0;
                                @endphp
                                @foreach($accountHeadsSearch as $accountHead)
                                    @php
                                        $opening = previousAccountTrailDebitCredit(request('start_date'),request('end_date'),$accountHead->id);
                                        $result = accountTrailDebitCredit(request('start_date'),request('end_date'),$accountHead->id);

                                         $debitTotal = $result['debitTotal'] + $opening['debitOpeningTotal'];
                                         $creditTotal = $result['creditTotal'] + $opening['creditOpeningTotal'];

                                         $totalDebit  += $debitTotal;
                                         $totalCredit  += $creditTotal;

                                         $balance = $debitTotal - $creditTotal;

                                         $totalBalance += $balance;

                                    @endphp
                                    <tr>
                                        <td >{{ $accountHead->account_code }}</td>
                                        <td style="text-align: left" class="text-left">{{ $accountHead->name }}</td>
                                        <td style="text-align: right" class="text-right">{{ number_format($result['debitTotal'] + $opening['debitOpeningTotal'],2) }}</td>
                                        <td style="text-align: right" class="text-right">{{ number_format($result['creditTotal'] + $opening['creditOpeningTotal'],2) }}</td>
                                        <td style="text-align: right" class="text-right">{{ number_format(($result['debitTotal'] + $opening['debitOpeningTotal']) - $result['creditTotal'] + $opening['creditOpeningTotal'],2)  }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th colspan="2" class="text-right">Total Balance</th>
                                    <th style="text-align: right" class="text-right">{{ number_format($totalDebit,2) }}</th>
                                    <th style="text-align: right" class="text-right">{{ number_format($totalCredit,2) }}</th>
                                    <th style="text-align: right" class="text-right">{{ number_format($totalBalance,2) }}</th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>

        var APP_URL = '{!! url()->full()  !!}';

        function getprint(print) {
            $('.print-heading').css('display','block');
            $('.extra_column').remove();
            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
