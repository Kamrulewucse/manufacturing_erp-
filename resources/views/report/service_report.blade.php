@extends('layouts.app')
@section('title')
    Service Report
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
                    <form action="{{ route('service.report') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start">Start Date <span
                                            class="text-danger">*</span></label>
                                    <input required type="text" id="start" autocomplete="off"
                                           name="start" class="form-control date-picker"
                                           placeholder="Enter Start Date" value="{{ request()->get('start') ?? $currentDate  }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end">End Date <span
                                            class="text-danger">*</span></label>
                                    <input required type="text" id="end" autocomplete="off"
                                           name="end" class="form-control date-picker"
                                           placeholder="Enter Start Date" value="{{ request()->get('end') ?? date('d-m-Y')  }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Supplier</label>

                                    <select class="form-control select2" name="customer">
                                        <option value="">All Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request()->get('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name}}</option>
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
                                        style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i>
                                    </strong>
                                </h2>
                                <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Supplier Report</strong>
                                <p class="">Printed by: {{Auth::user()->name}}</p>
                            </div>
                            <div class="col-sm-3 col-sm-offset-9">
                                <span class="date-top">Date: <strong
                                        style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;font-weight: normal;">{{ date('d-m-Y') }}</strong></span>
                            </div>
                        </div>
                        <div style="clear: both">
                            <div class="table-responsive" >
                                <table id="table" class="table table-bordered table-striped" style="z-index: 5;">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Paid</th>
                                        <th class="text-center">Due</th>
                                        <th class="extra_column">Action</th class="extra_column">
                                        {{-- <th class="text-center">Refund</th> --}}
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="text-center">{{$loop->iteration}}</td>
                                            <td>{{$order->date}}</td>
                                            <td>{{$order->client->name}}</td>
                                            <td class="text-center">৳ {{number_format($order->total,2)}}</td>
                                            <td class="text-center">৳ {{number_format($order->paid,2)}}</td>
                                            <td class="text-center">৳ {{number_format($order->due,2)}}</td>
                                            <td class="extra_column">
                                                <a href="{{ route('service_receipt.details', ['order' => $order->id]) }}">View
                                                    Invoice
                                                </a>
                                            </td>
                                            {{-- <td class="text-center">৳ {{number_format($order->refund,2)}}</td> --}}
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th class="text-right" colspan="3">Total</th>
                                        <th class="text-center">৳ {{number_format($orders->sum('total'))}}</th>
                                        <th class="text-center">৳ {{number_format($orders->sum('paid'))}}</th>
                                        <th class="text-center">৳ {{number_format($orders->sum('due'))}}</th>
                                         <th class="text-center"></th>
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
@endsection
@section('script')

    <script>
        $(function () {
            $('#table').DataTable();
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
