{{Form::open(array('url'=>'product_categorie','method'=>'post','enctype'=>'multipart/form-data'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'col-form-label'))}}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Product Category'),'required'=>'required'))}}
        </div>
        <div class="form-group">
            <label for="categorie_img" class="col-form-label">{{ __('Upload Categorie Image') }}</label>
            <input type="file" name="categorie_img" id="categorie_img"  class="form-control">
        </div>
    </div>
    <div class="form-group col-12 d-flex justify-content-end col-form-label">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Save')}}" class="btn btn-primary ms-2">
    </div>
</div>
{{Form::close()}}
