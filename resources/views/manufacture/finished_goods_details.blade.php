@extends('layouts.app')

@section('title')
    {{ $finishedGoods->product->name ?? '' }} Consumption Details
@endsection
@section('style')
    <style>
        table, .table, table td,
        .table-bordered {
            border: 1px solid #000000;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #000000 !important;
        }

        .table td, .table th {
            padding: 2px;
            vertical-align: middle;
        }
        @media print{
            @page {
                size: auto;
                margin: 15px !important;
            }
        }
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="#" onclick="getprint('printArea')" class="btn btn-dark btn-sm"><i class="fa fa-print"></i></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive-sm" id="printArea">
                        <div class="row print-heading">
                            <div class="col-12">
                                <h1 class="text-center m-0" style="font-size: 40px !important;font-weight: bold">
{{--                                    <img height="50px" src="{{ asset('img/logo.png') }}" alt="">--}}
                                    <h1 class="text-center">SAFETY MARK MANUFACTURING FACTORY</h1>
                                </h1>
                                <h3 class="text-center  m-0" style="font-size: 25px !important;">{{ $finishedGoods->product->name ?? '' }} Consumption Details</h3>
                                <h3 class="text-center" style="font-size: 19px !important;">Date: {{ \Carbon\Carbon::parse($finishedGoods->date)->format('d-m-Y') }}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Row Product Item</th>
                                        <th class="text-center">Unit</th>
                                        <th class="text-center">Consumption Quantity</th>
                                        <th class="text-center">Unit Price</th>
                                        <th class="text-center">Consumption Value</th>
                                        <th class="text-center">Loss Quantity(%)</th>
                                        <th class="text-center">Loss Quantity</th>
                                        <th class="text-center">Loss Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $totalConsumptionValue = 0;
                                        $totalConsumptionLossValue = 0;
                                    ?>
                                    @foreach($finishedGoods->finishedGoodsRowMaterials as $rowMaterial)
                                        <?php
                                            $totalConsumptionValue += $rowMaterial->consumption_unit_price * $rowMaterial->consumption_quantity;
                                            $totalConsumptionLossValue += $rowMaterial->consumption_loss_quantity * $rowMaterial->consumption_unit_price;
                                        ?>
                                        <tr>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($rowMaterial->date)->format('d-m-Y') }}</td>
                                            <td>{{ $rowMaterial->product->name ?? '' }}</td>
                                            <td class="text-center">{{ $rowMaterial->product->unit->name ?? '' }}</td>
                                            <td class="text-right">{{ number_format($rowMaterial->consumption_quantity,2) }}</td>
                                            <td class="text-right">{{ number_format($rowMaterial->consumption_unit_price,2) }}</td>
                                            <td class="text-right">{{ number_format($rowMaterial->consumption_unit_price * $rowMaterial->consumption_quantity,2) }}</td>
                                            <td class="text-right">{{ number_format($rowMaterial->consumption_loss_quantity_percent,2) }}</td>
                                            <td class="text-right">{{ number_format($rowMaterial->consumption_loss_quantity,2) }}</td>
                                            <td class="text-right">{{ number_format($rowMaterial->consumption_loss_quantity * $rowMaterial->consumption_unit_price,2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-right">Total</th>
                                            <td class="text-right">{{ number_format($finishedGoods->finishedGoodsRowMaterials->sum('consumption_quantity'),2) }}</td>
                                            <td class="text-right"></td>
                                            <td class="text-right">{{ number_format($totalConsumptionValue,2) }}</td>
                                            <td class="text-right"></td>
                                            <td class="text-right">{{ number_format($finishedGoods->finishedGoodsRowMaterials->sum('consumption_loss_quantity'),2) }}</td>
                                            <td class="text-right">{{ number_format($totalConsumptionLossValue,2) }}</td>
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

        function getprint(print) {
            $('.print-heading').css('display','block');
            $('.extra_column').remove();
            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
