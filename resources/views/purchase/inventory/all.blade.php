@extends('layouts.app')
@section('title','Inventory')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-striped display nowrap" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>S/L</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('inventory.datatable') }}',
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'product', name: 'product.name'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'unit_price', name: 'unit_price'},
                    {data: 'action', name: 'action'},
                ],
            });

        });
    </script>
@endsection
