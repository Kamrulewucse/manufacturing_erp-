@extends('layouts.app')
@section('title','Employee Add')
@section('style')
    <style>
        .input-group-addon i{
            padding-top:10px;
            padding-right: 10px;
            border: 1px solid #cecccc;
            padding-bottom: 10px;
            padding-left: 10px;
        }
    </style>
@endsection
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
                <form action="{{ route('employee.add') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
{{--                        <div class="form-group row {{ $errors->has('employee_type') ? 'has-error' :'' }}">--}}
{{--                            <label for="employee_type" class="col-sm-2 col-form-label">Type<span class="text-danger">*</span></label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <select name="employee_type" class="form-control select2" id="employee_type">--}}
{{--                                    <option value="">Select Type</option>--}}
{{--                                    @foreach($types as $employee_type)--}}
{{--                                        <option {{ old('employee_type') == $employee_type->id ? 'selected' : ''  }} value="{{ $employee_type->id }}">{{ $employee_type->name }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                                @error('employee_type')--}}
{{--                                <span class="help-block">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group row {{ $errors->has('division') ? 'has-error' :'' }}">
                            <label for="division" class="col-sm-2 col-form-label">Department<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="division" id="division">
                                    <option value="">Select Department</option>
                                    @foreach($divisions as $division)
                                        <option {{ old('division') == $division->id ? 'selected' : ''}} value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                @error('division')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('designation') ? 'has-error' :'' }}">
                            <label for="designation" class="col-sm-2 col-form-label">Designation <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="designation" id="designation">
                                    <option value="">Select Field</option>
                                </select>
                                @error('designation')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label for="name" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('name') }}" name="name" class="form-control" id="name" placeholder="Enter Employee Name">
                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('id_no') ? 'has-error' :'' }}">
                            <label for="id_no" class="col-sm-2 col-form-label">ID No.<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ $employeeId }}" name="id_no" class="form-control" id="id_no" placeholder="Enter Employee ID No." readonly>
                                @error('id_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('current_address') ? 'has-error' :'' }}">
                            <label for="current_address" class="col-sm-2 col-form-label">Current Address<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('current_address') }}" name="current_address" class="form-control" id="current_address" placeholder="Enter Current Address">
                                @error('current_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('permanent_address') ? 'has-error' :'' }}">
                            <label for="permanent_address" class="col-sm-2 col-form-label">Permanent Address<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('permanent_address') }}" name="permanent_address" class="form-control" id="permanent_address" placeholder="Enter Permanent Address">
                                @error('permanent_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('mobile') ? 'has-error' :'' }}">
                            <label for="mobile" class="col-sm-2 col-form-label">Mobile<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ old('mobile') }}" name="mobile" class="form-control" id="mobile" placeholder="Enter Employee Mobile">
                                @error('mobile')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('education_qualification') ? 'has-error' :'' }}">
                            <label for="education_qualification" class="col-sm-2 col-form-label">Education Qualification <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('education_qualification') }}" name="education_qualification" class="form-control" id="education_qualification" placeholder="Enter Education Qualification">
                                @error('education_qualification')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('nid') ? 'has-error' :'' }}">
                            <label for="nid" class="col-sm-2 col-form-label">NID No.<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('nid') }}" name="nid" class="form-control" id="nid" placeholder="Enter NID NO">
                                @error('nid')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('reporting_to') ? 'has-error' :'' }}">
                            <label for="reporting_to" class="col-sm-2 col-form-label">Reporting To <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('reporting_to') }}" name="reporting_to" class="form-control" id="reporting_to" placeholder="Enter Reporting To">
                                @error('reporting_to')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('marital_status') ? 'has-error' :'' }}">
                            <label for="marital_status" class="col-sm-2 col-form-label">Marital Status <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="marital_status" id="marital_status">
                                    <option value="">Select Marital Status</option>
                                    <option value="1" {{ old('marital_status') == '1' ? 'selected' : '' }}>Single</option>
                                    <option value="2" {{ old('marital_status') == '2' ? 'selected' : '' }}>Married</option>
                                </select>
                                @error('marital_status')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('gender') ? 'has-error' :'' }}">
                            <label for="gender" class="col-sm-2 col-form-label">Gender <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="gender" id="gender">
                                    <option value="">Select Gender</option>
                                    <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>Male</option>
                                    <option value="2" {{ old('gender') == '2' ? 'selected' : '' }}>Female</option>
                                    <option value="3" {{ old('gender') == '3' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('cv') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label" for="cv">CV <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control pull-right" id="cv" name="cv"
                                       autocomplete="off">
                                @error('cv')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('previous_salary') ? 'has-error' :'' }}">
                            <label for="previous_salary" class="col-sm-2 col-form-label">Previous Salary <span class="text-danger"></span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('previous_salary') }}" name="previous_salary" class="form-control" id="previous_salary" placeholder="Enter Previous Salary">
                                @error('previous_salary')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('gross_salary') ? 'has-error' :'' }}">
                            <label for="gross_salary" class="col-sm-2 col-form-label">Gross Salary <span class="text-danger"></span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('gross_salary') }}" name="gross_salary" class="form-control" id="gross_salary" placeholder="Enter Gross Salary">
                                @error('gross_salary')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('bank_account') ? 'has-error' :'' }}">
                            <label for="bank_account" class="col-sm-2 col-form-label">Bank Account Number <span class="text-danger"></span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('bank_account') }}" name="bank_account" class="form-control" id="bank_account" placeholder="Bank Account Number">
                                @error('bank_account')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('joining_date') ? 'has-error' :'' }}">
                            <label for="joining_date" class="col-sm-2 col-form-label">Joining Date <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right date-picker" name="joining_date" value="{{ empty(old('joining_date')) ? ($errors->has('joining_date') ? '' : date('Y-m-d')) : old('joining_date') }}" autocomplete="off">
                                </div>
                            </div>
                            <!-- /.input group -->
                            @error('joining_date')
                            <span class="help-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group row {{ $errors->has('confirmation_date') ? 'has-error' :'' }}">
                            <label for="confirmation_date" class="col-sm-2 col-form-label">Confirmation Date <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right date-picker" id="confirmation_date" name="confirmation_date" autocomplete="off" placeholder="Confirmation Date">
                                </div>
                            </div>
                            <!-- /.input group -->
                            @error('confirmation_date')
                            <span class="help-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group row {{ $errors->has('dob') ? 'has-error' :'' }}">
                            <label for="dob" class="col-sm-2 col-form-label">Date of Birth<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right date-picker" id="dob" name="dob" autocomplete="off" placeholder="Date Of Birth">
                                </div>
                            </div>
                            <!-- /.input group -->
                            @error('dob')
                            <span class="help-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group row {{ $errors->has('father_name') ? 'has-error' :'' }}">
                            <label for="father_name" class="col-sm-2 col-form-label">Father Name<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('father_name') }}" name="father_name" class="form-control" id="father_name" placeholder="Enter Father's Name">
                                @error('father_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('mother_name') ? 'has-error' :'' }}">
                            <label for="mother_name" class="col-sm-2 col-form-label">Mother Name<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('mother_name') }}" name="mother_name" class="form-control" id="mother_name" placeholder="Enter Mother's Name">
                                @error('mother_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('employee_image') ? 'has-error' :'' }}">
                            <label for="employee_image" class="col-sm-2 col-form-label">Image <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="employee_image">
                                @error('employee_image')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" value="{{ old('email') }}" name="email" class="form-control" id="email" placeholder="Enter Employee Email">
                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('username') ? 'has-error' :'' }}">
                            <label for="username" class="col-sm-2 col-form-label">Username<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('username') }}" name="username" class="form-control" id="username" placeholder="Enter Employee Username">
                                @error('username')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('password') ? 'has-error' :'' }}">
                            <label for="password" class="col-sm-2 col-form-label">Password<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password">
                                @error('password')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('password_confirmation') ? 'has-error' :'' }}">
                            <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm Password<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" placeholder="Enter Confirm Password"
                                       name="password_confirmation" autocomplete="new_password">
                                @error('password_confirmation')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('opening_balance') ? 'has-error' : '' }}" id="opening_balance_area">
                            <label class="col-sm-2 col-form-label">Opening Balance </label>
                            <div class="col-sm-10">
                                <input type="text" name="opening_balance"
                                       value="{{ old('opening_balance',0) }}" class="form-control" placeholder="Enter opening balance">
                                @error('opening_balance')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label for="status" class="col-sm-2 col-form-label">Status<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
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

        $(function (){

            var fieldSelected = '{{ old('designation') }}';

            $('#division').change(function () {

                var divisionId = $(this).val();

                $('#designation').html('<option value="">Select Designation</option>');

                if (divisionId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_designation') }}",
                        data: { divisionId: divisionId }
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
