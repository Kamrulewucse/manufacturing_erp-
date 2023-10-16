@extends('layouts.master')
@section('title')
    Project Report
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
                <div class="card-body">
                    <form action="{{ route('project.report') }}">
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Project</label>

                                    <select class="form-control select2" name="project">
                                        <option value="">All Project</option>
                                        @foreach($projects as $proj)
                                            <option
                                                value="{{ $proj->id }}" {{ request()->get('project') == $proj->id ? 'selected' : '' }}>{{ $proj->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> &nbsp;</label>

                                    <input class="btn btn-primary form-control" type="submit" value="Search">
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if($project!=null)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button>
                        <br><br>
                        <div id="prinarea">
                            <div class="row" id="heading_area" style="margin-bottom: 10px!important;display: none">
                                <div class="col-sm-12 text-center" style="font-size: 16px">
                                    <h2 style="margin-bottom: 0 !important;"><img width="75px" src="{{ asset('img/logo.jpeg') }}" alt="">
                                        <strong
                                            style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i></strong>
                                    </h2>
                                    <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Project
                                        Report</strong>
                                    <p class="">Printed by: {{Auth::user()->name}}</p>
                                </div>
                                <div class="col-sm-3 col-sm-offset-9">
                                <span class="date-top">Date: <strong
                                        style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;font-weight: normal;">{{ date('d-m-Y') }}</strong></span>
                                </div>
                            </div>
                            <div style="clear: both">
                                <div class="img-overlay">
                                    <img src="{{ asset('img/logo.jpeg') }}">
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3>Budget</h3>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Total Budget</th>
                                                <td>{{number_format($datas['project']->budget??0,2)}}</td>
                                            </tr>

                                        </table>
                                        <h3>Received & Expense</h3>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Total Received</th>
                                                <td>{{number_format($incomes??0,2)}}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Expense</th>
                                                <td>{{number_format($expenses??0,2)}}</td>
                                            </tr>
                                            <tr>
                                                <th>In Hand</th>
                                                <td>{{number_format($incomes-$expenses??0,2)}}</td>
                                            </tr>

                                        </table>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h3>Received TK From Stakeholder</h3>
                                        <div class="table-responsive">
                                            <table id="stackeholder-payment" class="table table-bordered">
                                                <thead>
                                                <tr>

                                                    <th>Name</th>
{{--                                                    <th>Total Installment</th>--}}
{{--                                                    <th>Completed Installment</th>--}}
{{--                                                    <th>Due Installment</th>--}}
                                                    <th>Budget</th>
                                                    <th>Due</th>

                                                </tr>
                                                </thead>
                                                <tbody>

                                                @php
                                                    $stktotal=0;
                                               @endphp
                                                @foreach($stakeholders as $stake)
                                                    @php
                                                        $total=0;
                                                         $total = $stake->totalBudgetPaymentAll($project->id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{$stake->name}}</td>
{{--                                                        <td>{{$stake->TotalInstallment??'0'}}</td>--}}
{{--                                                        <td>{{$stake->project->stakeholderPayment->instalment_count??'0'}}</td>--}}
{{--                                                        <td>{{$stake->project->stakeholderPayment->instalment_no??'0' - $stake->project->stakeholderPayment->instalment_count??'0'}}</td>--}}
                                                        <td>{{$stake->totalBudgetPaymentAll($project->id)??0}}</td>
                                                        <td class="bg-red">{{$stake->dueByProject($project->id)??0}}</td>


                                                    </tr>
                                                    @php
                                                        $stktotal+=$total;
                                                    @endphp
                                                @endforeach
                                                </tbody>
                                                <tfoot>
                                                <tr>

                                                    <th class="text-right">Total</th>
                                                    <th>{{number_format($stktotal,2)}}</th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3>Received TK From Others</h3>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Head Type</th>
                                                    <th>Sub Head Type</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                    $oitotal=0;
                                                @endphp
                                                @foreach($otherIncomes as $otherIncome)
                                                    <tr>
                                                        <td>{{$otherIncome->accountHead->name??''}}</td>
                                                        <td>{{$otherIncome->accountSubHead->name??''}}</td>
                                                        <td>{{$otherIncome->amount??0}}</td>
                                                    </tr>
                                                    @php
                                                        $oitotal+=$otherIncome->amount??0;
                                                    @endphp
                                                @endforeach
                                                </tbody>
                                                <tfoot>
                                                <th colspan="2" class="text-right">Total</th>
                                                <th>{{number_format($oitotal,2)}}</th>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <h3>Expenses TK From Others</h3>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Head Type</th>
                                                    <th>Sub Head Type</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                    $oxtotal=0;
                                                @endphp
                                                @foreach($otherExpenses as $otherExpense)
                                                    <tr>
                                                        <td>{{$otherExpense->accountHead->name??''}}</td>
                                                        <td>{{$otherExpense->accountSubHead->name??''}}</td>
                                                        <td>{{$otherExpense->amount??0}}</td>
                                                    </tr>
                                                    @php
                                                        $oxtotal+=$otherExpense->amount;
                                                    @endphp
                                                @endforeach
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-right">Total</th>
                                                    <th>{{number_format($oxtotal,2)}}</th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('script')

    <script>
        $(function () {

            $('#supplier-payments').DataTable();
            $('#supplier-details').DataTable();


            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

        });

    </script>
    <script>
        var APP_URL = '{!! url()->full()  !!}';

        function getprint(prinarea) {
            $('#heading_area').show();
            $('body').html($('#' + prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
