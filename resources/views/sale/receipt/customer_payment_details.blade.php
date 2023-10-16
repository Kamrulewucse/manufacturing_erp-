@extends('layouts.app')
@section('title')
    Supplier Payment Details
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="table-payments" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Financial Year</th>
                                        <th>Transaction Method</th>
                                        <th>Cheque no</th>
                                        <th>Amount</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($receiptPayments as $receiptPayment)
                                        <tr>
                                            <td>{{ $receiptPayment->date}}</td>
                                            <td>{{ $receiptPayment->financial_year}}</td>
                                            <td>
                                                @if($receiptPayment->payment_type == 2)
                                                    Cash
                                                @else
                                                    Bank
                                                @endif
                                            </td>
                                            <td>{{ $receiptPayment->cheque_no?? '' }}</td>
                                            <td>৳ {{ number_format($receiptPayment->amount, 2) }}</td>
                                            <td>{{ $receiptPayment->notes }}</td>
                                            <td>
                                                <a href="{{ route('sale_receipt.all_payment_details', ['payment' => $receiptPayment->id]) }}"  class="btn btn-primary btn-sm">Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
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
            $('#table-payments').DataTable({
                "order": [[ 0, "desc" ]],
            });

        });
    </script>
@endsection
