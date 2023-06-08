{{ Form::model($pageOption, ['route' => ['custom-page.update', $pageOption->id], 'method' => 'PUT']) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'form-label'))}}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name')]) }}
            @error('name')
                <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="form-group col-md-6">
        <div class="custom-control form-switch">
            <input type="checkbox" class="form-check-input" name="enable_page_header" id="enable_page_header"
                {{ $pageOption['enable_page_header'] == 'on' ? 'checked=checked' : '' }}>
            {{ Form::label('enable_page_header', __('Page Header Display'), ['class' => 'form-check-label mb-3']) }}
        </div>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('contents', __('Contents'), ['class' => 'col-form-label']) }}
        {{ Form::textarea('contents', null, ['class' => 'form-control summernote-simple','rows' => 3,'placeholder' => __('Contents')]) }}
        @error('contents')
            <span class="invalid-contents" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="form-group col-12 d-flex justify-content-end col-form-label">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
{{ Form::close() }}
