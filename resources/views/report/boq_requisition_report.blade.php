@extends('layouts.master')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet"
        href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    BOQ & Requisition Report
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
                    <form action="{{ route('requisition.boq.report') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Project</label>
                                    <select class="form-control select2" id="project" name="project" required>
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
                                    <label>Segment</label>
                                    <select class="form-control select2" id="segment" name="segment">
                                        <option value="">Select Segment</option>

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
            @if ($costingProducts)
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
                                <h4><strong> Requisition & BOQ Report</strong></h4>
                                @foreach ($projects as $project)
                                    <h4>{{ request()->get('project') == $project->id ? $project->name : '' }}</h4>
                                @endforeach

                            </div>


                            <div style="clear: both">


                                <div class="row">
                                    <div class="col-sm-12">
                                        @foreach ($costingProducts as $costing)
                                        <table class="table table-bordered" style="width:100%; float:left">
                                            <tr>
                                                <th class="text-center">Segment</th>
                                                <th class="text-center">Product</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-center">Requisition</th>
                                                <th class="text-center">Remaining</th>

                                            </tr>
                                            @php
                                                $rtotal=0;
                                            @endphp
                                            @foreach ($costing->estimateProducts as $costingProduct)
                                                <tr>
                                                    <td class="text-center">{{$costingProduct->costing->segment->name??''}}</td>
                                                    <td class="text-center">{{$costingProduct->product->name}}</td>
                                                    <td class="text-center">{{$costingProduct->quantity}} {{$costingProduct->product->unit->name}}</td>
                                                    <td class="text-center">{{$costingProduct->requisitionReport($costingProduct->costing->segment->id,$costingProduct->product->id)}} </td>
                                                    <td class="text-center">{{$costingProduct->quantity-$costingProduct->requisitionReport($costingProduct->costing->segment->id,$costingProduct->product->id)}} </td>


                                                </tr>
                                                @php
                                                    // $rtotal+=$costingProduct->costing_amount;
                                                @endphp
                                            @endforeach


                                        </table>
                                        @endforeach
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
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}">
    </script>

    <script>
        $(function() {
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            var selectedSegment='{{request()->get('segment')}}';

            $('body').on('change','#project', function () {
                var projectId = $(this).val();
                var itemProject = $(this);
               $('#segment').html('<option value="">Select Segment</option>');
                if (projectId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_segment') }}",
                        data: {projectId:projectId}
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (selectedSegment == item.id)
                                $('#segment').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#segment').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                    });

                }
            });
            $('#project').trigger('change');


        });

        var APP_URL = '{!! url()->full() !!}';

        function getprint(print) {

            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
