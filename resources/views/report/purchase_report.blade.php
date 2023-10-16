@extends('layouts.app')

@section('title')
    Purchase Report
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
                    <form action="{{ route('purchase.report') }}">
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

                                    <select class="form-control select2" name="supplier">
                                        <option value="">All Supplier</option>

                                        @foreach($suppliers as $supplier)
                                            <option
                                                value="{{ $supplier->id }}" {{ request()->get('supplier') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
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
        <div class="col-sm-12">
            <section class="card">
                <div class="card-heading">
                    <a href="#" role="button" onclick="getprint('print-area')" class="btn btn-primary btn-sm"><i
                            class="fa fa-print"></i> Print</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="print-area">
                        <div class="row" id="heading_area" style="margin-bottom: 10px!important;display: none">
                            <div class="col-sm-12 text-center" style="font-size: 16px">
                                <h2 style="margin-bottom: 0 !important;"><img width="75px"
                                  src="{{ asset('img/logo.png') }}" alt="">
                                    <strong
                                        style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i></strong>
                                </h2>
                                <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Purchase
                                    Report</strong>
                                <p class=" d-block">Printed by: {{Auth::user()->name}}</p>
                            </div>
                            <div class="col-sm-3 col-xs-offset-9">
                                <span class="date-top">Date:  <strong
                                        style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;font-weight: normal;">{{ date('d-m-Y') }}</strong></span>
                            </div>
                        </div>
                        <div style="clear: both">
                            <div class="img-overlay" style="display: none">
                                <img src="{{ asset('img/logo.jpeg') }}">
                            </div>
                            <div class="table-responsive" >
                            <table id="table" class="table table-bordered table-striped" style="z-index: 5;">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order No.</th>
                                    <th>Supplier</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th class="extra_column">Action</th class="extra_column">
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->date }}</td>
                                        <td>{{ $order->order_no }}</td>
                                        <td>{{ $order->supplier->name }}</td>
                                        <td>{{ number_format($order->total, 2) }}</td>
                                        <td>{{ number_format($order->paid, 2) }}</td>
                                        <td>{{ number_format($order->due, 2) }}</td>
                                        <td class="extra_column">
                                            <a href="{{ route('purchase_receipt.details', ['order' => $order->id]) }}">View
                                                Invoice
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                                <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Total</th>
                                    <th>{{ number_format($total, 2) }}</th>
                                    <th>{{ number_format($paid, 2) }}</th>
                                    <th>{{ number_format($due, 2) }}</th>
                                    <th class="extra_column"></th>
                                </tr>
                                </tfoot>
                            </table>
                            </div>
                        </div>

                        {{ $orders->appends($appends)->links() }}
                    </div>
                </div>
            </section>
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
