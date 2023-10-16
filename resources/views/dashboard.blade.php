@extends('layouts.app')
@section('title','Dashboard')
@section('content')

@if(auth()->user()->role == 2)
    <h1 class="text-center">Welcome to Safety Mark !</h1>
@else
    <div class="row">

        <div class="col-lg-3 col-6">

            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>৳ {{ number_format($todaySale-$todayReturn, 2) }}</h3>
                    <p>TODAY'S TOTAL SALE</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>৳ {{ number_format($todayCashSale, 2) }}</h3>
                    <p>TODAY'S TOTAL CASH SALE</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">

            <div class="small-box bg-info">
                <div class="inner">
                    <h3>৳ {{ number_format($todayDue, 2) }}</h3>
                    <p>TODAY'S TOTAL DUE</p>
                </div>
{{--                <div class="icon">--}}
{{--                    <i class="ion ion-bag"></i>--}}
{{--                </div>--}}
{{--                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>--}}
            </div>
        </div>
        <div class="col-lg-3 col-6">

            <div class="small-box bg-success">
                <div class="inner">
                    <h3>৳ {{ number_format($totalSale-$totalReturn, 2) }}</h3>
                    <p>TOTAL SALES</p>
                </div>
{{--                <div class="icon">--}}
{{--                    <i class="ion ion-bag"></i>--}}
{{--                </div>--}}
{{--                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>--}}
            </div>
        </div>
        <div class="col-lg-3 col-6">

            <div class="small-box bg-success">
                <div class="inner">
                    <h3>৳ {{ number_format($totalReturn, 2) }}</h3>
                    <p>TOTAL MONEY RETURNS</p>
                </div>
{{--                <div class="icon">--}}
{{--                    <i class="ion ion-bag"></i>--}}
{{--                </div>--}}
{{--                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>--}}
            </div>
        </div>
        <div class="col-lg-3 col-6">

            <div class="small-box bg-info">
                <div class="inner">
                    <h3>৳ {{ number_format($todayExpense, 2) }}</h3>
                    <p>TODAY'S TOTAL EXPENSE</p>
                </div>
{{--                <div class="icon">--}}
{{--                    <i class="ion ion-bag"></i>--}}
{{--                </div>--}}
{{--                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>--}}
            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-success">
                <div class="inner">
                    <h3>৳ {{ number_format($totalExpense, 2) }}</h3>
                    <p>TOTAL EXPENSE</p>
                </div>
            </div>
        </div>



    </div>


@endif
@endsection

@section('script')
    <script src="{{ asset('themes/backend/plugins/chartjs/Chart.bundle.min.js') }}"></script>
    <script>

        var ctx = document.getElementById('chart-sales-amount');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                // labels: saleAmountLabel,
                datasets: [{
                    // label: 'Sales Amount',
                    // data: saleAmount,
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor:  'rgba(60,141,188,1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: false,
                },
                tooltips: {
                    displayColors: false,
                    callbacks: {
                        label: function (tooltipItems, data) {
                            return   "৳" + tooltipItems.yLabel;
                        }
                    }
                }
            }
        });

        var ctx2 = document.getElementById("chart-order-count").getContext('2d');
        var myChart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                // labels: saleAmountLabel,
                datasets: [{
                    // data: orderCount,
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor:  'rgba(60,141,188,1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: false,
                },
                tooltips: {
                    displayColors: false
                }
            }
        });
    </script>
@endsection
