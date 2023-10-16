@extends('layouts.master')
@section('title')
    Physical Progress Report
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
                    <form action="{{ route('physical.project.report') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start')  }}"
                                               autocomplete="off">
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
                                               id="end" name="end" value="{{ request()->get('end')  }}"
                                               autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

{{--                            <div class="col-md-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>Projects</label>--}}

{{--                                    <select class="form-control select2" name="project">--}}
{{--                                        <option value="">All Project</option>--}}
{{--                                        @foreach($projects as $project)--}}
{{--                                            <option--}}
{{--                                                value="{{ $project->id }}" {{ request()->get('project') == $project->id ? 'selected' : '' }}>{{ $project->name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}

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
                                <h2 style="margin-bottom: 0 !important;"><img width="75px"
                                                                              src="{{ asset('img/logo.jpeg') }}" alt="">
                                    <strong
                                        style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i></strong>
                                </h2>
                                <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Supplier
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
                            <div class="table-responsive">
                                <table id="table" class="table table-bordered table-striped">
                                    @php
                                        $Total=0;
                                    @endphp
                                    <thead>
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Segment</th>
                                        <th class="text-center">Unit Done</th>
                                        <th class="text-center">Percentage</th>
                                        <th class="text-center">Project Percentage</th>
                                    </tr>
                                    </thead>
                                    @foreach($segments as $segment)
                                        <tbody>

                                        @php
                                            $segmentT=0;
                                            $projectT=0;
                                            $segmentName='';
                                        @endphp

                                        @foreach($progresses as $progress)
                                            @if($progress->product_segment_id == $segment->id)
                                                <tr>
                                                    <td class="text-center">{{$progress->date}}</td>
                                                    <td>{{$progress->segment->name}}</td>
                                                    <td>{{$progress->daily_unit_done}}</td>
                                                    <td>{{$progress->segment_progress_percentage}} %</td>
                                                    <td>{{$progress->project_progress_percentance}} %</td>
                                                    @php
                                                        $segmentName=$progress->segment->name;
                                                            $segmentT+=$progress->segment_progress_percentage;
                                                            $projectT+=$progress->project_progress_percentance;
                                                    @endphp
                                                </tr>

                                            @endif

                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <th colspan="2">Total for {{$segment->name}}</th>
                                            <td>{{number_format($segmentT,2)}} %</td>
                                            <td>{{number_format($projectT,2)}} %</td>
                                        </tr>
                                        @php
                                            $Total+=$projectT;
                                        @endphp
                                        </tbody>
                                    @endforeach
                                </table>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-6 offset-sm-6">


                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Total Project Complete</th>
                                        <td>{{number_format($Total,2)}} %</td>
                                    </tr>
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
