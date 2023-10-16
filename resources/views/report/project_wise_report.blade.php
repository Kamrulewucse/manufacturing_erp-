@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
    Project Wise Report
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
                    <form action="{{ route('project.report') }}">
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
                                    <label for="account_head">Project Name <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="project" id="project">
                                        <option value="All">All</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
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
            {{--            @if($payments || $receipts)--}}
            <section class="card">

                <div class="card-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                    <div class="adv-table" id="prinarea">
                        <div style="padding:10px; width:100%; text-align:center;">
                            <div class="row ">
{{--                                <div class="col-12 text-center" style="margin-top: 25px">--}}
{{--                                    <img height="100px" width="95%" src="{{ asset('uploads/header.PNG') }}" alt="">--}}
{{--                                </div>--}}
                                <div class="col-12">
                                    <h3 class="text-center m-0" style="font-size: 25px !important;">Project Wise Receive And Payment Report</h3>
                                    <h4 class="text-center m-0 mb-2" style="font-weight: 800 !important;">Date: {{ date('d-m-Y',strtotime(request('start'))) }} to {{ date('d-m-Y',strtotime(request('end'))) }}</h4>
                                </div>
                            </div>

                            <div style="clear: both">
                                <table class="table table-bordered" style="width:50%; float:left">
                                    <tr>
                                        <th colspan="6" class="text-center">Receipt</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" width="25%">Date</th>
                                        <th class="text-center" width="25%">Account Head</th>
                                        <th class="text-center" width="25%">Receipt Type</th>
{{--                                        <th class="text-center" width="25%">Client Name</th>--}}
                                        <th class="text-center" width="10%">Amount</th>
                                    </tr>

                                    @foreach($receipts as $receipt)
                                        <tr>
                                            <td>{{ date('d-m-Y',strtotime($receipt->date)) }}</td>
                                            <td>{{ $receipt->paymentAccountHead->name ?? ''}}</td>
                                            <td>
                                                @if($receipt->payment_type == 1)
                                                    Bank
                                                @else
                                                    Cash
                                                @endif

                                            </td>
{{--                                            <td class="text-center">{{ $receipt->client->name ?? '' }}</td>--}}
                                            <td class="text-center">৳ {{ number_format($receipt->amount,2) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <th colspan="3" class="text-right">Total</th>
                                        <th class="text-center">৳ {{ number_format($receipts->sum('amount'),2) }}</th>
                                    </tr>
                                </table>
                                <table class="table table-bordered" style="width:50%; float:left">
                                    <tr>
                                        <th colspan="6" class="text-center">Payment</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" width="25%">Date</th>
                                        <th class="text-center" width="25%">Account Head</th>
                                        <th class="text-center" width="25%">Payment Type</th>
{{--                                        <th class="text-center" width="25%">Payee Name</th>--}}
                                        <th class="text-center" width="10%">Amount</th>
                                    </tr>

                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ date('d-m-Y',strtotime($payment->date)) }}</td>
                                            <td>{{ $payment->paymentAccountHead->name ?? ''}}</td>
                                            <td>
                                                @if($payment->payment_type == 1)
                                                    Bank
                                                @else
                                                    Cash
                                                @endif

                                            </td>
{{--                                            <td class="text-center">{{ $payment->payee->name ?? ''}}</td>--}}
                                            <td class="text-center">৳ {{ number_format($payment->amount,2) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <th colspan="3" class="text-right">Total</th>
                                        <th class="text-center">৳ {{ number_format($payments->sum('amount'),2) }}</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {{--            @endif--}}
        </div>
    </div>
@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            var selectedProduct = '{{ request()->get('product') }}';
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
