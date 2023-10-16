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
                    <div class="row">
                        <div class="col-md-12 ">
                            <a style="float: right;" target="_blank" href="{{ route('booking_receipt.print', ['booking' => $booking->id]) }}" class="btn btn-primary"><i class="fa fa-print"></i>Receipt Print</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        @if(auth()->user()->role !=2)
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Customer No.</th>
                                    <td>{{ $booking->customer->name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Customer Mobile.</th>
                                    <td>{{ $booking->customer->mobile ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Customer Address</th>
                                    <td>{{ $booking->customer->address ?? ''}}</td>
                                </tr>
                            </table>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" class="text-center">Delivery Info</th>
                                </tr>

                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Delivery Date</th>
                                    <td>{{ \Carbon\Carbon::parse($booking->delivery_date)->format('d-m-Y') }}</td>
                                </tr>

                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>

                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @if ($booking->bookingDetails)
                                        @foreach($booking->bookingDetails as $bookingDetail)
                                            <tr>

                                                <td>{{ $bookingDetail->product->name ?? '' }}</td>
                                                <td class="">{{ $bookingDetail->quantity }}</td>
                                                <td>
                                                    @if($bookingDetail->status == 0)
                                                        <span class="badge badge-info">Pending</span>
                                                    @elseif($bookingDetail->status == 1)
                                                        <span class="badge badge-info">Processing</span>
                                                    @elseif($bookingDetail->status == 2)
                                                        <span class="badge badge-success">Ready For Stock</span>
                                                    @elseif($bookingDetail->status == 3)
                                                        <span class="badge badge-success">Complete</span>
                                                    @elseif($bookingDetail->status == 4)
                                                        <span class="badge badge-danger">Cancel</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="1" class="text-right">Total</th>
                                        <th class="">{{$booking->quantity }}</th>
                                    </tr>
                                    <tr>
                                        @if(auth()->user()->role !=2)
                                        <th colspan="2" class="text-right">Advance Amount</th>
                                        <th colspan="3">৳{{ number_format($booking->total_amount, 2) }}</th>
                                        @endif
                                    </tr>
                                    <tr>
                                        @if(auth()->user()->role !=2)
                                        <th colspan="2" class="text-right">Advance Adjust Amount</th>
                                        <th colspan="3">৳{{ number_format($booking->total_amount-$booking->advance_amount, 2) }}</th>
                                        @endif
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

@endsection

@section('script')

    <script>
        var APP_URL = '{!! url()->full()  !!}';

        $(function () {
            $('#table-payments').DataTable({
                "order": [[ 0, "desc" ]],
            });

        });
    </script>
@endsection
