@extends('layouts.master')
@section('title')
    Progress Report
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
                    <button class="float-right btn btn-primary" onclick="getprint('prinarea')">Print</button>
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
                            @foreach($projects as $project)
                                <div class="card">
                                    <div class="card-header">
                                        <h4>{{$project->name}}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <table class="table table-striped">
                                                    <tr>
                                                        <th>Total Project Percentage</th>
                                                        <th>: {{$project->totalProgress}} %</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Total Project Completed</th>
                                                        <th>: {{$project->totalCompleted}} %</th>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-sm-6">
                                                <table class="table table-striped">
                                                    <tr>
                                                        <th> Name</th>
                                                        <th>Total Percentage of Project</th>
                                                        <th>Complete Segment</th>
                                                    </tr>

                                                    @foreach($project->segments as $segment)
                                                        <tr>
                                                            <th>{{$segment->name}}</th>
                                                            <td>{{$segment->segment_percentage}} %</td>
                                                            <td>{{$segment->totalProgress}} %</td>
                                                        </tr>

                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

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
