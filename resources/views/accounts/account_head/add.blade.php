@extends('layouts.app')
@section('title','Account Head Add')
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Account Head Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{ route('account_head.add') }}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label for="name" class="col-sm-3 col-form-label">Name of the Account<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('name') }}" name="name" class="form-control" id="name" placeholder="Enter Designation">
                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('account_code') ? 'has-error' :'' }}">
                            <label for="account_code" class="col-sm-3 col-form-label">Account Code <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text"  value="{{ old('account_code',$maxCode) }}" name="account_code" class="form-control" id="account_code" placeholder="Account Code">
                                @error('account_code')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('type') ? 'has-error' :'' }}">
                            <label for="type" class="col-sm-3 col-form-label">Type <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select name="type" id="type" class="form-control select2">
                                    <option value="">Select Type</option>
                                    @foreach($types as $type)
                                        <option {{ old('type') == $type->id }} value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div id="sub_type_area" style="display: none" class="form-group row {{ $errors->has('sub_type') ? 'has-error' :'' }}">
                            <label for="sub_type" class="col-sm-3 col-form-label">Sub Type <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select name="sub_type" id="sub_type" class="form-control select2">
                                    <option value="">Select Sub Type</option>
                                    <option {{ old('sub_type') == 1 ? 'selected' : '' }} value="1">Fixed Assets</option>
                                    <option {{ old('sub_type') == 2 ? 'selected' : '' }} value="2">Current Assets</option>
                                </select>
                                @error('sub_type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('opening_balance') ? 'has-error' :'' }}">
                            <label for="opening_balance" class="col-sm-3 col-form-label">Opening Balance <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('opening_balance',0) }}" name="opening_balance" class="form-control" id="opening_balance" placeholder="Enter Opening Balance">
                                @error('opening_balance')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-3 col-form-label">Status <span class="text-danger">*</span></label>

                            <div class="col-sm-9">

                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="active" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}>
                                    <label for="active">
                                        Active
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inactive" name="status" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                    <label for="inactive">
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
                        <a href="{{ route('account_head.add') }}" class="btn btn-default float-right">Cancel</a>

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
        $(function () {

            $("#type").change(function () {
                var type = $(this).val();
                if (type != '') {
                    if (type == 1) {
                        $("#sub_type_area").show();
                    } else {
                        $("#sub_type_area").hide();
                    }
                } else {
                    $("#sub_type_area").hide();
                }
            });

            $("#type").trigger('change');
        });
    </script>
@endsection
