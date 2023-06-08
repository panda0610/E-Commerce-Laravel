{{Form::model($blog, array('route' => array('blog.update', $blog->id), 'method' => 'PUT','enctype'=>'multipart/form-data')) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('title',__('Title'),array('class'=>'form-label'))}}
            {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title')))}}
            @error('title')
            <span class="invalid-title" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label for="blog_cover_image" class="col-form-label">{{ __('Blog Cover image') }}</label>
            <input type="file" name="blog_cover_image" id="blog_cover_image"  class="form-control">
        </div>
    </div>
    <div class="form-group col-md-12">
        {{Form::label('detail',__('Detail'),array('class'=>'col-form-label')) }}
        {{Form::textarea('detail',null,array('class'=>'form-control summernote-simple','rows'=>3,'placehold   er'=>__('Detail')))}}
        @error('detail')
        <span class="invalid-detail" role="alert">
             <strong class="text-danger">{{ $message }}</strong>
         </span>
        @enderror
    </div>
</div>
<div class="form-group col-12 d-flex justify-content-end col-form-label">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
{{Form::close()}}
