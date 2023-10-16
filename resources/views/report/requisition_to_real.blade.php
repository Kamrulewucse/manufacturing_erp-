@extends('layouts.master')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet"
          href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
    Requisition and Project Report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-default">
                <div class="card-header">
                    <h3 class="card-title">Filter</h3>
                </div>
                <!-- /.box-header -->

                <div class="card-body">
                    <form action="{{ route('requisition.report') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Project</label>
                                    <select class="form-control select2" name="real_project" required>
                                        <option value="">Select Project</option>
                                        @foreach($realProjects as $project)
                                            <option
                                                {{ old('real_project') == $project->id ? 'selected' : '' }} value="{{$project->id}}">{{$project->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Estimate Project</label>
                                    <select class="form-control select2" name="estimate_project" required>
                                        <option value="">Select Project</option>
                                        @foreach($estimateProjects as $project)
                                            <option
                                                {{ old('estimate_project') == $project->id ? 'selected' : '' }} value="{{$project->id}}">{{$project->name}}</option>
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

@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script
        src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });

        var APP_URL = '{!! url()->full()  !!}';

        function getprint(print) {

            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
