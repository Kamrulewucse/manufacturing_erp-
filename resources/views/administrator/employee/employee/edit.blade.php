
@extends('layouts.app')
@section('title','Employee Add')

@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Employee Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('employee.edit',['employee' => $employee->id ]) }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('division') ? 'has-error' :'' }}">
                            <label for="division" class="col-sm-2 col-form-label">Department<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="division" id="division">
                                    <option value="">Select Department</option>
                                    @foreach($divisions as $division)
                                        <option {{ old('division',$employee->department_id) == $division->id ? 'selected' : ''}} value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                @error('division')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('field') ? 'has-error' :'' }}">
                            <label for="field" class="col-sm-2 col-form-label">Designation<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="designation" id="designation">
                                    <option value="">Select Designation</option>
                                </select>
                                @error('field')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('id_no') ? 'has-error' :'' }}">
                            <label for="id_no" class="col-sm-2 col-form-label">ID No. <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ empty(old('id_no')) ? ($errors->has('id_no') ? '' : $employee->id_no) : old('id_no') }}"
                                       name="id_no" class="form-control" id="id_no" placeholder="Enter Employee ID No." readonly>
                                @error('id_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label for="name" class="col-sm-2 col-form-label">Employee Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $employee->name) : old('name') }}"
                                       name="name" class="form-control" id="name" placeholder="Enter Employee Name">
                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('current_address') ? 'has-error' :'' }}">
                            <label for="current_address" class="col-sm-2 col-form-label">Employee Current Address<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ empty(old('current_address')) ? ($errors->has('current_address') ? '' : $employee->current_address) : old('current_address') }}"
                                       name="current_address" class="form-control" id="current_address" placeholder="Enter Employee Current Address">
                                @error('current_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('permanent_address') ? 'has-error' :'' }}">
                            <label for="permanent_address" class="col-sm-2 col-form-label">Employee Permanent Address<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ empty(old('permanent_address')) ? ($errors->has('permanent_address') ? '' : $employee->permanent_address) : old('permanent_address') }}"
                                       name="permanent_address" class="form-control" id="permanent_address" placeholder="Enter Employee Permanent Address">
                                @error('permanent_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('mobile') ? 'has-error' :'' }}">
                            <label for="mobile" class="col-sm-2 col-form-label">Employee Mobile<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ empty(old('mobile')) ? ($errors->has('mobile') ? '' : $employee->mobile) : old('mobile') }}"
                                       name="mobile" class="form-control" id="mobile" placeholder="Enter Employee Mobile">
                                @error('mobile')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('education_qualification') ? 'has-error' :'' }}">
                            <label for="education_qualification" class="col-sm-2 col-form-label">Employee Education Qualification<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ empty(old('education_qualification')) ? ($errors->has('education_qualification') ? '' : $employee->education_qualification) : old('education_qualification') }}"
                                       name="education_qualification" class="form-control" id="education_qualification" placeholder="Enter Employee Education Qualification">
                                @error('education_qualification')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('employee_image') ? 'has-error' :'' }}">
                            <label for="employee_image" class="col-sm-2 col-form-label">Employee Image</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="employee_image">
                                @error('employee_image')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                                @if ($employee->employee_image)
                                    <img src="{{ asset($employee->employee_image) }}" alt="" height="100px">
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label for="email" class="col-sm-2 col-form-label">Employee Email</label>
                            <div class="col-sm-10">
                                <input type="email" value="{{ empty(old('email')) ? ($errors->has('email') ? '' : $employee->email) : old('email') }}"
                                       name="email" class="form-control" id="email" placeholder="Enter Employee Email">
                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

{{--                        <div class="form-group row {{ $errors->has('username') ? 'has-error' :'' }}">--}}
{{--                            <label for="username" class="col-sm-2 col-form-label">Username<span class="text-danger">*</span></label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <input type="text" value="{{ empty(old('username')) ? ($errors->has('username') ? '' : $employee->user->username) : old('username') }}"--}}
{{--                                       name="username" class="form-control" id="username" placeholder="Enter Employee Username">--}}
{{--                                @error('username')--}}
{{--                                <span class="help-block">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row {{ $errors->has('password') ? 'has-error' :'' }}">--}}
{{--                            <label for="password" class="col-sm-2 col-form-label">Password<span class="text-danger">*</span></label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password">--}}
{{--                                @error('password')--}}
{{--                                <span class="help-block">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row {{ $errors->has('password') ? 'has-error' :'' }}">--}}
{{--                            <label for="password" class="col-sm-2 col-form-label">Confirm Password<span class="text-danger">*</span></label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <input type="password" class="form-control" placeholder="Enter Confirm Password"--}}
{{--                                       name="password_confirmation">--}}
{{--                                @error('password')--}}
{{--                                <span class="help-block">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row {{ $errors->has('opening_balance') ? 'has-error' : '' }}" id="opening_balance_area">--}}
{{--                            <label class="col-sm-2 col-form-label">Opening Balance </label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <input type="text" name="opening_balance"--}}
{{--                                       value="{{ old('opening_balance',0) }}" class="form-control" placeholder="Enter opening balance">--}}
{{--                                @error('opening_balance')--}}
{{--                                <span class="help-block">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label for="status" class="col-sm-2 col-form-label">Status<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="1" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($employee->status == '1' ? 'checked' : '')) :
                                            (old('status') == '1' ? 'checked' : '') }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="0" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($employee->status == '0' ? 'checked' : '')) :
                                            (old('status') == '0' ? 'checked' : '') }}>
                                        Inactive
                                    </label>
                                </div>
                                @error('status')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('employee') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
@endsection

@section('script')
    <script>
        $(function (){
            //employe type
            $('#employee_type').change(function() {
                var employeeType = $(this).val();
                if (employeeType == 2) {
                    $("#opening_balance_area").show();
                    $("#zone_hide_show").show();
                }else {
                    $("#opening_balance_area").hide();
                    $("#zone_hide_show").hide();
                }
            });

            $('#employee_type').trigger("change");
        });
    </script>
    <script>
        $(function (){
            var fieldSelected = '{{ old('designation',$employee->designation_id) }}';

            $('#division').change(function () {
                var departmentId = $(this).val();
                $('#designation').html('<option value="">Select Designation</option>');

                if (departmentId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_designation_edit') }}",
                        data: { departmentId: departmentId }
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (fieldSelected == item.id)
                                $('#designation').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#designation').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });
                }
            });

            $('#division').trigger('change');
        });
    </script>
@endsection


