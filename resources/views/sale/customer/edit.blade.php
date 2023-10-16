@extends('layouts.app')
@section('title', 'Customer Edit')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Customer Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST"
                    action="{{ route('customer.edit', ['customer' => $customer->id]) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="col-sm-2 col-form-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name" name="name"
                                    value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $customer->name) : old('name') }}">

                                @error('name')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('mobile_no') ? 'has-error' : '' }}">
                            <label class="col-sm-2 col-form-label">Mobile No. *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mobile No." name="mobile_no"
                                    value="{{ empty(old('mobile_no')) ? ($errors->has('mobile_no') ? '' : $customer->mobile) : old('mobile_no') }}">

                                @error('mobile_no')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('address') ? 'has-error' : '' }}">
                            <label class="col-sm-2 col-form-label">Address</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Address" name="address"
                                    value="{{ empty(old('address')) ? ($errors->has('address') ? '' : $customer->address) : old('address') }}">

                                @error('address')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label for="email" class="col-sm-2 col-form-label">Customer Email</label>
                            <div class="col-sm-10">
                                <input type="email"
                                    value="{{ empty(old('email')) ? ($errors->has('email') ? '' : $customer->email ?? '') : old('email') }}"
                                    name="email" class="form-control" id="email" placeholder="Enter Customer Email">
                                @error('email')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        @if ($customer->is_opening_show == 1)
                            <div class="form-group row {{ $errors->has('opening_due') ? 'has-error' : '' }}">
                                <label for="opening_due" class="col-sm-2 col-form-label">Opening Balance<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="number" value="{{ old('opening_due', $customer->opening_due) }}"
                                        name="opening_due" class="form-control" id="opening_due"
                                        placeholder="Enter Opening_due">
                                    @error('opening_due')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif


                        <div class="form-group row {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label for="status" class="col-sm-2 col-form-label">Status<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="1"
                                            {{ empty(old('status'))
                                                ? ($errors->has('status')
                                                    ? ''
                                                    : ($customer->status == '1'
                                                        ? 'checked'
                                                        : ''))
                                                : (old('status') == '1'
                                                    ? 'checked'
                                                    : '') }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="0"
                                            {{ empty(old('status'))
                                                ? ($errors->has('status')
                                                    ? ''
                                                    : ($customer->status == '0'
                                                        ? 'checked'
                                                        : ''))
                                                : (old('status') == '0'
                                                    ? 'checked'
                                                    : '') }}>
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
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection