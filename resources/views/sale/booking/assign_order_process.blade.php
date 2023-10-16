@extends('layouts.app')

@section('title')
    Booking Details
@endsection

@section('style')
    <style>
        .img-overlay {
            position: absolute;
            left: 0;
            top: 200px;
            width: 100%;
            height: 100%;
            /* overflow: hidden; */
            text-align: center;
            z-index: 9;
            opacity: 0.1;
            margin-top:100px;
        }

        .img-overlay img {
            width: 600px;
            height: auto;
        }

        .address-left{
            position: absolute;
            overflow: hidden;
            margin-top:1300px;
            margin-left:20px;
            /* z-index: 10000; */
        }

        .address-right{
            position: absolute;
            overflow: hidden;
            margin-top:1375px;
            right:35px;
            top:0;
        }

        .mobile{
            width: 20px;
            float: left;
            margin-right: 10px;
        }

        .mail{
            width: 20px;
            margin-right: 10px;
        }

        .web{
            width: 20px;
            margin-right: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <hr>
                    <div id="prinarea">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @if ($booking->bookingDetails)
                                            @foreach($booking->bookingDetails->sortBy('status') as $bookingDetail)
                                                <tr style="background-color: {{ $bookingDetail->remake_request === 1 ? '#00765E' : '' }}">
                                                    <td>
                                                        @if($bookingDetail->status == 1)
                                                            <span class="badge badge-info">Processing</span>
                                                         @elseif($bookingDetail->status == 2)
                                                            <span class="badge badge-warning">Ready For Stock</span>
                                                        @elseif($bookingDetail->status == 3)
                                                            <span class="badge badge-success">Complete</span>
                                                        @elseif($bookingDetail->status == 4)
                                                            <span class="badge badge-danger">Cancel</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $bookingDetail->product->name ?? '' }}</td>
                                                    <td class="">{{ $bookingDetail->quantity }}</td>
                                                    <td class="">
                                                        @if($bookingDetail->status == 1)
                                                            <a data-id="{{$bookingDetail->id}}" class="btn btn-success btn-sm btn-complete">Finish</a>
                                                            @if(Auth::user()->role !=2)
                                                                <a data-id="{{$bookingDetail->id}}" class="btn btn-danger btn-sm btn-cancel">Cancel</a>
                                                            @endif
                                                        @elseif($bookingDetail->status == 2)
                                                            @if(Auth::user()->role == 2)
                                                                <span class="badge badge-warning">waiting For Accept</span>
                                                            @else
                                                                <a data-id="{{$bookingDetail->id}}" class="btn btn-info btn-sm btn-accept">Accept</a>
                                                                <a data-id="{{$bookingDetail->id}}" class="btn btn-warning btn-sm btn-remake">ReMake</a>
                                                            @endif
                                                        @elseif($bookingDetail->status == 3)
                                                            <span class="badge badge-success">Complete</span>
                                                            <a data-id="{{$bookingDetail->id}}" class="btn btn-warning btn-sm btn-remake">ReMake</a>
                                                            @if(Auth::user()->role !=2)
                                                                <a data-id="{{$bookingDetail->id}}" class="btn btn-danger btn-sm btn-cancel">Cancel</a>
                                                            @endif
                                                        @elseif($bookingDetail->status == 4)
                                                            <span class="badge badge-danger">Cancel Order</span>
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-right">Total</th>
                                            <th class="">{{$booking->quantity }}</th>
                                            <th class=""></th>
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

@endsection

@section('script')

    <script>
        var APP_URL = '{!! url()->full()  !!}';
        function getprint(prinarea) {
            $('#heading_area').show();
            $('body').html($('#'+prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
        $('body').on('click', '.btn-complete', function () {
            var accountHeadId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "Post",
                        url: "{{ route('complete_assign_order') }}",
                        data: { id: accountHeadId }
                    }).done(function( response ) {
                        if (response.success) {
                            Swal.fire(
                                'Completed!',
                                response.message,
                                'success'
                            ).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    });

                }
            })

        });
        $('body').on('click', '.btn-cancel', function () {
            var accountHeadId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Canceled it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "Post",
                        url: "{{ route('cancel_assign_order') }}",
                        data: { id: accountHeadId }
                    }).done(function( response ) {
                        if (response.success) {
                            Swal.fire(
                                'Completed!',
                                response.message,
                                'success'
                            ).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    });

                }
            })

        });
        $('body').on('click', '.btn-accept', function () {
            var accountHeadId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Accept it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "Post",
                        url: "{{ route('stock_assign_order') }}",
                        data: { id: accountHeadId }
                    }).done(function( response ) {
                        if (response.success) {
                            Swal.fire(
                                'Accepted!',
                                response.message,
                                'success'
                            ).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    });

                }
            })

        });
        $('body').on('click', '.btn-remake', function () {
            var accountHeadId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, ReMake it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "Post",
                        url: "{{ route('remake_assign_order') }}",
                        data: { id: accountHeadId }
                    }).done(function( response ) {
                        if (response.success) {
                            Swal.fire(
                                'Accepted!',
                                response.message,
                                'success'
                            ).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    });

                }
            })

        });

        $(function () {
            $('#table-payments').DataTable({
                "order": [[ 0, "desc" ]],
            });

        });
    </script>
@endsection
