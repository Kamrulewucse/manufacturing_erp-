@extends('layouts.app')
@section('title','Product Status')

@section('style')
    <style>
        .form-control {
            width: 100%;
        }

        th {
            text-align: center;
        }
        select.form-control {
            min-width: 130px;
        }
        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
            vertical-align: middle;
        }
        td .form-group {
            margin-bottom: 0;
        }
        .hide-tech {
            display: none;
        }
    </style>
@endsection


@section('content')
        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Products Details</h3>
                    </div>
                    <div class="card-body">
                        <div id="own_product_area">
                            <div class="row">
                                <div class="col-xs-9 col-md-4">
                                    <div class="form-group">
                                        <label for="supporting_document">Serial Search</label>
                                        <input autocomplete="off" type="text" id="serial_no" class="form-control" placeholder="Enter Serial No">
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="multiple-sale-product">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-custom-form" >
                                            <thead>
                                            <tr>
                                                <th style="white-space: nowrap; min-width: 160px;">Serial</th>
                                                <th style="white-space: nowrap; min-width: 160px;">Product Name</th>
                                                <th style="white-space: nowrap; min-width: 160px;">Customer Name</th>
                                                <th style="white-space: nowrap; min-width: 140px;">Manufacture Date</th>
                                                <th style="white-space: nowrap; min-width: 140px;">Sale Date</th>
                                                <th style="white-space: nowrap; min-width: 140px;">Warranty</th>
                                                <th style="white-space: nowrap; min-width: 140px;">Status</th>
                                                <th class="{{ Auth::user()->role == 2 ? 'hide-tech' : '' }}" style="white-space: nowrap; min-width: 140px;">Action</th>
                                                <th style="white-space: nowrap;"></th>
                                            </tr>
                                            </thead>
                                            <tbody id="product-container">
{{--                                                search product details  here--}}
                                            </tbody>
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
        $(document).ready(function() {
            $('#serial_no').keydown(function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); // Prevent form submission
                    searchProduct();
                }
            });

            function searchProduct() {
                var serial = $('#serial_no').val();

                if (isSerialAlreadyAdded(serial)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Serial number already exists below.',
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('search_product_status_details') }}",
                    method: 'GET',
                    data: { serial: serial },
                    success: function(response) {
                        if (response.success) {
                            // Append the product details to the table
                            $('.product-item').remove();
                            var saleId = response.product.sale_id;
                            var route = "{{ route('sale_receipt.details', ['order' => ':saleId']) }}";
                            route = route.replace(':saleId', saleId);
                            if(response.product.date == '') {
                                status = '<span class="badge badge-info">In Stock</span>';
                            }else {
                                status = '<span class="badge badge-danger">Sold Out</span>';
                            }

                            <?php

                            $statusClass = (Auth::user()->role == 2) ? 'hide-tech' : '';
                            ?>


                            var newRow = '<tr class="product-item">' +
                                '<td><input type="text" class="form-control serial" name="serial" value="' + response.product.serial + '" readonly></td>' +
                                '<td><input type="text" class="form-control product" name="product" value="' + response.product.name + '" readonly></td>' +
                                '<td><input type="text" class="form-control customer" name="customer" value="' + response.product.customer + '" readonly></td>' +
                                '<td><input type="text" class="form-control manufacture_date" name="manufacture_date" value="' + response.product.manufacture_date + '" readonly></td>' +
                                '<td><input type="text" class="form-control sale_date" name="sale_date" value="' + response.product.date + '" readonly></td>' +
                                '<td><input type="text" class="form-control warranty" name="warranty" value="' + response.product.warranty + '" readonly></td>' +
                                '<td>' + status + '</td>' +
                                '<td class=" <?= $statusClass ?>">' + (response.product.date == '' ? ' ' : '<a href="' + route + '">View Invoice </a>') + '</td>' +
                                '<td class="text-center"><a role="button" class="btn btn-danger btn-sm btn-remove">X</a></td>' +
                                '</tr>';

                            $('#product-container').append(newRow);

                            // Clear the input fields
                            $('#serial_no').val('');

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text:response.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Input fields empty please enter serial number !',
                        });
                    }
                });
            }
            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
            });
            // Check if a serial number already exists
            function isSerialAlreadyAdded(serial) {
                var serials = $('.serial').map(function() {
                    return $(this).val();
                }).get();

                return serials.includes(serial);
            }

            // ... rest of your code ...
        });
    </script>


@endsection
