@extends('layouts.app')
@section('title','Account Head Edit')
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-default">
                <div class="card-header">
                    <h3 class="card-title">Account Head Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{ route('account_head.edit',['accountHead'=>$accountHead->id]) }}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label for="name" class="col-sm-3 col-form-label">Name of the Account <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $accountHead->name }}" name="name" class="form-control" id="name" placeholder="Enter Name of the Account">
                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('account_code') ? 'has-error' :'' }}">
                            <label for="account_code" class="col-sm-3 col-form-label">Account Code <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text"  value="{{ $accountHead->account_code }}" name="account_code" class="form-control" id="account_code" placeholder="Account Code">
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
                                        <option {{ $accountHead->account_head_type_id == $type->id ? 'selected' : '' }} value="{{ $type->id }}">{{ $type->name }}</option>
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
                                    <option {{ $accountHead->account_head_sub_type_id == 1 ? 'selected' : '' }} value="1">Fixed Assets</option>
                                    <option {{ $accountHead->account_head_sub_type_id == 2 ? 'selected' : '' }} value="2">Current Assets</option>
                                </select>
                                @error('sub_type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('opening_balance') ? 'has-error' :'' }}">
                            <label for="opening_balance" class="col-sm-3 col-form-label">Opening Balance <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('opening_balance',$accountHead->opening_balance) }}" name="opening_balance" class="form-control" id="opening_balance" placeholder="Enter Opening Balance">
                                @error('opening_balance')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-dark">Save</button>
                        <a href="{{ route('account_head') }}" class="btn btn-default float-right">Cancel</a>
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

            $("#type").change(function (){
                var type = $(this).val();
                if (type != ''){
                    if (type == 1){
                        $("#sub_type_area").show();
                    }else{
                        $("#sub_type_area").hide();
                    }
                }else{
                    $("#sub_type_area").hide();
                }
            });

            $("#type").trigger('change');

        });
    </script>
@endsection
