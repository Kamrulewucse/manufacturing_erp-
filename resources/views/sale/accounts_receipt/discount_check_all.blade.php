@extends('layouts.app')
@section('title', 'Technician Service')

@section('style')
    <style>
        .table-bordered>thead>tr>th,
        .table-bordered>tbody>tr>th,
        .table-bordered>tfoot>tr>th,
        .table-bordered>thead>tr>td,
        .table-bordered>tbody>tr>td,
        .table-bordered>tfoot>tr>td {
            white-space: nowrap;
        }

        .modal-content {
            position: relative;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            /*width: 600px;*/
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, .2);
            border-radius: .3rem;
            box-shadow: 0 0.25rem 0.5rem rgb(0 0 0 / 50%);
            outline: 0;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
{{--                                <th>Date</th>--}}
{{--                                <th>Service Order No.</th>--}}
                                <th>Job Id</th>
                                <th>Technician</th>
                                <th>Discount Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-receive">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Accept Service Payment From Technician</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">

                        <input type="hidden" id="service-Id" name="service_id">

                        <div class="form-group">
                            <label>Discount Amount</label>
                            <input class="form-control" name="discount_amount" id="discount-amount"
                                   placeholder ="Enter Discount Amount">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-receipt">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function() {
            var message = '{{ session('message') }}';
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('discount_check.datatable') }}',
                columns: [
                    // {
                    //     data: 'sales_order',
                    //     name: 'saleOrder.order_no'
                    // },
                    {
                        data: 'complain_job_id',
                        name: 'complain_job_id'
                    },
                    {
                        data: 'employee',
                        name: 'employee.name'
                    },
                    {
                        data: 'discount_amount',
                        name: 'discount_amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ],
                order: [
                    [0, "desc"]
                ],
            });

            $('body').on('click', '.btn-approve', function() {
                var serviceId = $(this).data('id');
                var discountAmount = $(this).data('discount-amount');

                $('#service-Id').val(serviceId);
                $('#discount-amount').val(discountAmount);
                $('#modal-receive').modal('show');
            });

            $('#modal-btn-receipt').click(function() {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('account_approved_discount_amount') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-receive').modal('hide');
                            Swal.fire(
                                'Success!',
                                response.message,
                                'success'
                            ).then((result) => {
                                //location.reload();
                                window.location.href = response.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });

            $('body').on('click', '.btn-cancel', function () {

                var orderId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Cancel This Discount!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Cancel it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "Post",
                            url: "{{ route('discount_check_cancel') }}",
                            data: { orderId: orderId }
                        }).done(function( response ) {
                            if (response.success) {
                                Swal.fire(
                                    'Cencel!',
                                    response.message,
                                    'success'
                                ).then((result) => {
                                    //location.reload();
                                    window.location.href = response.redirect_url;
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

        });

    </script>
@endsection
