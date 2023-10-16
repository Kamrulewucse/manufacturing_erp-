
@extends('layouts.app')
@section('title','Supplier Edit')

@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Supplier Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('supplier.edit', ['supplier' => $supplier->id]) }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label for="name" class="col-sm-2 col-form-label">Supplier Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $supplier->name) : old('name') }}"
                                       name="name" class="form-control" id="name" placeholder="Enter Supplier Name">
                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('owner_name') ? 'has-error' :'' }}">
                            <label for="owner_name" class="col-sm-2 col-form-label">Owner Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ empty(old('owner_name')) ? ($errors->has('owner_name') ? '' : $supplier->owner_name) : old('owner_name') }}"
                                       name="owner_name" class="form-control" id="owner_name" placeholder="Enter Owner Name">
                                @error('owner_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                            <label for="mobile_no" class="col-sm-2 col-form-label">Mobile No <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ empty(old('mobile_no')) ? ($errors->has('mobile_no') ? '' : $supplier->mobile) : old('mobile_no') }}"
                                       name="mobile_no" class="form-control" id="mobile_no" placeholder="Enter Mobile No">
                                @error('mobile_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ empty(old('email')) ? ($errors->has('email') ? '' : $supplier->email) : old('email') }}"
                                       name="email" class="form-control" id="email" placeholder="Enter Email">
                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('address') ? 'has-error' :'' }}">
                            <label for="address" class="col-sm-2 col-form-label">Address<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ empty(old('address')) ? ($errors->has('address') ? '' : $supplier->address) : old('address') }}"
                                       name="address" class="form-control" id="address" placeholder="Enter Address">
                                @error('address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('opening_due') ? 'has-error' :'' }}">
                            <label for="opening_due" class="col-sm-2 col-form-label">Opening Due<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ old('opening_due', $supplier->opening_due) }}"
                                       name="opening_due" class="form-control" id="opening_due" placeholder="Enter Opening_due">
                                @error('opening_due')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label for="status" class="col-sm-2 col-form-label">Status<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="1" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($supplier->status == '1' ? 'checked' : '')) :
                                            (old('status') == '1' ? 'checked' : '') }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label class="col-form-label">
                                        <input type="radio" name="status" value="0" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($supplier->status == '0' ? 'checked' : '')) :
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
                        <a href="{{ route('supplier') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
@endsection

