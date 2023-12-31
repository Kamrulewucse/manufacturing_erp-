@extends('layouts.app')
@section('title', 'Customer Add')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Customer Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('customer.add') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name" class="col-sm-2 col-form-label">Customer Name <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('name') }}" name="name" class="form-control"
                                    id="name" placeholder="Enter Customer Name">
                                @error('name')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('mobile_no') ? 'has-error' : '' }}">
                            <label for="mobile_no" class="col-sm-2 col-form-label">Mobile No <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ old('mobile_no') }}" name="mobile_no" class="form-control"
                                    id="mobile_no" placeholder="Enter Mobile No">
                                @error('mobile_no')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('address') ? 'has-error' : '' }}">
                            <label for="address" class="col-sm-2 col-form-label">Address</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('address') }}" name="address" class="form-control"
                                    id="address" placeholder="Enter Address">
                                @error('address')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('email') }}" name="email" class="form-control"
                                    id="email" placeholder="Enter Email">
                                @error('email')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('opening_due') ? 'has-error' : '' }}">
                            <label for="opening_due" class="col-sm-2 col-form-label">Opening Balance</label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ old('opening_due', 0) }}" name="opening_due"
                                    class="form-control" id="opening_due" placeholder="Enter opening balance">
                                @error('opening_due')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label for="status" class="col-sm-2 col-form-label">Status<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="1"
                                            {{ old('status') == '1' ? 'checked' : '' }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="0"
                                            {{ old('status') == '0' ? 'checked' : '' }}>
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
                        <a href="{{ route('customer.add') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
        </div>
    </div>
@endsection