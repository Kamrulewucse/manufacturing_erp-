@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection

@section('title')
    Purchase Inventory Edit
@endsection

@section('content')

    @if(Session::has('message'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('message') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Purchase Inventory Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('purchase_inventory.edit', ['inventory'=>$inventory->id]) }}">
                    @csrf

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Product Name </th>
                                    <th>Warehouse </th>
                                    <th width="10%">Quantity</th>
                                    <th width="10%">Unit Price</th>
                                    <th width="10%">Selling Price</th>
                                </tr>
                                </thead>

                                <tbody id="product-container">
                                <tr class="product-item">
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control product_item" name="product_item" value="{{$inventory->product->name??''}}" readonly required style="width: 100%;">
                                            <input type="hidden" class="form-control" name="purchase_inventory_id" value="{{$inventory->id}}">
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control product_category" name="product_category" value="{{$inventory->warehouse->name??''}}" readonly required style="width: 100%;" >
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="number" step="any" class="form-control quantity" value="{{ $inventory->quantity }}" name="quantity" required>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control unit_price" name="unit_price" value="{{ $inventory->unit_price }}" readonly required>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control selling_price" name="selling_price" value="{{ $inventory->selling_price }}" readonly required>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ asset('themes/backend/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <script>
        $(function () {

        })
    </script>
@endsection
