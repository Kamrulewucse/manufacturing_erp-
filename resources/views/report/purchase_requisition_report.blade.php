@extends('layouts.master')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet"
        href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Purchase & Requisition Report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filter</h3>
                </div>
                <!-- /.box-header -->

                <div class="card-body">
                    <form action="{{ route('purchase_requisition_report.all') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Project</label>
                                    <select class="form-control select2" name="project" required>
{{--                                        <option value="">Select Project</option>--}}
                                        @foreach ($projects as $project)
                                            <option {{ request()->get('project') == $project->id ? 'selected' : '' }}
                                                value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label> &nbsp;</label>
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
        <div class="col-sm-12">
            @if ($requisitions || $purchases)
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <a href="#" onclick="getprint('printArea')" class="btn btn-default btn-lg"><i
                                class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive-sm" id="printArea">
                            <div style="padding:10px; width:100%; text-align:center;">
                                <h2>Fortress Holdings Ltd.</h2>
                                <h4>Rupayan Tower, (12th Floor),
                                    Sayem Sobhan Anvir Rd, <br> Plot: 02, Dhaka 1229, Bangladesh.</h4>
                                <h4>Tel: +88 02 8432643-4, Mob: 01313 714089.</h4>
                                <h4>E-mail:scm@fortress.com.bd Web: www.fortress.com.bd</h4>
                                <h4><strong>Requisition  & Purchase Report</strong></h4>
                                @foreach ($projects as $project)
                                    <h4>{{ request()->get('project') == $project->id ? $project->name : '' }}</h4>
                                @endforeach

                            </div>
                            <hr>

                            <div style="clear: both">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4 class="text-center">Requisition Product And Quantity</h4>
                                        <table class="table table-bordered" style="width:100%; float:left">
                                            <tr>
                                                <th class="text-center">Name</th>
                                                <th class="text-center">Requisition</th>
                                                <th class="text-center">Purchase</th>
                                                <th class="text-center">Remaining</th>

                                            </tr>
                                            @php
                                                $total = 0;
                                                $ptotal = 0;
                                                $rtotal = 0;
                                            @endphp
                                            @foreach ($requisitions as $requisition)
                                                <tr>
                                                    <td>{{$requisition->product->name}}</td>
                                                    <td class="text-center">{{ $requisition->total }}</td>
                                                    <td class="text-center">{{ $requisition->purchaseTotal($currentProject->id) }}</td>
                                                    <td class="text-center">{{ $requisition->total-$requisition->purchaseTotal($currentProject->id) }}</td>

                                                </tr>
                                                @php
                                                    $total += $requisition->total;
                                                    $ptotal += $requisition->purchaseTotal($currentProject->id);
                                                    $rtotal += $requisition->total-$requisition->purchaseTotal($currentProject->id);
                                                @endphp
                                            @endforeach
                                            <tr>
                                                <th colspan="1" class="text-right">Total</th>
                                                <th class="text-center">{{ number_format($total, 2) }}</th>
                                                <th class="text-center">{{ number_format($ptotal, 2) }}</th>
                                                <th class="text-center">{{ number_format($rtotal, 2) }}</th>
                                            </tr>

                                        </table>
                                    </div>



                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script
        src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}">
    </script>

    <script>
        $(function() {
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });

        var APP_URL = '{!! url()->full() !!}';

        function getprint(print) {

            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
