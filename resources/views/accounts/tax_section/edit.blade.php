@extends('layouts.app')
@section('title','Section Number of TDS Edit')
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-default">
                <div class="card-header">
                    <h3 class="card-title">Section Number of TDSs Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{ route('section_number_of_tds.edit',['taxSection'=>$taxSection->id]) }}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('source') ? 'has-error' :'' }}">
                            <label for="source" class="col-sm-3 col-form-label">Source<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('source',$taxSection->source) }}" name="source" class="form-control" id="source" placeholder="Enter Source">
                                @error('source')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('section') ? 'has-error' :'' }}">
                            <label for="section" class="col-sm-3 col-form-label">Section</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('section',$taxSection->section) }}" name="section" class="form-control" id="section" placeholder="Enter Section">
                                @error('section')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('sort') ? 'has-error' :'' }}">
                            <label for="sort" class="col-sm-3 col-form-label">Sort<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('sort',$taxSection->sort) }}" name="sort" class="form-control" id="sort" placeholder="Enter Sort">
                                @error('sort')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
@endsection

