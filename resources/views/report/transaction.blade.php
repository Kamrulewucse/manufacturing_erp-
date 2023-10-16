@extends('layouts.master')

@section('title')
    Transaction Report
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
                    <form action="{{ route('report.transaction') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start')  }}" autocomplete="off" required>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>End Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off" required>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account Head Type</label>

                                    <select class="form-control select2" name="type" id="account_head_type">
                                        <option value="">All Type</option>

                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" {{ request()->get('type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account Head Sub Type</label>

                                    <select class="form-control select2" name="sub_type" id="account_head_sub_type">
                                        <option value="">All Sub Type</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>	&nbsp;</label>

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
        <div class="col-sm-12" style="min-height:300px">
            @if($result)
                <section class="card">

                    <div class="card-body">
                        <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>
                        <br>
                        <div class="adv-table table-responsive" id="prinarea">
                            <div class="row" id="heading_area" style="margin-bottom: 10px!important;display: none">
                                <div class="col-sm-12 text-center" style="font-size: 16px">
                                    <h2 style="margin-bottom: 0 !important;"><img width="75px" src="{{ asset('img/logo.jpeg') }}" alt="">
                                        <strong
                                            style="border-bottom: 2px dotted #000;"><i>{{ config('app.name') }}</i></strong>
                                    </h2>
                                    <strong style="border: 2px solid #000;padding: 1px 10px;font-size: 19px;">Bank Statement
                                        Report</strong>
                                    <p class="">Printed by: {{Auth::user()->name}}</p>
                                </div>
                                <div class="col-sm-3 col-sm-offset-9">
                                <span class="date-top">Date: <strong
                                        style="border: 1px solid #000;padding: 1px 10px;font-size: 16px;width: 100%;font-weight: normal;">{{ date('d-m-Y') }}</strong></span>
                                </div>
                            </div>
                            <div class="img-overlay" style="z-index: 1;">
                                <img src="{{ asset('img/logo.jpeg') }}">
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Date(D/M/Y)</th>
                                    <th>Account Head</th>
                                    <th>Account Sub Head</th>
                                    <th width="">Particular</th>
                                    <th width="">Note</th>
                                    <th width="">Payment Type</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($result as $item)
                                    <tr>
                                        <td>{{ date('d-m-Y',strtotime($item->date)) }}</td>
                                        <td>{{ $item->accountHead->name }}</td>
                                        <td>{{ $item->accountSubHead->name }}</td>
                                        <td>{{ $item->particular }}</td>
                                        <td>{{ $item->note }}</td>
                                        <td>
                                            @if($item->transaction_method == 1)
                                                Cash
                                            @elseif($item->transaction_method == 2)
                                                Bank
                                            @elseif($item->transaction_method == 3)
                                                Mobile Banking
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $item->amount }}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                                <tfoot>
                                <tr>
                                    <th class="text-right" colspan="6">Total</th>
                                    <th class="text-right">{{ $result->sum('amount') }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection

@section('script')

    <script>
        $(function () {
            var accountHeadSubTypeSelected = '{{ request()->get('sub_type') }}';

            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            //Initialize Select2 Elements
            $('.select2').select2();

            $('#account_head_type').change(function () {
                var typeId = $(this).val();

                $('#account_head_sub_type').html('<option value="">All Sub Type</option>');

                if (typeId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_account_head_sub_type') }}",
                        data: { typeId: typeId }
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (accountHeadSubTypeSelected == item.id)
                                $('#account_head_sub_type').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#account_head_sub_type').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });
                }
            });

            $('#account_head_type').trigger('change');
        });

        var APP_URL = '{!! url()->full()  !!}';

        function getprint(prinarea) {
            $('#heading_area').show();
            $('body').html($('#' + prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection

