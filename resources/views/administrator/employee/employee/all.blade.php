
@extends('layouts.app')
@section('title','Employee')
@section('style')
    <style>
        table.table-bordered.dataTable th, table.table-bordered.dataTable td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <a href="{{ route('employee.add') }}" class="btn btn-primary bg-gradient-primary">Add Employee</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="white-space: nowrap">S/L</th>
                                    <th style="white-space: nowrap">ID NO</th>
                                    <th style="white-space: nowrap">Employee Name</th>
                                    <th style="white-space: nowrap">Mobile</th>
                                    <th style="white-space: nowrap">Department</th>
                                    <th style="white-space: nowrap">Designation</th>
                                    <th style="white-space: nowrap">Address</th>
                                    <th style="white-space: nowrap">Status</th>
                                    <th style="white-space: nowrap">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('employee.datatable') }}',
                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'id_no', name: 'id_no',orderable: false},
                    {data: 'name', name: 'name',orderable: false},
                    {data: 'mobile', name: 'mobile'},
                    {data: 'department', name: 'department.name',orderable: false},
                    {data: 'designation', name: 'designation.name', orderable: false},
                    {data: 'permanent_address', name: 'permanent_address', orderable: false},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "responsive": true, "autoWidth": false,
            });

            var designationSelected;
            $('body').on('click', '.btn-change-designation', function () {
                var employeeId = $(this).data('id');

                $.ajax({
                    method: "GET",
                    url: "{{ route('get_employee_details') }}",
                    data: { employeeId: employeeId }
                }).done(function( response ) {
                    $('#modal-employee-id').val(response.id_no);
                    $('#modal-name').val(response.name);
                    $('#modal-id').val(response.id);
                    $('#modal-department').val(response.department_id);
                    designationSelected = response.designation_id;
                    $('#modal-department').trigger('change');

                    $('#modal-update-designation').modal('show');
                });
            });

            $('#modal-department').change(function () {
                var departmentId = $(this).val();
                $('#modal-designation').html('<option value="">Select Designation</option>');

                if (departmentId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_employee_designation') }}",
                        data: { departmentId: departmentId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (designationSelected == item.id)
                                $('#modal-designation').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#modal-designation').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        designationSelected = '';
                    });
                }
            });

            $('#modal-btn-update').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('employee.designation_update') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-update-designation').modal('hide');
                            Swal.fire(
                                'Updated!',
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
                    }
                });
            });

        });
    </script>
@endsection

