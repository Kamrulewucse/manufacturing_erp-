@extends('layouts.app')
@section('title','Excel File Import')
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Excel File Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{ route('common_import') }}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('excel_file') ? 'has-error' :'' }}">
                            <label for="excel_file" class="col-sm-2 col-form-label">Excel File <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="file"  name="excel_file" class="form-control" id="excel_file">
                                @error('excel_file')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
@endsection
