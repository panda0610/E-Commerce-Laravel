{{ Form::model($productCategorie, ['route' => ['product_categorie.update', $productCategorie->id],'method' => 'PUT','enctype' => 'multipart/form-data']) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Product Category')]) }}
            @error('name')
                <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group">
            <label for="categorie_img" class="col-form-label">{{ __('Upload Categorie Image') }}</label>
            <input type="file" name="categorie_img" id="categorie_img" class="form-control">
        </div>
    </div>
</div>
<div class="form-group col-12 d-flex justify-content-end col-form-label">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
{{ Form::close() }}
