@extends('layouts.master')
@section('title')
    Stakeholder Report
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
                    <form action="{{ route('stakeholder.report') }}">
                        <div class="row">
                            {{--                            <div class="col-md-3">--}}
                            {{--                                <div class="form-group">--}}
                            {{--                                    <label>Start Date</label>--}}

                            {{--                                    <div class="input-group date">--}}
                            {{--                                        <div class="input-group-addon">--}}
                            {{--                                            <i class="fa fa-calendar"></i>--}}
                            {{--                                        </div>--}}
                            {{--                                        <input type="text" class="form-control pull-right"--}}
                            {{--                                               id="start" name="start" value="{{ request()->get('start')  }}" autocomplete="off" >--}}
                            {{--                                    </div>--}}
                            {{--                                    <!-- /.input group -->--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}

                            {{--                            <div class="col-md-3">--}}
                            {{--                                <div class="form-group">--}}
                            {{--                                    <label>End Date</label>--}}

                            {{--                                    <div class="input-group date">--}}
                            {{--                                        <div class="input-group-addon">--}}
                            {{--                                            <i class="fa fa-calendar"></i>--}}
                            {{--                                        </div>--}}
                            {{--                                        <input type="text" class="form-control pull-right"--}}
                            {{--                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off" >--}}
                            {{--                                    </div>--}}
                            {{--                                    <!-- /.input group -->--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Project</label>

                                    <select class="form-control select2" name="project" id="project">
                                        <option value="">All Project</option>
                                        @foreach($projects as $project)
                                            <option
                                                value="{{ $project->id }}" {{ request()->get('project') == $project->id ? 'selected' : '' }}>{{ $project->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Stakeholder</label>

                                    <select class="form-control select2" name="stakeholder" id="stakeholder">
                                        <option value="">All Stakeholder</option>
                                        {{--                                        @foreach($stakeholders as $stakeholder)--}}
                                        {{--                                            <option value="{{ $stakeholder->id }}" {{ request()->get('stakeholder') == $stakeholder->id ? 'selected' : '' }}>{{ $stakeholder->name}}</option>--}}
                                        {{--                                        @endforeach--}}
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
    @if ($projectStakeholder)

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button>
                        <br><br>
                        <div id="prinarea">
                            <div class="row" id="heading_area" style="margin-bottom: 10px!important;display: none">
                                <div class="col-sm-12 text-center" style="font-size: 16px">
                                    <h2 style="margin-bottom: 0 !important;"><img width="75px"
                                                                                  src="{{ asset('img/logo.jpeg') }}"
                                                                                  alt="">
                                        <strong
                                            style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i></strong>
                                    </h2>
                                    <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Stakeholder
                                        Report</strong>
                                    <p class="">Printed by: {{Auth::user()->name}}</p>
                                </div>
                                <div class="col-sm-3 col-sm-offset-9">
                                <span class="date-top">Date: <strong
                                        style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;font-weight: normal;">{{ date('d-m-Y') }}</strong></span>
                                </div>
                            </div>
                            <div style="clear: both">
                                <div class="img-overlay" id="image_area" style="display: none;">
                                    <img src="{{ asset('img/logo.jpeg') }}">
                                </div>

                                <div class="row">
                                    <div class="col-sm-5">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Name</th>
                                                <td>{{$projectStakeholder->stakeholder->name??''}}</td>
                                            </tr>
                                            <tr>
                                                <th>Address</th>
                                                <td>{{$projectStakeholder->stakeholder->address??''}}</td>
                                            </tr>
                                            <tr>
                                                <th>Phone No</th>
                                                <td>{{$projectStakeholder->stakeholder->mobile_no??''}}</td>
                                            </tr>
                                            <tr>
                                                <th>Project</th>
                                                <td>{{$projectStakeholder->project->name??''}}</td>
                                            </tr>
                                            <tr>
                                                <th>Address</th>
                                                <td>{{$projectStakeholder->project->address??''}}</td>
                                            </tr>

                                        </table>
                                    </div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6">
                                        <table class="table table-bordered">

                                            <tr>
                                                <th>Total Project Payment</th>
                                                <td>{{number_format($projectStakeholder->budget_total??0,2)}}</td>
                                            </tr>
                                            <tr>
                                                <th> Total Instalment</th>
                                                <td>{{$projectStakeholder->budget_instalment??0}}</td>
                                            </tr>
                                            <tr>
                                                <th>Per Instalment Amount</th>
                                                <td>{{number_format($projectStakeholder->budget_per_instalment_amount??0,2)}} </td>
                                            </tr>


                                        </table>

                                    </div>
                                </div>
                                <div class="row">
                                <div class="table-responsive" >

                                    <table id="table" class="table table-bordered table-striped" >
                                        <thead>
                                        <tr>
                                            <th class="text-center">Sl</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Instalment Purpose</th>
                                            <th class="text-center">Instalment Number</th>
                                            <th class="text-center"> Payment Type</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Note</th>

                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($payments as $payment)

                                            <tr>
                                                <td class="text-center">{{$loop->iteration}}</td>
                                                <td>{{$payment->date??''}}</td>
                                                @if($payment->project_payment_type == 1)
                                                    <td>Project Budget</td>
                                                @else
                                                    <td>Profit Share</td>
                                                @endif

                                                <td>{{$payment->instalment_no??''}}</td>
                                                @if($payment->transaction_method==1)
                                                    <td>Cash</td>
                                                @elseif($payment->transaction_method==2)
                                                    <td>Bank</td>
                                                @else
                                                    <td>Mobile Banking</td>
                                                @endif
                                                <td class="text-center">৳ {{number_format($payment->total,2)}}</td>
                                                <td class="text-center"> {{$payment->note}}</td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th></th>
                                            <th class="text-right" colspan="4">Total</th>
                                            <th class="text-center"> ৳ {{number_format($payments->sum('total')??0,2)}}</th>
                                            <td></td>

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
    @endif
@endsection
@section('script')

    <script>
        $(function () {
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            var selectStakeholder = '{{ request()->get('stakeholder')}}';

            $('body').on('change', '#project', function () {
                var projectId = $(this).val();

                $('#stakeholder').html('<option value="">Select Stakeholder</option>');
                // var selectedProduct = itemCategory.closest('tr').find('.product').attr("data-selected-product");
                if (projectId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_stakeholder') }}",
                        data: {projectId: projectId}
                    }).done(function (response) {
                        $.each(response, function (index, item) {
                            //    console.log(response);
                            if (selectStakeholder == item.stakeholder.id)
                                $('#stakeholder').append('<option value="' + item.stakeholder.id + '" selected>' + item.stakeholder.name + '</option>');
                            else
                                $('#stakeholder').append('<option value="' + item.stakeholder.id + '">' + item.stakeholder.name + '</option>');
                        });

                    });

                }
            });

            $('#project').trigger('change');


        });


    </script>
    <script>
        var APP_URL = '{!! url()->full()  !!}';

        function getprint(prinarea) {
            $('#heading_area').show();
            $('#image_area').show();
            $('body').html($('#' + prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
