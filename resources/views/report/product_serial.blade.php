@extends('layouts.app')
@section('title')
    Product Serial
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
                    <form action="{{ route('product_serial.report') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start To</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" name="start" value="{{ request()->get('start')  }}" placeholder="Start Serial"
                                               autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From End</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" name="end" value="{{ request()->get('end')  }}" placeholder="Serial End"
                                               autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
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
                                <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Serial Wise Report</strong>
                                <p class="">Printed by: {{Auth::user()->name}}</p>
                            </div>
                            <div class="col-sm-3 col-sm-offset-9"> <span class="date-top">Date: <strong style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;font-weight: normal;">{{ date('d-m-Y') }}</strong></span>
                            </div>
                        </div>
                        <div style="clear: both">
                            <div class="table-responsive" >
                                <table id="table" class="table table-bordered table-striped" style="z-index: 5;">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Serial Number</th>
                                        <th class="text-center">Warranty</th>
                                        <th class="text-center">Sale Date</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Order</th>
                                    </tr>
                                    </thead>
                                    @if($serials)
                                        <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach($serials as $serial)
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-center">{{ $serial->product->name ?? '' }}</td>
                                                <td class="text-center">{{ $serial->serial }}</td>
                                                <td class="text-center">{{ $serial->product->warranty ?? '' }}</td>
                                                <td class="text-center">{{$serial->inventoryLog->saleOrder->date ?? ''}}</td>
                                                <td class="text-center">
                                                    @if($serial->quantity == 1)
                                                        <span class="badge badge-info">In Stock</span>
                                                    @elseif($serial->quantity == 0)
                                                        <span class="badge badge-warning">Sold Out</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($serial->quantity == 0)
                                                    <a href="{{route('sale_receipt.details',['order'=>$serial->inventoryLog->saleOrder->id ?? 0])}}">{{$serial->inventoryLog->saleOrder->order_no ?? ''}}</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach
                                        </tbody>
                                    @endif
                                </table>
                            </div>
                        </div>
                        @if(count($missingSerials) != 0)
                        <h4>Missing Serial No :</h4>
                        @foreach($missingSerials as $missingSerial)
                           <b> {{ $missingSerial ?? '' }}</b>,
                        @endforeach
                        @endif
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
