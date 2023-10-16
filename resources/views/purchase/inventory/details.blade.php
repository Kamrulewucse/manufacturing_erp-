@extends('layouts.app')

@section('title')
    Inventory Details-{{ $product->name }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Filter</h3>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <div class="row">
{{--                        <div class="col-md-4">--}}
{{--                            <div class="form-group">--}}
{{--                                <label>Date</label>--}}

{{--                                <div class="input-group">--}}
{{--                                    <div class="input-group-addon">--}}
{{--                                        <i class="fa fa-calendar"></i>--}}
{{--                                    </div>--}}
{{--                                    <input type="text" class="form-control pull-right" id="date" autocomplete="off">--}}
{{--                                </div>--}}
{{--                                <!-- /.input group -->--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Type</label>

                                <select class="form-control" id="type">
                                    <option value="">All Type</option>
                                    <option value="1">In</option>
                                    <option value="2">Out</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Order</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('inventory.details.datatable') }}",
                    data: function (d) {
                        d.product_id = {{ $product->id }},
                        d.date = $('#date').val(),
                        d.type = $('#type').val()
                    }
                },
                columns: [
                    {data: 'date', name: 'date'},
                    {data: 'type', name: 'type'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'unit_price', name: 'unit_price'},
                    {data: 'purchase_order', name: 'purchaseOrder.order_no'},
                ],
                order: [[ 0, "desc" ]],
            });

            //Date range picker
            $('#date').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                table.ajax.reload();
            });

            $('#date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });

            $('#date, #type').change(function () {
                table.ajax.reload();
            });
        })
    </script>
@endsection
