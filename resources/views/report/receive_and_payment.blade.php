@extends('layouts.master')
@section('title')
    Receive and Payment
@endsection
@section('style')
    <style>
        .img-overlay {
            position: absolute;
            left: 0;
            top: 200px;
            width: 100%;
            height: 100%;
            overflow: hidden;
            text-align: center;
            z-index: 9;
            opacity: 0.2;
        }

        .img-overlay img {
            width: 400px;
            height: auto;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Filter</h3>
                </div>
                <!-- /.box-header -->

                <div class="card-body">
                    <form action="{{ route('project.report.receive_and_payment') }}">
                        <div class="row">
                            <div class="col-md-4">
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
                            <div class="col-md-4">
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

{{--                            <div class="col-md-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>Segment</label>--}}

{{--                                    <select class="form-control select2" name="segment">--}}
{{--                                        <option value="1">All Segment</option>--}}
{{--                                        @foreach($segments as $segment)--}}
{{--                                            <option--}}
{{--                                                value="{{ $segment->id }}" {{ request()->get('segment') == $segment->id ? 'selected' : '' }}>{{ $segment->name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}

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
            {{--            @if($incomes)--}}
            <section class="card">

                <div class="card-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                    <div class="adv-table table-responsive" id="prinarea">
                        <div class="row" id="heading_area" style="margin-bottom: 10px!important;display: none">
                            <div class="col-sm-12 text-center" style="font-size: 16px">
                                <h2 style="margin-bottom: 0 !important;"><img width="75px" src="{{ asset('img/logo.jpeg') }}" alt="">
                                    <strong
                                        style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i></strong>
                                </h2>
                                <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Receive and Payment Report</strong>
                                <p class="">Printed by: {{Auth::user()->name}}</p>
                            </div>
                            <div class="col-sm-3 col-sm-offset-9">
                                <span class="date-top">Date: <strong
                                        style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;font-weight: normal;">{{ date('d-m-Y') }}</strong></span>
                            </div>
                        </div>
                        <div class="img-overlay" style="z-index: 1;">
                            <img src="{{ asset('img/logo.jpeg') }}">
                        </div>
                        <div style="padding:10px; width:100%; text-align:center;">
                            <h2></h2>
                            <h4></h4>
                            <h4></h4>
                        </div>
                        <table class="table table-bordered" style="margin-bottom: 0px">
                            <tr>
                                <th class="text-center">{{ date("F d, Y", strtotime(request()->get('start'))).' - '.date("F d, Y", strtotime(request()->get('end'))) }}</th>
                            </tr>
                        </table>

                        <div style="clear: both">
                            <table class="table table-bordered" style="width:50%; float:left">
                                <tr>
                                    <th colspan="6" class="text-center">Receive</th>
                                </tr>
                                <tr>
                                    <th class="text-center" width="25%">Account Head</th>
                                    <th class="text-center" width="10%">Amount</th>
                                </tr>

                                @foreach($incomes as $income)
                                    @if ($income->account_head_type_id == 2)
                                        <tr>
                                            <td>{{ $income->accountHead->name }}</td>
                                            <td class="text-center">৳ {{ number_format(($income->amount),2) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $income->accountHead->name }}</td>
                                            <td class="text-center">৳ {{ number_format($income->amount,2) }}</td>
                                        </tr>
                                    @endif
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
                                        <td><br></td>
                                        <td></td>
                                    </tr>
                                @endfor

                                <tr>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">৳ {{ number_format($incomes->sum('amount'),2) }}</th>
                                </tr>
                            </table>
                            <table class="table table-bordered" style="width:50%; float:left">
                                <tr>
                                    <th colspan="6" class="text-center">Payment</th>
                                </tr>
                                <tr>
                                    <th class="text-center" width="25%">Account Head</th>
                                    <th class="text-center" width="10%">Amount</th>
                                </tr>

                                @foreach($expenses as $expense)

                                    <tr>
                                        <td>{{ $expense->accountHead->name }}</td>
                                        <td class="text-center">৳ {{ number_format($expense->amount,2) }}</td>
                                    </tr>

                                @endforeach

                                @for($i=count($expenses); $i<$maxCount; $i++)
                                    <tr>
                                        <td><br></td>
                                        <td></td>
                                    </tr>
                                @endfor

                                <tr>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">৳ {{ number_format($expenses->sum('amount'),2) }}</th>
                                </tr>
                            </table>
                        </div>

                        @if($incomes)
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        <div class="card-header with-border">
                                            <h3 class="card-title">Summary</h3>
                                        </div>
                                        <!-- /.card-header -->

                                        <div class="card-body">
                                            @if($incomes->sum('amount') >= $expenses->sum('amount'))
                                                <strong>In Hand: </strong> {{ number_format($incomes->sum('amount') - $expenses->sum('amount'), 2) }}
                                            @else
                                                <strong>Over Cost: </strong> {{ number_format($expenses->sum('amount') - $incomes->sum('amount'), 2) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>


            </section>
            {{--            @endif--}}
        </div>
    </div>
@endsection

@section('script')

    <script>
        $(function () {

            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

        });

        var APP_URL = '{!! url()->full()  !!}';

        function getprint(prinarea) {
            $('#heading_area').show();
            $('body').html($('#' + prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>

@endsection
