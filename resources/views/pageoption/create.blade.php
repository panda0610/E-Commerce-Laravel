{{Form::open(array('url'=>'custom-page','method'=>'post'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'form-label'))}}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="form-group col-md-6">
        <div class="custom-control form-switch">
            <input type="checkbox" class="form-check-input" name="enable_page_header" id="enable_page_header">
            {{Form::label('enable_page_header',__('Page Header Display'),array('class'=>'form-check-label mb-3')) }}
        </div>
    </div>
    <div class="form-group col-md-12">
        {{Form::label('contents',__('Content'),array('class'=>'col-form-label')) }}
        {{Form::textarea('contents',null,array('class'=>'form-control summernote-simple','rows'=>3,'placehold   er'=>__('Content')))}}
    </div>
    <div class="form-group col-12 d-flex justify-content-end col-form-label">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Save')}}" class="btn btn-primary ms-2">
    </div>
</div>
{{Form::close()}}
