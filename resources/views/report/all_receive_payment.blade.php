@extends('layouts.master')
@section('style')

@endsection
@section('title')
    All Receive And Payment
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Filter</h3>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <form action="{{ route('report.all_receive_payment') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start')  }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Segment</label>

                                    <select class="form-control select2" name="segment">
                                        <option value="1">All Segment</option>
                                        @foreach($segments as $segment)
                                            <option
                                                value="{{ $segment->id }}" {{ request()->get('segment') == $segment->id ? 'selected' : '' }}>{{ $segment->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>	&nbsp;</label>

                                    <input class="btn btn-primary form-control" type="submit" value="Submit">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12" style="min-height:300px">
            @if($incomes)
                <section class="panel">

                    <div class="panel-body">
                        <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                        <div class="adv-table" id="prinarea">
                            <table class="table table-bordered" style="margin-bottom: 0px">
                                <tr>
                                    <th class="text-center">{{ date("F d, Y", strtotime(request()->get('start'))).' - '.date("F d, Y", strtotime(request()->get('end'))) }}</th>
                                </tr>
                            </table>

                            <div style="clear: both">
                                <table class="table table-bordered" style="width:50%; float:left">
                                    <tr>
                                        <th colspan="5" class="text-center">Debit</th>
                                    </tr>
                                    <tr>
                                        <th width="10%">Date</th>
                                        <th width="25%">Particular</th>
{{--                                        <th width="20%">Note</th>--}}
                                        <th width="15%">Payment Type</th>
                                        <th width="20%">Bank Details</th>
                                        <th width="10%">Amount</th>
                                    </tr>

                                    @foreach($incomes as $income)
                                        <tr>
                                            <td>{{ $income->date->format('j F, Y') }}</td>
                                            <td>{{ $income->particular }}</td>
{{--                                            <td>{{ $income->note }}</td>--}}
                                            <td>
                                                @if($income->transaction_method == 1)
                                                    Cash
                                                @elseif($income->transaction_method == 2)
                                                    Bank
                                                @elseif($income->transaction_method == 3)
                                                    Mobile Banking
                                                @endif
                                            </td>
                                            <td>
                                                @if ($income->transaction_method == 2)
                                                    {{ $income->bank->name.' - '.$income->account->account_no }}
                                                @endif
                                            </td>
                                            <td align="right">{{ $income->amount }}</td>
                                        </tr>
                                    @endforeach

                                    <?php
                                    $incomesCount = count($incomes);
                                    $expensesCount = count($expenses);

                                    if ($incomesCount > $expensesCount)
                                        $maxCount = $incomesCount;
                                    else
                                        $maxCount = $expensesCount;

                                    $maxCount += 2;
                                    ?>

                                    @for($i=count($incomes); $i<$maxCount; $i++)
                                        <tr>
                                            <td><br><br></td>
                                            <td></td>
                                            <td></td>
{{--                                            <td></td>--}}
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor

                                    <tr>
                                        <td colspan="4">Total</td>
                                        <td align="right">{{ $incomes->sum('amount') }}</td>
                                    </tr>
                                </table>
                                <table class="table table-bordered" style="width:50%; float:left">
                                    <tr>
                                        <th colspan="5" class="text-center">Credit</th>
                                    </tr>
                                    <tr>
                                        <th width="10%">Date</th>
                                        <th width="25%">Particular</th>
{{--                                        <th width="20%">Note</th>--}}
                                        <th width="15%">Payment Type</th>
                                        <th width="20%">Bank Details</th>
                                        <th width="10%">Amount</th>
                                    </tr>

                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->date->format('j F, Y') }}</td>
                                            <td>{{ $expense->particular }}</td>
{{--                                            <td>{{ $expense->note }}</td>--}}
                                            <td>
                                                @if($expense->transaction_method == 1)
                                                    Cash
                                                @elseif($expense->transaction_method == 2)
                                                    Bank
                                                @elseif($expense->transaction_method == 3)
                                                    Mobile Banking
                                                @endif
                                            </td>
                                            <td>
                                                @if ($expense->transaction_method == 2)
                                                    {{ $expense->bank->name.' - '.$expense->account->account_no }}
                                                @endif
                                            </td>
                                            <td align="right">{{ $expense->amount }}</td>
                                        </tr>
                                    @endforeach

                                    @for($i=count($expenses); $i<$maxCount; $i++)
                                        <tr>
                                            <td><br><br></td>
                                            <td></td>
                                            <td></td>
{{--                                            <td></td>--}}
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor

                                    <tr>
                                        <td colspan="4">Total</td>
                                        <td align="right">{{ $expenses->sum('amount') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>

    @if($incomes)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Summary</h3>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body">
                        @if($incomes->sum('amount') >= $expenses->sum('amount'))
                            <strong>Balance: </strong> {{ number_format($incomes->sum('amount') - $expenses->sum('amount'), 2) }}
                        @else
                            <strong>Loss: </strong> {{ number_format($expenses->sum('amount') - $incomes->sum('amount'), 2) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
