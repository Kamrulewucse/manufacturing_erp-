@extends('layouts.app')
@section('title','Product Add')

@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Product Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('product_add') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('product_type') ? 'has-error' :'' }}">
                            <label for="product_type" class="col-sm-2 col-form-label">Product Type <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="product_type" class="form-control select2" id="product_type">
                                    <option value="">Select Product Type</option>
                                    <option value="1" {{ old('product_type') == 1  ? 'selected' : '' }}>Finish Good</option>
                                    <option value="2" {{ old('product_type') == 2  ? 'selected' : '' }}>Row Material</option>
                                </select>
                                @error('product_type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('category') ? 'has-error' :'' }}" id="category">
                            <label for="category" class="col-sm-2 col-form-label">Category <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="category" class="form-control select2" id="category">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}" {{ old('category') == $category->id  ? 'selected' : '' }}>{{$category->name}}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label for="name" class="col-sm-2 col-form-label">Product Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('name') }}" name="name" class="form-control" id="name" placeholder="Enter Product Name">
                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('unit') ? 'has-error' :'' }}">
                            <label for="unit" class="col-sm-2 col-form-label">unit <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="unit" class="form-control select2" id="unit">
                                    <option value="">Select unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{$unit->id}}" {{ old('unit') == $unit->id  ? 'selected' : '' }}>{{$unit->name}}</option>
                                    @endforeach
                                </select>
                                @error('unit')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('warranty') ? 'has-error' :'' }}">
                            <label for="warranty" class="col-sm-2 col-form-label">Warranty</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('warranty') }}" name="warranty" class="form-control" id="warranty" placeholder="Enter warranty">
                                @error('warranty')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('warning_quantity') ? 'has-error' :'' }}">
                            <label for="warning_quantity" class="col-sm-2 col-form-label">Warning Quantity <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('warning_quantity') }}" name="warning_quantity" class="form-control" id="warning_quantity" placeholder="Enter warning quantity">
                                @error('warning_quantity')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('description') ? 'has-error' :'' }}">
                            <label for="description" class="col-sm-2 col-form-label">Description</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('description') }}" name="description" class="form-control" id="description" placeholder="Enter Product Description">
                                @error('description')
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
                        <a href="{{ route('all_product') }}" class="btn btn-default float-right">Cancel</a>
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
            var categorySelected = '{{ old('category') }}';
            $('#category').change(function () {
                var categoryID = $(this).val();
                // alert(categoryID);

                $('#subcategory').html('<option value="">Select Sub Category</option>');

                if (categoryID != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_subCategory') }}",
                        data: { categoryID: categoryID }
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (categorySelected == item.id)
                                $('#subcategory').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#subcategory').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        $('#subcategory').trigger('change');
                    });
                }
            });

            $('#category').trigger('change');
            $("#category").hide();
            $('#product_type').change(function (){
                var product_type = $(this).val();
                if (product_type == '1'){
                    $("#category").show();
                }else{
                    $("#category").hide();
                }

            });
            $('#product_type').trigger("change");
        });
    </script>
@endsection
